import Cell from "./Entity/Cell.js";
import { Field } from "./field.js";
import { WSPlugin } from "./WS/plugin.js";

export class Game{
    constructor({
        seed = 1234,
        field = $("#gameField"),
    } = {}) {
        this.seed = seed;
        this.field = field;
        this.cells = [];
        
        this.type = {
            CREATE      : "create",
            ERROR       : "error",
            CREATEGAME  : "create_game",
            CREATESERVER: "create_server",
            JOINPLAYER  : "new_player",
            GETSERVERS  : "GETSERVERS",
            JOINSERVER  : "JOINSERVER",
            JOINGAME    : "JOINGAME",
            OPEN_CELL   : "OPENCELL",
            OPEN_CELLS  : "OPENCELLS",
            SET_FLAG    : "SETFLAG",
            INFO        : "info",
        };

        try {
            WSPlugin.on("onopen", () => {
                this.doAction({"type" : this.type.GETSERVERS});
            });
            WSPlugin.on("onmessage", (json) => {
                console.log(json);
                if (!json.type) return;

                if (json.type == this.type.CREATE){
                    this.createField(json);
                }
                if (json.type == this.type.OPEN_CELL){
                    for (let i = 0; i < json.data.length; i++) {
                        this.openCell(
                            json.data[i].col,
                            json.data[i].row,
                            json.data[i].isBomb,
                            json.data[i].number
                        );
                    }
                }
                if (json.type == this.type.GETSERVERS){
                    WSPlugin.callbacks.updateServers(json.data);
                    if (json.data.length == 0) {
                        this.doAction({"type" : this.type.CREATESERVER});
                    }
                }
                if (json.type == this.type.JOINPLAYER){
                    WSPlugin.callbacks.updateClients(json.data);
                }
                if (json.type == this.type.INFO){
                    WSPlugin.callbacks.onInfo(json);
                }
            });
            WSPlugin.on("onclose", () => {
                console.log('Disconnected from the server');
            });
            WSPlugin.on("onerror", console.error);
        } catch (err) {
            console.warn(err);
        }
    }

    createField(data) {
        const field = [];
        this.cells = [];
        if (data.rows > 0 && data.cols > 0) {
            for (let row = 0; row < data.rows; row++) {
                this.cells[row] = [];
                const $row = $("<div>").addClass("d-flex justify-content-center").attr("data-row", row);
                for (let col = 0; col < data.cols; col++) {
                    const $col = $("<img>").addClass("cell-32")
                    .attr({
                        "draggable" : "false",
                        "data-col" : col,
                        "src" : new Field().getCell("cell")
                    });
                    this.cells[row][col] = new Cell({
                        col : col,
                        row : row,
                        ui : $col,
                    });
                    $row.append($col);
                }
                field.push($row);
            }
        }

        this.field.empty();
        this.field.append(field);
    }

    start({
        cols = 10,
        rows = 10,
        seed = 1234,
        difficult = "easy"
    } = {}) {
        this.doAction({
            "type" : this.type.CREATE,
            "cols" : cols,
            "rows" : rows,
            "seed" : seed,
            "difficult" : difficult
        });
    }

    events() {
        $(document).ready(() => {

            this.field.on("click", 'img[data-col]', event => {
                const target = $(event.target);
                let col = target.data("col");
                let row = target.closest("[data-row]").data("row");

                this.lbClick(col, row);
            });

            this.field.on("contextmenu", 'img[data-col]', event => {
                event.preventDefault();
                const target = $(event.target);
                let col = target.data("col");
                let row = target.closest("[data-row]").data("row");

                this.rbClick(col, row);
            });

        });
    }

    // При нажати на левую кнопку мыши
    lbClick(col, row){
        col = Math.abs(parseInt(col));
        row = Math.abs(parseInt(row));

        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (this.cells[row][col].isOpen()) return;
        if (this.cells[row][col].isFlag()) return;
        
        this.doAction({
            "type" : this.type.OPEN_CELL,
            "col" : col, 
            "row" : row, 
        });
        return;
    }

    
    // При нажати на правую кнопку мыши
    rbClick(col, row){
        col = Math.abs(parseInt(col));
        row = Math.abs(parseInt(row));

        this.toggleFlag(col, row);
    }

    openCell(col, row, isBomb, number){
        const cell = this.cells[row][col].ui;
        if (!cell.length > 0) return;

        if (this.cells[row][col].isOpen()) return;
        if (this.cells[row][col].isFlag()) return;

        if (isBomb) {
            cell.attr({
                src : new Field().getCell("bomb")
            });
        } else {
            cell.attr({
                src : new Field().getCell("cell" + number)
            });
        }
        cell.attr("data-open", "1");

        this.cells[row][col].open(number);
    }

    toggleFlag(col, row){
        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (!cell.length > 0) return;

        if (this.cells[row][col].isOpen()) return;
        if (this.cells[row][col].isFlag()){
            this.cells[row][col].removeFlag();
            cell.attr({
                "src": new Field().getCell("cell")
            });
            cell.removeAttr("data-flag");
        } else {
            this.cells[row][col].setFlag();
            cell.attr({
                "data-flag" : "true",
                "src": new Field().getCell("flag")
            });
        }
    }

    doAction(data) {
        WSPlugin.send(data);
    }
}