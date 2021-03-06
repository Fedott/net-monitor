
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
        const request = new Request();
        request.id = this.id++;

        return request;
    }
}

const RequestFactoryInstance = new RequestFactoryClass();
export let RequestFactory = RequestFactoryInstance;

class WsConnectorClass {
    requests: {[id:number]: Request};
    wsConnection: WebSocket;
    globalResponseListeners: Function[];
    isConnected: boolean = false;
    connectionListeners: Function[];

    constructor() {
        this.requests = {};
        this.globalResponseListeners = [];
        this.connectionListeners = [];

        this.connectToWebSocket();
    }

    private connectToWebSocket() {
        this.wsConnection = new WebSocket('ws://192.168.1.17:1788/ping');
        this.wsConnection.addEventListener('message', this.parseResponse.bind(this));
        this.wsConnection.addEventListener('open', this.connected.bind(this));
        this.wsConnection.addEventListener('close', this.disconnected.bind(this));
    };

    connected() {
        this.isConnected = true;
        this.emmitConnectionStatusEvent();
        console.log(this.isConnected, 'connected');
    }

    disconnected() {
        this.isConnected = false;
        console.log(this.isConnected, 'disconnected');

        setTimeout(this.connectToWebSocket.bind(this), 1000);
    }

    emmitConnectionStatusEvent() {
        this.connectionListeners.forEach((func) => {
            func(this.isConnected);
        })
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
        const data = JSON.parse(event.data);
        const requestId = data.id;
        if (null != this.requests[requestId]) {
            const response = new Response();
            response.result = data.result;
            if (null != this.requests[requestId].resultFunction) {
                this.requests[requestId].resultFunction(response);
            }
        } else if (null === requestId) {
            this.globalResponseListeners.forEach((func) => {
                func(data.result);
            })
        }
    }
}
const WsConnectorInstance = new WsConnectorClass();
export let WsConnector = WsConnectorInstance;
