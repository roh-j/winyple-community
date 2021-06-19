<?
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[friendtalk_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 프랜드톡 value
$friend_comment_value = iconv("UTF-8", "CP949", rawurldecode($_POST[friend_comment]));

// 전달된 값이 없을 때
if (empty($friend_comment_value)){
	echo "friendtalk_empty_error";
	exit;
}
// 프랜드톡 1000Byte 초과 입력 시
if (strlen($friend_comment_value) > "1000"){
	echo "friendtalk_count_error";
	exit;
}
// 게시물 검색
$read_query = "select * from board where id='$_POST[board_id]'";
$read_result = mysql_query($read_query, $connect);
$read_row = mysql_fetch_array($read_result);

// 유저 정보 가져오기
$user_query = "select * from user where name='$_COOKIE[user]'";
$user_result = mysql_query($user_query, $connect);
$user_row = mysql_fetch_array($user_result);

// 프랜드톡 업데이트
$update_query = "select count(*) from talkupdate where bid='$_POST[board_id]' and name='$_COOKIE[user]'";
$update_result = mysql_query($update_query, $connect);
$update_row = mysql_fetch_row($update_result);

// 프랜드톡 알림 등록
if($update_row[0] == "0"){
	$friendtalk_query = "insert into talkupdate (id, bid, name, talkup) values ('', '$_POST[board_id]', '$_COOKIE[user]', '0')";
	$friendtalk_result = mysql_query($friendtalk_query, $connect);
}
// 프랜드톡 등록
$friendtalk_query = "insert into friendtalk (id, bid, name, comment, wdate, ip, writer) values ('', '$_POST[board_id]', '$_COOKIE[user]', '$friend_comment_value', now(), '$_SERVER[REMOTE_ADDR]', '$read_row[name]')";
$friendtalk_result = mysql_query($friendtalk_query, $connect);

// 프랜드톡 알림 업데이트
$board_friendtalk_update_query = "update talkupdate set talkup='1' where bid='$_POST[board_id]' and name!='$_COOKIE[user]'";
$board_friendtalk_update_result = mysql_query($board_friendtalk_update_query, $connect);

mysql_close($connect);
?>