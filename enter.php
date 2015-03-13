<?php
session_start();
require_once 'functions.php';

if($_SESSION['admin']){
	header("Location: index.php");
	exit;
}

//$admin = 'admin';
//$pass = 'a029d0df84eb5549c641e04a9ef389e5';

//echo md5("artel");
if($_POST['submit']){
    $u = trim($_POST['user']);
    $p = md5($_POST['pass']);
    if (check_password($u,$p)) {
		$_SESSION['admin'] = $u;
		header("Location: index.php");
		exit;
	}else {
        echo '<p>Логин или пароль неверны!</p>';
    }
}
?>

<html>


<body>

<div class="content2">
    <div class="form-wrapper">
        <div class="linker">
            <span class="ring"></span>
            <span class="ring"></span>
            <span class="ring"></span>
            <span class="ring"></span>
            <span class="ring"></span>
        </div>
        <form class="login-form" action="#" method="post">
           LOG: <input type="text" name="user" placeholder="Логин" < />
            <br> PAS: <input type="password" name="pass" placeholder="Пароль" />
            <button type="submit" name="submit" value="Войти">ВОЙТИ</button>
        </form>
    </div>
</div>



</body>

</html>
<link rel="stylesheet" href="style2.css">