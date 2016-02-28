/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-mdl.d.ts" />

import * as React from 'react'
import {ListItem, ListItemContent, ListItemAction, Switch} from 'react-mdl'

export interface Ip {
    ip: string;
}

export interface IpItemState {}

export interface IpItemProps {
    item: Ip;
    ping?: boolean;
}

export class IpListItem extends React.Component<IpItemProps, IpItemState> {
    startPing() {
        
    }

    render() {
        return (
            <ListItem>
                <ListItemContent>{this.props.item.ip}</ListItemContent>
                <ListItemAction>
                    <Switch />
                </ListItemAction>
            </ListItem>
        );
    }
}
