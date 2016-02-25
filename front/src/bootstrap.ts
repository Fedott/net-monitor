/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/browser/ambient/react-dom/react-dom.d.ts" />

import * as React from 'react';
import * as ReactDOM from 'react-dom';
import {IpList} from './ipList';

ReactDOM.render(React.createElement(IpList), document.getElementById('ip-list'));
