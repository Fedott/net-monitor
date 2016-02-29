
export class Request {
    id: number;
    command: string;
    params;
    resultFunction: any;
}

export class Response {
    result;
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
    globalListeners: Function[];

    constructor() {
        this.wsConnection = new WebSocket('ws://localhost:1788/ping');
        this.wsConnection.addEventListener('message', this.parseResponse.bind(this));
        this.requests = {};
        this.globalListeners = [];
    }
    
    sendRequest(request: Request) {
        this.requests[request.id] = request;
        
        this.wsConnection.send(JSON.stringify({
            id: request.id,
            command: request.command,
            params: request.params,
        }));
    }

    parseResponse(event: MessageEvent) {
        var data = JSON.parse(event.data);
        var requestId = data.id;
        if (null != this.requests[requestId]) {
            var response = new Response();
            response.result = data.result;
            if (null != this.requests[requestId].resultFunction) {
                this.requests[requestId].resultFunction(response);
            }
        } else if (null === requestId) {
            this.globalListeners.forEach((func) => {
                func(data.result);
            })
        }
    }
}
var WsConnectorInstance = new WsConnectorClass();
export var WsConnector = WsConnectorInstance;
