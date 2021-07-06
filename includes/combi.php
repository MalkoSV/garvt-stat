<?php
    //соединение с БД
    require_once 'connect.php';
    
    //достаем все голы с авторами, ассистентами, ассистентами2
    $db_goals = $db->query('SELECT game_id, half, time_point AS goaltime, author_id, assist_id, assist2_id FROM goals');
    //запрос на создание таблицы
    $sql = $db->query('DROP TABLE IF EXISTS `combi`');
    $sql = 'CREATE TABLE `combi` ( `id` tinyint NOT NULL AUTO_INCREMENT, '
            . '`game_id` tinyint NOT NULL, '
            . '`half` tinyint NOT NULL, '
            . '`p1` tinyint NOT NULL, '
            . '`p2` tinyint NOT NULL, '
            . '`p3` tinyint NOT NULL, '
            . '`p4` tinyint NOT NULL, PRIMARY KEY (`id`)) ';
    $db->exec($sql);
    $prev_goal = '00:00:00';
    $prev_game = 0;
    $prev_half = 0;
    $k = 0;
    while ($row = $db_goals->fetch()) {
        $db_subst = $db->prepare('SELECT id AS subnum, player_in, player_out, time_point AS subtime '
                . 'FROM substitutions '
                . 'WHERE (game_id=? AND  half=? AND time_point<? )');
        $game = $row['game_id'];
        $half = $row['half'];
        $goaltime = $row['goaltime'];
        $game_moment = array( $game, $half, $goaltime );
        $db_subst->execute($game_moment);

        $subst_arr = $db_subst->fetchall();
        $now = count($subst_arr) - 1;
        
        // Проверка: были ли замены между голами
        if ( ($subst_arr[$now]['subtime'] > $prev_goal) or ($game != $prev_game) or ($half != $prev_half) ) {
            $out = NULL;
            $player = NULL;
            $author = $row['author_id'];
            $assist = $row['assist_id'];
            $assist2 = $row['assist2_id'];
            $player[1] = $subst_arr[$now]['player_in']; // тот, кто последний вышел на замену
            $i = 2;
            if ( ($player[1] != $author) and ($author > 1) ) { 
                $player[$i] = $author;    // автор гола
                $i++;
            }
            if ( ($player[1] != $assist) and ($assist > 1) ) { 
                $player[$i] = $assist;    // ассистент
                $i++;
            }
            if ( ($player[1] != $assist2) and ($assist2 > 1) ) { 
                $player[$i] = $assist2;    // ассистент2
                $i++;
            }
            $j = 1;  // счетчик ушедших с поля до гола
            while ($i < 5){
                $out[$j] = $subst_arr[$now]['player_out'];
                $j++;
                $now--;
                $elim = array_merge($player, $out);
                if (in_array($subst_arr[$now]['player_in'], $elim) != 1){
                    $player[$i] = $subst_arr[$now]['player_in'];
                    $i++;
                }
                $j++;
             } 
        }
        $prev_goal = $goaltime;
        $prev_game = $game;
        $prev_half = $half;
        sort($player);
//echo 'Игроки на поле: '.$player[0].'/'.$player[1].'/'.$player[2].'/'.$player[3].'<br>';
        $pl = $db->prepare('INSERT INTO `combi` (game_id,half,p1,p2,p3,p4) VALUES (:g, :h, :p1, :p2, :p3, :p4)');
        $pl->bindValue(':g', $game);
        $pl->bindValue(':h', $half);
        $pl->bindValue(':p1', $player[0]);
        $pl->bindValue(':p2', $player[1]);
        $pl->bindValue(':p3', $player[2]);
        $pl->bindValue(':p4', $player[3]);
        $pl->execute();
    }
    
    echo 'Таблица сочетаний  готова!';
