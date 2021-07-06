<?php
    //ищем полное время, проведенное на поле каждым игроком

    //достаем всех игроков
    $db_name = $db->query('SELECT id, concat(surname," ",name) AS name FROM players WHERE id > 1');
    $player_time = array();
    while ($row = $db_name->fetch()) {    // Перебираем всех игроков
        $name = $row['name'];
        $longtime = 0;
        $flag = 0; // 0 - out, 1 - in
        // делаем выборку всех записей из substitutions, где игрок упоминается
        $db_subst = $db->prepare('SELECT * '
                . 'FROM substitutions '
                . 'WHERE (player_in = ? OR player_out = ?)');
        $id = $row['id'];
        $db_subst->execute(array($id,$id));        
        while ($subst = $db_subst->fetch()) {  // Перебираем по конкретному игроку все выходы на поле
            $game = $subst['game_id'];
            $half = $subst['half'];
            if ( $subst['player_in'] == $id) {
                if ($flag == 1) {
                    $db_video = $db->prepare('SELECT end '
                            . 'FROM video '
                            . 'WHERE (game_id = ?) AND (half = ?)');
                    $db_video->execute(array($game_in, $half_in));
                    $str_out = $db_video->fetchcolumn();
                    $time_out = strtotime($str_out);
                    $longtime += $time_out - $time_in - timeout_shift($db, $game_in, $half_in, $str_in, $str_out);
                    $flag = 0;
                }
                $str_in = $subst['time_point'];
                $time_in = strtotime($str_in);
                $game_in = $game;
                $half_in = $half;
                $flag = 1;
            } else {
                $str_out = $subst['time_point'];
                $time_out = strtotime($str_out);
                $flag = 0;
                $longtime += $time_out - $time_in - timeout_shift($db, $game, $half, $str_in, $str_out);
            }
        }
        if ($flag == 1) {
            $db_video = $db->prepare('SELECT end '
                    . 'FROM video '
                    . 'WHERE (game_id = ?) AND (half = ?)');
            $db_video->execute(array($game_in, $half_in));
            $str_out = $db_video->fetchcolumn();
            $time_out = strtotime($str_out);
            $longtime += $time_out - $time_in - timeout_shift($db, $game, $half, $str_in, $str_out);
            $flag = 0;
        }            
        $player_time[$name] = $longtime;
    }
