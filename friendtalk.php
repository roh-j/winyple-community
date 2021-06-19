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

// 어드민 정보 가져오기
$admin_info_query = "select * from user where name='admin'";
$admin_info_result = mysql_query($admin_info_query, $connect);
$admin_info_row = mysql_fetch_array($admin_info_result);

// 친구 권한 확인
$friend_query = "select count(*) from friends where name='$_COOKIE[user]' and mate='$read_row[name]' and identify='2'";
$friend_result = mysql_query($friend_query, $connect);
$friend_row = mysql_fetch_row($friend_result);

// 한페이지에 보여질 게시물 수
$page_list_size = "30";

// 프랜드톡 글 가져오기
$page_query = "select * from friendtalk where bid='$_REQUEST[page]' order by id desc limit $_REQUEST[friendtalk], $page_list_size";
$page_result = mysql_query($page_query, $connect);

// 프랜드톡 갯수 가져오기
$page_count = mysql_query("select count(*) from friendtalk where bid ='$_REQUEST[page]'", $connect);
$total_row = mysql_fetch_row($page_count);
$total_page = $total_row[0];

if ($friend_row[0] > "0" || $read_row[name] == $_COOKIE[user] && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user]) || $_COOKIE[user] == "admin" && $_COOKIE[sid] == $admin_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
		if ($total_page == "0"){
		?>
				<div class="friendtalk_contents">
			<br /><center>등록된 Friend Talk이 없습니다.</center><br />
		</div>
		<?
		}
		else{
				while ($friendtalk_row = mysql_fetch_array($page_result)){
			// 게시물 작성자 권한 확인
			$read_writer_info_query = "select * from user where name='$friendtalk_row[name]'";
			$read_writer_info_result = mysql_query($read_writer_info_query, $connect);
			$read_writer_info_row = mysql_fetch_array($read_writer_info_result);

						if ($friendtalk_row[name] == $_COOKIE[user]){
								$class_type = "background-color: #f1f1f1;";
								$img_name = "frame_multi_me_cancel.jpg";
						}
						else{
								$class_type = "";
								$img_name = "frame_multi_cancel.jpg";
						}
						$cut_wdate = substr($friendtalk_row[wdate], 0, 16);
						$friendtalk = nl2br(str_replace(" ", "&nbsp;", htmlspecialchars($friendtalk_row[comment])));
			$friendtalk_contents = preg_replace("/([^\"\'\=\>])(http|https|HTTP|HTTPS)\:\/\/(.[^ \n\<\"\']+)/", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", " ".$friendtalk);
			?>
			<div class="friendtalk_contents" style="<?=$class_type?>">
				<span>
				<?
				if ($read_writer_info_row[advertise] == "1"){
					?>
					<font color="red"><?=$friendtalk_row[name]?></font>
					<?
				}
				else{
					?>
					<font color="#7195af"><?=$friendtalk_row[name]?></font>
					<?
				}
				?>
				</span>&nbsp;<font color="gray"><?=$cut_wdate?></font>&nbsp;&nbsp;<a href="friendtalk_predelete.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$_REQUEST[friendtalk]?>&amp;friendtalk_id=<?=$friendtalk_row[id]?>"><img src="images/<?=$img_name?>" style="vertical-align: middle;" border="0" width="7" height="7" alt="삭제" /></a><br /><br /><?=$friendtalk_contents?>
			</div>
			<?
				}
		}
		// 페이지
		$start_page = $_REQUEST[friendtalk]+1;
		$end_page = $_REQUEST[friendtalk]+$page_list_size;

		?>
	<div class="friendtalk_page">
	<?
		if ($_REQUEST[friendtalk] > "0"){
				$prev_page = $start_page - 31;
		?>
				<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$prev_page?>">이전</a>&nbsp;&nbsp;
		<?
		}
		echo "{$start_page}&nbsp;&nbsp;-&nbsp;&nbsp;{$end_page}";
		if ($end_page < $total_page){
				?>
		&nbsp;&nbsp;<a href="read.php?page=<?=$_REQUEST[page]?>&amp;no=<?=$_REQUEST[no]?>&amp;sticker=<?=$_REQUEST[sticker]?>&amp;friendtalk=<?=$end_page?>">다음</a>
		<?
		}
		echo "</div>";
	?>
	<form method="post" action="friendtalk_insert.php" name="friendtalk_form">
	<input type="hidden" id="friendtalk_check" value="error" />
	<textarea name="comment" id="friend_comment" rows="" cols="" style="width: 224px; height: 45px;" class="input_contents"></textarea>
	<input type="submit" class="frame_button" value="확인" onClick="return friendtalk_submit('<?=$_REQUEST[page]?>', '<?=$_REQUEST[friendtalk]?>', '<?=$_REQUEST[no]?>', '<?=$_REQUEST[sticker]?>')" />
	</form>
	<?
}
else{
		?>
	<div class="friendtalk_confirm">
		<br /><center><?=$read_row[name]?>님과 친구하세요!</center><br />
	</div>
	<?
}
?>