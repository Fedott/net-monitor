/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {IpListItem, Ip} from './ipListItem';

export interface IpListState {
    ipList?: Ip[];
}

export interface IpListPops {}

export class IpList extends React.Component<IpListPops, IpListState> {
    state: IpListState = {ipList: []};

    reloadList() {
        console.log('reload');
    }

    render() {
        var IpItems = this.state.ipList.map(item => {
            return <IpListItem item={item} />
        });

        return (
            <div>
                <a className="mdl-button" id="refresh-ips" onClick={this.reloadList} >Обновить</a>
                <ul className="mdl-list ip-list">{IpItems}</ul>
            </div>
        );
    }
}
