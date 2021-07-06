<?php
    //достаем все голы игрока
    $db_goals = $db->prepare('SELECT gli, gmi, name, half, std, type, tp, delta, url, vid '
                            . 'FROM (SELECT gli, gmi, g.half, std, tp, delta, url, v.id AS vid, type '
                                    . 'FROM (SELECT goals.id AS gli, game_id AS gmi, half, time_point AS tp, standart AS std, t.description AS type '
                                            . 'FROM goals '
                                            . 'JOIN list_goal_types t ON goals.type_id = t.id '
                                            . 'WHERE author_id = ?) g '
                                    . 'JOIN video v ON (g.gmi = v.game_id AND g.half = v.half)) t1 '
                            . 'JOIN (SELECT games.id, name '
                            . 'FROM games '
                            . 'JOIN rivals ON rival_id = rivals.id) t2 ON (t1.gmi = t2.id) '
                            . 'ORDER BY t1.gli');
    $db_goals->execute(array($id));
   
    // выводим результат
?>
<h3>Полный список голов:</h3>
 
    <table border='1'>
<?php
    $i = 0;
?>  
    <tr>
        <th>№</th>
        <th>№ матча</th>
        <th>Соперник</th>
        <th>Тайм</th>
        <th>Стандарт</th>
        <th>Ситуация</th>
    </tr>       
<?php   
    while ($row = $db_goals->fetch()){
        $i++;
        $num = $row['gmi'];
        $name = $row['name'];
        $half = $row['half'];
        $type = $row['type'];
        $std = ($row['std'] == 1) ? 'Да' : '';
        $vid = $row['vid'];
        $tp = $row['tp'];
        $delta = $row['delta'];
        $sec = strtotime($tp)-strtotime($delta)-5;
        $link = ($sec < 0) ? delta_video($db, $vid, $sec) : $row['url'].$sec;
?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $num ?></td>
            <td><?php echo $name ?></td>
            <td><?php echo $half ?></td>
            <td><?php echo $std ?></td>
            <td><a href =<?php echo $link ?>><?php echo $type ?>   </a></td>
        </tr>    
<?php
    }
?>  
    </table>