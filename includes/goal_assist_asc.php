<?php
    //соединение с БД
    require_once 'connect.php';

    //получаем данные по авторам голов *****************************************
    $db_pair = $db->query('SELECT g2.author, concat(players.surname," ",players.name) AS assist, COUNT(*) AS "num" '
            . 'FROM (SELECT concat(p.surname," ",p.name) AS author, assist_id FROM goals g JOIN players p ON g.author_id=p.id WHERE (assist_id > 0) AND (g.game_id !=2) ) g2 '
            . 'JOIN players ON g2.assist_id=players.id GROUP BY concat(author," ",assist) ORDER BY COUNT(*) DESC, g2.author');
    
    //выводим результат
?>  
    <h3>Статистика "Результативные связки:"</h3>
    <table border="1">
        <tr>
            <th>Автор гола</th>
            <th>Ассистент</th>
            <th>Голов</th>
        </tr>
<?php   
    while ($row = $db_pair->fetch()){
        $author = $row['author'];
        $assist = $row['assist'];
        $num = $row['num'];
?>
        <tr>
            <td><?php echo $author ?></td>
            <td><?php echo $assist ?></td>
            <td><?php echo $num ?></td>
        </tr>
<?php
    }
?>  
    </table>
