<?php
    //соединение с БД
    require_once 'connect.php';
    // полученные ID игровой ситуации и метка "забитые/пропущенные"
//    $id = $_POST['types_id'];
//    $who = $_POST['who'];
    $id = filter_input (INPUT_POST, 'types_id', FILTER_SANITIZE_NUMBER_INT);    
    $who = filter_input (INPUT_POST, 'who', FILTER_SANITIZE_NUMBER_INT);    
    
    //достаем игровую ситуацию
    $db_types = $db->prepare('SELECT description FROM list_goal_types WHERE id=?');
    $db_types->execute(array($id));
    $type_ar = $db_types->fetch();
    $type = $type_ar['description'];

    //получаем данные по голам
    if ($who == 1){
        $db_goals = $db->prepare('SELECT gn, half, tp, delta, name, url, author, vid '
                . 'FROM (SELECT g.gi, g.gn, g.half, g.tp, delta, url, author, v.id AS vid '
                        . 'FROM (SELECT goals.id AS gi, game_id AS gn, half, time_point AS tp, concat(surname," ",name) AS author '
                                . 'FROM goals '
                                . 'JOIN players ON author_id = players.id '            // к голам добавляются фамилии авторов
                                . 'WHERE type_id = ?) g '
                        . 'JOIN video v ON (g.gn = v.game_id AND g.half = v.half)) t1 '
                . 'JOIN (SELECT games.id, name '
                        . 'FROM games '               // к играм добавляются имена соперников
                        . 'JOIN rivals ON rival_id = rivals.id) t2 '
                . 'ON t1.gn = t2.id ORDER BY t1.gi');
    } else {
        $db_goals = $db->prepare('SELECT gn, half, tp, delta, name, url, vid '
                . 'FROM (SELECT g.id AS gi, g.game_id AS gn, g.half, time_point AS tp, delta, url, v.id AS vid '
                        . 'FROM goals_missed g '
                        . 'JOIN video v ON (g.game_id = v.game_id AND g.half = v.half) '
                        . 'WHERE type_id = ?) t1 '
                . 'JOIN (SELECT games.id, name '
                        . 'FROM games '
                        . 'JOIN rivals ON rival_id = rivals.id) t2 '
                . 'ON t1.gn = t2.id ORDER BY t1.gi');
    }
    $db_goals->execute(array($id));
    
    // выводим результат
?>

<h3>Список <?php echo ($who == 1) ? 'забитых' : 'пропущенных' ?> мячей в ситуации <br>"<?php echo $type ?>"</h3>
 
    <table border='1'>
<?php
    $i = 0;
    if ($who == 1) {
?>  
        <tr>
            <th>№</th>
            <th>№ матча</th>
            <th>Соперник</th>
            <th>Тайм</th>
            <th>Автор (ссылка)</th>
        </tr>       
<?php   
        while ($row = $db_goals->fetch()){
            $i++;
            $num = $row['gn'];
            $name = $row['name'];
            $half = $row['half'];
            $author = $row['author'];
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
                <td><a href =<?php echo $link ?>><?php echo $author ?>   </a></td>
            </tr>    
<?php
        }
    } else {
?>          
        <tr>
            <th>№</th>
            <th>№ матча</th>
            <th>Ссылка на гол</th>
            <th>Тайм</th>
        </tr>       
<?php   while ($row = $db_goals->fetch()){
            $i++;
            $num = $row['gn'];
            $name = $row['name'];
            $half = $row['half'];
            $vid = $row['vid'];
            $tp = $row['tp'];
            $delta = $row['delta'];
            $sec = strtotime($tp)-strtotime($delta)-5;
            $link = ($sec < 0) ? delta_video($db, $vid, $sec) : $row['url'].$sec;
?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $num ?></td>
                <td><a href =<?php echo $link ?>><?php echo $name ?>   </a></td>
                <td><?php echo $half ?></td>
            </tr>    
<?php
        }
    }
?>  
    </table>