<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[comment_delete_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 댓글 가져오기
$comment_query = "select * from comment where bid='$_POST[page]' and id='$_POST[comment_id]'";
$comment_result = mysql_query($comment_query, $connect);
$comment_row = mysql_fetch_array($comment_result);

// 댓글 삭제
if($comment_row[name] == $_COOKIE[user] && $user_info_row[passwd] == md5($_POST[userpasswd])){
	$delete_query = "delete from comment where bid='$_POST[page]' and id='$_POST[comment_id]'";
	$delete_result = mysql_query($delete_query, $connect);
}
else{
	echo "passwd_error";
}
mysql_close($connect);
?>