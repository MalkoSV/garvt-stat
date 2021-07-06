<?php
    // полученный ID игрока
//    $game_id = $_POST['game_id'];
    $game_id = filter_input (INPUT_POST, 'game_id', FILTER_SANITIZE_NUMBER_INT);    
    //соединение с БД
    require_once 'connect.php';

    //достаем соперника
    $db_name = $db->prepare('SELECT r.name FROM games g JOIN rivals r ON  rival_id = r.id WHERE g.id = ?');
    $db_name->execute(array($game_id));
    $name = $db_name->fetchcolumn();

    //достаем данные об игре (дата, судьи)
    $db_date = $db->prepare("SELECT date FROM games WHERE id = ?");
    $db_date->execute(array($game_id));
    $date = date("Y, F j, l, H:i", strtotime($db_date->fetchcolumn())); // переводит в новый формат
    
    $db_referеes = $db->prepare('SELECT concat(r1.surname," ",r1.name) AS ref1, concat(r2.surname," ",r2.name) AS ref2 '
            . 'FROM games g '
            . 'JOIN referеes r1 ON  referee1_id = r1.id '
            . 'JOIN referеes r2 ON  referee2_id = r2.id '
            . 'WHERE g.id = ?');
    $db_referеes->execute(array($game_id));
    while ($ref = $db_referеes->fetch()) {
        $ref1 = $ref['ref1'];
        $ref2 = $ref['ref2'];
    }

    $db_goal = $db->prepare('SELECT COUNT(*) FROM goals WHERE game_id = ?');
    $db_goal->execute(array($game_id));
    $goals = $db_goal->fetchcolumn();

    $db_missed = $db->prepare('SELECT COUNT(*) FROM goals_missed WHERE game_id = ?');
    $db_missed->execute(array($game_id));
    $missed = $db_missed->fetchcolumn();
    
    $db_goal1 = $db->prepare('SELECT COUNT(*) FROM goals WHERE game_id = ? AND half = 1');
    $db_goal1->execute(array($game_id));
    $goals1 = $db_goal1->fetchcolumn();

    $db_missed1 = $db->prepare('SELECT COUNT(*) FROM goals_missed WHERE game_id = ? AND half = 1');
    $db_missed1->execute(array($game_id));
    $missed1 = $db_missed1->fetchcolumn();

    $db_all = $db->prepare('SELECT concat(p.surname," ",p.name) AS author, COUNT(*) AS num '
            . 'FROM goals JOIN players p ON p.id = author_id '
            . 'WHERE game_id = ? GROUP BY author_id '
            . 'ORDER BY COUNT(*) DESC');
    $db_all->execute(array($game_id));

?>  
    <h2><?php echo $name.' ('.$game_id.'-й тур)' ?></h2>
    <p><?php echo $date ?></p>
    <p><?php echo 'Судьи: '.$ref1.', '.$ref2 ?></p>
    <h2><?php echo $goals.':'.$missed.' ('.$goals1.':'.$missed1.')' ?></h2>
    <h3>Голы забивали:</h3>
<?php
    while ($all = $db_all->fetch()) {
        echo $all['author'].'-'.$all['num'].'<br>';
    }
    

