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
    // Для id 
    var myId = null;
    var serverId = null;
    var gameId = null;
    function updateId() {
        if (!myId) return;
        ui.client.list.find('tr[data-id="' + myId + '"]').addClass("table-primary");
    }
    
    WSPlugin.callbacks.updateServers = (servers) => {
        ui.server.list.empty();
        for (let i = 0; i < servers.length; i++) {
            const server = servers[i];
            ui.server.list.append(
                $("<tr>").attr("data-id", server.id)
                .append([
                    $("<td>").text(server.id),
                    $("<td>").text(server.name),
                    $("<td>").append([
                        $("<a>").attr("href", "#").text("Войти"),
                        $("<a>").attr("href", "#").addClass("text-danger ms-3").text("X").attr("data-remove", server.id)
                    ])
                ])
            );
        }
    }
    WSPlugin.callbacks.updateClients = (clients) => {
        ui.client.list.empty();
        for (let i = 0; i < clients.length; i++) {
            const client = clients[i];
            ui.client.list.append($("<tr>").attr("data-id", client.id)
                .append([
                    $("<td>").text(client.id),
                    $("<td>").text(client.name)
                ])
            );
        }
        updateId();
    };
    WSPlugin.callbacks.onInfo = (info) => {
        myId = info.id;
        updateId();
    };
    ui.server.list.on("click", "[data-remove]", (event) => {
        const clicked = $(event.currentTarget);
        const serverId = clicked.attr("data-remove");
        if (!serverId) return;

        WSPlugin.send({
            "type" : "DELSERVER",
            "id" : serverId
        })
    });
    ui.server.create.on("click", () => {
        WSPlugin.send({
            "type" : "CREATESERVER"
        })
    })
});