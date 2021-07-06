<?php
    //соединение с БД
    require_once 'connect.php';

    //получаем данные по авторам голов *****************************************
    $db_all = $db->query('SELECT concat(players.surname," ",players.name) AS name, COUNT(*) AS "num" '
            . 'FROM goals JOIN players ON goals.author_id=players.id '
            . 'WHERE goals.game_id != 2 '
            . 'GROUP BY goals.author_id ORDER BY COUNT(*) DESC');
    
    $db_num = $db->query('SELECT COUNT(*) AS number FROM goals WHERE goals.game_id != 2 ');
    $all_goals = $db_num->fetch();
    $all = $all_goals['number'];

    //получаем данные по ассистам **********************************************
    $db_assist = $db->query('SELECT concat(players.surname," ",players.name) AS name, COUNT(*) AS "num" '
            . 'FROM goals JOIN players ON goals.assist_id=players.id '
            . 'WHERE goals.game_id != 2 '
            . 'GROUP BY goals.assist_id ORDER BY COUNT(*) DESC');

    //получаем данные по ассистам2 *********************************************
    $db_assist2 = $db->query('SELECT concat(players.surname," ",players.name) AS name, COUNT(*) AS "num" '
            . 'FROM goals JOIN players ON goals.assist2_id=players.id '
            . 'WHERE goals.game_id != 2 '
            . 'GROUP BY goals.assist2_id ORDER BY COUNT(*) DESC');

    //получаем данные по гол+пас ***********************************************
    $db_gp = $db->query('SELECT concat(p.surname," ",p.name) AS name, g.num_g, a.num_a, g.num_g+a.num_a AS gp '
            . 'FROM players p, (SELECT author_id AS scorer, COUNT(*) AS num_g FROM goals WHERE goals.game_id != 2 GROUP BY author_id) g, '
            . '(SELECT assist_id AS assistant, COUNT(*) AS num_a FROM goals WHERE goals.game_id != 2 GROUP BY assist_id) a '
            . 'WHERE (p.id=g.scorer AND p.id=a.assistant) '
            . 'ORDER BY gp DESC');

    //получаем данные по гол+пас+пас2 ******************************************
    $db_gpp = $db->query('SELECT concat(p.surname," ",p.name) AS name, '
            . 'g.num_g, a.num_a, b.num_a2, g.num_g+a.num_a+b.num_a2 AS gpp '
            . 'FROM players p, '
            . '(SELECT author_id AS scorer, COUNT(*) AS num_g FROM goals WHERE goals.game_id != 2 GROUP BY author_id) g, '
            . '(SELECT assist_id AS assistant, COUNT(*) AS num_a FROM goals WHERE goals.game_id != 2 GROUP BY assist_id) a, '
            . '(SELECT assist2_id AS assistant2, COUNT(*) AS num_a2 FROM goals WHERE goals.game_id != 2 GROUP BY assist2_id) b '
            . 'WHERE (p.id=g.scorer AND p.id=a.assistant AND p.id=b.assistant2) ORDER BY gpp DESC');

    //получаем данные по голевым ситуациям *************************************
    $db_situation = $db->query('SELECT list_goal_types.description AS "type", COUNT(*) AS "num" '
            . 'FROM goals JOIN list_goal_types ON list_goal_types.id=goals.type_id '
            . 'WHERE goals.game_id != 2 '
            . 'GROUP BY list_goal_types.description ORDER BY COUNT(*) DESC');

    //получаем данные по стандартам
    $db_std = $db->query('SELECT COUNT(*) AS number FROM goals WHERE standart=1 AND goals.game_id != 2');
    $std = $db_std->fetch();
    $all_std = $std['number'];
        
    //получаем данные "чем забивали"
    $db_w = $db->query('SELECT list_goal_whereby.description AS "whereby", COUNT(*) AS "num" '
            . 'FROM goals JOIN list_goal_whereby ON goals.whereby_id=list_goal_whereby.id '
            . 'WHERE goals.game_id != 2 '
            . 'GROUP BY goals.whereby_id ORDER BY COUNT(*) DESC');
    
    //получаем данные по таймам
    $db_half = $db->query('SELECT COUNT(*) AS number FROM goals WHERE half=1 AND goals.game_id != 2');
    $half = $db_half->fetch();
    $half1 = $half['number'];
        
    //выводим результат
?>  
    <h3>Статистика "Забитые мячи"</h3>
    <h4>Всего забили - <?php echo $all ?></h4>
    <h4>Бомбардиры</h4>
    <table border="1">
        <tr>
            <th>Игрок</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_all->fetch()){
        $name = $row['name'];
        $num = $row["num"];
?>
        <tr>
            <td><?php echo $name ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>

    <h4>Ассистенты</h4>
    <table border="1">
        <tr>
            <th>Игрок</th>
            <th>Передач</th>
        </tr>
<?php   
    while ($row = $db_assist->fetch()){
        $name = $row['name'];
        $num = $row["num"];
?>
        <tr>
            <td><?php echo $name ?></td>
            <td><?php echo $num ?></td>
        </tr>
        
<?php
    }
?>  
    </table>
    
    <h4>Ассистенты (вторые пасы)</h4>
    <table border="1">
        <tr>
            <th>Игрок</th>
            <th>II пас</th>
        </tr>

<?php   
    while ($row = $db_assist2->fetch()){
        $name = $row['name'];
        $num = $row["num"];
?>
        <tr>
            <td><?php echo $name ?></td>
            <td><?php echo $num ?></td>
        </tr>
        
<?php
    }
?>  
    </table>
   
    <h4>ГОЛ + ПАС</h4>
    <table border="1">
        <tr>
            <th>Игрок</th>
            <th>Голов</th>
            <th>Передач</th>
            <th>Гол + Пас</th>
        </tr>

<?php   
        while ($row = $db_gp->fetch()){
            $name = $row['name'];
            $num_g = $row['num_g'];
            $num_a = $row['num_a'];
            $gp = $row['gp'];
?>
            <tr>
                <td><?php echo $name ?></td>
                <td><?php echo $num_g ?></td>
                <td><?php echo $num_a ?></td>
                <td><?php echo $gp ?></td>
            </tr>
<?php
        }
?>  
    </table>    
    
    <h4>ГОЛ + ПАС + ПАС2</h4>
    <table border="1">
        <tr>
            <th>Игрок</th>
            <th>Голов</th>
            <th>Передач</th>
            <th>Пас2</th>
            <th>Гол+Пас+Пас2</th>
        </tr>
<?php   
        while ($row = $db_gpp->fetch()){
            $name = $row['name'];
            $num_g = $row['num_g'];
            $num_a = $row['num_a'];
            $num_a2 = $row['num_a2'];
            $gpp = $row['gpp'];
?>
            <tr>
                <td><?php echo $name ?></td>
                <td><?php echo $num_g ?></td>
                <td><?php echo $num_a ?></td>
                <td><?php echo $num_a2 ?></td>
                <td><?php echo $gpp ?></td>
            </tr>
<?php
        }
?>  
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
   
    <h4>Чем забивали голы:</h4>
    <table border="1">
        <tr>
            <th>Часть тела</th>
            <th>Голов</th>
        </tr>

<?php   
        while ($row = $db_w->fetch()){
            $as = $row['whereby'];
            $num = $row["num"];
?>
            <tr>
                <td><?php echo $as ?></td>
                <td><?php echo $num ?></td>
            </tr>
        
<?php
        }
?>  
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