/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

import {List} from 'react-mdl';
import {Chart} from "./charts";

export interface IpListState {
    ipList?: {[id:string]: Ip};
}

export interface IpListProps {}

export class IpList extends React.Component<IpListProps, IpListState> {
    state: IpListState;
    reloadListCycle: boolean = false;

    constructor(props:IpListProps, context:any) {
        super(props, context);

        this.state = {ipList: {}};
    }

    toggleReloadList() {
        this.reloadListCycle = !this.reloadListCycle;

        this.reloadList();
    }

    reloadList() {
        if (!this.reloadListCycle) {
            return;
        }

        var request:Request = RequestFactory.createRequest();
        request.command = 'getIps';
        request.resultFunction = this.updateCallback.bind(this);

        WsConnector.sendRequest(request);
    }

    updateCallback(response: Response) {
        var oldList = this.state.ipList;
        var newList: {[id:string]: Ip} = {};
        response.result.ips.forEach(function (ipText:string) {
            if (null == oldList[ipText]) {
                newList[ipText] = {ip: ipText};
            } else {
                newList[ipText] = oldList[ipText];
            }
        });

        this.setState({ipList: newList});

        setTimeout(this.reloadList.bind(this), 1000);
    }

    render() {
        var IpItems = [];
        for (var itemKey in this.state.ipList) {
            var item:Ip = this.state.ipList[itemKey];
            IpItems.push(<IpListItem item={item} key={item.ip} />);
        }

        return (
            <div>
                <a className="mdl-button" id="refresh-ips" onClick={this.toggleReloadList.bind(this)}>Запустить/Остановить обновление</a>
                <List>{IpItems}</List>
            </div>
        );
    }
}
