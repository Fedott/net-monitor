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
    checked?: boolean;
}

export interface IpItemState {}

export interface IpItemProps {
    item: Ip;
}

export class IpListItem extends React.Component<IpItemProps, IpItemState> {
    toggleChecked() {
        this.props.item.checked = !this.props.item.checked;
        this.forceUpdate();
    }

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
        Container.traceDialog.startTrace(this.props.item.ip);
    }

    render() {
        var style = {
            color: null,
        };
        if (this.props.item.checked) {
            style.color = "lightgray";
        }

        return (
            <ListItem style={style}>
                <ListItemContent>{this.props.item.ip}</ListItemContent>
                <ListItemAction>
                    <IconButton name="cancel" onClick={this.toggleChecked.bind(this)} />
                    <IconButton name="track_changes" onClick={this.startTrace.bind(this)} />
                    <Switch onChange={this.togglePing.bind(this)} />
                </ListItemAction>
            </ListItem>
        );
    }
}
