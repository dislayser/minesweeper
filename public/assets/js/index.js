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
            list: $("#serverList #games"),
            create: $("#newGame"),
        },
        client: {
            list: $("#serverList #clients")
        }
    };

    // Для ID
    var myId = null;
    var serverId = null;
    var gameId = null;
    const activeClass = "table-warning";
    function updateMyId() {
        if (!myId) return;
        ui.client.list.find('tr[data-id]').removeClass(activeClass);
        ui.client.list.find('tr[data-id="' + myId + '"]').addClass(activeClass);
    }
    function updateMyServerId() {
        if (!myId) return;
        if (!serverId) return;
        ui.server.list.find('tr[data-id]').removeClass(activeClass);
        ui.server.list.find('tr[data-id="' + serverId + '"]').addClass(activeClass);
        WSPlugin.send({
            "type" : "GETGAMES",
            "serverId" : serverId,
        })
    }
    function updateMyGameId() {
        if (!myId) return;
        if (!serverId) return;
        if (!gameId) return;
        ui.game.list.find('tr[data-id]').removeClass(activeClass);
        ui.game.list.find('tr[data-id="' + serverId + '"]').addClass(activeClass);
    }
    
    WSPlugin.callbacks.updateServers = (servers) => {
        ui.server.list.empty();
        for (let i = 0; i < servers.length; i++) {
            const server = servers[i];
            ui.server.list.append(
                $("<tr>").attr("data-id", server.id)
                .append([
                    $("<td>").text(server.id),
                    $("<td>").text(server.user_id),
                    $("<td>").text(server.name),
                    $("<td>").append([
                        $("<a>").attr("href", "#").text("Войти").attr("data-join", server.id),
                    ]),
                    $("<td>").append([
                        $("<a>").attr("href", "#").addClass("text-danger").attr("data-remove", server.id).append($("<i>").addClass("bi-x"))
                    ])
                ])
            );
        }
    }
    
    WSPlugin.callbacks.updateGames = (games) => {
        ui.game.list.empty();
        for (let i = 0; i < games.length; i++) {
            const game = games[i];
            ui.game.list.append(
                $("<tr>").attr("data-id", game.id)
                .append([
                    $("<td>").text(game.id),
                    $("<td>").text(game.server),
                    $("<td>").text(game.name),
                    $("<td>").append([
                        $("<a>").attr("href", "#").text("Играть").attr("data-join", game.id),
                    ]),
                    $("<td>").append([
                        $("<a>").attr("href", "#").addClass("text-danger").attr("data-remove", game.id).append($("<i>").addClass("bi-x"))
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
        updateMyId();
    };
    WSPlugin.callbacks.onJoinServer = (data) => {
        console.log(data);
        serverId = data.id;
        updateMyServerId();
    }

    WSPlugin.callbacks.onInfo = (info) => {
        myId = info.id;
        updateMyId();
    };
    ui.server.list.on("click", "[data-join]", (event) => {
        const clicked = $(event.currentTarget);
        const id = clicked.attr("data-join");
        if (!id) return;
        WSPlugin.send({
            "type" : "JOINSERVER",
            "serverId" : id
        })
    });
    ui.server.list.on("click", "[data-remove]", (event) => {
        const clicked = $(event.currentTarget);
        const id = clicked.attr("data-remove");
        if (!id) return;

        WSPlugin.send({
            "type" : "DELSERVER",
            "serverId" : id
        })
    });
    ui.server.create.on("click", () => {
        WSPlugin.send({
            "type" : "CREATESERVER"
        })
    })
});