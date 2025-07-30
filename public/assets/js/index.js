import { Game } from "./game.js";
import { WSPlugin } from "./WS/plugin.js";

$(document).ready(function() {
    const ui = {
        server: {
            create: $("#newServer"),
            join: $("#joinServer"),
            list: $("#serverList #list"),
        },
        game: {
            configs: () => {
                const form = $("form#gameConfigs");
                return {
                    cols: parseInt(form.find("#cols").val()),
                    rows: parseInt(form.find("#rows").val()),
                    seed: parseInt(
                        form.find("#seed").val() ??
                        Math.floor(Math.random() * 1000000)
                    ),
                    difficult: form.find("#difficult").val(),
                };
            },
            list: $("#gameList #list"),
            create: $("#newGame"),
        }
    };
    WSPlugin.on("onopen", () => {
        console.log("onopen");
    })
    WSPlugin.on("onmessage", (data) => {
        console.log("MSG: ", data);
    })
    console.log(WSPlugin.ws);
});