class Game{
    constructor({
        seed = 1234,
        field = $("#gameField"),
    } = {}) {
        this.seed = seed;
        this.field = field;

        this.flags = [];
        this.opened = [];
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
        return;
        $.ajax({
            url : "/api/v1/game",
            method : "GET",
            data : {
                "seed" : this.seed,
                "options" : "",
                "_csrf" : $('input[name="_csrf"]').val() ?? null
            }
        })
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