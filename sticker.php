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
// 스티커 이미지 가져오기
$sticker_user_query = "select * from user_sticker where name='$_COOKIE[user]'";
$sticker_user_result = mysql_query($sticker_user_query, $connect);
$sticker_user_row = mysql_fetch_array($sticker_user_result);

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 스티커 총 갯수
$sticker_total_query = "select count(*) from board_sticker where bid='$_REQUEST[page]'";
$sticker_total_result = mysql_query($sticker_total_query, $connect);
$sticker_total_page = mysql_fetch_row($sticker_total_result);

// 한페이지에 보여질 게시물 수
$sticker_page_size = "15";
$sticker_board_query = "select * from board_sticker where bid='$_REQUEST[page]' order by id asc limit $_REQUEST[sticker], $sticker_page_size";
$sticker_board_result = mysql_query($sticker_board_query, $connect);

// 스티커 표시
?>
<div class="read_sticker_contents">
<?
while($sticker_board_row = mysql_fetch_array($sticker_board_result)){
		$sticker_src = "upload/sticker/$sticker_board_row[sticker]";
	?>
	<div class="read_sticker_image">
		<img src="<?=$sticker_src?>" border="0" title="<?=$sticker_board_row[name]?>님이 주셨습니다." alt="" />
	</div>
	<?
}
echo "</div>";

if (empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
	?>
	<div class="read_sticker_user">로그인 해주세요</div>
	<?
}
else if (empty($sticker_user_row[name])){
	?>
	<div class="read_sticker_user"><a href="user.php">스티커 이미지를 등록해 주세요</a></div>
	<?
}
else{
		?>
	<div class="read_sticker_user">
		<form action="sticker_insert.php" method="post">
			<input type="hidden" id="sticker_check" value="error" />
			<input type="image" id="sticker_submit_image" onmouseover="sticker_image_over();" onmouseout="sticker_image_out();" src="images/sticker_submit.jpg" onclick="return sticker_image_submit('<?=$_REQUEST[page]?>', '<?=$_REQUEST[sticker]?>', '<?=$_REQUEST[no]?>', '<?=$_REQUEST[friendtalk]?>')" />
		</form>
	</div>
	<?
}
// 페이지
$start_sticker_page = $_REQUEST[sticker] + 1;
$end_sticker_page = $_REQUEST[sticker]+$sticker_page_size;
?>
<div class="read_sticker_page">
<?
if ($_REQUEST[sticker] > "0"){
	$prev_sticker_page = $start_sticker_page - 16;
	?>
		<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$prev_sticker_page?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>">이전</a>&nbsp;&nbsp;
	<?
}
echo "{$start_sticker_page}&nbsp;&nbsp;-&nbsp;&nbsp;{$end_sticker_page}";
if ($end_sticker_page < $sticker_total_page[0]){
	?>
	&nbsp;&nbsp;<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$end_sticker_page?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>">다음</a>
	<?
}
echo "</div>";
?>