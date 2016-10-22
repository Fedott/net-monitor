/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

import {List, Switch, Badge} from 'react-mdl';

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

        var request:Request = RequestFactory.createRequest();
        request.command = 'getIps';
        request.resultFunction = this.updateCallback.bind(this);
        request.params = {withoutFilter: !this.state.filtered};

        WsConnector.sendRequest(request);
    }

    updateCallback(response: Response) {
        var oldList = this.state.ipList;
        var newList: {[id:string]: Ip} = {};
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
        var IpItems = [];
        for (var itemKey in this.state.ipList) {
            var item:Ip = this.state.ipList[itemKey];
            IpItems.push(<IpListItem item={item} key={item.ip} />);
        }

        var stopStartMessage;
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
