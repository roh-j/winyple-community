<?
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_REQUEST[view_time])){
		echo "<script type='text/javascript' language='javascript'>
		window.alert('잘못된 접근입니다.');
		location.replace('index.php');
	</script>";
		exit;
}
// 친구 표시
$friends_query = "select * from friends where name='$_COOKIE[user]' and identify='2' order by intimacy desc limit $_REQUEST[page], 10";
$friends_result = mysql_query($friends_query, $connect);

// 친구 표시 갯수
$friens_count_query = "select count(*) from friends where name='$_COOKIE[user]' and identify='2'";
$friends_count_result = mysql_query($friens_count_query, $connect);
$friends_count_total = mysql_fetch_row($friends_count_result);

// 친구가 등록 되어 있지 않을 때
if ($friends_count_total[0] == "0"){
	?>
	<div class="user_myfriend_image"><img src="upload/sticker/winyple.jpg" width="50" height="50" border="0" alt="" /></div>
	<div class="user_myfriend_friend"></div>
	<div class="user_myfriend_info">지인분들과 친구하세요!</div>
	<?
}
// 친구 정보 표시
while ($friends_row = mysql_fetch_array($friends_result)){
	// 친구 스티커 이미지 가져오기
		$friends_image_query = "select * from user_sticker where name='$friends_row[mate]'";
		$friends_image_result = mysql_query($friends_image_query, $connect);
		$friends_image_row = mysql_fetch_array($friends_image_result);
	if (empty($friends_image_row[sticker])) $friends_image_row[sticker] = "winyple.jpg";

	// 친구 Talk Messenger 가져오기
		$friends_my_talk_query = "select * from board where name='$friends_row[mate]' and messenger='1'";
		$friends_my_talk_result = mysql_query($friends_my_talk_query, $connect);
		$friends_my_talk_row = mysql_fetch_array($friends_my_talk_result);

	// 친구 게시물 갯수 가져오기
		$friends_board_count_query = "select count(*) from board where name='$friends_row[mate]'";
		$friends_board_count_result = mysql_query($friends_board_count_query, $connect);
		$friends_board_count_total = mysql_fetch_row($friends_board_count_result);

	// 친구 이름 가져오기
		$friends_original_name_query = "select * from user where name='$friends_row[mate]'";
		$friends_original_name_result = mysql_query($friends_original_name_query, $connect);
		$friends_original_name_row = mysql_fetch_array($friends_original_name_result);
		if (empty($friends_original_name_row[original])) $original_name = "";
		else $original_name = "({$friends_original_name_row[original]})";

	// 친구 새로운 게시물 확인
		$board_count_query = "select count(*) from friends where mate='$friends_row[mate]' and name='$_COOKIE[user]' and boardup='1'";
		$board_count_result = mysql_query($board_count_query, $connect);
		$board_count_row = mysql_fetch_row($board_count_result);
	?>
	<div class="user_myfriend_image"><img src="upload/sticker/<?=$friends_image_row[sticker]?>" width="50" height="50" border="0" alt="" /></div>
	<div class="user_myfriend_friend"><?=$friends_row[mate]?><?=$original_name?></div>
	<div class="user_myfriend_info">게시물 <?=$friends_board_count_total[0]?>개&nbsp;<a href="search.php?no=0&amp;type=name&amp;search=<?=$friends_row[mate]?>"><img src="images/user_file_search.jpg" border="0" width="10" height="10" alt="검색" /></a>
	<?
	if ($board_count_row[0] > "0"){
		?>
		<img src="images/new_icon.jpg" border="0" width="10" height="10" alt="업데이트" />
		<?
	}
	?>
	<a href="read.php?page=<?=$friends_my_talk_row[id]?>&amp;no=0&amp;sticker=0&amp;friedntalk=0"><font color="green">Talk</font></a></div>
	<?
}
?>