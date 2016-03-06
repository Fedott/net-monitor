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

    constructor(props) {
        super(props);

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
        this.addContent(response.result.output);
    }

    stopTrace() {
        this.setState({openDialog: false, content: ''});
    }

    addContent(additionContent: string) {
        this.state.content += additionContent;

        this.forceUpdate();
    }

    render() {
        return (
            <Dialog open={this.state.openDialog}>
                <DialogTitle>TracePath {this.ip}</DialogTitle>
                <DialogContent>{this.state.content}</DialogContent>
                <DialogActions>
                    <Button type="button" onClick={this.stopTrace}>Cancel</Button>
                </DialogActions>
            </Dialog>
        );
    }
}
