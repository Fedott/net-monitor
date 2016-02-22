console.log('Hello, world');

var socket = new WebSocket("ws://localhost:1788/ping");

var data = [];

document.addEventListener("DOMContentLoaded", function(event) {
    document.getElementById('refresh-ips').onclick = function () {
        var request = WsRequestFactory.getNewRequest();
        request.command = "getIps";

        socket.send(JSON.stringify(request));
        var eventListener = function (event) {
            var data = JSON.parse(event.data);
            if (data.id == request.id) {
                console.log(data);
                event.target.removeEventListener('message', eventListener);
            }
        };
        socket.addEventListener('message', eventListener)
    };
});
