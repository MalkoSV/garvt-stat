<h3>Список голов по категориям:</h3>
<?php
    //достаем все случившиеся ситуации голов и их количество
    $db_situa = $db->prepare('SELECT type_id, description, COUNT(*) AS num '
                                . 'FROM goals '
                                . 'JOIN list_goal_types gt ON type_id = gt.id '
                                . 'WHERE (author_id = ?) '
                                . 'GROUP BY type_id '
                                . 'ORDER BY COUNT(*) DESC');
    $db_situa->execute(array($id));
    
    while ($situa = $db_situa->fetch()){
        $db_gt = $db->prepare('SELECT gli, gmi, name, half, std, type, tp, delta, url, vid '
                                . 'FROM (SELECT gli, gmi, g.half, std, tp, delta, url, v.id AS vid, type '
                                        . 'FROM (SELECT goals.id AS gli, game_id AS gmi, half, time_point AS tp, standart AS std, t.description AS type '
                                                . 'FROM goals '
                                                . 'JOIN list_goal_types t ON goals.type_id = t.id '
                                                . 'WHERE author_id = ? AND type_id = ?) g '
                                        . 'JOIN video v ON (g.gmi = v.game_id AND g.half = v.half)) t1 '
                                . 'JOIN (SELECT games.id, name '
                                . 'FROM games '
                                . 'JOIN rivals ON rival_id = rivals.id) t2 ON (t1.gmi = t2.id) '
                                . 'ORDER BY t1.gli');
        $db_gt->execute(array($id,$situa['type_id']));

        // выводим результат
    ?>
        <h3><?php echo $situa['description'].' = '.$situa['num'] ?></h3>
        <table border='1'>
    <?php
        $i = 0;
    ?>  
        <tr>
            <th>№</th>
            <th>№ матча</th>
            <th>Соперник</th>
            <th>Тайм</th>
        </tr>       
    <?php   
        while ($row = $db_gt->fetch()){
            $i++;
            $num = $row['gmi'];
            $name = $row['name'];
            $half = $row['half'];
            $type = $row['type'];
            $std = ($row['std'] == 1) ? 'Да' : '';
            $vid = $row['vid'];
            $tp = $row['tp'];
            $delta = $row['delta'];
            $sec = strtotime($tp)-strtotime($delta)-5;
            $link = ($sec < 0) ? delta_video($db, $vid, $sec) : $row['url'].$sec;
    ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $num ?></td>
                <td><a href =<?php echo $link ?> > <?php echo $name ?></a></td>
                <td><?php echo $half ?></td>
            </tr>    
    <?php
        }
    ?>  
        </table>
    <?php
    }
    ?>  
