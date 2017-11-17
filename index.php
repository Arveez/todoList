<?php

include '/home/rv/apache/www/projets_persos/todoList/src/script.php';
//shell_exec('php bin/chat-server.php')
?>
<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title>essais VUES</title>
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

</head>
<body>
<div id="app">
    <h1>les courses</h1>
    <ul>
        <li v-for="item in items" v-bind:id="item.id">
            {{ item.name }}
        </li>
    </ul>

    <form method="">
        <input autofocus="autofocus" type="text" name="toBeAdded">
    </form>
</div>
<script>

    var app = new Vue({
        el: '#app',
        data: {
            items: <?php echo $all ?>

                /*[
                {id: 22, text: 'jambon'},
                {id: 32, text: 'oeufs'},
                {id: 3, text: 'vin'},
                {id: 7, text: 'pain'}
            ]*/
        }
    });

    $(function () {

        var all = '<?php echo $all; ?>';
        console.log(all);
        var conn = new WebSocket('ws://localhost:8080');
        conn.onopen = function (e) {
            console.log("Connection established!");
        };
        conn.onmessage = function (e) {
            console.log(e.data);
            if ('refresh' != e.data) {

                deleteOne(e.data);
            } else {
                location.reload();
            }
        };

        function createOne(name) {
           console.log('create one: '+name);
           conn.send(name);
        }

        function deleteOne(id) {
            console.log('bootaaa');

            todel = id;

            app.items.forEach(function (el) {
                if (el.id == todel) {
                    itemIndex = app.items.indexOf(el);
                    if (app.items.splice(itemIndex, 1)) {
                        conn.send(todel);
                    }
                }
            })
        }

        $('form').on('submit', function(e) {
            e.preventDefault();
            console.log('form submitt');
            createOne($(this).find('input').val());
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