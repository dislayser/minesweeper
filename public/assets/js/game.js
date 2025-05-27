import { WSGAME } from "./ws_game";

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
                console.log('Message from server:', event.data);
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
        
        this.openCell(col, row)
        this.ws.send(JSON.stringify({
            "col" : col, 
            "row" : row, 
        }));
        return;
    }

    
    // При нажати на правую кнопку мыши
    rbClick(col, row){
        col = Math.abs(parseInt(col));
        row = Math.abs(parseInt(row));

        this.toggleFlag(col, row);
    }

    openCell(col, row){
        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (!cell.length > 0) return;
        if (this.flags[row] && this.flags[row][col] === "on") return;
        cell.attr({
            src : new Field().getCell("cell0")
        });
    }

    toggleFlag(col, row){
        const cell = this.field.find(`[data-row="${row}"] img[data-col="${col}"]`);
        if (!cell.length > 0) return;
        if (!this.flags[row]) this.flags[row] = [];

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