<?php
    //получаем данные забитым голам
    $db_goals = $db->prepare('SELECT COUNT(*) FROM goals WHERE author_id = ?');
    $db_goals->execute(array($id));
    $goals = $db_goals->fetchcolumn();

    //получаем данные по ассистам
    $db_assists = $db->prepare('SELECT COUNT(*) FROM goals WHERE assist_id = ?');
    $db_assists->execute(array($id));
    $assists = $db_assists->fetchcolumn();

    //получаем данные по вторым пасам
    $db_part = $db->prepare('SELECT COUNT(*) FROM goals WHERE assist2_id = ?');
    $db_part->execute(array($id));
    $part = $db_part->fetchcolumn();

    // голов со стандартов    
    $db_std = $db->prepare('SELECT COUNT(*) FROM goals WHERE standart=1 AND author_id = ?');
    $db_std->execute(array($id));
    $std = $db_std->fetchcolumn();

    //получаем данные голы по таймам
    $db_half = $db->prepare('SELECT COUNT(*) FROM goals WHERE half=1 AND author_id = ?');
    $db_half->execute(array($id));
    $half = $db_half->fetchcolumn();
    
    //получаем данные по ошибкам при пропущенных
    $db_loser = $db->prepare('SELECT COUNT(*) FROM goals_missed WHERE loser_id = ?');
    $db_loser->execute(array($id));
    $loser = $db_loser->fetchcolumn();

    $db_loser2 = $db->prepare('SELECT COUNT(*) FROM goals_missed WHERE loser2_id = ?');
    $db_loser2->execute(array($id));
    $loser2 = $db_loser2->fetchcolumn();

    //получаем данные по ситуациям
    $db_situation = $db->prepare('SELECT list_goal_types.description AS type, COUNT(*) AS num '
            . 'FROM goals JOIN list_goal_types ON list_goal_types.id=goals.type_id '
            . 'WHERE author_id = ? GROUP BY list_goal_types.description '
            . 'ORDER BY COUNT(*) DESC');
    $db_situation->execute(array($id));

    //получаем данные чем забил
    $db_body = $db->prepare('SELECT list_goal_whereby.description AS whereby, COUNT(*) AS value '
            . 'FROM goals JOIN list_goal_whereby ON list_goal_whereby.id=goals.whereby_id '
            . 'WHERE goals.author_id= ? GROUP BY list_goal_whereby.id '
            . 'ORDER BY COUNT(*) DESC');
    $db_body->execute(array($id));
    
    // выводим результат
?>  
    <h4>Основные показатели:</h4>
    <table border='1'>
        <tr>
            <td>Голов</td>
            <td><?php echo $goals ?></td>
        </tr>       
        <tr>
            <td>Ассистов</td>
            <td><?php echo $assists ?></td>
        </tr>    
        <tr>
            <td>Вторых пасов</td>
            <td><?php echo $part ?></td>
        </tr>
        <tr>
            <td>Гол+Пас</td>
            <td><?php echo $goals+$assists ?></td>
        </tr>
        <tr>
            <td>Гол+Пас+Пас(II)</td>
            <td><?php echo $goals+$assists+$part ?></td>
        </tr>
    </table>
    
    <h4>Голы по игровым ситуациям:</h4>
    <table border="1">
        <tr>
            <th>Ситуация</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_situation->fetch()){
        $type = $row['type'];
        $num = $row['num'];
?>
        <tr>
            <td><?php echo $type ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>

    <h4>Чем забил:</h4>
    <table border="1">
        <tr>
            <th>Чем</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_body->fetch()){
        $whereby = $row['whereby'];
        $num = $row['value'];
?>
        <tr>
            <td><?php echo $whereby ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>
    
    <h4>Голы со стандартов:</h4>
    <table border="1">
        <tr>
            <th>Стандарт</th>
            <th>Голов</th>
        </tr>
        <tr>
            <td>Да</td>
            <td><?php echo $std ?></td>
        </tr>
        <tr><td>Нет</td>
            <td><?php echo $goals - $std ?></td>
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
            <td><?php echo $half ?></td>
        </tr>
        <tr>
            <td>Второй</td>
            <td><?php echo $goals - $half ?></td>
        </tr>
    </table>
    
    <h4>Голевые ошибки:</h4>
    <table border="1">
        <tr>
            <td>Явная</td>
            <td><?php echo $loser ?></td>
        </tr>
        <tr>
            <td>Мог сыграть лучше</td>
            <td><?php echo $loser2 ?></td>
        </tr>
        <tr>
            <td>ВСЕГО</td>
            <td><?php echo $loser+$loser2 ?></td>
        </tr>
    </table>
    