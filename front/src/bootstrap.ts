import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {IpList} from './ipList';
import {TraceDialog} from "./traceDialog";

const ipListElement = React.createElement(IpList);
const traceDialogElement = React.createElement(TraceDialog, {});

ReactDOM.render(ipListElement, document.getElementById('ip-list'));
ReactDOM.render(traceDialogElement, document.getElementById("traceDialog"));
