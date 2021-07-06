<?php
    // получаем размер временного отрезка, в минутах
    $timeInterval = filter_input (INPUT_POST, 'interval', FILTER_SANITIZE_NUMBER_INT);    
    // число временных отрезков в тайме
    $IntervalsNumber = floor( 20 / $timeInterval );
?>
    <h3>Игровые отрезки</h3>
    <h4>в минутах ( по <?php echo $timeInterval ?> )</h4>
    <table border='1'>
        <tr>
            <th rowspan="2">Отрезок, минут</th>
            <th colspan="2">1 тайм</th>
            <th></th>
            <th colspan="2">2 тайм</th>
        </tr>       
        <tr>
            <th>Забито</th>
            <th>Пропущено</th>
            <th></th>
            <th>Забито</th>
            <th>Пропущено</th>
        </tr>       
<?php

    //соединение с БД
    require_once 'connect.php';
	    
    for ($i = 0; $i < $IntervalsNumber; $i++) {
        $array = array( $timeInterval * $i - 1, $timeInterval * ($i + 1));
        //считаем голы за отрезок времени (1-й тайм)************************
        $dbGoals = $db->prepare('SELECT count(*) FROM goals WHERE half = 1 AND MINUTE(time_point) > ? AND MINUTE(time_point) < ?');
        $dbGoals->execute( $array );

        $dbGoalsMissed = $db->prepare('SELECT count(*) FROM goals_missed WHERE half = 1 AND MINUTE(time_point) > ? AND MINUTE(time_point) < ?');
        $dbGoalsMissed->execute( $array );

        //считаем голы за отрезок времени (2-й тайм)************************
        $dbGoals2 = $db->prepare('SELECT count(*) FROM goals WHERE half = 2 AND MINUTE(time_point) > ? AND MINUTE(time_point) < ?');
        $dbGoals2->execute( $array );

        $dbGoalsMissed2 = $db->prepare('SELECT count(*) FROM goals_missed WHERE half = 2 AND MINUTE(time_point) > ? AND MINUTE(time_point) < ?');
        $dbGoalsMissed2->execute( $array );
?>
        <tr>
            <td><?php echo $timeInterval * $i ?>:00-<?php echo $timeInterval * ($i + 1) ?>:00</td>
            <td><?php echo $dbGoals->fetchcolumn() ?></td>
            <td><?php echo $dbGoalsMissed->fetchcolumn() ?></td>
            <td></td>
            <td><?php echo $dbGoals2->fetchcolumn() ?></td>
            <td><?php echo $dbGoalsMissed2->fetchcolumn() ?></td>
        </tr>
<?php
    }
	$lastPoint = $timeInterval * $IntervalsNumber - 1;
	
	//считаем голы за последний отрезок времени (1-й тайм)************************
	$dbGoals = $db->prepare('SELECT count(*) FROM goals WHERE half = 1 AND MINUTE(time_point) > ?');
	$dbGoals->execute( array($lastPoint) );

	$dbGoalsMissed = $db->prepare('SELECT count(*) FROM goals_missed WHERE half = 1 AND MINUTE(time_point) > ?');
	$dbGoalsMissed->execute( array($lastPoint) );

	//считаем голы за последний отрезок времени (1-й тайм)************************
	$dbGoals2 = $db->prepare('SELECT count(*) FROM goals WHERE half = 2 AND MINUTE(time_point) > ?');
	$dbGoals2->execute( array($lastPoint) );

	$dbGoalsMissed2 = $db->prepare('SELECT count(*) FROM goals_missed WHERE half = 2 AND MINUTE(time_point) > ?');
	$dbGoalsMissed2->execute( array($lastPoint) );
?>
        <tr>
            <td>после <?php echo $lastPoint + 1 ?>:00</td>
            <td><?php echo $dbGoals->fetchcolumn() ?></td>
            <td><?php echo $dbGoalsMissed->fetchcolumn() ?></td>
            <td></td>
            <td><?php echo $dbGoals2->fetchcolumn() ?></td>
            <td><?php echo $dbGoalsMissed2->fetchcolumn() ?></td>
        </tr>
    </table>
