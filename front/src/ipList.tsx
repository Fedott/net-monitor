/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

import {List} from 'react-mdl';

export interface IpListState {
    ipList?: {[id:string]: Ip};
}

export interface IpListPops {}

export class IpList extends React.Component<IpListPops, IpListState> {
    state: IpListState;

    constructor(props:IpListPops, context:any) {
        super(props, context);

        this.state = {ipList: {}};
    }

    reloadList(event) {
        console.log('reload');

        var request:Request = RequestFactory.createRequest();
        request.command = 'getIps';
        request.resultFunction = this.updateCallback.bind(this);

        WsConnector.sendRequest(request);
    }

    updateCallback(response: Response) {
        var oldList = this.state.ipList;
        var newList: {[id:string]: Ip} = {};
        response.data.ips.forEach(function (ipText:string) {
            if (null == oldList[ipText]) {
                newList[ipText] = {ip: ipText};
            } else {
                newList[ipText] = oldList[ipText];
            }
        });

        this.setState({ipList: newList});

    }

    render() {
        var IpItems = [];
        for (var itemKey in this.state.ipList) {
            var item = this.state.ipList[itemKey];
            IpItems.push(<IpListItem item={item} key={item.ip} />);
        }

        console.log(IpItems);

        return (
            <div>
                <a className="mdl-button" id="refresh-ips" onClick={this.reloadList.bind(this)} >Обновить</a>
                <List>{IpItems}</List>
            </div>
        );
    }
}
