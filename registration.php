<?php

// Defining each variable.
$name = $_POST["username"];
$pass = $_POST["password1"];
$pass_ = $_POST["password2"];



//　Determine if a username was POST-ed.
if(empty($name)){
	header( "Location: login.html" );
    exit;
}



// Access to MySQL Data and Get it as Array.
$db = parse_url($_SERVER['DATABASE_URL']);
$db['dbname'] = ltrim($db['path'], '/');
$dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset=utf8";

try {
    $db = new PDO($dsn, $db['user'], $db['pass']);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM user";
    $prepare = $db->prepare($sql);
    $prepare->execute();

    echo '<pre>';
    $prepare->execute();
    $nameAndPass = $prepare->fetchAll(PDO::FETCH_ASSOC);
    //print_r(h($nameAndPass));
    echo "\n";
    echo '</pre>';
    
    $sql_ = "SELECT * FROM link";
    $prepare_ = $db->prepare($sql_);
    $prepare_->execute();

    echo '<pre>';
    $prepare_->execute();
    $timetableInfo = $prepare_->fetchAll(PDO::FETCH_ASSOC);
    //print_r(h($timetableInfo));
    echo "\n";
    echo '</pre>';

} catch (PDOException $e) {
    echo 'Error: ' . h($e->getMessage());
}

function h($var)
{
    if (is_array($var)) {
        return array_map('h', $var);
    } else {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}



// Verify that the user name does not already exist.
$nameArray = array_column($nameAndPass, 'name');
$judge = in_array("$name", $nameArray);

if($judge === true){
    header( "Location: reregistration2.html" );
    exit;
}



// Verify that the two passwords match
if($pass !== $pass_){
    header( "Location: reregistration1.html" );
    exit;
}



// Registration to MySQL.
date_default_timezone_set('Asia/Tokyo');
$creaed_at = date('Y-m-d H:i:s');
$sql = "INSERT INTO user VALUE (0, '$name', '$pass', '$creaed_at', '$creaed_at')";
$stmt = $db->prepare($sql);
$stmt->execute();

$sql_ = "INSERT INTO link VALUE (0, '$name',
 '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#',
 '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#',
 '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#', '-', '#')";
$stmt_ = $db->prepare($sql_);
$stmt_->execute();

?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <title>TOHOKULINKS</title>
        <meta name="description" content="授業で使う各サイトに素早くアクセスできます！">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS -->
        <link rel="stylesheet" href="https://unpkg.com/destyle.css@1.0.5/destyle.css">
        <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">
        <link href="login.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com"> 
        <link href="https://fonts.googleapis.com/css2?family=Signika:wght@500&display=swap" rel="stylesheet">
    </head>


	<body>
        <div class="container">
    		<section class="hero">
    			<h1 class="title">TOHOKU LINKS</h1>
    			<p>
                    <?php echo htmlspecialchars($name); ?> さん,　登録が完了しました。 <br><br>
                </p>
                <a class="complete" href="login.html"> LOGIN画面へ</a>

    		</section>
        </div>
	</body>
</html>