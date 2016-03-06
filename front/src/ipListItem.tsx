/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-mdl.d.ts" />

import * as React from 'react'
import {ListItem, ListItemContent, ListItemAction, Switch, IconButton} from 'react-mdl'
import {RequestFactory, WsConnector} from "./ws/serverConnector";
import {Chart} from "./charts";
import {Container} from "./container";

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
        var request = RequestFactory.createRequest();
        if (this.props.item.ping) {
            request.command = "startPing";
        } else {
            request.command = "stopPing";
            Container.chart.clearDataByIp(this.props.item.ip);
        }
        request.params = {ip: this.props.item.ip};
        
        WsConnector.sendRequest(request);
    }

    startTrace() {
        Container.traceDialog.open();
        Container.traceDialog.addContent("Test trace output");
    }

    render() {
        return (
            <ListItem>
                <ListItemContent>{this.props.item.ip}</ListItemContent>
                <ListItemAction>
                    <IconButton name="track_changes" onClick={this.startTrace.bind(this)} />
                    <Switch onChange={this.togglePing.bind(this)} />
                </ListItemAction>
            </ListItem>
        );
    }
}
