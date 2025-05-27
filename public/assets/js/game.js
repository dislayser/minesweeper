// import { WSGAME } from "./ws_game";

class Game{
    constructor({
        seed = 1234,
        field = $("#gameField"),
    } = {}) {
        this.seed = seed;
        this.field = field;
        
        this.type = {
            GET_SERVERS : "get_servers",
            SET_SERVER : "set_server",
            OPEN_CELL : "open_cell",
            OPEN_CELLS : "open_cells",
            SET_FLAG : "set_flag",
            NEW_SERVER : "new_server"
        };
        this.flags = [];
        this.opened = [];  

        try {
            this.ws = new WebSocket('ws://localhost:8080');  
            this.ws.onopen = () => {
                this.ws.send(JSON.stringify({
                    "token" : $('input[name="_csrf"]').val() ?? null
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
                    this.openCell(json.x, json.y, json.isBomb, json.number);
                }
            };
            this.ws.onerror = (error) => {
                console.error('WebSocket error:', error);
            };
            this.ws.onclose = () => {
                console.log('Disconnected from the server');
            };
            
        } catch (error) {
            
        }
    }

    createField(data) {
        const field = [];
        if (data.rows > 0 && data.cols > 0) {
            for (let row = 0; row < data.rows; row++) {
                const $row = $("<div>").addClass("d-flex justify-content-center").attr("data-row", row);
                for (let col = 0; col < data.cols; col++) {
                    const $col = $("<img>").addClass("cell-16")
                    .attr({
                        "draggable" : "false",
                        "data-col" : col,
                        "src" : new Field().getCell("cell")
                    });
                    $row.append($col);
                }
                field.push($row);
            }
        }

        this.field.empty();
        this.field.append(field);
    }

    events() {
        $(document).ready(() => {

            this.field.on("click", 'img[data-col]', event => {
                const target = $(event.target);
                // console.log(target);
                let col = target.data("col");
                let row = target.closest("[data-row]").data("row");

                this.lbClick(col, row);
            });

            this.field.on("contextmenu", 'img[data-col]', event => {
                event.preventDefault();
                const target = $(event.target);
                // console.log(target);
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
        if (cell.data("open") == 1) return;
        
        // this.openCell(col, row)
        this.ws.send(JSON.stringify({
            "type" : "open",
            "x" : col, 
            "y" : row, 
        }));
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
        if (cell.data("open") == 1) return;
        if (this.flags[row] && this.flags[row][col] === "on") return;
        if (isBomb) {
            cell.attr({
                src : new Field().getCell("bomb")
            });
        } else {
            cell.attr({
                src : new Field().getCell("cell" + number)
            });
            if (number === 0) {
                this.field.find(`[data-row="${row+1}"] img[data-col="${col-1}"]`).click();
                this.field.find(`[data-row="${row+1}"] img[data-col="${col}"]`).click();
                this.field.find(`[data-row="${row+1}"] img[data-col="${col+1}"]`).click();
                this.field.find(`[data-row="${row}"] img[data-col="${col-1}"]`).click();
                // this.field.find(`[data-row="${row}"] img[data-col="${col}"]`).click();
                this.field.find(`[data-row="${row}"] img[data-col="${col+1}"]`).click();
                this.field.find(`[data-row="${row-1}"] img[data-col="${col-1}"]`).click();
                this.field.find(`[data-row="${row-1}"] img[data-col="${col}"]`).click();
                this.field.find(`[data-row="${row-1}"] img[data-col="${col+1}"]`).click();
            }
        }
        cell.attr("data-open", "1");
    }

    toggleFlag(col, row){
        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (!cell.length > 0) return;
        if (!this.flags[row]) this.flags[row] = [];
        if (cell.data("open") == 1) return;

        if (this.flags[row][col] === "on"){
            this.flags[row][col] = "off";
            cell.attr({
                "data-flag" : "false",
                "src": new Field().getCell("cell")
            });
        } else {
            this.flags[row][col] = "on";
            cell.attr({
                "data-flag" : "true",
                "src": new Field().getCell("flag")
            });
        }
        console.log(this.flags);
    }
}