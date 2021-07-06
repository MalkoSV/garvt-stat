<?php
    //соединение с БД
    require_once 'connect.php';

    $db_og = $db->query('SELECT v.game_id, r.name, standart, gt.description, delta, time_point, url, v.id AS vid '
                        . 'FROM goals_missed gl '
                        . 'JOIN games g ON gl.game_id = g.id '
                        . 'JOIN rivals r ON rival_id = r.id '
                        . 'JOIN list_goal_types gt ON type_id = gt.id '
                        . 'JOIN video v ON (gl.game_id = v.game_id) AND (gl.half = v.half)');
?>

<h3>Список всех пропущенных</h3>
 
<table border='1'>
    <tr>
        <th>№</th>
        <th>№ матча</th>
        <th>Соперник</th>
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
                <td><?php echo $standart ?></td>
                <td><a href =<?php echo $link ?>><?php echo $type ?>   </a></td>
            </tr>    
<?php
        }
?>          
    </table>    
