/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {Button, Dialog, DialogActions, DialogContent, DialogTitle} from 'react-mdl';
import {Container} from './container';
import {RequestFactory, WsConnector, Response, Request} from './ws/serverConnector';

interface TraceDialogState {
    openDialog: boolean;
    content: string;
}

interface TraceDialogProps {}

export class TraceDialog extends React.Component<TraceDialogProps, TraceDialogState> {
    state: TraceDialogState;
    ip: string;

    constructor(props, context:any) {
        super(props, context);

        this.state = {openDialog: false, content: ''};
        this.startTrace = this.startTrace.bind(this);
        this.stopTrace = this.stopTrace.bind(this);
        this.traceCallback = this.traceCallback.bind(this);

        Container.traceDialog = this;
    }

    startTrace(ip:string) {
        this.setState({openDialog: true, content: ''});
        
        this.ip = ip;
        
        var request = RequestFactory.createRequest();
        request.command = "startTrace";
        request.params = {
            "ip": ip
        };
        request.resultFunction = this.traceCallback;

        WsConnector.sendRequest(request);
    }
    
    traceCallback(response: Response) {
        this.addContent(response.result.content);
    }

    stopTrace() {
        this.setState({openDialog: false, content: ''});
    }

    addContent(additionContent: string) {
        this.state.content += additionContent;

        this.forceUpdate();
    }

    render() {
        var i = 1;
        var content = this.state.content.split("\n").map((str) => {
            i++;
            return (
                <span key={i}>
                    {str} <br/>
                </span>
            )
        });

        if (this.state.openDialog) {
            setTimeout(() => {
                document.querySelector("dialog").style.top = "10px";
            })
        }

        return (
            <Dialog open={this.state.openDialog}>
                <DialogTitle>TracePath {this.ip}</DialogTitle>
                <DialogContent>{content}</DialogContent>
                <DialogActions>
                    <Button type="button" onClick={this.stopTrace}>Cancel</Button>
                </DialogActions>
            </Dialog>
        );
    }
}
