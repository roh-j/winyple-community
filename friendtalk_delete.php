<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[friendtalk_delete_check])){
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

// 프랜드톡 정보 가져오기
$friendtalk_query = "select * from friendtalk where bid='$_POST[page]' and id='$_POST[friendtalk_id]'";
$friendtalk_result = mysql_query($friendtalk_query, $connect);
$friendtalk_row = mysql_fetch_array($friendtalk_result);

// 게시글 정보 가져오기
$talkupdate_user_query = "select * from board where id='$_POST[page]'";
$talkupdate_user_result = mysql_query($talkupdate_user_query, $connect);
$talkupdate_user_row = mysql_fetch_array($talkupdate_user_result);

// 프랜드톡 삭제
if($friendtalk_row[name] == $_COOKIE[user] && $user_info_row[passwd] == md5($_POST[userpasswd])){
	$delete_query = "delete from friendtalk where bid='$_POST[page]' and id='$_POST[friendtalk_id]'";
	$delete_result = mysql_query($delete_query, $connect);
	$talkupdate_count_query = "select count(*) from friendtalk where bid='$_POST[page]' and name='$_COOKIE[user]'";
	$talkupdate_count_result = mysql_query($talkupdate_count_query, $connect);
	$talkupdate_count_row = mysql_fetch_row($talkupdate_count_result);
	if ($talkupdate_count_row[0] == "0" && $talkupdate_user_row[name] != $friendtalk_row[name]){
		$talkupdate_delete_query = "delete from talkupdate where bid='$_POST[page]' and name='$_COOKIE[user]'";
		$talkupdate_delete_result= mysql_query($talkupdate_delete_query, $connect);
	}
}
else{
	echo "passwd_error";
}
mysql_close($connect);
?>