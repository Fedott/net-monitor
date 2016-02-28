/// <reference path="../typings/browser/ambient/react/react.d.ts" />

declare namespace __ReactMDL {
    import {Component} from 'react';
    
    export class List extends Component<any, any> {}
    export class ListItem extends Component<any, any> {}
    export class ListItemContent extends Component<any, any> {}
    export class ListItemAction extends Component<any, any> {}
    export class Checkbox extends Component<any, any> {}
    export class Switch extends Component<any, any> {}
}

declare module 'react-mdl' {
    export = __ReactMDL;
}