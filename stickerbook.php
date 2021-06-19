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
// 스티커북 이미지 가져오기
$stickerbook_query = "select * from board_sticker where writer='$_COOKIE[user]'";
$stickerbook_result = mysql_query($stickerbook_query, $connect);
$stickerbook_row = mysql_fetch_array($stickerbook_result);

// 스티커북 총 갯수
$stickerbook_total_query = "select count(*) from board_sticker where writer='$_COOKIE[user]'";
$stickerbook_total_result = mysql_query($stickerbook_total_query, $connect);
$stickerbook_total_row = mysql_fetch_row($stickerbook_total_result);

// 한페이지에 보여질 게시물 수
$stickerbook_page_size = "16";
$stickerbook_board_query = "select * from board_sticker where writer='$_COOKIE[user]' order by id desc limit $_REQUEST[page], $stickerbook_page_size";
$stickerbook_board_result = mysql_query($stickerbook_board_query, $connect);

// 스티커 표시
if ($stickerbook_total_row[0] == "0"){
	echo "<br /><center>받은 스티커가 없습니다.</center><br />";
}
else{
	while($stickerbook_board_row = mysql_fetch_array($stickerbook_board_result)){
		$sticker_src = "upload/sticker/$stickerbook_board_row[sticker]";
		?>
		<div class="user_stickerbook_image">
			<img src="<?=$sticker_src?>" border="0" title="<?=$stickerbook_board_row[name]?>님이 주셨습니다." alt="" />
		</div>
		<?
	}
}
?>