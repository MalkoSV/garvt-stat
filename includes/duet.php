<?php
    //соединение с БД
    require_once 'connect.php';

    $db_pairs = $db->query('SELECT id, concat(surname," ",name) AS fullname FROM players WHERE (id != 1)');
    $j = 0;
    while ($row = $db_pairs->fetch()) {
        $names[$j][0] = $row['id'];
        $names[$j][1] = $row['fullname'];
        $j++;
    }
    $ar = count($names) - 1;
    $i = 0;
    $pairs = array();
    while ($i < $ar){
        $k = $i + 1;
        while ($k <= $ar) {
            $pairs[] = array($names[$i][0],$names[$i][1],$names[$k][0],$names[$k][1]);
            $k++;
        }
        $i++;
    }
    $n = count($pairs); //число сочетаний попарно: [0]...[$n-1]

    //получаем данные по парам *****************************************
    for ($i = 0; $i < $n; $i++) {
        $db_duet = $db->prepare('SELECT COUNT(*) FROM combi '
                . 'WHERE  (p1=? OR p2=? OR p3=? OR p4=?) AND '
                . '(p1=? OR p2=? OR p3=? OR p4=?)');
        $duet = array($pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][2], $pairs[$i][2], $pairs[$i][2], $pairs[$i][2]);
        $db_duet->execute($duet);
        $pairs[$i][4] = $db_duet->fetchcolumn();

        $db_duet2 = $db->prepare('SELECT COUNT(*) FROM combi_a '
                . 'WHERE  (p1=? OR p2=? OR p3=? OR p4=?) AND '
                . '(p1=? OR p2=? OR p3=? OR p4=?)');
        $duet2 = array($pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][2], $pairs[$i][2], $pairs[$i][2], $pairs[$i][2]);
        $db_duet2->execute($duet2);
        $pairs[$i][5] = $db_duet2->fetchcolumn();
        $pairs[$i][6] = $pairs[$i][4] - $pairs[$i][5];
}

    for ($i = 0; $i < $n; $i++) {
        if (($pairs[$i][4] == 0) AND ($pairs[$i][5] == 0)) {
        } else {
            $duo[] = $pairs[$i];
        }
}

    foreach ($duo as $value){
        $delta[] = $value[6];
    }
    array_multisort($delta, SORT_DESC, $duo);

    //выводим результат
?>  
    <h3>Статистика "ДВОЙКИ на площадке:"</h3>
    <table border="1">
        <tr>
            <th>Игрок 1</th>
            <th>Игрок 2</th>
            <th>Забитые</th>
            <th>Пропущ.</th>
            <th>+/-</th>
        </tr>
<?php   
    $n = count($duo); //число сочетаний попарно: [0]...[$n-1]
    for ($i = 0; $i < $n; $i++) {
?>
        <tr>
            <td><?php echo $duo[$i][1] ?></td>
            <td><?php echo $duo[$i][3] ?></td>
            <td><?php echo $duo[$i][4] ?></td>
            <td><?php echo $duo[$i][5] ?></td>
            <td><?php echo $duo[$i][6] ?></td>
        </tr>
<?php
    }
?>  
    </table>
