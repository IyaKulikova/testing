<?php
require_once 'config.php';
/**
* распечатка массива
**/
function print_arr($arr){
	echo '<pre>'  . print_r($arr, true) . '</pre>';
}

function array_rand_win ($array, $num_req=1) {
    $size = sizeof($array);
    if ($num_req>$size)
        return FALSE;

    $indexes = array();
    for ($cur_index=0; $cur_index<$num_req; $cur_index++) {
        $next_rand = mt_rand(0, $size-$cur_index-1);
        foreach($indexes as $ind) {
            if ($ind<=$next_rand)
                $next_rand++;
            else
                break;
        }
        $indexes[] = $next_rand;
        sort($indexes);
    }
   $keys = array_keys($array);
    if ($num_req==1)
        return $keys[$indexes[0]];

    $return_array = array();
    foreach($indexes as $ind)
        $return_array[] = $keys[$ind];
    return $return_array;
}


/**
* получение списка тестов
**/
function get_tests(){
	global $db;
	$query = "SELECT * FROM test WHERE enable = '1'";
	$res = mysqli_query($db, $query);
	if(!$res) return false;
	$data = array();
	while($row = mysqli_fetch_assoc($res)){
		$data[] = $row;
	}
	return $data;
}
/**
 * получение списка скилоов
 **/
function get_skills(){
    global $db;
    $query = "SELECT * FROM skills";
    $res = mysqli_query($db, $query);
    if(!$res) return false;
    $data = array();
    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }
    return $data;
}

/**
* получение данных теста
**/
function get_test_data($test_id, $skill_id){

	if( !$test_id ) return;
	global $db;
	$query = "SELECT q.question, q.parent_test, a.id, a.answer, a.parent_question, a.correct_answer
		FROM questions q
		LEFT JOIN answers a
			ON q.id = a.parent_question
		LEFT JOIN test
			ON test.id = q.parent_test
				WHERE q.parent_test = $test_id AND test.enable = '1' AND  `parent_skill`=$skill_id
				";

//    ORDER BY RAND() LIMIT 10

    $res = mysqli_query($db, $query);
	$data = null;
	while($row = mysqli_fetch_assoc($res)){
		if( !$row['parent_question'] ) return false;
        if (!$row['question'] ){
            echo "Тест в разработке - заполнены не все ответы. Тест: '  . $test_id . ' Уровень: "  . $skill_id ;
            return;        }
        if (!$row['answer']){
            echo "Тест в разработке - заполнены не все вопросы. Тест: '  . $test_id . ' Уровень: "  . $skill_id . ' в вопросе: '  . $row['parent_question'];
            return;        }
//        if (!$row['correct_answer']){
//            echo 'Тест в разработке - заполнены не все правильные ответы. Тест: '  . $test_id . ' Уровень: '  . $skill_id . ' Вопрос: '  . $row['parent_question'];
//            return;        }
		$data[$row['parent_question']][0] = $row['question'];
		$data[$row['parent_question']][$row['id']]['answer']  = $row['answer'];
        $data[$row['parent_question']][$row['id']]['correct_answer'] = $row['correct_answer'];
//        echo $row['parent_question'] . ' - ' . $row['id'] . '<br>';

    }

    if ($data==null) return false;
    $limit = 30;
    if ($limit>=count($data)) $limit = count($data);
   $odata=$data;
    $i=0;
//Заполняем и перемешиваем массив индексов
    foreach($data as $q=> $a){
        $ind[$i]=$q;
        $i++;
//        if ($i==$limit) break;
    }
    shuffle($ind);
//Заполняем массив в перемешанной последовательности
    $i=0;

    foreach($data as $q=> $a){
       $new_data[$ind[$i]] = $data[$ind[$i]];
        $i++;
        if ($i==$limit) break;
  }
    return $new_data;
}

/**
* строим пагинацию
**/
function pagination($count_questions, $test_data){
	$keys = array_keys($test_data);
	$pagination = '<br><div class="pagination">';
   $k=21;
//    $count_questions
    for($i = 1; $i <= ($count_questions); $i++){
		$key = array_shift($keys);
        if($i==$k) {
            $pagination .= "<br><br>";
            $k+=20;
        }

		if( $i == 1 ) {
            $pagination .= '<a class="nav-active" href="#question-'.$key. '" id="page-'.$i .'"' .  '>' . $i . '</a>';
        }else{
			$pagination .= '<a href="#question-'.$key. '" id="page-'.$i .'"' .  '>' . $i . '</a>';
		}
	}

   $pagination .= '</br></br><p><a  href="#question-0 " id = "page-0"> НАЗАД  </a>';
   $pagination .= '<a  href="#question-00 " id = "page-00"> ВПЕРЕД  </a></p></br>';

  $pagination .= '</div>';

  return $pagination;
}

/**
 * выводим результаты тестирования
 **/
function print_result($result){

	 //переменные результатов
	$all_count = count($result); // кол-во вопросов
	$correct_answer_count = 0; // кол-во верных ответов
	$incorrect_answer_count = 0; // кол-во неверных ответов
	$percent = 0; // процент верных ответов

 //подсчет результатов
	foreach($result as $item){
//        print_arr($item);


		if( isset($item['correct_answer'])) {
            if ($item['correct_answer']==1)   $correct_answer_count++;
        }
	}

	$incorrect_answer_count = $all_count - $correct_answer_count;
	$percent = round( ($correct_answer_count / $all_count * 100), 0);

//	if( $percent < 10 )return ' Вы набрали менее 10%, попробуйте пройти тест заново';

	// вывод результатов
	$print_res = '<div class="test-data">';
		$print_res .= '<div class="count-res">';
			$print_res .= "<p>Всего вопросов: <b>{$all_count}</b></p>";
			$print_res .= "<p>Из них отвечено верно: <b>{$correct_answer_count}</b></p>";
			$print_res .= "<p>Из них отвечено неверно: <b>{$incorrect_answer_count}</b></p>";
			$print_res .= "<p>% верных ответов: <b>{$percent}</b></p>";
		$print_res .= '</div>';	// .count-res
	$print_res .= '</div>'; // .test-data

	return $print_res;
}


/**
 * Определяем номер последней попытки пользователя
 **/

function get_last_lap($u)
{
    global $db;
    $res3 = mysqli_query($db,
        "SELECT * FROM `laps` WHERE `username` = '$u' ORDER BY `id` DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($res3))  $lap = $row['id'];
    else  $lap = 0;

    unset ($res3,$row,$u);
    return $lap;
}

/**
 * Сохраняем результат тестирования в базу
 **/
function save_result($result, $time_id1)
{
    global $db;
    $u = $_SESSION['admin'] ;
    $lap = get_last_lap($u)+1;
    unset ($res3,$row);

//    date_default_timezone_set('Europe/Moscow');
    $s = @getdate();
   $l = $s['year'] . "." . $s['mon'] . "." . $s['mday'] . " " . $s ['hours'] . ":" . $s['minutes'];

    //Сохраняем результаты
    foreach($result as $e=> $d){
        if ($d!=0){
           $a = $d['answer'] ;
        $query="INSERT INTO  `results` SET `user_q` = $e, `user_a` = $a ,`lap` = $lap";
        mysqli_query($db,$query);

            $query = "INSERT INTO  `laps` SET id = $lap, `lap_data` = '$l' ,`username` = '$u' , `lap_time` = $time_id1 ";
        mysqli_query($db,$query);

    }
    }
    return true;
}
/**
 * Показываем последний сохраненный результат
 **/
function show_last_result(){

    global $db;
    $u = $_SESSION['admin'] ;
    $lap = get_last_lap($u);
    $current_lap = $lap;

    $query= "SELECT * FROM  `results`
              LEFT JOIN `laps` ON results.lap = laps.id
              LEFT JOIN `answers` ON results.user_a = answers.id
              WHERE  `username` =  '$u'  ORDER BY  `lap` DESC " ;
    $res3= mysqli_query($db,$query);
    $i=0;

    $my_result = " ";
    while($row = mysqli_fetch_assoc($res3)) {
//       print_arr($row);

        if ($i == 0) {
            $my_result .=  '<br>'.'<br>'.' <div class = "test-data"> Пользователь:' . $u . '<br>';
            $my_result .= "Номер последней попытки: ".  $current_lap . '<br>';
            $my_result .= "Длительность попытки: " . $row['lap_time'] . ' секунд' . '<br>';
            $my_result .= "Дата и время попытки: " . $row['lap_data'] . '</div><hr>  ';
            $i++;
        }

        if ($row['lap'] == $current_lap) {

            $my_result .= $i . ") Вопрос № " . $row['user_q'];
            if ($row['correct_answer'] == 1) {
                $my_result .= '<span class = "ok2"> - Ответ № ' . $row['user_a'] . ' правильный  ->>';
            } else {
                $my_result .= '<span class = "error2"> - Ответ № ' . $row['user_a'] . ' неправильный  ->>';
            };
            $my_result .= " " . $row['answer'] . " " . ' </span>';
            $my_result .= '<br>';
            $i++;
        } else {
            $current_lap--;
            $i = 0;
        }
    }

    unset ($res3, $row);
    return $my_result;
}

/**
 * Проверка пароля
 **/
function check_password($u,$p){
   global $db;
   $query= "SELECT * FROM `users`  WHERE `username` = '$u' AND `password` = '$p' " ;
   $res3= mysqli_query($db,$query);
   $r=mysqli_num_rows($res3);
   if (mysqli_fetch_assoc($res3)) return true;
   return false;

}