<?php
    //соединение с БД
    require_once 'connect.php';

    $db_og = $db->query('SELECT v.game_id, r.name, concat(p.surname," ",p.name) AS author, '
            . 'concat(pa.surname," ",pa.name) AS assist, standart, gt.description, delta, time_point, url, v.id AS vid '
                        . 'FROM goals '
                        . 'JOIN games ON goals.game_id = games.id '
                        . 'JOIN rivals r ON rival_id = r.id '
                        . 'LEFT JOIN players p ON author_id = p.id '
                        . 'LEFT JOIN players pa ON assist_id = pa.id '
                        . 'JOIN list_goal_types gt ON type_id = gt.id '
                        . 'JOIN video v ON (goals.game_id = v.game_id) AND (goals.half = v.half)');

?>

<h3>Список всех голов</h3>
 
<table border='1'>
    <tr>
        <th>№</th>
        <th>№ матча</th>
        <th>Соперник</th>
        <th>Автор</th>
        <th>Ассист</th>
        <th>Стандарт</th>
        <th>Ситуация</th>
    </tr>       
<?php   
    $i = 0;
    while ($row = $db_og->fetch()){
            $i++;
            $num = $row['game_id'];
            $rival = $row['name'];
            $author = $row['author'];
            $assist = $row['assist'];
            $standart = ($row['standart'] == 1) ? 'Да' : '';
            $type = $row['description'];
            $vid = $row['vid'];
            $tp = $row['time_point'];
            $delta = $row['delta'];
            $sec = strtotime($tp)-strtotime($delta)-5;
            $link = ($sec < 0) ? delta_video($db, $vid, $sec) : $row['url'].$sec;
 ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $num ?></td>
                <td><?php echo $rival ?></td>
                <td><a href =<?php echo $link ?>><?php echo $author ?>   </a></td>
                <td><?php echo $assist ?></td>
                <td><?php echo $standart ?></td>
                <td><?php echo $type ?></td>
            </tr>    
<?php
        }
?>          
    </table>    
