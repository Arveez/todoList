<?php

include '/home/rv/apache/www/projets_persos/todoList/src/script.php';
//shell_exec('php bin/chat-server.php')
?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title>les courses VUES</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<div id="app">
    <h1>les courses</h1>
    <form method="">
        <input autofocus="autofocus" type="text" name="toBeAdded">
    </form>
    <ul>
        <li v-for="item in items" v-bind:id="item.id">
            {{ item.name }}
        </li>
    </ul>

</div>
<script>

    var app = new Vue({
        el: '#app',
        data: {
            items: <?php echo $all ?>
        }
    });

    $(function () {

        var conn = new WebSocket('ws://192.168.0.1:8080');

        conn.onopen = function (e) {
            console.log("Connection established!");
        };

        conn.onmessage = function (e) {
            if (e.data.isInteger) {
                deleteOne(e.data);
                console.log(e.data);
            } else {
                location.reload();
            }
        };

        function createOne(name) {
           conn.send(name);
        }

        function deleteOne(id) {
            idToDel = id;
            app.items.forEach(function (el) {
                if (el.id == idToDel) {
                    itemIndex = app.items.indexOf(el);
                    if (app.items.splice(itemIndex, 1)) {
                        conn.send(idToDel);
                    }
                }
            })
        }

        $('form').on('submit', function(event) {
            event.preventDefault();

            var toAddName = $(this).find('input').val();
            createOne(toAddName);

            $(this).find('input').val('');
        });

        $('li').on('click', function() {
                deleteOne(
                    $(this).attr('id'))
            }
        );

    });

</script>
</body>
</html>