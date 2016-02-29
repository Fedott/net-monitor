/// <reference path="../typings/browser/ambient/react/react.d.ts" />
/// <reference path="../typings/react-d3.d.ts" />

import * as React from 'react';
import {LineChart} from 'react-d3';

export class Chart extends React.Component<any, any> {

    render() {
        var lineData = [
            {
                name: "192.168.1.1",
                values: [
                    {x: 1, y: 10},
                    {x: 2, y: 9},
                    {x: 3, y: 8},
                    {x: 4, y: 11},
                    {x: 5, y: 12},
                    {x: 6, y: 16},
                    {x: 7, y: 1},
                    {x: 8, y: 14},
                    {x: 9, y: 13},
                ]
            }
        ];
        var viewBox = {
            x: 0,
            y: 0,
            width: 500,
            height: 400
        };

        return (
            <LineChart
                legend={true}
                width={700}
                height={400}
                data={lineData}
                viewBoxObject={viewBox}
                title="Latency chart"
                yAxisLabel="Latency"
                xAxisLabel="Count"
                gridHorizontal={true}

            />
        );
    }
}

