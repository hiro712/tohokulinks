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
    header( "Location: index.html" );
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
<meta name="description"  content="TOHOKULINKS|授業で使う各サイトに素早くアクセスできます！">
<meta name="keywords"  content="TOHOKULINKS, 東北大, 授業, 時間割, classroom, google">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<!--=============Google Font ===============-->
<link href="https://fonts.googleapis.com/css?family=Lato:900&display=swap" rel="stylesheet">
<!--==============レイアウトを制御する独自のCSSを読み込み===============-->
<!--機能編 9-4-1 ニュースティッカー-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.css">
<!--モーダルウィンドウ-->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/css/modaal.min.css">
<!--自作のCSS-->
<link rel="stylesheet" type="text/css" href="reset.css">
<link rel="stylesheet" type="text/css" href="parts.css">
<link rel="stylesheet" type="text/css" href="layout.css">
</head>

<body>
<div id="splash">
<div id="splash-logo"><div class="bgextend bgLRextend"><span class="bgappear">TOHOKULINKS</span></div></div>
<!--/splash--></div>
<div class="splashbg"></div><!---画面遷移用-->

<div id="wrapper">
<header id="header">
<h1><a href="#">TOHOKULINKS</a></h1>
    
<nav id="pc-nav">
        <ul>
            <li><a href="#classroom">Classroom</a></li>
            <li><a href="#links">Links</a></li>
            <li><a href="#faq">Faq</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>
</nav>   
</header>


<div id="container">
<main id="main-area">
    
<section id="top" class="scroll-point">
    <h2>TOHOKULINKS</h2>
    <strong class="imp">
        <?php if( $judge === false ){
        echo("エラー:");
        echo($takenname);
        echo("さんは存在しなかったため登録はできませんでした。");
        echo("\n");}?>
    </strong><br>
    <strong>こんにちは、<?php echo htmlspecialchars($name); ?> さん。</strong>
</section>

<section id="topics">
    <h2>News</h2><a href="#info0" class="info"><small><u>クリック</u>すると詳細を表示します。</small></a>
    <ul class="slider">
        <!-- ここからNEWSのタイトルを記入 -->
        <li><a href="#info0" class="info"><time datetime="2021-11-02">2021.11.15</time>新しいUIに変更になりました</a></li>
        <li><a href="#info0" class="info"><time datetime="2021-11-15">2021.11.01</time>利用者数が300人を超えました</a></li>
        <!-- ここまでNEWSのタイトルを記入 -->
    </ul>
</section>

    <div id="info0" class="hide-area">
        <!-- ここからNEWSを記入 -->

        <u><p>2021/11/07<br></p></u>
          <p class="imp">11/15(月)0:00より新UIに変更になります。それに伴いURLが変更になるため、ブックマークなどしている場合はご注意ください。新しいURLは<a href="https://tohokulinks.herokuapp.com/index.html" target="_blank">https://tohokulinks.herokuapp.com/index.html</a>になります。現在はまだ使用できません。<span class= "newicon">NEW</span></p><br>

        <u><p>2021/10/04<br></p></u>
          <p>「Google Classroom」と「LINKS」のセクションを入れ替えました。「Google Classroom」を使う頻度の方が多いため、ページ上部にあったほうが良いというご提案をいただきました。</p><br>

        <u><p>2021/10/01<br></p></u>
          <p>「TOHOKULINKS」を使っていただきありがとうございます。さて、今日から後期が始まりました。良かったらこのサイトをTwitterなどで広めていただけると嬉しいです。これからも開発・運営へのご協力よろしくおねがいします。</p><br>

        <u><p>2021/10/01<br></p></u>
          <p>「使い方」を更新しました。09/27にお知らせに書いた、間違った時間割を登録してしまった場合の項目を追加しました。</p><br>

        <u><p>2021/10/01<br></p></u>
          <p>全ての Google Classroom へのリンクを新しいタブで開くようにしました。</p><br>

        <u><p>2021/09/27<br></p></u>
          <p>間違って時間割を登録してしまった場合の対処法をご紹介します！まず、もう一度時間割を登録すると上書きできます。入力ミスなどはもう一度登録してください。
              科目名のみの変更は科目名に新しい科目名、URLに自分のユーザー名を入力することで変更できます。また、元の"-"の状態に戻すには、科目名に"-"、URLに"delete"と入力します。
              分からないことがありましたら「お問い合わせ」よりご連絡ください。</p><br>

        <u><p>2021/09/23<br></p></u>
          <p>Ver.1.4に伴って、「使い方」を更新しました。ご覧ください。</p><br>

        <u><p>2021/09/23<br></p></u>
          <p class="update">Ver.1.4アップデート情報！ ご要望により、「LINKS」に登録できるURLを3つ追加しました。</p><br>

        <u><p>2021/09/20<br></p></u>
          <p class= "update">Ver.1.2.0アップデート情報！ ご要望により、ユーザー名変更とアカウント削除の項目を追加しました。「アカウント管理」よりパスワード・ユーザー名変更、アカウント削除、プライバシーポリシーの閲覧が行えます。その他、細かい変更を行いました。</p><br>

        <u><p>2021/09/20<br></p></u>
          <p>本日午前2時-4時の間、アップデートのため一時的にサーバーを停止しました。</p><br>

        <u><p>2021/09/18<br></p></u>
          <p class= "update">Ver.1.1.0アップデート情報！ ログイン画面にもお知らせを表示しました。</p><br>

        <u><p>2021/09/18<br></p></u>
          <p>時間割を削除したいときは、科目名に - 、URLに delete と入力してください。</p><br>

        <u><p>2021/09/18<br></p></u>
          <p class= "imp">必ず使い方をよく読んでからご利用ください。</p><br>

        <u><p>2021/09/18<br></p></u>
          <p>ユーザー名、パスワードは40文字以内で登録してください。40文字以上で登録しても40文字目までしか反映されません。</p><br>

        <u><p>2021/09/18<br></p></u>
          <p>本日TOHOKULINKSをTwitterにて公開しました！</p><br>

        <u><p>2021/09/02<br></p></u>
          <p>本日TOHOKULINKSを公開しました。登録してご利用ください。</p><br>

        <!-- ここまでNEWSを記入 -->

    </div>

<div class="clear"></div> <!--空のdiv.float調整用-->

<section id="classroom" class="scroll-point">
    <h2>Classroom</h2>

        <table class="class-list fadeUpTrigger">
            <tr>
                <th><a class="btn" href="#">=</a></th>
                <th><a class="btn" href="#">月</a></th>
                <th><a class="btn" href="#">火</a></th>
                <th><a class="btn" href="#">水</a></th>
                <th><a class="btn" href="#">木</a></th>
                <th><a class="btn" href="#">金</a></th>
            </tr>
            <tr>
                <td><a class="btn" href="#">1</a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_1"]); ?>" target="_blank"><?php print_r($arrayLine["class_1"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_2"]); ?>" target="_blank"><?php print_r($arrayLine["class_2"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_3"]); ?>" target="_blank"><?php print_r($arrayLine["class_3"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_4"]); ?>" target="_blank"><?php print_r($arrayLine["class_4"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_5"]); ?>" target="_blank"><?php print_r($arrayLine["class_5"]); ?></a></td>
            </tr>
            <tr>
                <td><a class="btn" href="#">2</a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_6"]); ?>" target="_blank"><?php print_r($arrayLine["class_6"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_7"]); ?>" target="_blank"><?php print_r($arrayLine["class_7"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_8"]); ?>" target="_blank"><?php print_r($arrayLine["class_8"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_9"]); ?>" target="_blank"><?php print_r($arrayLine["class_9"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_10"]); ?>" target="_blank"><?php print_r($arrayLine["class_10"]); ?></a></td>
            </tr>
            <tr>
                <td><a class="btn" href="#">3</a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_11"]); ?>" target="_blank"><?php print_r($arrayLine["class_11"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_12"]); ?>" target="_blank"><?php print_r($arrayLine["class_12"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_13"]); ?>" target="_blank"><?php print_r($arrayLine["class_13"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_14"]); ?>" target="_blank"><?php print_r($arrayLine["class_14"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_15"]); ?>" target="_blank"><?php print_r($arrayLine["class_15"]); ?></a></td>
            </tr>
            <tr>
                <td><a class="btn" href="#">4</a>
                <td><a class="btn" href="<?php print_r($arrayLine["url_16"]); ?>" target="_blank"><?php print_r($arrayLine["class_16"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_17"]); ?>" target="_blank"><?php print_r($arrayLine["class_17"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_18"]); ?>" target="_blank"><?php print_r($arrayLine["class_18"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_19"]); ?>" target="_blank"><?php print_r($arrayLine["class_19"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_20"]); ?>" target="_blank"><?php print_r($arrayLine["class_20"]); ?></a></td>
            </tr>
            <tr>
                <td><a class="btn" href="#">5</a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_21"]); ?>" target="_blank"><?php print_r($arrayLine["class_21"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_22"]); ?>" target="_blank"><?php print_r($arrayLine["class_22"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_23"]); ?>" target="_blank"><?php print_r($arrayLine["class_23"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_24"]); ?>" target="_blank"><?php print_r($arrayLine["class_24"]); ?></a></td>
                <td><a class="btn" href="<?php print_r($arrayLine["url_25"]); ?>" target="_blank"><?php print_r($arrayLine["class_25"]); ?></a></td>
            </tr>
        </table>

        <p class="fadeUpTrigger"><a href="#info1" class="info">時間割登録はここから</a></p>
        <div id="info1" class="hide-area">
            <h2 class="maincolor">Registration</h2>
            <strong>ここでは時間割の登録ができます。</strong>

            <div class="input">
                <form action="index.php" method='post' target='_self'> 
                    <table class ="input-list">
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
            </div>
        </div>

</section>
    
<section id="links" class="scroll-point">
    <h2>Links</h2>
    
    <table class="link-list fadeUpTrigger">
        <tr>
            <td><a class="btn" href="https://www.tohoku.ac.jp/japanese/" target="_blank">大学HP</a></td>
            <td><a class="btn" href="<?php print_r($arrayLine["url_26"]); ?>" target="_blank"><?php print_r($arrayLine["class_26"]); ?></a></td>
            <td><a class="btn" href="<?php print_r($arrayLine["url_27"]); ?>" target="_blank"><?php print_r($arrayLine["class_27"]); ?></a></td>
        </tr>
        <tr>
            <td><a class="btn" href="http://www2.he.tohoku.ac.jp/zengaku/zengaku.html" target="_blank">全学教育</a></td>
            <td><a class="btn" href="https://www.srp.tohoku.ac.jp/" target="_blank">ポータルサイト</a></td>
            <td><a class="btn" href="https://istu4g.dc.tohoku.ac.jp/srp_login.php" target="_blank">ISTU</a></td>
        </tr>
        <tr>
            <td><a class="btn" href="<?php print_r($arrayLine["url_28"]); ?>" target="_blank"><?php print_r($arrayLine["class_28"]); ?></a></td>
            <td><a class="btn" href="<?php print_r($arrayLine["url_29"]); ?>" target="_blank"><?php print_r($arrayLine["class_29"]); ?></a></td>
            <td><a class="btn" href="<?php print_r($arrayLine["url_30"]); ?>" target="_blank"><?php print_r($arrayLine["class_30"]); ?></a></td>
        </tr>
    </table>
</section>

    
<section id="faq" class="scroll-point">
    <h2>FAQ</h2>
    <ul class="accordion-area">
			<li class="fadeUpTrigger">
					<section class="open">
						<h3 class="title">登録した時間割の削除の方法を教えてください。</h3>
						<div class="box">
							<p>時間割を削除したいときは、科目名に - 、URLに delete と入力してください。</p>
						</div>
					</section>
				</li>
				<li class="fadeUpTrigger">
					<section>
						<h3 class="title">パスワードを変更したい、アカウントを削除したい。</h3>
						<div class="box">
							<p>ページ右上のMENUよりパスワードの変更とアカウントの削除ができます。ユーザー名の変更はできません。</p>
						</div>
					</section>
				</li>
				<li class="fadeUpTrigger">
					<section>
						<h3 class="title">使い方がわかりません。</h3>
						<div class="box">
							<p>ページ右上のMENUより使い方を確認できます。それでもわからない場合はお問い合わせフォームよりお問い合わせください。</p>
						</div>
					</section>
				</li>
                <li class="fadeUpTrigger">
					<section>
						<h3 class="title">以前のUIの方がよかった。</h3>
						<div class="box">
							<p>以前のUIは<a href="#"><u>こちら→</u></a>から利用できます。どうぞご利用ください。</p>
						</div>
					</section>
				</li>
			</ul>
</section>
<section id="contact" class="scroll-point">
    <h2>Contact</h2>
    <div class="framewrap">
        <iframe class="fadeUpTrigger" src="https://docs.google.com/forms/d/e/1FAIpQLSc7SrIDrwsBux8b9L6SyLvkbXUbi2wcE2O3QIjshA6uf0zkQw/viewform?embedded=true" width="640" height="1200" frameborder="0" marginheight="0" marginwidth="0">読み込んでいます…</iframe>
    </div>
	<div class="framewrap2">
        <p class="fadeUpTrigger" style="font-weight: bolder;"><a href="https://docs.google.com/forms/d/e/1FAIpQLSc7SrIDrwsBux8b9L6SyLvkbXUbi2wcE2O3QIjshA6uf0zkQw/viewform?embedded=true">お問い合わせはこちらから→</a></p>
    </div>
</section>
</main>
<!--/container--></div>	

<footer id="footer">

<div class="openbtn"><span></span><span>Menu</span><span></span></div>
<div id="g-nav">
<div id="g-nav-list">
        <ul>
            <li><a href="#classroom">Classroom</a></li>
            <li><a href="#links">Links</a></li>
            <li><a href="#faq">Faq</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#info1" class="info">Registration</a></li>
			<li><a href="manual.pdf">How to Use</a></li>
			<li><a href="index.html">Logout</a></li>
            <li><a href="changepw.html">パスワード変更</a></li>
            <li><a href="delete.html">アカウント削除</a></li>
        </ul>
</div>
</div> 
    <p class="footer-logo">TOHOKULINKS</p>
    <small><u><a href="#info2" class="info">プライバシーポリシー</a></u></small><br>
    <small>&copy; 2021 Hiroto ABE, Tohoku University Inc. All Rights Reserved. Ver.1.4.3</small>
	<p id="page-top"><a href="#">Top</a></p>

    <div id="info2" class="hide-area">
        <h2 style="text-align: center;">プライバシーポリシー</h2>

	<p>本ウェブサイト上で提供するサービス（以下,「本サービス」といいます。）における，
		ユーザーの個人情報の取扱いについて，以下のとおりプライバシーポリシー（以下，「本ポリシー」といいます。）を定めます。
	</p><br>
	
	<h3>第一条（個人情報）</h3>
	<p>個人情報とは、個人情報保護法にいう「個人情報」を指すものとし、生存する個人に関する情報であって、特定の個人を識別できる情報を指します。
		個人情報のうち「履歴情報および特性情報」とは、上記に定める「個人情報」以外のものをいい、ご利用日時、ご利用の方法、ユーザーのIPアドレス、
		端末の個体識別情報などを指します。
	</p><br>

	<h3>第二条（本サービスが取得する個人情報）</h3>
	<p>本サービスが取得する情報は以下のとおりです。<br>
		１．本サービス利用者から提供される情報<br>
		登録する際に入力していただく、ユーザー名、パスワード。また、時間割登録時の「科目名」とその「URL」。<br>
		
		２．自動的に受け取る情報<br>
		本サービス利用者のコンピュータがインターネットに接続する際に使用されるIPアドレス、接続情報、本サービスへのアクセス、
		本サービスからのアクセス、本サービスを介したアクセスに関するアクセス先のURL・日付・時間等の情報等。
	</p><br>

	<h3>第三条（個人情報を収集・利用する目的）</h3>
	<p>
		本サービスは、取得した個人情報を安全に管理し、次の目的のために個人情報を利用できるものとし、ユーザーはこれに同意します。<br>
		1．ユーザーからのお問い合わせへの対応<br>
		2．本アプリの利用履歴に関する情報を統計処理・分析し、ユーザーを含む第三者への情報提供サービス<br>
		3．本アプリの利用状況の管理<br>
		4．本アプリのサービスの品質向上や企画・開発等<br>
		5．本アプリのサービスにおけるユーザー満足度向上策等の検討<br>
		6．ユーザーへのアンケート調査の実施<br>
		7．上記の目的に附随する目的
	</p><br>

	<h3>第四条（個人情報の第三者提供）</h3>
	<p>当サービスは、次に掲げる場合を除いて、あらかじめユーザーの同意を得ることなく、第三者に個人情報を提供することはありません。ただし、個人情報保護法その他の法令で認められる場合を除きます。<br>
		1．法令に基づく場合<br>
		2．人の生命、身体または財産の保護のために必要がある場合であって、本人の同意を得ることが困難であるとき<br>
		3．公衆衛生の向上または児童の健全な育成の推進のために、特に必要がある場合であって、本人の同意を得ることが困難であるとき<br>
		4．国の機関もしくは地方公共団体またはその委託を受けた者が法令の定める事務を遂行することに対して協力する必要がある場合であって、本人の同意を得ることにより、当該事務の遂行に支障を及ぼすおそれがあるとき<br>
		5．予め次の事項を告知あるいは公表をしている場合<br>
		①利用目的に第三者への提供を含むこと<br>
		②第三者に提供されるデータの項目<br>
		③第三者への提供の手段または方法<br>
		④本人の求めに応じて個人情報の第三者への提供を停止すること<br>
		前項の定めにかかわらず、次に掲げる場合は第三者には該当しないものとします。<br>
		6．当社が利用目的の達成に必要な範囲内において個人情報の取扱いの全部または一部を委託する場合<br>
		7．合併その他の事由による事業の承継に伴って個人情報が提供される場合<br>
		8．個人情報を特定の者との間で共同して利用する場合であって、その旨並びに共同して利用される個人情報の項目、共同して利用する者の範囲、利用する者の利用目的および当該個人情報の管理について責任を有する者の氏名または名称について、あらかじめ本人に通知し、または本人が容易に知り得る状態に置いているとき
	</p><br>

	<h3>第五条（個人情報の開示）</h3>
	<p>当サービスは、本人から個人情報の開示を求められたときは、本人に対し、遅滞なくこれを開示します。ただし、開示することにより次のいずれかに該当する場合は、その全部または一部を開示しないこともあり、開示しない決定をした場合には、その旨を遅滞なく通知します。<br>
		１．本人または第三者の生命、身体、財産その他の権利利益を害するおそれがある場合<br>
		２．当サービス適正な実施に著しい支障を及ぼすおそれがある場合<br>
		３．その他法令に違反することとなる場合前項の定めにかかわらず、履歴情報および特性情報などの個人情報以外の情報については、原則として開示いたしません。
	</p><br>

	<h3>第六条（個人情報の訂正及び削除）</h3>
	<p>1．ユーザーは、当サービスの保有する自己の個人情報が誤った情報である場合には、当サービスが定める手続きにより、個人情報の訂正、追加または削除（以下、「訂正等」といいます。）を請求することができます。<br>
		2．当サービスは、ユーザーから前項の請求を受けてその請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の訂正等を行うものとします。<br>
		3．当サービスは、前項の規定に基づき訂正等を行った場合、または訂正等を行わない旨の決定をしたときは遅滞なく、これをユーザーに通知します。
	</p><br>

	<h3>第七条（個人情報の利用停止）</h3>
	<p>1．当サービスは、本人から、個人情報が、利用目的の範囲を超えて取り扱われているという理由、または不正の手段により取得されたものであるという理由により、その利用の停止または消去（以下、「利用停止等」といいます。）を求められた場合には、遅滞なく必要な調査を行います。<br>
		2．前項の調査結果に基づき、その請求に応じる必要があると判断した場合には、遅滞なく、当該個人情報の利用停止等を行います。<br>
		3．当サービスは、前項の規定に基づき利用停止等を行った場合、または利用停止等を行わない旨の決定をしたときは、遅滞なく、これをユーザーに通知します。<br>
		4．前2項にかかわらず、利用停止等に多額の費用を有する場合その他利用停止等を行うことが困難な場合であって、ユーザーの権利利益を保護するために必要なこれに代わるべき措置をとれる場合は、この代替策を講じるものとします。
	</p><br>

	<h3>第八条（免責）</h3>
	<p>1. 本アプリは現状有姿で提供されます。当サービスは、本アプリの完全性、有用性、動作保証、特定の目的への適合性、使用機器への適合性その他一切の事項について保証しません。
		また、通信障害、システム機器等の瑕疵、障害又は本アプリの利用により利用者又は第三者が被った損害について、一切の責任を負いません。<br>
		2. ユーザーの操作により、本アプリが、他のアプリを呼び出す場合又は他のアプリの機能を利用する場合、当該アプリの仕様、動作及び効果等について、当サービスは一切の責任を負いません。
	</p><br>

	<h3>第九条（本アプリの変更、中断又は終了）</h3>
	<p>1. 当サービスは、ユーザーへの事前の通知なく、本アプリの内容、表示、操作方法、運営方法等を変更し、又は本アプリの提供を中断、終了することができます。
		この場合、当サービスはかかる変更・中断等に起因して生じる事象について一切責任を負いません。<br>
		2. ユーザーが本ポリシーに定める事項の一つにでも違反した場合、当サービスは、何らの通知を行うことなく当該ユーザーとの間において本ポリシーを解約し、当該ユーザーについて、
		本アプリの利用を終了させることができることとします。
	</p><br>

	<h3>第十条（プライバシーポリシーの変更）</h3>
	<p>1. 本ポリシーの内容は，法令その他本ポリシーに別段の定めのある事項を除いて，ユーザーに通知することなく，変更することができるものとします。
		2. 当サービスが別途定める場合を除いて，変更後のプライバシーポリシーは，本ウェブサイトに掲載したときから効力を生じるものとします。
	</p>
	
	<p>
		<br><br><br>
		以上<br>
		2021/9/20
	</p>
    </div>
</footer>
<!--/wrapper--></div>
   
<!--=============JS ===============--> 
<!--jQuery-->
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<!--機能編 9-4-1 ニュースティッカー-->   
<script src="https://cdnjs.cloudflare.com/ajax/libs/bxslider/4.2.15/jquery.bxslider.min.js"></script>
<!--機能編 9-1-5 スクロールをするとエリアの高さに合わせて線が伸びる-->  
<script src="https://cdnjs.cloudflare.com/ajax/libs/scrollgress/2.0.0/scrollgress.min.js"></script>
<!--モーダルのJS-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Modaal/0.4.4/js/modaal.min.js"></script>
<!--自作のJS-->   
<script src="script.js"></script>
</body>
</html>