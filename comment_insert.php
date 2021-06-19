<?
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[comment_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 게시물 검색
$read_query = "select * from board where id='$_POST[board_id]'";
$read_result = mysql_query($read_query, $connect);
$read_row = mysql_fetch_array($read_result);

// 회원정보 가져오기
$user_query = "select * from user where name='$_COOKIE[user]'";
$user_result = mysql_query($user_query, $connect);
$user_row = mysql_fetch_array($user_result);

// 댓글 value
$comment_value = iconv("UTF-8", "CP949", rawurldecode($_POST[comment]));
$reply_value = iconv("UTF-8", "CP949", rawurldecode(strip_tags($_POST[reply])));

// 전달된 값이 없을 때
if (empty($comment_value)){
	echo "comment_empty_error";
	exit;
}
// My Talk 일때
if ($read_row[messenger] == "1" && $read_row[name] != $_COOKIE[user]){
	echo "mytalk_insert_error";
	exit;
}
// 댓글 & My Talk 1000Byte 초과 입력 시
if (strlen($comment_value) > "1000"){
	if ($read_row[messenger] == "1"){
		echo "mytalk_count_error";
		exit;
	}
	else{
		echo "comment_count_error";
		exit;
	}
}
// 댓글 등록
$comment_query = "insert into comment (id, bid, name, comment, wdate, ip, reply, writer) values ('', '$_POST[board_id]', '$_COOKIE[user]', '$comment_value', now(), '$_SERVER[REMOTE_ADDR]', '0', '$read_row[name]')";
$comment_result = mysql_query($comment_query, $connect);

// 답글 업데이트
if ($reply_value !="comment" && $reply_value != $_COOKIE[user]){
	$update_query = "update comment set reply='1' where bid='$_POST[board_id]' and id='$_POST[reply_id]'";
	$update_result = mysql_query($update_query, $connect);
}
// 게시물 작성자에게 댓글 알림표시
if($read_row[name] != $_COOKIE[user] && $reply_value != $read_row[name]){
	$board_comment_update_query = "update board set commentupdate='1' where id='$_POST[board_id]'";
	$board_comment_update_result = mysql_query($board_comment_update_query, $connect);
}
// My Talk 업데이트
if ($read_row[messenger] == "1" && $read_row[name] == $_COOKIE[user]){
	$mytalk_update_query = "update friends set talkup='1' where mate='$_COOKIE[user]' and identify='2'";
	$mytalk_update_result = mysql_query($mytalk_update_query, $connect);
}
echo "$read_row[messenger]";
mysql_close($connect);
?>