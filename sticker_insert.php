<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[sticker_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 스티커 이미지 가져오기
$sticker_query = "select * from user_sticker where name='$_COOKIE[user]'";
$sticker_result = mysql_query($sticker_query, $connect);
$sticker_row = mysql_fetch_array($sticker_result);

// 스티커 이미지 갯수
$sticker_check_query = "select count(*) from board_sticker where bid='$_POST[board_id]' and name='$_COOKIE[user]'";
$sticker_check_result = mysql_query($sticker_check_query, $connect);
$sticker_check_row = mysql_fetch_row($sticker_check_result);

// 게시물 정보 가져오기
$board_check_query = "select * from board where id='$_POST[board_id]'";
$board_check_result = mysql_query($board_check_query, $connect);
$board_check_row = mysql_fetch_array($board_check_result);

// 자신이 쓴 게시물에 스티커 등록 시
if ($board_check_row[name] == $_COOKIE[user]){
	echo "sticker_insert_error_1";
	exit;
}
// 스티커를 이미 준 경우
if($sticker_check_row[0] > "0"){
	echo "sticker_insert_error_2";
	exit;
}
// 게시물에 스티커 등록
$sticker_insert_query = "insert into board_sticker (id, bid, name, sticker, wdate, writer, messenger) values ('', '$_POST[board_id]', '$_COOKIE[user]', '$sticker_row[sticker]', now(), '$board_check_row[name]', '$board_check_row[messenger]')";
$sticker_insert_result = mysql_query($sticker_insert_query, $connect);
$board_sticker_update_query = "update board set stickerupdate='1' where id='$_POST[board_id]'";
$board_sticker_update_result = mysql_query($board_sticker_update_query, $connect);

mysql_close($connect);
?>