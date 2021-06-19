<?
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
include "db_info.php";

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 아티스트 value
$artist_value = iconv("UTF-8", "CP949", rawurldecode($_POST[friends]));

// 잘못된 경로로 접근하였을 때
if (empty($_POST[friends])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 로그인이 되어 있지 않을 때
if (empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
	echo "login_fail";
	exit;
}
// 친구 검색
$friends_count_query = "select count(*) from friends where name='$_COOKIE[user]' and mate='$_POST[friends]'";
$friends_count_result = mysql_query($friends_count_query, $connect);
$friends_count_row = mysql_fetch_row($friends_count_result);

// 아티스트 검색
$aritst_count_query = "select count(*) from artist where name='$_COOKIE[user]' and people='$artist_value'";
$artist_count_result = mysql_query($aritst_count_query, $connect);
$artist_count_row = mysql_fetch_row($artist_count_result);

// mate 검색
$friends_check_query = "select count(*) from friends where name='$_POST[friends]' and mate='$_COOKIE[user]'";
$friends_check_result = mysql_query($friends_check_query, $connect);
$friends_check_row = mysql_fetch_row($friends_check_result);

// 친구 수 제한
$friends_info_count_query = "select count(*) from friends where name='$_POST[friends]' and identify='2'";
$friends_info_count_result = mysql_query($friends_info_count_query, $connect);
$friends_info_count_row = mysql_fetch_row($friends_info_count_result);

// 아티스트가 이미 등록 되어있을 때
if ($artist_count_row[0] > "0" && $_POST[type] == "artist_query"){
	echo "artist_fail_1";
	exit;
}
// 아티스트 등록
else if ($artist_count_row[0] == "0" && $_POST[type] == "artist_query"){
	$artist_insert_query = "insert into artist (id, name, people) values ('', '$_COOKIE[user]', '$artist_value')";
	$artist_insert_result = mysql_query($artist_insert_query, $connect);
	echo "arist_query_success";
	exit;
}
// 친구요청 수락
if ($_POST[type] == "friends_accept_query"){
	$friends_insert_query = "insert into friends (id, name, mate, identify, boardup, talkup, view, intimacy) values ('', '$_COOKIE[user]', '$_POST[friends]', '2', '1', '0', '0', '0')";
	$friends_insert_result = mysql_query ($friends_insert_query, $connect);
	$friends_query = "update friends set identify='2' where mate='$_COOKIE[user]' and name='$_POST[friends]'";
	$friends_result = mysql_query($friends_query, $connect);
	echo "friends_accept_query_success";
	exit;
}
// 친구 수 제한
if ($friends_info_count_row[0] > "150"){
	echo "friends_fail_1";
}
// 자신에게 친구 추가를 요청 할때
if ($_COOKIE[user] == $_POST[friends] && $_POST[type] == "friends_query"){
	echo "friends_fail_2";
	exit;
}
// 친구요청 확인 중이거나 등록이 되어있을때
if ($friends_count_row[0] > "0" && $_POST[type] == "friends_query"){
	echo "friends_fail_3";
	exit;
}
// 친구추가 하려는 사람이 이미 자신을 친구추가 요청 했을때
if ($friends_check_row[0] > "0" && $_POST[type] == "friends_query"){
	echo "friends_fail_4";
	exit;
}
// 친구 요청 입력
$friends_query = "insert into friends (id, name, mate, identify, boardup, talkup, view, intimacy) values ('', '$_COOKIE[user]', '$_POST[friends]', '1', '1', '0', '0', '0')";
$friends_result = mysql_query($friends_query, $connect);

mysql_close($connect);
?>