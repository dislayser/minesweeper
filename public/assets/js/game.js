import Cell from "./Entity/Cell.js";
import { Field } from "./field.js";

export class Game{
    constructor({
        seed = 1234,
        field = $("#gameField"),
    } = {}) {
        this.seed = seed;
        this.field = field;
        this.cells = [];
        
        this.type = {
            CREATE : "create",
            OPEN_CELL : "OPENCELL",
            OPEN_CELLS : "OPENCELLS",
            SET_FLAG : "SETFLAG",
        };

        try {
            this.ws = new WebSocket('ws://localhost:8080');  
            this.ws.onopen = () => {
                this.ws.send(JSON.stringify({
                    "token" : $('input[name="_csrf"]').val() ?? "some CSRF token"
                }));
            };
            this.ws.onmessage = (event) => {
                console.log(event.data);

                let json = JSON.parse(event.data);

                // Обработка собщений
                if (json.type && json.type == "create"){
                    this.createField(json);
                }
                if (json.type && json.type == "cell"){
                    this.openCell(json.col, json.row, json.isBomb, json.number);
                }
            };
            this.ws.onerror = (error) => {
                console.error('WebSocket error:', error);
            };
            this.ws.onclose = () => {
                console.log('Disconnected from the server');
            };
            
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
        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (!cell.length > 0) return;

        if (this.cells[row][col].isOpen() == 1) return;
        if (this.cells[row][col]) return;
        
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
        if (this.ws) this.ws.send(JSON.stringify(data));
    }
}