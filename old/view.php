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
                    Google Classroom、大学HP、ポータルサイトなどにアクセスできます。<br><br>

                    <?php if( $judge === false ){
                        echo("エラー:");
                        echo($takenname);
                        echo("さんは存在しなかったため登録はできませんでした。");
                        echo("\n");
                    }?>

                    こんにちは、<?php echo htmlspecialchars($name); ?> さん。<br><br>
                    下にスクロールしてご利用ください。<br>
		</p>
                    <a class="back" href="account.html">アカウント管理</a>
                    <a class="back" href="form.html">お問い合わせ</a>
		　　　　　　　　<a class="back" href="manual.pdf">使い方</a>
		    <a class="enews" href="info.html">お知らせ</a>
	　　　　　　　　　　　　　　　　　　　　　　　　

                <div class="infomation">
		    <a class="twitter-timeline" data-width="300" data-height="50%" data-theme="light" data-chrome="noheader nofooter transparent" data-link-color="#777" href="https://twitter.com/tohoku_univ?ref_src=twsrc%5Etfw">Tweets by tohoku_univ</a>
                    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
                    <p>ニュース/お知らせなど</p>
			<iframe src="info.html" width="295" height="45%"></iframe>
                </div>


                <div class="twitter">
                    
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
                            <a class="btn2" href="<?php print_r($arrayLine["url_1"]); ?>" target="_blank"><?php print_r($arrayLine["class_1"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_2"]); ?>" target="_blank"><?php print_r($arrayLine["class_2"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_3"]); ?>" target="_blank"><?php print_r($arrayLine["class_3"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_4"]); ?>" target="_blank"><?php print_r($arrayLine["class_4"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_5"]); ?>" target="_blank"><?php print_r($arrayLine["class_5"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">2</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_6"]); ?>" target="_blank"><?php print_r($arrayLine["class_6"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_7"]); ?>" target="_blank"><?php print_r($arrayLine["class_7"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_8"]); ?>" target="_blank"><?php print_r($arrayLine["class_8"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_9"]); ?>" target="_blank"><?php print_r($arrayLine["class_9"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_10"]); ?>" target="_blank"><?php print_r($arrayLine["class_10"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">3</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_11"]); ?>" target="_blank"><?php print_r($arrayLine["class_11"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_12"]); ?>" target="_blank"><?php print_r($arrayLine["class_12"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_13"]); ?>" target="_blank"><?php print_r($arrayLine["class_13"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_14"]); ?>" target="_blank"><?php print_r($arrayLine["class_14"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_15"]); ?>" target="_blank"><?php print_r($arrayLine["class_15"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">4</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_16"]); ?>" target="_blank"><?php print_r($arrayLine["class_16"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_17"]); ?>" target="_blank"><?php print_r($arrayLine["class_17"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_18"]); ?>" target="_blank"><?php print_r($arrayLine["class_18"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_19"]); ?>" target="_blank"><?php print_r($arrayLine["class_19"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_20"]); ?>" target="_blank"><?php print_r($arrayLine["class_20"]); ?></a>
                        </tr>
                        <tr>
                            <a class="btn2" href="#">5</a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_21"]); ?>" target="_blank"><?php print_r($arrayLine["class_21"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_22"]); ?>" target="_blank"><?php print_r($arrayLine["class_22"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_23"]); ?>" target="_blank"><?php print_r($arrayLine["class_23"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_24"]); ?>" target="_blank"><?php print_r($arrayLine["class_24"]); ?></a>
                            <a class="btn2" href="<?php print_r($arrayLine["url_25"]); ?>" target="_blank"><?php print_r($arrayLine["class_25"]); ?></a>
                        </tr>
                       
                    </table>
                    <a class="back" href="login.html">LOGIN画面へ</a>
                </div>

    	    </section>

		
    	    <section class="tohoku">
                <div class="wrapper">
        			<h2 class="title">LINKS</h2>
                    <table class="link-list">
                        <tr>
                            <a class="btn1" href="https://www.tohoku.ac.jp/japanese/" target="_blank">大学HP</a>
                            <a class="btn1" href="<?php print_r($arrayLine["url_26"]); ?>" target="_blank"><?php print_r($arrayLine["class_26"]); ?></a>
                            <a class="btn1" href="<?php print_r($arrayLine["url_27"]); ?>" target="_blank"><?php print_r($arrayLine["class_27"]); ?></a>
			</tr>
                        <tr>
                            <a class="btn1" href="http://www2.he.tohoku.ac.jp/zengaku/zengaku.html" target="_blank">全学教育</a>
                            <a class="btn1" href="https://www.srp.tohoku.ac.jp/" target="_blank">ポータルサイト</a>
                            <a class="btn1" href="https://istu4g.dc.tohoku.ac.jp/srp_login.php" target="_blank">ISTU</a>
			</tr>
                        <tr>
                            <a class="btn1" href="<?php print_r($arrayLine["url_28"]); ?>" target="_blank"><?php print_r($arrayLine["class_28"]); ?></a>
                            <a class="btn1" href="<?php print_r($arrayLine["url_29"]); ?>" target="_blank"><?php print_r($arrayLine["class_29"]); ?></a>
                            <a class="btn1" href="<?php print_r($arrayLine["url_30"]); ?>" target="_blank"><?php print_r($arrayLine["class_30"]); ?></a>  
                        </tr>
                       
                    </table>
                </div>
    	    </section>
		

            <section class="input">
                <h2 class="title">--Registration--</h2>
                
                <form action="view.php" method='post' target='_self'> 
                    <table class ="table1">
                        <tr><th>＝</th><th>月</th><th>火</th><th>水</th><th>木</th><th>金</th></tr>
                        <tr>
                            <td>1</td>
                            <td class="radio1"><input type="radio" name="radio" value="0" required></td>
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
			    <td class="radio1"><input type="radio" name="radio" value="27" ></td>
                            <td class="radio1"><input type="radio" name="radio" value="28" ></td>
		            <td class="radio1"><input type="radio" name="radio" value="29" ></td>
                        </tr>

                    </table>
                    <div class="cp_iptxt">
			<label class="ef">
			    <input type="text" placeholder="科目名" name="lecture" required>
			</label>
		    </div>
		    <div class="cp_iptxt">
			<label class="ef">
			    <input type="text" placeholder="URL" name="gurl" required>
			</label>
	            </div>
                    <input type="hidden" name="username" value=<?php echo($name); ?>>
                    <input type="hidden" name="password" value=<?php echo($pass); ?>>
                    <button type="submit">送信</button>
                </form>

            </section>

        </div>

        <footer>
            <div class="menu">
                <ul>
                    <li>    &copy; 2021 Hiroto ABE, Tohoku University Inc. All Rights Reserved.   Ver.1.4.3 </li>
                </ul>
            </div>
        </footer>

	</body>
    
</html>
