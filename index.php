<head>
    <title>Статистика команды "ГрАвт"</title>
</head>

<body>

<?php

define("M1", 10);
define("M2", 23);
define("P1", 110);
define("P2", 260);
define("MAXM", 100);

$n1Max = floor(MAXM / M1);
for ($n1 = 0; $n1 <= $n1Max; $n1++){
	$n2 = floor((MAXM - $n1 * M1) / M2);
	$numberStonesAndSum[$n1] = array($n1, $n2, sumProfit($n1, $n2));
}
$bestProfit = array();
foreach ($numberStonesAndSum as $value) {
	if ($value[2] > $bestProfit[2]) {
		$bestProfit = $value;
	}
}
var_dump($bestProfit);


function sumProfit($n1, $n2){
    return $n1*P1 + $n2*P2;
}


	
?>





    <h3>СТАТИСТИКА КОМАНДЫ "ГРАВТ"</h3>
    <h3>Игры</h3>
    <form action='/includes/games.php' method='post'>
        <select name='game_id'>
            <?php    
                //соединение с БД
                require_once 'includes/connect.php';

                $db_games = $db->query('SELECT g.id, r.name FROM games g JOIN rivals r ON  rival_id = r.id');

                while ($games = $db_games->fetch()){
                    $id = $games['id'];
                    $name = $games['name'];
                ?>
                    <option value=<?php echo $id ?>><?php echo $name ?></option>
                <?php
                }
                ?>    
        </select>
        <p><input type="submit" value="Выбрать и нажать"></p>
    </form>
    <hr size="1">    
    <h3>Забитые мячи</h3>
    <a href ='/includes/goals_stat.php'>Общая статистика</a><br>
<!--   <a href ='/includes/goals_stat_asc.php'>Общая статистика (без Аскета)</a><br>   -->  
    <a href ='/includes/list_goals.php'>Список голов</a><br>
    <a href ='/includes/list_goals_types.php'>Список голов по ситуациям</a><br>
    <a href ='/includes/fast_goal.php'>Сколько прошло после выхода на площадку</a><br>
    <hr size="1">

    <h3>Пропущенные мячи</h3>
    <a href ='/includes/goals_missed.php'>Общая статистика</a><br>
    <a href ='/includes/list_goals_missed.php'>Список голов</a><br>
    <a href ='/includes/list_goals_types_missed.php'>Список голов по ситуациям</a><br>
    <hr size="1">    
    
    <h3>Игровые отрезки, в минутах</h3>
    <form method="post" action='/includes/intervals.php'>
        <p>
            <select name=interval size=1>
                <option value=1>1</option>
                <option value=2>2</option>
                <option value=3>3</option>
                <option value=4>4</option>
                <option value=5>5</option>
                <option value=6>6</option>
                <option value=7>7</option>
                <option value=8>8</option>
                <option value=9>9</option>
                <option value=10>10</option>
            </select>
        </p>
        <p><input type='submit' value='Выбрать и нажать'></p>
    </form>
    <hr size="1">

    <h3>Сочетания</h3>
    <h4>Подготовка таблиц</h4>
    <a href ='/includes/combi.php'>Наши голы: "четверки" игроков</a><br>
    <a href ='/includes/combi_missed.php'>Пропущенные: "четверки" игроков</a><br>
<!--     <a href ='/includes/combi_asc.php'>Наши голы: "четверки" игроков (без Аскета)</a><br> -->
    <hr size="1">    
    <a href ='/includes/goal_assist.php'>Связки "Гол+Пас"</a><br>
<!--    <a href ='/includes/goal_assist_asc.php'>Связки "Гол+Пас" (без Аскета)</a><br> -->
    <a href ='/includes/fours.php'>"Четверки"</a><br>
    <a href ='/includes/fours_missed.php'>АНТИ "четверки"</a><br>
    <a href ='/includes/duet.php'>"Двойки"</a><br>
    <a href ='/includes/ones_all.php'>"Одиночки"</a><br>
    <hr size="1">    

    <h3>Вклад игроков</h3>
    <a href ='/includes/personal.php'>Индивидуальная турнирная таблица</a><br>
    <hr size="1">    
    
    <h4>ПО ИГРОКАМ:</h4>
 
    <form action='/includes/player.php' method='post'>
        <select name='player_id'>
            <?php    
                //соединение с БД
                require_once 'includes/connect.php';

                $db_players = $db->query('SELECT id, surname, name FROM players ORDER BY surname');

                while ($players = $db_players->fetch()){
                    $id = $players['id'];
                    $surname = $players['surname'];
                    $name = $players['name'];
                ?>
                    <option value=<?php echo $id ?>><?php echo $surname.' '.$name ?></option>
                <?php
                }
                ?>    
        </select>
        <p><input type="submit" value="Выбрать и нажать"></p>
    </form>
    <hr size="1">    

    <h4>ИГРОВЫЕ СИТУАЦИИ:</h4>

    <form action='/includes/goals_types.php' method='post'>
        <select name='types_id'>
            <?php    
                //соединение с БД
                require_once 'includes/connect.php';

                $db_types = $db->query('SELECT id, description FROM list_goal_types ORDER BY id');

                while ($types = $db_types->fetch()){
                    $id = $types['id'];
                    $type = $types['description'];
                ?>
                    <option value=<?php echo $id ?>><?php echo $type ?></option>
                <?php
                }
                ?>    
        </select>
        <p>
           <input name='who' type='radio' value=1>Забитые
           <input name='who' type='radio' value=0 checked='checked'>Пропущенные
       </p>
       <p><input type='submit' value='Выбрать и нажать'></p>
    </form>


</body>

