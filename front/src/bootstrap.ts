/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/browser/ambient/react-dom/react-dom.d.ts" />

import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {IpList, IpListProps} from './ipList';
import {Chart} from "./charts";
import {TraceDialog} from "./traceDialog";

var ipListElement = React.createElement(IpList);
var traceDialogEleent = React.createElement(TraceDialog, {});

ReactDOM.render(ipListElement, document.getElementById('ip-list'));
ReactDOM.render(traceDialogEleent, document.getElementById("traceDialog"));
