<h3>Список всех пропущенных (по ситуациям):</h3>

<?php
    //соединение с БД
    require_once 'connect.php';

    $db_situa = $db->query('SELECT type_id, description, COUNT(*) AS num '
                            . 'FROM goals_missed '
                            . 'JOIN list_goal_types gt ON type_id = gt.id '
                            . 'GROUP BY type_id ORDER BY COUNT(*) DESC');
    while ($situa = $db_situa->fetch()){
        $db_og = $db->prepare('SELECT v.game_id, r.name, gt.description, delta, time_point, url, v.id AS vid '
                            . 'FROM goals_missed ga '
                            . 'JOIN games ON ga.game_id = games.id '
                            . 'JOIN rivals r ON rival_id = r.id '
                            . 'JOIN list_goal_types gt ON type_id = gt.id '
                            . 'JOIN video v ON (ga.game_id = v.game_id) AND (ga.half = v.half) '
                            . 'WHERE type_id = ?');
        $db_og->execute(array($situa['type_id']));        
    ?>
    <h3><?php echo $situa['description'].' = '.$situa['num'] ?></h3>

    <table border='1'>
        <tr>
            <th>№</th>
            <th>№ матча</th>
            <th>Соперник</th>
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
                    <td><a href =<?php echo $link ?>><?php echo $rival ?></a></td>
                </tr>    
        <?php
            }
        ?>  
            </table>
            <br>    
        <?php
    }
    ?>  
