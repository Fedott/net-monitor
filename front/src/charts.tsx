/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-d3.d.ts" />

import * as React from 'react';
import {LineChart} from 'react-d3';
import {WsConnector} from './ws/serverConnector';
import {Container} from "./container";

export interface ChartState {
    ipData: {[ip:string]: Array<number>};
}

export class Chart extends React.Component<any, ChartState> {
    constructor(props:any, context:any) {
        super(props, context);

        WsConnector.globalResponseListeners.push(this.updateData.bind(this));
        this.state = {ipData: {}};

        Container.chart = this;
    }

    updateData(data) {
        data.forEach((item) => {
            if (null == this.state.ipData[item.ip]) {
                this.state.ipData[item.ip] = [];
            }

            this.state.ipData[item.ip].push(item.latency);

            if (this.state.ipData[item.ip].length > 50) {
                this.state.ipData[item.ip].shift();
            }

        });

        this.forceUpdate();
    }
    
    clearDataByIp(ip:string) {
        if (null != this.state.ipData[ip]) {
            this.state.ipData[ip] = null;
        }
    }

    render() {
        var viewBox = {
            x: 0,
            y: 0,
            width: 900,
            height: 400
        };

        return (
            <LineChart
                legend={true}
                width={1000}
                height={400}
                data={this.getLineData()}
                viewBoxObject={viewBox}
                title="Latency chart"
                yAxisLabel="Latency"
                xAxisLabel="Count"
                gridHorizontal={true}
            />
        );
    }

    private getLineData() {
        var lineData = [];
        for (var ip in this.state.ipData) {
            if (null == this.state.ipData[ip]) {
                continue;
            }
            
            var row = {
                name: ip,
                values: this.state.ipData[ip].map((latency, index) => {
                    return {x: index, y: latency};
                }),
            };

            lineData.push(row);
        }

        return lineData;
    }
}

