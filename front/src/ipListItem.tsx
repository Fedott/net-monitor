/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-mdl.d.ts" />

import * as React from 'react'
import {ListItem, ListItemContent, ListItemAction, Switch, IconButton, Button} from 'react-mdl'
import {RequestFactory, WsConnector} from "./ws/serverConnector";
import {Chart} from "./charts";
import {Container} from "./container";

export interface Ip {
    ip: string;
    lastTracedIp: string;
    traceLatency: string;
    traceSteps: number;
    traceFails: number;
    ping?: boolean;
    checked?: boolean;
}

export interface IpItemState {
    ip: Ip;
}

export interface IpItemProps {
    item: Ip;
}

export class IpListItem extends React.Component<IpItemProps, IpItemState> {
    constructor(props: IpItemProps, state: IpItemState) {
        super(props, state);

        this.state = {
            ip: props.item,
        };
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            ip: nextProps.item,
        })
    }

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
        if (this.props.item.ping) {
            this.togglePing();
            this.forceUpdate();
        }

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
                <ListItemContent>{this.state.ip.ip}</ListItemContent>
                <ListItemAction>
                    <div>{this.state.ip.traceSteps} | {this.state.ip.traceLatency} | {this.state.ip.traceFails}</div>
                </ListItemAction>
                <ListItemAction>
                    <Button onClick={this.toggleChecked.bind(this)} disabled={this.state.ip.checked}>Hide</Button>
                </ListItemAction>
                <ListItemAction>
                    <Button onClick={this.startTrace.bind(this)} disabled={this.state.ip.checked}>Trace</Button>
                </ListItemAction>
            </ListItem>
        );
    }
}
