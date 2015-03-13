<?php
require "auth.php";
?>

<?php
ini_set("display_errors", 1);
error_reporting(-1);
require_once 'config.php';
require_once 'functions.php';


if( isset($_POST['test']) ) {
    $test = (int)$_POST['test'];
    $skill = (int)$_POST['skill'];
    $time_id2 = (int)$_POST['time_id'];
    $result = $_POST;
    unset($result['test'],$result['skill'],$result['time_id'], $_POST);

     $res= print_result($result);
    //Время выполнения теста
    $time_id1 =time()-$time_id2;
    //Функция сохраняет результаты в базу данных
    if (save_result ($result, $time_id1)) $res .= "Результаты тестирования сохранены" ;

    echo $res;
     die;
}

//print_arr($_GET);
if( isset($_GET['test']) && isset($_GET['skill']) ){
    $test_id = (int)$_GET['test'];
    $skill_id = (int)$_GET['skill'];
    $time_id=time();

    $test_data = get_test_data($test_id,$skill_id);

    if( is_array($test_data) ){
        $count_questions = count($test_data);
        $pagination = pagination($count_questions, $test_data);
    }
    else {
        unset ($test_data);

    }


   }
// список тестиров и скилок для заполнения радиокнопок выбора
$tests = get_tests();
$skills = get_skills();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style2.css">

    <meta charset="UTF-8">
    <title>Система тестирования Soft-Artel </title>
</head>

<body>

<!--Кнопка выхода для смены пользователя-->
<div class="exit_form2">
    <a href="index.php?do=logout">Выход</a>
</div>
<!--Кнопка для показа результатов последнего тестирования-->
<div class="button">
    <button class="center btn" id="btn2"> Показать результаты </button>
</div>


<form  onsubmit="" name="input_data" action="index.php" method="get"">
<!--Вводим тест-->
    <div class="block" id = "block1">
    <div class="tests_block">
    <?php if( $tests ): ?>
    <h3>Варианты тестов</h3>
        <?php foreach($tests as $test): ?>
            <p class = "my_skills"><input type="radio"
                                          id="?test=<?=$test['id']?>"
                                          name="test"
                                          value = "<?=$test['id']?>">
                <label for = "?test=<?=$test['id']?>"><?=$test['test_name']?> </label> </p>
        <?php endforeach;?>
        </div> <!-- test_blocks-->

<!--Вводим скилл-->
        <div class="skills_block" >
            <h3>Уровень </h3>
    <?php
    foreach ($skills as $skill):?>
        <h3 class = "my_skills" data-id="<?=$skill['id']?>" id="skill-<?=$skill?>">
            <input type="radio"
                   id = "skill-<?=$skill['id']?>"
                   name="skill"
                   test_id="?test=<?=$test['id']?>"
                   value = "<?=$skill['id']?>">
            <label for = "skill-<?=$skill['id']?>"><?=$skill['skill_name']?> </label> </h3>
    <?php    endforeach;    ?>

    <input type="submit" value="  Начать тест  "class = "button" id="submit1">
    </div> <!-- Skills Blocks-->
       </div>  <!-- Blocks-->
</form>

<div class="wrap" id="wrap1">
    <br><hr>
    <div class="content">

        <?php if( isset($test_data) ): ?>

            <?=$pagination?>

            <span class="none" id="test-id"><?=$test_id?></span>
            <span class="none" id="skill-id"><?=$skill_id?></span>
            <span class="none" id="time-id"><?=$time_id?></span>

            <div class="test-data">
                <?php
                foreach($test_data as $id_question => $item): // получаем каждый конкретный вопрос + ответы ?>
                    <div class="question" data-id="<?=$id_question?>" id="question-<?=$id_question?>">
                        <?php foreach($item as $id_answer => $answer): // проходимся по массиву вопрос/ответы ?>
                            <?php if( !$id_answer ):  // выводим вопрос ?>
                                <p class="q"><?=$answer?></p>
                            <?php else:  // выводим варианты ответов?>
                                <p class="a">
                                    <input type="radio"
                                           id="answer-<?=$id_answer?>"
                                           name="question-<?=$id_question?>"
                                           correct_answer = "<?=$answer['correct_answer']?>"
                                           value="<?=$id_answer?>">
                                    <?php   if($answer['answer']!=="softartel") {  ?>
                                        <label for="answer-<?=$id_answer?>"><?=$answer['answer']?></label></p>
                                    <?php   } else {  ?>
                                        <label for="answer-<?=$id_answer?>">Введите ответ: <input type="text" name = "text-<?=$id_question?>" ></label></p>
                                    <?php   }   // SoftArtel?>
                            <?php  endif; // $id_answer ?>
                        <?php endforeach; // $item   ?>
                    </div> <!-- .question -->
                <?php endforeach; // $test_data ?>
            </div> <!-- .test-data -->

            <div class="buttons">
                <button class="center btn" id="btn">Закончить тест</button>
            </div>

        <?php else: // isset($test_data) ?>
            Выберите тест - тест в разработке или не выбран
        <?php endif; // isset($test_data) ?>

    </div> <!-- .content -->

    <?php else: // $tests ?>
        <h3>Нет тестов</h3>
    <?php endif; // $tests ?>
</div> <!-- .wrap -->


    <div class="last_result" id ="last_result1" >
         <?php echo show_last_result();?>
    </div>


<script src="http://code.jquery.com/jquery-latest.js">
    //<script src="jquery-latest.js">
</script>
<script src="scripts.js"></script>

</body>
</html>