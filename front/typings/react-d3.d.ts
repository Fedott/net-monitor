declare namespace __ReactD3 {
    import {Component} from 'react';

    export class LineChart extends Component<any, any> {}
}

declare module 'react-d3' {
    export = __ReactD3;
}
