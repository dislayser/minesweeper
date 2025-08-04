import { Game } from "./game.js";
import { WSPlugin } from "./WS/plugin.js";

$(document).ready(function() {
    var game = new Game({
        field: $("#gameField")
    });

    game.events();

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
        },
        client: {
            list: $("#serverList #clients")
        }
    };
    
    WSPlugin.callbacks.updateServers = (servers) => {
        ui.server.list.empty();
        for (let i = 0; i < servers.length; i++) {
            const server = servers[i];
            ui.server.list.append($("<div>").addClass("px-3 py-2").text(server.name));
        }
    }
    WSPlugin.callbacks.updateClients = (clients) => {
        ui.client.list.empty();
        for (let i = 0; i < clients.length; i++) {
            const client = clients[i];
            ui.client.list.append($("<div>")
                .addClass("badge bg-secondary-subtle border border-secondary-subtle text-secondary-emphasis rounded-pill")
                .append([
                    $("<b>").text(client.name)
                ])
            );
        }
    };
    ui.server.create.on("click", () => {
        
    })
});