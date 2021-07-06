<?php
    //соединение с БД
    require_once 'connect.php';

    //получаем данные по авторам голов *****************************************
    $db_name = $db->query('SELECT id, concat(surname," ",name) AS name FROM players WHERE id > 1');

    $player = array();
    
    while ($row = $db_name->fetch()){
        $id = $row['id'];
        $db_pl = $db->prepare('SELECT COUNT(*) FROM combi WHERE (p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)');
        $db_pl->execute(array($id,$id,$id,$id));
        $our = $db_pl->fetchcolumn();
        
        $db_a = $db->prepare('SELECT COUNT(*) FROM combi_a WHERE (p1 = ?) OR (p2 = ?) OR (p3 = ?) OR (p4 = ?)');
        $db_a->execute(array($id,$id,$id,$id));
        $against = $db_a->fetchcolumn();
        
        $player[$row['name']]['our'] = $our;
        $player[$row['name']]['against'] = $against;
        $player[$row['name']]['profit'] = $our - $against;
    }
    foreach ($player as $key=>$row){
        $ar_name[$key] = $key;
        $ar_num[$key] = $row['profit'];
    }
    array_multisort($ar_num, SORT_DESC, $player);
    require_once 'time.php';
    //выводим результат
?>  
    <h3>Количество забитых/пропущенных при игроке:</h3>
    <table border="1">
        <tr>
            <th>№</th>
            <th>Игрок</th>
            <th>Время</th>
            <th>Забитые</th>
            <th>Пропущ.</th>
            <th>+/-</th>
            <th>+ 40мин</th>
            <th>- 40мин</th>
            <th>+/- (40 мин)</th>
        </tr>
<?php
        $i = 0;
    foreach ($player as $key=>$value) {
        $i++;
?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $key ?></td>
            <td><?php echo sectotime($player_time[$key]) ?></td>
            <td><?php echo $value['our'] ?></td>
            <td><?php echo $value['against'] ?></td>
            <td><?php echo $value['profit'] ?></td>
            <td><?php echo sprintf('%01.1f', $value['our'] / $player_time[$key] * 2400) ?></td>
            <td><?php echo sprintf('%01.1f', $value['against'] / $player_time[$key] * 2400) ?></td>
            <td><?php echo sprintf('%01.1f', $value['profit'] / $player_time[$key] * 2400) ?></td>
        </tr>
<?php
    }
?>  
    </table>
