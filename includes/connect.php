<?php
    // тип СУБД, хост сервера и имя базы данных
    $host = '127.0.0.1';
    $dbn = 'gravt';
    $user = 'root';
    $password = '';
    $charset = 'utf8';
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = "mysql:host=$host;dbname=$dbn;charset=$charset";

    //соединение с БД
    $db = new PDO($dsn, $user, $password, $opt);

    function delta_video($db, $idv, $sec){
        $idv -= 1;
        $db_vd = $db->prepare('SELECT url FROM video WHERE id=?');
        $db_vd->execute(array($idv));
        $link = $db_vd->fetchcolumn();
        $sec += 1800;
        $link .= $sec;
        return $link;
    }

    function timeout_shift($db, $game, $half, $in, $out){
        $long = 0;
        $db_timeouts = $db->prepare('SELECT start, end '
        . 'FROM timeouts '
        . 'WHERE (game_id = ?) AND (half = ?) AND (start > ?) AND (start < ?)');
        $db_timeouts->execute(array($game, $half, $in, $out));

        while ($row = $db_timeouts->fetch()){
            $long += strtotime($row['end']) - strtotime($row['start']);
            }
        return $long;
    }

    function sectotime($value) {
        $s = $value % 60;
        $h = floor($value / 3600);
        $m = floor(($value % 3600)/60);
        $time = '%02d:%02d:%02d';
        return sprintf($time, $h, $m, $s);
    }

?>  
