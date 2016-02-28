
export class Request {
    id: number;
    command: string;
    resultFunction: any;
}

export class Response {
    data;
}

class RequestFactoryClass {
    id: number = 1;

    createRequest() {
        var request = new Request();
        request.id = this.id++;

        return request;
    }
}

var RequestFactoryInstance = new RequestFactoryClass();
export var RequestFactory = RequestFactoryInstance;

class WsConnectorClass {
    requests: {[id:number]: Request};
    wsConnection: WebSocket;

    constructor() {
        this.wsConnection = new WebSocket('ws://localhost:1788/ping');
        this.wsConnection.addEventListener('message', this.parseResponse.bind(this));
        this.requests = {};
    }
    
    sendRequest(request: Request) {
        this.requests[request.id] = request;
        
        this.wsConnection.send(JSON.stringify({
            id: request.id,
            command: request.command
        }));
    }

    parseResponse(event: MessageEvent) {
        var data = JSON.parse(event.data);
        var requestId = data.id;
        if (null !== this.requests[requestId]) {
            var response = new Response();
            response.data = data;
            this.requests[requestId].resultFunction(response);
        }
    }
}
var WsConnectorInstance = new WsConnectorClass();
export var WsConnector = WsConnectorInstance;
