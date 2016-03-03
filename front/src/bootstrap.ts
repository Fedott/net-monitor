/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/browser/ambient/react-dom/react-dom.d.ts" />

import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {IpList, IpListProps} from './ipList';
import {Chart} from "./charts";

var chartElement = React.createElement(Chart);
var ipListElement = React.createElement(IpList);

ReactDOM.render(ipListElement, document.getElementById('ip-list'));
ReactDOM.render(chartElement, document.getElementById('chart-area'));
