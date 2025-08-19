export default class Storage {
    constructor() {
        this.data = {};
    }

    get(keys){
        if (typeof keys == "string") {
            keys = keys.split(".");
        }
        let result = this.data;
        for (let index = 0; index < keys.length; index++) {
            const key = keys[index];
            if (this.data) {}
            
        }
    }
}