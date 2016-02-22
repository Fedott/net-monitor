var WsRequestFactory = {
    lastId: 1,
    getNewRequest: function () {
        return {
            id: this.lastId++
        };
    }
};
