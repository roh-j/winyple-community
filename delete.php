<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[board_delete_check])){
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

// 게시물 정보 가져오기
$query = "select * from board where id='$_POST[page]'";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);

if ($row[name] == $_COOKIE[user] && $user_info_row[passwd] == md5($_POST[userpasswd])){
	if ($row[messenger] == "0"){
		$bg_img_src = "upload/background/$row[bgimg]";
		if ($row[type] == "image"){
			// 이미지 삭제
			$img_src = "upload/images/$row[img]";
			$thumbnail_src = "upload/thumbnail/$row[img]";
			unlink($img_src);
			unlink($thumbnail_src);
		}
		// 배경 이미지 삭제
		switch ($row[bgimg]){
			case "default.jpg": break;
			case "mint.jpg": break;
			case "love.jpg": break;
			case "gray.jpg": break;
			default:
			$board_img_count_query = "select count(*) from board where bgimg='$row[bgimg]'";
			$board_img_count_result = mysql_query($board_img_count_query, $connect);
			$board_img_count_row = mysql_fetch_row($board_img_count_result);
			if ($board_img_count_row[0] == "1"){
				unlink($bg_img_src);
			}
			break;
		}
		// 게시물 삭제
		$delete_query = "delete from board where id='$_POST[page]'";
		$delete_result = mysql_query($delete_query, $connect);

		// 댓글 삭제
		$comment_delete_query = "delete from comment where bid='$_POST[page]'";
		$comment_delete_result = mysql_query($comment_delete_query, $connect);

		// 스티커 삭제
		$sticker_delete_query = "delete from board_sticker where bid='$_POST[page]'";
		$sticker_delete_result = mysql_query($sticker_delete_query, $connect);

		// 프랜드톡 삭제
		$friendtalk_delete_query = "delete from friendtalk where bid='$_POST[page]'";
		$friendtalk_delete_result = mysql_query($friendtalk_delete_query, $connect);
		
		// 프랜드톡 알림 삭제
		$talkupdate_delete_query = "delete from talkupdate where bid='$_POST[page]'";
		$talkupdate_delete_result = mysql_query($talkupdate_delete_query, $connect);
		echo "<script type='text/javascript'>
		location.replace('index.php');
		</script>";
	}
	else{
		echo "talkmessenger_delete_error";
		exit;
	}
}
else{
	echo "passwd_error";
}
mysql_close($connect);
?>