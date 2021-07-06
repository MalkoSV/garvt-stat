<?php
    // полученный ID игрока
//    $id = $_POST['player_id'];
    $id = filter_input (INPUT_POST, 'player_id', FILTER_SANITIZE_NUMBER_INT);    
    
    //соединение с БД
    require_once 'connect.php';

    //достаем имя-фамилию
    $db_name = $db->prepare('SELECT surname, name FROM players WHERE id = ?');
    $db_name->execute(array($id));
    $name_ar = $db_name->fetch();
    $name = $name_ar['surname'].' '.$name_ar['name'];
?>
    <h2><?php echo $name ?></h2>
    <h3>(информация по забитым мячам)</h3>    

<?php
    //статистика по забитым
//    require_once 'our_goals.php';
    require_once 'player_goals.php';
    require_once 'player_types.php';
    require_once 'player_duet.php';
    require_once 'player_stats.php';
?>  
