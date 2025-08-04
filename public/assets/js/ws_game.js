export const WSGAME = {
    URL: (window.location.protocol.toLowerCase() === "https:" ? "wss://localhost:8080" : "ws://localhost:8080"),
    ws : function() {
        if (!window.WebSocket) {
            return;
        }
        if (this.ws) {
            return this.ws;
        }
        try {
            this.ws = new WebSocket(this.URL);
            return this.ws;
        } catch (e) {
            console.log("Ошибка: ", e);
            return;
        }
    },
    call: function(data, success, error) {
        this.ws().onclose = function(e) {
            console.log("Соединение закрыто WSGAME: ", e);
            return;
        };
        this.ws().onmessage = function(event) {
            const json = JSON.parse(event.data);
            socket.close();
            success(event, json);
        };
        this.ws().onopen = function() {
            this.ws().send(JSON.stringify(data));
        };
    }
};