<?php

// Defining each variable.
$name = NULL;
$name = $_POST["username"];
$pass = $_POST["password"];
$time = NULL;
$lect = NULL;
$gurl = NULL;

$judge = true;  //　A variable to determine whether a user name or URL is entered when registering a timetable.

$time = $_POST["radio"] +1;
$lect = $_POST["lecture"];
$gurl = $_POST["gurl"];

$userID = 0;



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



//　Determine if a username was POST-ed.
if(empty($name)){
    header( "Location: login.html" );
}



// Determine if a user exist and the Password is correct.
$nameArray = array_column($nameAndPass, 'name');
if( array_search("$name", $nameArray, true) === false){
    header( "Location: relogin.html" );
    exit;
}
else{
    $userID = array_search("$name", $nameArray);
}

$passArray = array_column($nameAndPass, 'usercol');
if( $passArray[$userID] !== $pass ){
    header( "Location: relogin.html" );
    exit; 
}



// Make a usertable as Array.
$arrayLine = $timetableInfo[$userID];
$realUserID = $arrayLine['id'];



// Copy the class code of a user.
if( (strpos($gurl,'http') === false) && ($gurl !== NULL)){
    if(in_array("$gurl", $nameArray) === false){
        $judge = false;
        $takenname = $gurl;
    }
    else{
        $takenUserID = array_search("$gurl", $nameArray);
        $gurl = $timetableInfo[$takenUserID]["url_$time"];
    }
}



// UPDATE the Array.
if( ($gurl !== NULL)&&($judge === true) ){
    $updatedGurl = $gurl;
    $updatedLect = $lect;
    $arrayLine["url_$time"] = $updatedGurl;
    $arrayLine["class_$time"] = $updatedLect;
    
}



// UPDATE the MySQL.
if( ($gurl !== NULL)&&($judge === true) ){
    $sql = "UPDATE link SET class_$time = :class_$time, url_$time = :url_$time WHERE id = $realUserID";
    $stmt = $db->prepare($sql);

    $params = array(":class_$time" => "$updatedLect", ":url_$time" => "$updatedGurl");
    $stmt->execute($params);
}

date_default_timezone_set('Asia/Tokyo');
$loginTime = date('Y-m-d H:i:s');
$sql_ = "UPDATE user SET login = :loginTime WHERE name = '$name'";
$stmt_ = $db->prepare($sql_);
$params_ = array(":loginTime" => "$loginTime");
$stmt_->execute($params_);

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
        <link href="style.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com"> 
        <link href="https://fonts.googleapis.com/css2?family=Signika:wght@500&display=swap" rel="stylesheet">

    </head>

	<body>
        <div class="container">
    		<section class="hero">
                <p></p>
    			<h1 class="title">TOHOKU LINKS</h1>
    			<p>
                    Googe Classroom、大学HP、ポータルサイトなどにアクセスできます。<br><br><br>

                    <?php if( $judge === false ){
                        echo("エラー:");
                        echo($takenname);
                        echo("さんは存在しなかったため登録はできませんでした。");
                        echo("\n");
                    }?>

                    <br>こんにちは、<?php echo htmlspecialchars($name); ?> さん。<br><br>
                    下にスクロールしてご利用ください。<br>
                    <a href="changepw.html">→パスワード変更はこちら</a><br>
                    <a href="manual.pdf">→使い方はこちら</a><br>
                </p>

                <div class="infomation">
		    <p>ニュース/お知らせなど</p>
                    <iframe src="info.html" width="300" height="600"></iframe>
                </div>


                <div class="twitter">
                    <a class="twitter-timeline" data-width="250" data-height="650" data-theme="light" data-chrome="noheader nofooter" data-link-color="#777" href="https://twitter.com/tohoku_univ?ref_src=twsrc%5Etfw">Tweets by tohoku_univ</a>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                </div>

    		</section>

    		<section class="tohoku">
                <div class="wrapper">
        			<h2 class="title">LINKS</h2>
                    <table class="link-list">
                        <tr>
                            <a class="btn1" href="https://www.tohoku.ac.jp/japanese/">大学HP</a>
                            <a class="btn1" href="<?php print_r($arrayLine["url_26"]); ?>"><?php print_r($arrayLine["class_26"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn1" href="<?php print_r($arrayLine["url_27"]); ?>"><?php print_r($arrayLine["class_27"]); ?></a>
                            <a class="btn1" href="http://www2.he.tohoku.ac.jp/zengaku/zengaku.html">全学教育</a>
                        </tr>
                        <tr>
                            <a class="btn1" href="https://www.srp.tohoku.ac.jp/">ポータルサイト</a>
                            <a class="btn1" href="https://istu3g.dc.tohoku.ac.jp/istu3g/auth/login">ISTU</a>
                        </tr>
                       
                    </table>
                </div>
    		</section>

    		<section class="classroom">
    			<h2 class="title">GoogleClassroom</h2>
                <div class="wrapper">
                    <table class="link-list">
                        <tr>
                            <a class="btn2" href="#">=</a>
                            <a class="btn2" href="#">月</a>
                            <a class="btn2" href="#">火</a>
                            <a class="btn2" href="#">水</a>
                            <a class="btn2" href="#">木</a>
                            <a class="btn2" href="#">金</a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">1</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_1"]); ?>"><?php print_r($arrayLine["class_1"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_2"]); ?>"><?php print_r($arrayLine["class_2"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_3"]); ?>"><?php print_r($arrayLine["class_3"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_4"]); ?>"><?php print_r($arrayLine["class_4"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_5"]); ?>"><?php print_r($arrayLine["class_5"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">2</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_6"]); ?>"><?php print_r($arrayLine["class_6"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_7"]); ?>"><?php print_r($arrayLine["class_7"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_8"]); ?>"><?php print_r($arrayLine["class_8"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_9"]); ?>"><?php print_r($arrayLine["class_9"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_10"]); ?>"><?php print_r($arrayLine["class_10"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">3</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_11"]); ?>"><?php print_r($arrayLine["class_11"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_12"]); ?>"><?php print_r($arrayLine["class_12"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_13"]); ?>"><?php print_r($arrayLine["class_13"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_14"]); ?>"><?php print_r($arrayLine["class_14"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_15"]); ?>"><?php print_r($arrayLine["class_15"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">4</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_16"]); ?>"><?php print_r($arrayLine["class_16"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_17"]); ?>"><?php print_r($arrayLine["class_17"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_18"]); ?>"><?php print_r($arrayLine["class_18"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_19"]); ?>"><?php print_r($arrayLine["class_19"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_20"]); ?>"><?php print_r($arrayLine["class_20"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">5</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_21"]); ?>"><?php print_r($arrayLine["class_21"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_22"]); ?>"><?php print_r($arrayLine["class_22"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_23"]); ?>"><?php print_r($arrayLine["class_23"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_24"]); ?>"><?php print_r($arrayLine["class_24"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_25"]); ?>"><?php print_r($arrayLine["class_25"]); ?></a>
                        </tr>
                       
                    </table>
                    <a class="back" href="login.html">LOGIN画面へ</a>
                </div>


    		</section>

            <section class="input">
                <h2 class="title">--Registration--</h2>
                
                <form action="view.php" method='post' target='_self'> 
                    <table class ="table1">
                        <tr><th>＝</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th></tr>
                        <tr>
                            <td>1</td>
                            <td class="radio1"><input type="radio" name="radio" value="0" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="1" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="2" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="3" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="4" ></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td class="radio1"><input type="radio" name="radio" value="5" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="6" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="7" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="8" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="9" ></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td class="radio1"><input type="radio" name="radio" value="10" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="11" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="12" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="13" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="14" ></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td class="radio1"><input type="radio" name="radio" value="15" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="16" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="17" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="18" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="19" ></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td class="radio1"><input type="radio" name="radio" value="20" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="21" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="22" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="23" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="24" ></td>
                        </tr>
                        <tr>
                            <td>-</td>
                            <td class="radio1"><input type="radio" name="radio" value="25" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="26" ></td>
                        </tr>

                    </table>
                    <p><br>科目名：<input type="text" name="lecture" required></p>
                    <p>URL：<input type="text" name="gurl" required></p>
                    <input type="hidden" name="username" value=<?php echo($name); ?>>
                    <input type="hidden" name="password" value=<?php echo($pass); ?>>
                    <button type="submit">送信</button>
                </form>

            </section>

        </div>

        <footer>
            <div class="menu">
                <ul>
                    <li>    ©️ 2021 Hiroto ABE, Tohoku University Inc. All Rights Reserved.   Ver.1.0.0 </li>
                </ul>
            </div>
        </footer>

	</body>
    
</html>
