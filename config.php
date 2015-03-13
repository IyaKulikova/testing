<?php

define("HOST", "mysql.hostinger.ru");
define("USER", "u479654819_1");
define("PASS", "123456");
define("DB", "u479654819_1");


// first information
$db = @mysqli_connect(HOST, USER, PASS, DB) or die('Нет соединения с БД');
mysqli_set_charset($db, 'utf8') or die('Не установлена кодировка соединения');