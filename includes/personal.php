<?php
    //соединение с БД
    require_once 'connect.php';

    //получаем данные по авторам голов *****************************************
    $db_name = $db->query('SELECT id, concat(surname," ",name) AS name FROM players WHERE id > 1');
    $db_game = $db->query('SELECT COUNT(*) AS num FROM games');
    $num = $db_game->fetchcolumn();
    $player = array();
    
    while ($row = $db_name->fetch()){
        $id = $row['id'];
        $name = $row['name'];
        for ($i = 1; $i <= $num; $i++) {
            $db_subst = $db->prepare('SELECT COUNT(*) FROM substitutions WHERE player_in = ? AND game_id = ?');
            $db_subst->execute(array($id,$i));
            if ($db_subst->fetchcolumn() > 0){
                $db_goals = $db->prepare('SELECT COUNT(*) FROM combi WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ?');
                $db_goals->execute(array($id,$id,$id,$id,$i));
                $goals = $db_goals->fetchcolumn(); // забитые при игроке в i-й игре
                $db_missed = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ?');
                $db_missed->execute(array($id,$id,$id,$id,$i));
                $missed = $db_missed->fetchcolumn(); // пропущенные при игроке в i-й игре
                $player[$name]['goals'] += $goals;
                $player[$name]['missed'] += $missed;
                if ($goals > $missed) {
                    $player[$name]['win']++;
                } else {
                    if ($goals < $missed){
                        $player[$name]['lose']++;
                    } else {
                        $player[$name]['draw']++;
                    }
                }

                $db_subst1 = $db->prepare('SELECT COUNT(*) FROM substitutions WHERE player_in = ? AND game_id = ? AND half = 1');
                $db_subst1->execute(array($id,$i));
                if ($db_subst1->fetchcolumn() > 0){
                    $db_goals1 = $db->prepare('SELECT COUNT(*) FROM combi WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ? AND half = 1');
                    $db_goals1->execute(array($id,$id,$id,$id,$i));
                    $goals1 = $db_goals1->fetchcolumn(); // забитые при игроке в 1 тайме в i-й игре
                    $db_missed1 = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ? AND half = 1');
                    $db_missed1->execute(array($id,$id,$id,$id,$i));
                    $missed1 = $db_missed1->fetchcolumn(); // пропущенные при игроке в 1 тайме в i-й игре
                    $player[$name]['goals1'] += $goals1;
                    $player[$name]['missed1'] += $missed1;
                    if ($goals1 > $missed1) {
                        $player[$name]['win1']++;
                    } else {
                        if ($goals1 < $missed1){
                            $player[$name]['lose1']++;
                        } else {
                            $player[$name]['draw1']++;
                        }
                    }
                }
                
                $db_subst2 = $db->prepare('SELECT COUNT(*) FROM substitutions WHERE player_in = ? AND game_id = ? AND half = 2');
                $db_subst2->execute(array($id,$i));
                if ($db_subst2->fetchcolumn() > 0){
                    $db_goals2 = $db->prepare('SELECT COUNT(*) FROM combi WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ? AND half = 2');
                    $db_goals2->execute(array($id,$id,$id,$id,$i));
                    $goals2 = $db_goals2->fetchcolumn(); // забитые при игроке в 1 тайме в i-й игре
                    $db_missed2 = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE ((p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)) AND game_id = ? AND half = 2');
                    $db_missed2->execute(array($id,$id,$id,$id,$i));
                    $missed2 = $db_missed2->fetchcolumn(); // пропущенные при игроке в 1 тайме в i-й игре
                    $player[$name]['goals2'] += $goals2;
                    $player[$name]['missed2'] += $missed2;
                    if ($goals2 > $missed2) {
                        $player[$name]['win2']++;
                    } else {
                        if ($goals2 < $missed2){
                            $player[$name]['lose2']++;
                        } else {
                            $player[$name]['draw2']++;
                        }
                    }
                }
                
            }
            $player[$name]['points'] = $player[$name]['win'] * 3 + $player[$name]['draw'];
            $player[$name]['points1'] = $player[$name]['win1'] * 3 + $player[$name]['draw1'];
            $player[$name]['points2'] = $player[$name]['win2'] * 3 + $player[$name]['draw2'];
            
        }
    }
//***************************************************************************************    
    $name = 'ГрАвт';
    for ($i = 1; $i <= $num; $i++) {
        $db_goals = $db->prepare('SELECT COUNT(*) FROM combi WHERE game_id = ?');
        $db_goals->execute(array($i));
        $goals = $db_goals->fetchcolumn(); // забитые в i-й игре
        $db_missed = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE game_id = ?');
        $db_missed->execute(array($i));
        $missed = $db_missed->fetchcolumn(); // пропущенные при игроке в i-й игре
        $player[$name]['goals'] += $goals;
        $player[$name]['missed'] += $missed;
        if ($goals > $missed) {
            $player[$name]['win']++;
        } else {
            if ($goals < $missed){
                $player[$name]['lose']++;
            } else {
                $player[$name]['draw']++;
            }
        }

        $db_subst1 = $db->prepare('SELECT COUNT(*) FROM substitutions WHERE game_id = ? AND half = 1');
        $db_subst1->execute(array($i));
        if ($db_subst1->fetchcolumn() > 0){
            $db_goals1 = $db->prepare('SELECT COUNT(*) FROM combi WHERE game_id = ? AND half = 1');
            $db_goals1->execute(array($i));
            $goals1 = $db_goals1->fetchcolumn(); // забитые в 1 тайме в i-й игре
            $db_missed1 = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE game_id = ? AND half = 1');
            $db_missed1->execute(array($i));
            $missed1 = $db_missed1->fetchcolumn(); // пропущенные в 1 тайме в i-й игре
            $player[$name]['goals1'] += $goals1;
            $player[$name]['missed1'] += $missed1;
            if ($goals1 > $missed1) {
                $player[$name]['win1']++;
            } else {
                if ($goals1 < $missed1){
                    $player[$name]['lose1']++;
                } else {
                    $player[$name]['draw1']++;
                }
            }
        }

        $db_subst2 = $db->prepare('SELECT COUNT(*) FROM substitutions WHERE game_id = ? AND half = 2');
        $db_subst2->execute(array($i));
        if ($db_subst2->fetchcolumn() > 0){
            $db_goals2 = $db->prepare('SELECT COUNT(*) FROM combi WHERE game_id = ? AND half = 2');
            $db_goals2->execute(array($i));
            $goals2 = $db_goals2->fetchcolumn(); // забитые в 1 тайме в i-й игре
            $db_missed2 = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE game_id = ? AND half = 2');
            $db_missed2->execute(array($i));
            $missed2 = $db_missed2->fetchcolumn(); // пропущенные в 1 тайме в i-й игре
            $player[$name]['goals2'] += $goals2;
            $player[$name]['missed2'] += $missed2;
            if ($goals2 > $missed2) {
                $player[$name]['win2']++;
            } else {
                if ($goals2 < $missed2){
                    $player[$name]['lose2']++;
                } else {
                    $player[$name]['draw2']++;
                }
            }
        }
        $player[$name]['points'] = $player[$name]['win'] * 3 + $player[$name]['draw'];
        $player[$name]['points1'] = $player[$name]['win1'] * 3 + $player[$name]['draw1'];
        $player[$name]['points2'] = $player[$name]['win2'] * 3 + $player[$name]['draw2'];
    }
//***************************************************************************************    
    foreach ($player as $key=>$row){
        $ar_name[$key] = $key;
        $ar_num[$key] = $row['points'];
    }
    array_multisort($ar_num, SORT_DESC, $player);

    //выводим результат
?>  
    <h3>Индивидуальные матчи:</h3>
    <h4>ИГРЫ</h4>
    <table border="1">
        <tr>
            <th>№</th>
            <th>Игрок</th>
            <th>Игр</th>
            <th>В</th>
            <th>Н</th>
            <th>П</th>
            <th>Мячи</th>
            <th>РМ</th>
            <th>Очков</th>
        </tr>
<?php
        $i = 0;
    foreach ($player as $key=>$value) {
        $i++;
?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $key ?></td>
            <td><?php echo $value['win'] + $value['draw'] + $value['lose']?></td>
            <td><?php echo $value['win'] ?></td>
            <td><?php echo $value['draw'] ?></td>
            <td><?php echo $value['lose'] ?></td>
            <td><?php echo $value['goals'].' - '.$value['missed'] ?></td>
            <td><?php echo $value['goals'] - $value['missed'] ?></td>
            <td><?php echo $value['points'] ?></td>
        </tr>
<?php
    }
?>  
    </table>

<?php
    foreach ($player as $key=>$row){
        $ar_name[$key] = $key;
        $ar_num[$key] = $row['points1'];
    }
    array_multisort($ar_num, SORT_DESC, $player);

    //выводим результат
?>  
    <h4>ПЕРВЫЕ таймы</h4>
    <table border="1">
        <tr>
            <th>№</th>
            <th>Игрок</th>
            <th>Игр</th>
            <th>В</th>
            <th>Н</th>
            <th>П</th>
            <th>Мячи</th>
            <th>РМ</th>
            <th>Очков</th>
        </tr>
<?php
        $i = 0;
    foreach ($player as $key=>$value) {
        $i++;
?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $key ?></td>
            <td><?php echo $value['win1'] + $value['draw1'] + $value['lose1']?></td>
            <td><?php echo $value['win1'] ?></td>
            <td><?php echo $value['draw1'] ?></td>
            <td><?php echo $value['lose1'] ?></td>
            <td><?php echo $value['goals1'].' - '.$value['missed1'] ?></td>
            <td><?php echo $value['goals1'] - $value['missed1'] ?></td>
            <td><?php echo $value['points1'] ?></td>
        </tr>
<?php
    }
?>  
    </table>

<?php
    foreach ($player as $key=>$row){
        $ar_name[$key] = $key;
        $ar_num[$key] = $row['points2'];
    }
    array_multisort($ar_num, SORT_DESC, $player);

    //выводим результат
?>  
    <h4>ВТОРЫЕ таймы</h4>
    <table border="1">
        <tr>
            <th>№</th>
            <th>Игрок</th>
            <th>Игр</th>
            <th>В</th>
            <th>Н</th>
            <th>П</th>
            <th>Мячи</th>
            <th>РМ</th>
            <th>Очков</th>
        </tr>
<?php
        $i = 0;
    foreach ($player as $key=>$value) {
        $i++;
?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $key ?></td>
            <td><?php echo $value['win2'] + $value['draw2'] + $value['lose2']?></td>
            <td><?php echo $value['win2'] ?></td>
            <td><?php echo $value['draw2'] ?></td>
            <td><?php echo $value['lose2'] ?></td>
            <td><?php echo $value['goals2'].' - '.$value['missed2'] ?></td>
            <td><?php echo $value['goals2'] - $value['missed2'] ?></td>
            <td><?php echo $value['points2'] ?></td>
        </tr>
<?php
    }
?>  
    </table>
    