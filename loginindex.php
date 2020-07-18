<?php
require_once("connMysqlE3.php");//引入連接資料庫=>與require()的差別在於不會重複引入的動作
session_start();
//檢查是否經過登入，若有登入則重新導向
if(isset($_SESSION["loginMemberE3"]) && ($_SESSION["loginMemberE3"]!="")){
	//若帳號等級為 member 則導向會員中心
	if($_SESSION["memberLevelE3"]=="member"){
		header("Location: member_centerE3.php");
	//否則則導向管理中心
	}else{
		header("Location: member_adminE3.php");	
	}
}

if(isset($_POST["username"]) && isset($_POST["passwd"])){//執行會員登入
	
	$query_RecLogin = "SELECT l_username, l_passwd, l_level FROM memberdata WHERE l_username=?";
	$stmt=$db_link->prepare($query_RecLogin);
	$stmt->bind_param("s", $_POST["username"]);
	$stmt->execute();
	//取出帳號密碼的值綁定結果
	$stmt->bind_result($username, $passwd, $level);	
	$stmt->fetch();
	$stmt->close();
	//跟資料夾的資料比對密碼，若登入成功則呈現登入狀態
	if(password_verify($_POST["passwd"],$passwd)){
		//計算登入次數及更新登入時間
		$query_RecLoginUpdate = "UPDATE memberdata SET l_login=l_login+1, l_logintime=NOW() WHERE l_username=?";
		$stmt=$db_link->prepare($query_RecLoginUpdate);
	    $stmt->bind_param("s", $username);
	    $stmt->execute();	
	    $stmt->close();
		//設定登入者的名稱及等級
		$_SESSION["loginMemberE3"]=$username;
		$_SESSION["memberLevelE3"]=$level;
		//使用Cookie記錄登入資料
		if(isset($_POST["rememberme"])&&($_POST["rememberme"]=="true")){
			setcookie("remUser", $_POST["username"], time()+365*24*60);
			setcookie("remPass", $_POST["passwd"], time()+365*24*60);
		}else{
			if(isset($_COOKIE["remUser"])){
				setcookie("remUser", $_POST["username"], time()-100);
				setcookie("remPass", $_POST["passwd"], time()-100);
			}
		}
		//若帳號等級為 member 導向會員中心
		if($_SESSION["memberLevelE3"]=="member"){
			header("Location: member_centerE3.php");
		//否則則導向管理中心
		}else{
			header("Location: member_adminE3.php");	
		}
	}else{
		header("Location: index.php?errMsg=1");
	}
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登入系統</title>
<link href="loginsystemstyle.css" rel="stylesheet" type="text/css">
</head>

<body>
   <!-- ↓RWD選單按鈕↓ -->
   <input type="checkbox" name="" id="menu_control">

<label for="menu_control" class="menu_btn">
   <span>選單</span>
</label>
  <!--↑RWD選單按鈕↑ -->

<div id="div01"class="header">
    <ul>
    <li>test1
        <ul class="drop-menu">
            <li>01</li>
            <li>02</li>
            <li>03</li>
            <li>04</li>
        </ul>
    </li>
    <li>test2
        <ul class="drop-menu">
            <li>10</li>
            <li>11</li>
            <li>12</li>
            <li>13</li>
        </ul>
    </li>
    <li>test3
        <ul class="drop-menu">
            <li>22</li>
            <li>23</li>
            <li>24</li>
            <li>25</li>
        </ul>
    </li>
    <li>test4
        <ul class="drop-menu">
            <li>26</li>
            <li>27</li>
            <li>28</li>
            <li>29</li>
        </ul>
    </li>
    </ul>
 </div>

<table width="400" border="0" align="center" cellpadding="4" cellspacing="0">
  <tr>
    <td class=""><table width="100%" border="0" cellspacing="0" cellpadding="10">
      
<div class="boxregion1"><?php if(isset($_GET["errMsg"]) && ($_GET["errMsg"]=="1")){?>
          <div class="errDiv"> 登入帳號或密碼錯誤！</div>
          <?php }?>
          <p class="heading1">登入</p>
          <form name="form1" method="post" action="">
            <div class="acc">
            <p>帳號：
              <input name="username" type="text" class="logintextbox heading2" id="username" value="<?php if(isset($_COOKIE["remUser"]) && ($_COOKIE["remUser"]!="")) echo $_COOKIE["remUser"];?>">
            </p>
            <p>密碼：
              <input name="passwd" type="password" class="logintextbox heading2" id="passwd" value="<?php if(isset($_COOKIE["remPass"]) && ($_COOKIE["remPass"]!="")) echo $_COOKIE["remPass"];?>">
            </p>
            <p>
              <input class="rememberme" name="rememberme" type="checkbox" id="rememberme" value="true" checked>記住我的帳號密碼。</p>
            <p>
              <input class="loginbutton"type="submit" name="loginbutton" id="loginbutton" value="登入">
            </p>
            </div>
            </form>
        <div class="boxregion2">
          <p class="center2"><a href="admin_passmail.php">忘記密碼，補寄密碼信。</a></p>
          <p><a href="member_join.php">馬上申請會員</a></p>
        </div>
</div>
        </td>
        </tr>
    </table></td>
    </tr>
</table>

<div class="footer">
       <a>Copyright © 2020 by lhu e3</a>
 </div>

</body>
</html>
