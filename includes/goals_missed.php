<?php
    //соединение с БД
    require_once 'connect.php';
    
    //получаем данные по количеству пропущенных
    $db_num = $db->query('SELECT COUNT(*) AS number FROM goals_missed');
    $all_goals = $db_num->fetch();
    $all = $all_goals['number'];

    //получаем данные по ситуациям
    $db_situation = $db->query('SELECT gt.description AS type, COUNT(*) AS num '
            . 'FROM goals_missed ga JOIN list_goal_types gt ON gt.id=ga.type_id '
            . 'GROUP BY gt.description '
            . 'ORDER BY COUNT(*) DESC');
    
    //получаем данные по таймам
    $db_half = $db->query('SELECT COUNT(*) AS number FROM goals_missed WHERE half=1');
    $half = $db_half->fetch();
    $half1 = $half['number'];

    //получаем данные по стандартам
    $db_std = $db->query('SELECT COUNT(*) AS number FROM goals_missed WHERE standart=1');
    $std = $db_std->fetch();
    $all_std = $std['number'];

    //выводим результат
?>  
    <h3>Статистика "Пропущенные мячи"</h3>
    <h4>Всего пропустили - <?php echo $all ?> </h4>

    <h4>Голы по игровым ситуациям:</h4>
    <table border="1">
        <tr>
            <th>Ситуация</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_situation->fetch()){
        $type = $row['type'];
        $num = $row["num"];
?>
        <tr>
            <td><?php echo $type ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>

    <h4>Голов со стандартов:</h4>
    <table border="1">
        <tr>
            <th>Стандарт</th>
            <th>Голов</th>
        </tr>
        <tr>
            <td>Да</td>
            <td><?php echo $all_std ?></td>
        </tr>
        <tr>
            <td>Нет</td>
            <td><?php echo $all - $all_std ?></td>
        </tr>
    </table>

    <h4>Голы по таймам:</h4>
    <table border="1">
        <tr>
            <th>Тайм</th>
            <th>Голов</th>
        </tr>
        <tr>
            <td>Первый</td>
            <td><?php echo $half1 ?></td>
        </tr>
        <tr>
            <td>Второй</td>
            <td><?php echo $all - $half1 ?></td>
        </tr>
    </table>  