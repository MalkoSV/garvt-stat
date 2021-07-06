<?php
    //соединение с БД
    require_once 'connect.php';

    //получаем данные по авторам голов *****************************************
    $db_fours = $db->query('SELECT concat(n1.surname," ",n1.name) AS nm1, '
            . 'concat(n2.surname," ",n2.name) AS nm2, '
            . 'concat(n3.surname," ",n3.name) AS nm3, '
            . 'concat(n4.surname," ",n4.name) AS nm4, '
            . 'COUNT(*) AS num FROM combi c '
            . 'JOIN players n1 ON c.p1=n1.id '
            . 'JOIN players n2 ON c.p2=n2.id '
            . 'JOIN players n3 ON c.p3=n3.id '
            . 'JOIN players n4 ON c.p4=n4.id '
            . 'GROUP BY concat(nm1,nm2,nm3,nm4) '
            . 'ORDER BY COUNT(*) DESC, nm1,nm2,nm3,nm4');
    
    //выводим результат
?>  
    <h3>Статистика "Голевые сочетания на площадке (забитые голы):"</h3>
    <table border="1">
        <tr>
            <th>Игрок 1</th>
            <th>Игрок 2</th>
            <th>Игрок 3</th>
            <th>Игрок 4</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_fours->fetch()){
        $nm1 = $row['nm1'];
        $nm2 = $row['nm2'];
        $nm3 = $row['nm3'];
        $nm4 = $row['nm4'];
        $num = $row['num'];
?>
        <tr>
            <td><?php echo $nm1 ?></td>
            <td><?php echo $nm2 ?></td>
            <td><?php echo $nm3 ?></td>
            <td><?php echo $nm4 ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>
