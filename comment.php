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
// 게시물 검색
$read_query = "select * from board where id='$_REQUEST[page]'";
$read_result = mysql_query($read_query, $connect);
$read_row = mysql_fetch_array($read_result);

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 한페이지에 보여질 게시물 수
$page_list_size = "30";

// 데이터 베이스에서 글 가져오기
if ($read_row[messenger] == "1"){
	$page_query = "select * from comment where bid ='$_REQUEST[page]' order by id desc limit $_REQUEST[no], $page_list_size";
}
else{
	$page_query = "select * from comment where bid ='$_REQUEST[page]' order by id asc limit $_REQUEST[no], $page_list_size";
}
$page_result = mysql_query($page_query, $connect);

// 댓글 갯수 가져오기
$page_count = mysql_query("select count(*) from comment where bid ='$_REQUEST[page]'", $connect);
$total_row = mysql_fetch_row($page_count);
$total_page = $total_row[0];

if ($read_row[messenger] == "1"){
	?>
	<div class="contents_header">
		<h3>마이 톡
		<?
		echo "+".$total_page;
		?>
		</h3>
	</div>
	<?
}
else{
	?>
	<div class="contents_header">
		<h3>댓글
		<?
		echo "+".$total_page;
		?>
		</h3>
	</div>
	<?
}
if ($total_page == "0" && $read_row[messenger] == "0"){
	?>
	<div class="comment_contents">
		<br /><center>등록된 댓글이 없습니다.</center><br />
	</div>
	<?
}
if ($total_page == "0" && $read_row[messenger] == "1"){
	?>
	<div class="comment_contents">
		<br /><center>등록된 My Talk이 없습니다.</center><br />
	</div>
	<?
}
else{
		while ($comment_row = mysql_fetch_array($page_result)){
		// 게시물 작성자 권한 확인
		$read_writer_info_query = "select * from user where name='$comment_row[name]'";
		$read_writer_info_result = mysql_query($read_writer_info_query, $connect);
		$read_writer_info_row = mysql_fetch_array($read_writer_info_result);

				if ($comment_row[name] == $_COOKIE[user] && $read_row[messenger] != "1"){
						$class_type = "background-color: #f1f1f1;";
						$img_name = "frame_multi_me_cancel.jpg";
				}
				else{
						$class_type = "";
						$img_name = "frame_multi_cancel.jpg";
				}
				$comment = nl2br(str_replace(" ", "&nbsp;", htmlspecialchars($comment_row[comment])));
		$comment_contents = preg_replace("/([^\"\'\=\>])(http|https|HTTP|HTTPS)\:\/\/(.[^ \n\<\"\']+)/", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", " ".$comment);
		?>
		<div class="comment_contents" style="<?=$class_type?>">
			<span>
			<?
			if ($read_writer_info_row[advertise] == "1"){
				?>
				<font color="red"><?=$comment_row[name]?></font>
				<?
			}
			else{
				?>
				<font color="#7195af"><?=$comment_row[name]?></font>
				<?
			}
			?>
			</span>&nbsp;<font color="gray"><?=$comment_row[wdate]?></font>
			<?
			if ($read_row[messenger] == "0"){
				?>
				&nbsp;<span><a href="#" onclick="reply_value('<?=$comment_row[name]?>', '<?=$comment_row[id]?>', '<?=$_COOKIE[user]?>'); return false;">답글</a></span>
				<?
			}
			?>
			&nbsp;<a href="comment_predelete.php?page=<?=$_REQUEST[page]?>&amp;comment_id=<?=$comment_row[id]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>"><img src="images/<?=$img_name?>" style="vertical-align: middle;" border="0" width="8" height="8" alt="삭제" /></a><br /><br /><?=$comment_contents?>
		</div>
		<?
	}
}
// 페이지
$start_page = $_REQUEST[no] + 1;
$end_page = $_REQUEST[no] + $page_list_size;
?>
<div class="comment_page">
<?
if ($_REQUEST[no] > "0"){
	$prev_page = $start_page - 31;
	?>
		<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$prev_page?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>">이전</a>&nbsp;&nbsp;
	<?
}
echo "{$start_page}&nbsp;&nbsp;-&nbsp;&nbsp;{$end_page}";
if ($end_page < $total_page){
	?>
		&nbsp;&nbsp;<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$end_page?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>">다음</a>
	<?
}
echo "</div>";

if(empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
		echo"<center>로그인 해주세요</center>";
}
else{
	?>
		<form method="post" action="comment_insert.php" name="comment_form">
		<input type="hidden" id="comment_check" value="error" />
	<input type="hidden" name="reply" id="reply" value="comment" />
	<input type="hidden" id="reply_id" name="reply_id" value="default" />
		<textarea name="comment" id="comment" rows="" cols="" style="width: 666px; height: 60px;" class="input_contents"></textarea>
	<input type="submit" class="frame_button" value="확인" onclick="return comment_submit('<?=$_REQUEST[page]?>', '<?=$_REQUEST[no]?>', '<?=$_REQUEST[sticker]?>', '<?=$_REQUEST[friendtalk]?>')" />
	</form>
	<?
}
?>