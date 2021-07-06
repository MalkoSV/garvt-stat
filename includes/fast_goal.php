<?php
    //соединение с БД
    require_once 'connect.php';

    //объединяем авторов голов, момент и соперников ****************************
    $dbAllGoals = $db->query('SELECT concat(players.surname," ",players.name) AS fullname, time_point, game_id, half, author_id, rivals.name AS rival '
            . 'FROM goals JOIN players ON goals.author_id=players.id '
            . 'JOIN games ON goals.game_id=games.id '
            . 'JOIN rivals ON rivals.id=games.rival_id');

    $goals = array();
    while ($row = $dbAllGoals->fetch()){
        $timePoint = $row['time_point'];
        $game = $row["game_id"];
        $half = $row["half"];
        $author = $row["author_id"];
        $rival = $row["rival"];
        $fullName = $row["fullname"];

        //Ищем выходы на поле перед голом***************************************
        $dbIn = $db->prepare("SELECT time_point FROM substitutions WHERE game_id = ? AND half = ? AND player_in = ? AND time_point < ?");
        $dbIn->execute(array($game,$half,$author,$timePoint));
        while ($row1 = $dbIn->fetch()){
            $substitutionTime = $row1["time_point"];
        }
        $interval = strtotime($timePoint) - strtotime($substitutionTime);
        $goals[] = array($fullName,$rival,$game,$half,$interval);
    }    

    foreach ($goals as $key=>$row){
        $ar_name[$key] = $key;
        $ar_intervall[$key] = $row[4];
    }
    array_multisort($ar_intervall, SORT_ASC, $goals);
    $n = count($goals);

    //выводим результат
?>  
    <h3>Через сколько забил после выхода на площадку</h3>
    <table border="1">
        <tr>
            <th></th>
            <th>Автор гола</th>
            <th>Соперник</th>
            <th>Игра №</th>
            <th>Тайм</th>
            <th>Прошло</th>
        </tr>
<?php   
    for ($i = 0; $i < $n; $i++) {
?>
        <tr>
            <td><?php echo $i+1 ?></td>
            <td><?php echo $goals[$i][0] ?></td>
            <td><?php echo $goals[$i][1] ?></td>
            <td><?php echo $goals[$i][2] ?></td>
            <td><?php echo $goals[$i][3] ?></td>
            <td><?php echo sectotime($goals[$i][4]) ?></td>
        </tr>
<?php
    }
?>  
    </table>
