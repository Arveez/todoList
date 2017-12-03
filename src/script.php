<?php
$db = new PDO('mysql:host=localhost;dbname=errands', 'root', '²');

function all()
{
    global $db;
    $all = [];
    $req = $db->query('SELECT * FROM errands ORDER BY id DESC');
     while ($one = $req->fetch(PDO::FETCH_ASSOC)) {
         $all[] = $one;
     }
    return json_encode($all);
}
$all = all();
