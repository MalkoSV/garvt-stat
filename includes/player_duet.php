<?php
    $db_pairs = $db->prepare('SELECT id, concat(surname," ",name) AS fullname FROM players WHERE (id != 1) AND (id != ?)');
    $db_pairs->execute(array($id));
    $j = 0;
    while ($row = $db_pairs->fetch()) {
        $pairs[$j][0] = $row['id'];
        $pairs[$j][1] = $row['fullname'];
        $j++;
    }
    $n = count($pairs);

    //получаем данные по парам *****************************************
    for ($i = 0; $i < $n; $i++) {
        $db_duet = $db->prepare('SELECT COUNT(*) FROM combi '
                . 'WHERE  (p1=? OR p2=? OR p3=? OR p4=?) AND '
                . '(p1=? OR p2=? OR p3=? OR p4=?)');
        $duet = array($id, $id, $id, $id, $pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][0]);
        $db_duet->execute($duet);
        $pairs[$i][2] = $db_duet->fetchcolumn();

        $db_duet2 = $db->prepare('SELECT COUNT(*) FROM combi_a '
                . 'WHERE  (p1=? OR p2=? OR p3=? OR p4=?) AND '
                . '(p1=? OR p2=? OR p3=? OR p4=?)');
        $duet2 = array($id, $id, $id, $id, $pairs[$i][0], $pairs[$i][0], $pairs[$i][0], $pairs[$i][0]);
        $db_duet2->execute($duet2);
        $pairs[$i][3] = $db_duet2->fetchcolumn();
        $pairs[$i][4] = $pairs[$i][2] - $pairs[$i][3];
    }
    for ($i = 0; $i < $n; $i++) {
        if (($pairs[$i][2] == 0) AND ($pairs[$i][3] == 0)) {
        } else {
            $duo[] = $pairs[$i];
        }
    }
    $delta=array();
    foreach ($duo as $value){
        $delta[] = $value[4];
    }
    array_multisort($delta, SORT_DESC, $duo);

    //выводим результат
?>  
    <h3>Голы с партнерами:</h3>
    <table border="1">
        <tr>
            <th>Партнер</th>
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
            <td><?php echo $duo[$i][2] ?></td>
            <td><?php echo $duo[$i][3] ?></td>
            <td><?php echo $duo[$i][4] ?></td>
        </tr>
<?php
    }
?>  
    </table>
