/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-mdl.d.ts" />

import * as React from 'react'
import {ListItem, ListItemContent, ListItemAction, Switch} from 'react-mdl'

export interface Ip {
    ip: string;
    ping?: boolean;
}

export interface IpItemState {}

export interface IpItemProps {
    item: Ip;
}

export class IpListItem extends React.Component<IpItemProps, IpItemState> {
    togglePing() {
        this.props.item.ping = !this.props.item.ping;

        console.log(this.props.item);
    }

    render() {
        return (
            <ListItem>
                <ListItemContent>{this.props.item.ip}</ListItemContent>
                <ListItemAction info="Ping">
                    <Switch onChange={this.togglePing.bind(this)} />
                </ListItemAction>
            </ListItem>
        );
    }
}
