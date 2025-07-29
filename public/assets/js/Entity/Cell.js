export default class Cell{
    constructor({
        col,
        row,
        ui,
    } = {}){
        this.col = col;
        this.row = row;
        this.ui = ui;
        this.flag = false;
        this.opened = false;
        this.number = null;
    }

    open(number) {
        this.opened = true;
        this.number = number;
    }

    isOpen() {
        return this.opened;
    }

    isFlag() {
        return this.flag;
    }

    setFlag(){
        this.flag = true;
    }

    removeFlag(){
        this.flag = false;
    }
}