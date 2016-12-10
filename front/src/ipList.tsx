import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

import {List, Switch} from 'react-mdl';

export interface IpListState {
    ipList?: {[id:string]: Ip};
    filtered?: boolean;
    wsConnected?: boolean;
}

export interface IpListProps {}

export class IpList extends React.Component<IpListProps, IpListState> {
    state: IpListState;
    reloadListCycle: boolean = false;

    constructor(props:IpListProps, context:any) {
        super(props, context);

        this.state = {ipList: {}, filtered: true, wsConnected: WsConnector.isConnected};
        WsConnector.connectionListeners.push(this.wsConnectionStatusListener.bind(this));
    }

    wsConnectionStatusListener(status: boolean) {
        this.setState({wsConnected: status});
    }

    toggleReloadList() {
        this.reloadListCycle = !this.reloadListCycle;

        this.reloadList();
    }

    reloadList() {
        if (!this.reloadListCycle) {
            this.forceUpdate();
            return;
        }

        const request: Request = RequestFactory.createRequest();
        request.command = 'getIps';
        request.resultFunction = this.updateCallback.bind(this);
        request.params = {withoutFilter: !this.state.filtered};

        WsConnector.sendRequest(request);
    }

    updateCallback(response: Response) {
        const oldList = this.state.ipList;
        const newList: {[id: string]: Ip} = {};
        response.result.ips.forEach(function (ipObject) {
            newList[ipObject.ip] = {
                ip: ipObject.ip,
                lastTracedIp: ipObject.lastTracedIp,
                traceLatency: ipObject.traceLatency,
                traceSteps: ipObject.traceSteps,
                traceFails: ipObject.traceFails,
            };

            if (null != oldList[ipObject.ip]) {
                newList[ipObject.ip].checked = oldList[ipObject.ip].checked;
            }
        });

        this.setState({ipList: newList});

        setTimeout(this.reloadList.bind(this), 1000);
    }

    toggleFiltered() {
        // this.state.filtered = !this.state.filtered;
        this.setState({filtered: !this.state.filtered});
    }

    render() {
        const IpItems = [];
        for (let itemKey in this.state.ipList) {
            const item: Ip = this.state.ipList[itemKey];
            IpItems.push(<IpListItem item={item} key={item.ip} />);
        }

        let stopStartMessage;
        if (this.reloadListCycle) {
            stopStartMessage = "Остановить обновление";
        } else {
            stopStartMessage = "Запустить обновление";
        }

        return (
            <div>
                <div id="controls">
                    <span id="control-reload">
                        <button
                            className="mdl-button"
                            id="refresh-ips"
                            onClick={this.toggleReloadList.bind(this)}
                        >{stopStartMessage}</button>
                    </span>
                    <span id="control-filter">
                        <Switch onChange={this.toggleFiltered.bind(this)} checked={this.state.filtered} />
                    </span>
                </div>
                <List>{IpItems}</List>
            </div>
        );
    }
}
