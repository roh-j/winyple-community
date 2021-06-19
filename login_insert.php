<?
include "db_info.php";
// 로그아웃 처리
if (!empty($_POST[type]) && $_POST[type] == "logout"){
		if (empty($_COOKIE[user]) || empty($_COOKIE[sid])){
				echo "logout_fail";
				exit;
		}
		else{
		setcookie("user", "", 0, "/");
		setcookie("sid", "", 0, "/");
		setcookie("view", "", 0, "/");
		setcookie("feel", "", 0, "/");
		setcookie("view_setting", "update", 0, "/");
		exit;
	}
}
// 유저 정보 가져오기
$query = "select * from user where name='$_POST[userid]'";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);

// 기본 로그인 처리
if ($row[name] == $_POST[userid] && $row[passwd] == md5($_POST[userpasswd])){
		setcookie("user", $_POST[userid], 0, "/");
		setcookie("sid", md5($_POST[userpasswd]), 0, "/");
	setcookie("view_setting", "friends", 0, "/");
	$today = getdate();
	$today_value = $today[year] + $today[mon] + $today[mday];
	if ($today_value != $row[feel_day]){
		$feel_update_query = "update user set today_feel='', feel_day='' where name='$row[name]'";
		$feel_update_result = mysql_query($feel_update_query);
	}
	echo "$row[name]";
}
// check 값이 login 일때
else{
		echo "login_fail";
		exit;
}
mysql_close($connect);
?>