/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-d3.d.ts" />

import * as React from 'react';
import {LineChart} from 'react-d3';
import {WsConnector} from './ws/serverConnector';

export interface ChartState {
    ipData: {[ip:string]: Array<number>};
}

export class Chart extends React.Component<any, ChartState> {
    constructor(props:any, context:any) {
        super(props, context);

        WsConnector.globalListeners.push(this.updateData.bind(this));
        this.state = {ipData: {}};
    }

    updateData(data) {
        var newPingList = {};
        data.forEach((item) => {
            if (null == this.state.ipData[item.ip]) {
                this.state.ipData[item.ip] = [];
            }

            this.state.ipData[item.ip].push(item.latency);

            if (this.state.ipData[item.ip].length > 50) {
                this.state.ipData[item.ip].shift();
            }

            newPingList[item.ip] = 1;
        });

        for (var ip in this.state.ipData) {
            if (1 != newPingList[ip]) {
                this.state.ipData[ip] = null;
            }
        }

        this.forceUpdate();
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

