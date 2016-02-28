/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

import {List} from 'react-mdl';

export interface IpListState {
    ipList?: Ip[];
}

export interface IpListPops {}

export class IpList extends React.Component<IpListPops, IpListState> {
    state: IpListState;

    constructor(props:IpListPops, context:any) {
        super(props, context);

        this.state = {ipList: []};
    }

    reloadList(event) {
        console.log('reload');

        var request:Request = RequestFactory.createRequest();
        request.command = 'getIps';
        request.resultFunction = this.updateCallback.bind(this);

        WsConnector.sendRequest(request);
    }

    updateCallback(response: Response) {
        var ipList:Ip[] = response.data.ips.map(function (ipText:string) {
            return {ip: ipText};
        });

        this.setState({ipList: ipList});

    }

    render() {
        var i = 0;
        var IpItems = this.state.ipList.map(item => {
            return <IpListItem item={item} key={i++} />
        });

        console.log(IpItems);

        return (
            <div>
                <a className="mdl-button" id="refresh-ips" onClick={this.reloadList.bind(this)} >Обновить</a>
                <List>{IpItems}</List>
            </div>
        );
    }
}
