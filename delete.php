<?php

// Defining each variable.
$name = $_POST["username"];
$pass = $_POST["password"];



//　Determine if a username was POST-ed.
if($name == NULL){
	header( "Location: login.html" );
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



// Make name and pass Array.
$nameArray = array_column($nameAndPass, 'name');
$passArray = array_column($nameAndPass, 'usercol');
$userID = array_search("$name", $nameArray);



// Determine if the Password is correct.
if( $passArray[$userID] !== $pass ){
    header( "Location: relogin.html" );
    exit; 
}



// Varify that userID exists.
$nameArray = array_column($nameAndPass, 'name');
$userID = array_search("$name", $nameArray);

if($userID === false){
    header( "Location: login.html" );
    exit; 
}



// Delete Username.
$arrayLine = $nameAndPass[$userID];
$realUserID = $arrayLine['id'];

$sql = "DELETE FROM user WHERE id = $realUserID";
$stmt = $db->prepare($sql);
$stmt->execute();

$sql_ = "DELETE FROM link WHERE id = $realUserID";
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
                    <?php echo htmlspecialchars($name); ?> さん、アカウントを削除しました。 <br><br>
                </p>
                <a class="complete" href="login.html"> LOGIN画面へ</a>

    		</section>
        </div>
	</body>
</html>