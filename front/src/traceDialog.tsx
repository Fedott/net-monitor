/// <reference path="../typings/browser/ambient/react/react.d.ts" />

import * as React from 'react';
import {Button, Dialog, DialogActions, DialogContent, DialogTitle} from 'react-mdl';
import {Container} from './container';

interface TraceDialogState {
    openDialog: boolean;
    content: string;
}

interface TraceDialogProps {}

export class TraceDialog extends React.Component<TraceDialogProps, TraceDialogState> {
    state: TraceDialogState;

    constructor(props) {
        super(props);

        this.state = {openDialog: false, content: ''};
        this.open = this.open.bind(this);
        this.close = this.close.bind(this);

        Container.traceDialog = this;
    }

    open() {
        this.setState({openDialog: true, content: ''});
    }

    close() {
        this.setState({openDialog: false, content: ''});
    }

    addContent(additionContent: string) {
        this.state.content += additionContent;

        this.forceUpdate();
    }

    render() {
        return (
            <Dialog open={this.state.openDialog}>
                <DialogTitle>TracePath output</DialogTitle>
                <DialogContent>{this.state.content}</DialogContent>
                <DialogActions>
                    <Button type="button" onClick={this.close}>Cancel</Button>
                </DialogActions>
            </Dialog>
        );
    }
}
