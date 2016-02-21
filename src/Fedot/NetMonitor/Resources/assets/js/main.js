console.log('Hello, world');

var socket = new WebSocket("ws://localhost:1788/ping");

var data = [];

document.addEventListener("DOMContentLoaded", function(event) {
    var chart = new CanvasJS.Chart("chart",{
        title :{
            text: "Net Monitor"
        },
        data: [{
            type: "line",
            dataPoints: data
        }]
    });

    chart.render();
});
