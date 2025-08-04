export class WSPlugin{
    static ws = null;
    static callbacks = {};

    static {
        if (!this.ws) this.ws = new WebSocket((location.protocol == "https:" ? "wss" : "ws") + '://' + location.hostname  + ":8080");
    }

    static on(event, fn){
        const events = ["onmessage", "onopen", "onerror", "onclose"];
        
        if (!this.ws) return;
        if (!events.includes(event)) return;
        if (!typeof fn === "function") return;
        
        if (event === "onmessage") {
            this.ws.onmessage = (message) => {
                const data = JSON.parse(message.data);
                fn(data);
            };
        } else {
            this.ws[event] = fn;
        }
    }

    static send(data = []){
        console.log(data);
        if (this.ws) this.ws.send(JSON.stringify(data));
    }
}