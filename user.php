<?
include "db_info.php";

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 친구 정보 가져오기
$friends_query = "select * from friends where name='$_COOKIE[user]' and identify='2'";
$friends_result = mysql_query($friends_query, $connect);

// 스티커 이미지 가져오기
$sticker_query = "select * from user_sticker where name='$_COOKIE[user]'";
$sticker_result = mysql_query($sticker_query, $connect);
$sticker_row = mysql_fetch_array($sticker_result);

// 스티커북 총 갯수
$stickerbook_total_query = "select count(*) from board_sticker where writer='$_COOKIE[user]'";
$stickerbook_total_result = mysql_query($stickerbook_total_query, $connect);
$stickerbook_total_row = mysql_fetch_row($stickerbook_total_result);

// 댓글 업데이트 목록 표시
$board_comment_update_query = "select * from board where commentupdate='1' and name='$_COOKIE[user]'";
$board_comment_update_result = mysql_query($board_comment_update_query, $connect);

// 댓글 업데이트 목록 갯수
$board_comment_update_count_query = "select count(*) from board where commentupdate='1' and name='$_COOKIE[user]'";
$board_comment_update_count_result = mysql_query($board_comment_update_count_query, $connect);
$board_comment_update_count_row = mysql_fetch_row($board_comment_update_count_result);

// 스티커 업데이트 목록 표시
$board_sticker_update_query = "select * from board where stickerupdate='1' and name='$_COOKIE[user]'";
$board_sticker_update_result = mysql_query($board_sticker_update_query, $connect);

// 스티커 업데이트 목록 갯수
$board_sticker_update_count_query = "select count(*) from board where stickerupdate='1' and name='$_COOKIE[user]'";
$board_sticker_update_count_result = mysql_query($board_sticker_update_count_query, $connect);
$board_sticker_update_count_row = mysql_fetch_row($board_sticker_update_count_result);

// 친구 업데이트 목록 표시
$board_friends_update_query = "select * from friends where mate='$_COOKIE[user]' and identify='1'";
$board_friends_update_result = mysql_query($board_friends_update_query, $connect);

// 친구 업데이트 목록 갯수
$board_friends_update_count_query = "select count(*) from friends where mate='$_COOKIE[user]' and identify='1'";
$board_friends_update_count_result = mysql_query($board_friends_update_count_query, $connect);
$board_friends_update_count_row = mysql_fetch_row($board_friends_update_count_result);

// 프랜드 톡 업데이트 목록 표시
$board_friendtalk_update_query = "select * from talkupdate where talkup='1' and name='$_COOKIE[user]'";
$board_friendtalk_update_result = mysql_query($board_friendtalk_update_query, $connect);

// 프랜드 톡 업데이트 목록 갯수
$board_friendtalk_update_count_query = "select count(*) from talkupdate where talkup='1' and name='$_COOKIE[user]'";
$board_friendtalk_update_count_result = mysql_query($board_friendtalk_update_count_query, $connect);
$board_friendtalk_update_count_row = mysql_fetch_row($board_friendtalk_update_count_result);

// 답글 업데이트 목록 표시
$board_commentupdate_query = "select * from comment where reply='1' and name='$_COOKIE[user]'";
$board_commentupdate_result = mysql_query($board_commentupdate_query, $connect);

// 답글 업데이트 목록 갯수
$board_commentupdate_count_query = "select count(*) from comment where reply='1' and name='$_COOKIE[user]'";
$board_commentupdate_count_result = mysql_query($board_commentupdate_count_query, $connect);
$board_commentupdate_count_row = mysql_fetch_row($board_commentupdate_count_result);

// My Talk 업데이트 목록 표시
$mytalk_update_query = "select * from friends where name='$_COOKIE[user]' and talkup='1' and identify='2'";
$mytalk_update_result = mysql_query($mytalk_update_query, $connect);

// My Talk 업데이트 목록 갯수
$mytalk_count_update_query = "select count(*) from friends where name='$_COOKIE[user]' and talkup='1' and identify='2'";
$mytalk_count_update_result = mysql_query($mytalk_count_update_query, $connect);
$mytalk_count_update_row = mysql_fetch_row($mytalk_count_update_result);

// My Talk 주소
$mytalk_me_query = "select * from board where name='$_COOKIE[user]' and messenger='1'";
$mytalk_me_result = mysql_query($mytalk_me_query, $connect);
$mytalk_me_row = mysql_fetch_array($mytalk_me_result);

// 친구 표시 갯수
$friens_count_query = "select count(*) from friends where name='$_COOKIE[user]' and identify='2'";
$friends_count_result = mysql_query($friens_count_query, $connect);
$friends_count_total = mysql_fetch_row($friends_count_result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> WinyPle ! :: User </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<link href="global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
//<![CDATA[

// 유닉스 타임
fetch_unix_timestamp = function(){
		return parseInt(new Date().getTime().toString().substring(0, 10))
}
function login_check(type){
	if (type == "id" && document.login_form.userid.value == "아이디") document.login_form.userid.value = "";
	else if (type == "password" && document.login_form.userpasswd.value == "비밀번호") document.login_form.userpasswd.value = "";
	else if (type == "id_return" && document.login_form.userid.value == "") document.login_form.userid.value = "아이디";
	else if (type == "password_return" && document.login_form.userpasswd.value == "") document.login_form.userpasswd.value = "비밀번호";
}
function page_click(){
		$("#wrap").hide();
		$("#loading").show();
}
function search_check(){
		if (document.user_search_form.search.value == ""){
				alert("검색어는요?");
				document.user_search_form.search.focus();
				return false;
		}
}
function logout_submit(){
	var type = "logout";
	$.ajax({
		type: "POST",
		data: "type=" +type,
		url: "login_insert.php",
		success: function(logout_result){
			if (logout_result == "logout_fail"){
				alert("로그아웃에 실패했어요. 왜 그런지 모르겠네요!");
				$(document.location = "index.php");
			}
			else{
				$(document.location = "index.php");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function login_submit(){
	var userid = $("#userid").val();
	var userpasswd = $("#userpasswd").val();
	$.ajax({
		type: "POST",
		data: "userid=" +userid+ "&userpasswd=" +userpasswd,
		url: "login_insert.php",
		success: function(login_result){
			if (login_result == "login_fail"){
				alert("아이디 또는 비밀번호가 잘못되었네요!");
			}
			else{
				return view_setting('friends');
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function friends_add(friend){
	var friends = friend;
	var type= "friends_query"
	$.ajax({
		type: "POST",
		data: "friends=" +friends+ "&type=" +type,
		url: "friends_insert.php",
		success: function(friends_result){
			if (friends_result == "login_fail"){
				alert("로그인 해주셔야죠!");
			}
			else if (friends_result == "friends_fail_1"){
				alert("150명 까지만 가능해요!");
			}
			else if (friends_result == "friends_fail_2"){
				alert("자신에게 친구추가 하시려구요? ^^");
			}
			else if (friends_result == "friends_fail_3"){
				alert("친구요청을 하셨거나 친구등록이 되어있네요~ 확인해보세요.");
			}
			else if (friends_result == "friends_fail_4"){
				alert(friend+"님이 이미 친구요청을 하셨네요! 빨리 수락하세요!");
			}
			else{
				alert(friend+"님에게 친구요청을 보냈어요!");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
}
function friends_accept_query(friend){
	var friends = friend;
	var type= "friends_accept_query"
	$.ajax({
		type: "POST",
		data: "friends=" +friends+ "&type=" +type,
		url: "friends_insert.php",
		success: function(friends_result){
			if (friends_result == "friends_accept_query_success"){
				alert(friend+ "님과 친구가 되었어요!");
				location.reload();
			}
			else{
				alert("친구 수락에 실패했어요! 이놈의 오류!");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
}
function board_load(page, friends_total, stickerbook_total){
	var timestamp = fetch_unix_timestamp();
	var friends_count = "10";
	var friends_view = parseInt(page) + parseInt(friends_count);
	var stickerbook_count = "16";
	var stickerbook_view = parseInt(page) + parseInt(stickerbook_count);
	$("#user_myfriend_box").load("friends.php", "page="+page+"&view_time="+timestamp)
	$("#user_stickerbook_box").load("stickerbook.php", "page="+page+"&view_time="+timestamp)
	if (friends_view < friends_total){
		$("#user_myfriend_view_box").html("<span><a href='#' onclick='friends_load("+friends_view+", "+friends_total+"); return false;'>더보기</a></span>");
	}
	else{
		$("#user_myfriend_view_box").html("<font color='gray'>더보기</font>");
	}
	if (stickerbook_view < stickerbook_total){
		$("#user_stickerbook_view_box").html("<span><a href='#' onclick='stickerbook_load("+stickerbook_view+", "+stickerbook_total+"); return false;'>더보기</a></span>");
	}
	else{
		$("#user_stickerbook_view_box").html("<font color='gray'>더보기</font>");
	}
}
function friends_load(page, total){
	var timestamp = fetch_unix_timestamp();
	var count = "10";
	var view = parseInt(page) + parseInt(count);
	if (page == "0"){
		$("#user_myfriend_box").load("friends.php", "page="+page+"&view_time="+timestamp);
	}
	else{
		$("#user_myfriend_box").append($("<div>").load("friends.php", "page="+page+"&view_time="+timestamp));
	}
	if (view < total){
		$("#user_myfriend_view_box").html("<span><a href='#' onclick='friends_load("+view+", "+total+"); return false;'>더보기</a></span>");
	}
	else{
		$("#user_myfriend_view_box").html("<font color='gray'>더보기</font>");
	}
}
function stickerbook_load(page, total){
	var timestamp = fetch_unix_timestamp();
	var count = "16";
	var view = parseInt(page) + parseInt(count);
	if (page == "0"){
		$("#user_stickerbook_box").load("stickerbook.php", "page="+page+"&view_time="+timestamp)
	}
	else{
		$("#user_stickerbook_box").append($("<div>").load("stickerbook.php", "page="+page+"&view_time="+timestamp))
	}
	if (view < total){
		$("#user_stickerbook_view_box").html("<span><a href='#' onclick='stickerbook_load("+view+", "+total+"); return false;'>더보기</a></span>");
	}
	else{
		$("#user_stickerbook_view_box").html("<font color='gray'>더보기</font>");
	}
}
function view_setting(setting){
	var view_setting = setting;
	$.ajax({
		type: "POST",
		data: "view_setting=" +view_setting,
		url: "index.php",
		success: function(setting_result){
			if (setting_result == "success"){
				location.reload();
			}
			else{
				alert("관심게시물을 불러오던 중 오류가 났어요!");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function sticker_query(type){
	$("#name_insert_form, #name_insert_cancel").hide();
	$("#name_insert_image, #user_sticker_upload").show();
	$("input[name=type]").val(type);
}
function form_cancel(){
	var cancel_value = confirm("입력을 취소 할까요?");
	if (cancel_value == true){
		$(document.location = "index.php");
	}
}
function contents_upload(){
	alert("어떤 자료를 올리실지 궁금하네요!");
	$(document.location = "upload.php");
}
$(document).ready(function(){
	$("#main_logo").mouseover(function(){
		$("#logo").attr("src", "images/logo_jquery.png");
	});
	$("#main_logo").mouseout(function(){
		$("#logo").attr("src", "images/logo.png");
	});
	$("input[name=type]").val("default");
	$("#name_insert_image").click(function(){
		$("#name_insert_image, #user_sticker_upload").hide();
		$("#name_insert_form, #name_insert_cancel").show();
		$("input[name=type]").val("name");
	})
	$("#name_insert_cancel").click(function(){
		$("#name_insert_form, #name_insert_cancel, #user_sticker_upload").hide();
		$("#name_insert_image").show();
		$("input[name=type]").val("default");
	})
	$("#upload_cancel").click(function(){
		$("#name_insert_form, #name_insert_cancel, #user_sticker_upload").hide();
		$("#name_insert_image").show();
		$("input[name=type]").val("default");
	})
});
//]]>
</script>
</head>

<body onload="board_load('0', '<?=$friends_count_total[0]?>', '<?=$stickerbook_total_row[0]?>')">
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-37635213-1']);
_gaq.push(['_trackPageview']);

(function(){
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<div id="header_background"></div>
<?
// 로그인이 되어 있지 않을 때
if (empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
	?>
	<div id="frame_login">
		<form method="post" action="login_insert.php" name="login_form">
		<input type="text" name="userid" value="아이디" id="userid" onfocus="login_check('id')" onblur="login_check('id_return')" class="main_input" />
		<input type="password" name="userpasswd" id="userpasswd" onfocus="login_check('password')" value="비밀번호" onblur="login_check('password_return')" class="main_input" />
		<input type="submit" onclick="return login_submit()" value="로그인" class="main_menu_button" />
		</form>
		<div style="text-align: right; margin-top: 35px; margin-right: 65px;"><a href="member.php">회원이 아니십니까?</a></div>
	</div>
	</body>
	</html>
	<?
	exit;
}
?>
<div id="loading"><img src="images/loading.gif" alt="로딩" border="0" /></div>
<div id="wrap">
	<!-- 위니플 로고 -->
	<div id="main_logo">
		<h1><a href="/"><img src="images/logo.png" alt="위니플" border="0" id="logo" /></a></h1>
	</div>
	<!-- 로그인 -->
	<div id="main_login">
		<div class="login_user">
			<?=$_COOKIE[user]?> 님<a href="user.php"><img src="images/user_modify.png" border="0" align="top" width="16" height="16" alt="설정" /></a>&nbsp;
			<input type="button" value="Upload" class="login_menu_button" onclick="contents_upload(); return false;" />&nbsp;
			<input type="button" value="로그아웃" class="login_menu_button" onclick="logout_submit(); return false;" />
		</div>
	</div>
	<!-- 유저화면 좌 -->
	<div id="user_left">
		<div class="contents_frame_header"><h2>회원정보</h2></div>
		<form action="user_modify.php" method="post" enctype="multipart/form-data">
		<div class="user_modify">
			<img src="images/user_name_notice.jpg" border="0" alt="" />
			<div class="user_name_insert"><a href="#" onclick="return false;"><img src="images/user_name.jpg" id="name_insert_image" border="0" alt="" /></a>
			<input type="text" id="name_insert_form" name="name" class="main_input" style="display: none;" />
			<a href="#" onclick="return false;"><img id="name_insert_cancel" src="images/frame_multi_cancel.jpg" width="12" height="12" border="0" style="display: none;" alt="취소" /></a>
		</div>
		<img src="images/user_sticker.jpg" style="margin-top: 10px;" alt="" /><br />
		<?
		// 스티커 이미지가 등록 되어 있지 않을 때
		if(empty($sticker_row[sticker])){
			$sticker_query = "sticker";
		}
		else{
			$sticker_query = "update";
		}
		?>
		<div id="user_sticker_choose">
			<div class="user_sticker_images">
				<? 
				if(empty($sticker_row[sticker])){
					?>
					<img src="upload/sticker/winyple.jpg" border="0" alt="" />
					<?
				}
				else{
					?>
					<img src="upload/sticker/<?=$sticker_row[sticker]?>" border="0" alt="" />
					<?
				} 
				?>
			</div>
			<div class="user_sticker_contents">
				<? 
				if(empty($sticker_row[sticker])){
					echo "스티커 이미지를 등록해 주세요!";
				}
				else{
					echo "최근 수정일 : $sticker_row[wdate]";
				}
				?>
			</div>
			<div class="user_sticker_modify">
				<? 
				if(empty($sticker_row[sticker])){
					?> 
					<a href="#" onclick="sticker_query('<?=$sticker_query?>'); return false;"><img id="choose_type_image_upload" src="images/user_choose.jpg" border="0" alt="" /></a>
					<?
				} 
				else{
					?> 
					<a href="#" onclick="sticker_query('<?=$sticker_query?>'); return false;"><img id="choose_type_image_upload" src="images/user_sticker_modify.jpg" border="0" alt="" /></a>
					<? 
				} 
				?>
			</div>
		</div>
		<div id="user_sticker_upload" class="user_image_upload_box">
			<div class="user_image_upload">
				<?
				if(empty($sticker_row[sticker])){
					echo "스티커 업로드";
				}
				else{
					echo "스티커 수정";
				}
				?>
			</div>
			<div class="user_image_upload_blank">
			<a href="#" onclick="return false;"><img src="images/frame_multi_cancel.jpg" id="upload_cancel" width="12" height="12" border="0" align="bottom" alt="취소" /></a></div>
			<div class="input_image"><input type="file" name="sticker_file" /></div>
		</div>
		<img src="images/user_update.jpg" border="0" style="margin-top: 10px;" alt="" />
		<!-- 알림 목록 -->
		<div id="user_update_table">
			<?
			// 친구 요청 표시
			while ($board_friends_update_row = mysql_fetch_array($board_friends_update_result)){
				$friends_name_query = "select * from user where name='$board_friends_update_row[name]'";
				$friends_name_result = mysql_query($friends_name_query, $connect);
				$friends_name_row = mysql_fetch_array($friends_name_result);
				if (empty($friends_name_row[original])) $friends_name = "";
				else $friends_name = "({$friends_name_row[original]})";
				?>
				<div class="user_update">
					<font color="green"><?=$board_friends_update_row[name]?><?=$friends_name?></font>님이 친구요청을 하셨습니다.
					<a href="#" onclick="friends_accept_query('<?=$board_friends_update_row[name]?>'); return false;"><img src="images/frame_multi_add.jpg" border="0" width="10" height="10" alt="친구추가" /></a>
				</div>
				<?
			}
			// My Talk 업데이트 내역 표시
			while ($mytalk_update_row = mysql_fetch_array($mytalk_update_result)){
				$mytalk_link_query = "select * from board where name='$mytalk_update_row[mate]' and messenger='1'";
				$mytalk_link_result = mysql_query($mytalk_link_query, $connect);
				$mytalk_link_row = mysql_fetch_array($mytalk_link_result);
				?>
				<div class="user_update">
					<a href="read.php?page=<?=$mytalk_link_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0&amp;check=talk" title="Talk Messenger"><font color="green"><?=$mytalk_update_row[mate]?></font>님이 Talk을 남기셨습니다!</a>
				</div>
				<?
			}
			// 프랜드톡 업데이트 내역 표시
			while ($board_friendtalk_update_row = mysql_fetch_array($board_friendtalk_update_result)){
				$talkupdate_query = "select * from board where id='$board_friendtalk_update_row[bid]'";
				$talkupdate_result = mysql_query($talkupdate_query, $connect);
				$talkupdate_row = mysql_fetch_array($talkupdate_result);
				?>
				<div class="user_update">
					<a href="read.php?page=<?=$board_friendtalk_update_row[bid]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0&amp;check=friendtalk" title="<?=$talkupdate_row[title]?>"><?=$talkupdate_row[title]?></a><br />
					<img src="images/user_point.jpg" border="0" align="top" alt="" />&nbsp;&nbsp;<font color="green">프랜드톡이 달렸습니다!</font>
				</div>
				<?
			}
			// 스티커 업데이트 내역 표시
			while ($board_sticker_update_row = mysql_fetch_array($board_sticker_update_result)){
				$sticker_count_query = "select count(*) from board_sticker where bid ='$board_sticker_update_row[id]'";
				$sticker_count_result = mysql_query($sticker_count_query, $connect);
				$sticker_count_row = mysql_fetch_row($sticker_count_result);
				?>
				<div class="user_update">
					<a href="read.php?page=<?=$board_sticker_update_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0&amp;check=sticker" title="<?=$board_sticker_update_row[title]?>"><?=$board_sticker_update_row[title]?></a><br />
					<img src="images/user_point.jpg" border="0" align="top" alt="" />&nbsp;&nbsp;<font color="red"><?=$sticker_count_row[0]?></font><font color="gray">개의 스티커를 받았습니다.</font>
				</div>
				<?
			}
			// 댓글 업데이트 내역 표시
			while ($board_comment_update_row = mysql_fetch_array($board_comment_update_result)){
				$comment_count_query = "select count(*) from comment where bid ='$board_comment_update_row[id]'";
				$comment_count_result = mysql_query($comment_count_query, $connect);
				$comment_count_row = mysql_fetch_row($comment_count_result);
				?>
				<div class="user_update">
					<a href="read.php?page=<?=$board_comment_update_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0&amp;check=comment" title="<?=$board_comment_update_row[title]?>"><?=$board_comment_update_row[title]?></a><br />
					<img src="images/user_point.jpg" border="0" align="top" alt="" />&nbsp;&nbsp;<font color="red"><?=$comment_count_row[0]?></font><font color="gray">개의 댓글이 달렸습니다.</font>
				</div>
				<?
			}
			// 답글 업데이트 내역 표시
			while ($board_commentupdate_row = mysql_fetch_array($board_commentupdate_result)){
				$comment_contents = mb_strimwidth(strip_tags($board_commentupdate_row[comment]), 0, 50, '..', 'euckr');
				$comment_board_name_query = "select * from board where id ='$board_commentupdate_row[bid]'";
				$comment_board_name_result = mysql_query($comment_board_name_query, $connect);
				$comment_board_name_row = mysql_fetch_array($comment_board_name_result);
				?>
				<div class="user_update">
					<a href="read.php?page=<?=$board_commentupdate_row[bid]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0&amp;check=reply" title="<?=$comment_board_name_row[title]?>"><?=$comment_board_name_row[title]?></a><br />
					<img src="images/user_point.jpg" border="0" align="top" alt="" />&nbsp;&nbsp;<?=$comment_contents?>&nbsp;<font color="gray">답글이 달렸습니다.</font>
				</div>
				<?
			}
			// 결과가 없을 때
			if ($board_comment_update_count_row[0] == "0" && $board_sticker_update_count_row[0] == "0" && $board_friends_update_count_row[0] == "0" && $board_friendtalk_update_count_row[0] == "0" && $board_commentupdate_count_row[0] == "0" && $mytalk_count_update_row[0] == "0"){
				?>
				<div class="user_update">알림이 없습니다.</div>
				<?
			}
			?>
			<!-- 스티커 북 -->
			<div id="user_stickerbook_box"></div>
			<div id="user_stickerbook_view_box"></div>
		</div>
		<input type="hidden" name="modify_check" value="error" />
		<input type="hidden" name="type" />
		</div>
		<input type="button" class="frame_button" value="취소" onclick="form_cancel(); return false;" /><input type="submit" onclick="page_click()" class="frame_button" value="확인" style="margin-right: 15px;" />
		</form>
	</div>
	<!-- 유저화면 우 -->
	<div id="user_right">
		<div class="widget_frame_header"><h2>메뉴</h2></div>
		<div class="user_menu">
			<font color="gray">※ 댓글, 스티커, 프랜드톡, 친구소식을<br />
			&nbsp;&nbsp;&nbsp;알림에서 확인할 수 있습니다.<br /><br />
			※ 친구가 올린 게시물에 프랜드톡을<br />
			&nbsp;&nbsp;&nbsp;남겨보세요!<br /><br />
			※ 지금 내 생각을 Talk에 남기세요!</font><br /><br />
			<a href="search.php?no=0&amp;type=name&amp;search=<?=$_COOKIE[user]?>"><img src="images/upload_search_me.jpg" border="0" alt="검색" /></a>&nbsp;
			<a href="read.php?page=<?=$mytalk_me_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0"><img src="images/talk_messenger_me.jpg" border="0" alt="메신저" /></a>
		</div>
		<!-- 이름 검색 -->
		<div id="user_name_search_box">
			<div id="search_form" style="text-align: right; height: 28px;">
				<form name="user_search_form" method="get" action="user.php">
				<input type="text" class="main_input" name="search" /><input type="submit" value="Search" class="user_search_botton" onclick="return search_check()" />
				</form>
			</div>
			<?
			// 검색 결과 표시
			if (!empty($_GET[search])){
				// 아이디 검색 결과
				$id_search_query = "select * from user where name like '%$_GET[search]%' and identify='1'";
				$id_search_result = mysql_query($id_search_query, $connect);

				// 검색 결과 갯수
				$id_search_count_query = "select count(*) from user where name like '%$_GET[search]%' and identify='1'";
				$id_search_count_result = mysql_query($id_search_count_query, $connect);
				$id_search_count_total = mysql_fetch_row($id_search_count_result);

				// 이름 검색 결과
				$name_search_query = "select * from user where original like '%$_GET[search]%' and identify='1'";
				$name_search_result = mysql_query($name_search_query, $connect);

				// 이름 검색 결과 갯수
				$name_search_count_query = "select count(*) from user where original like '%$_GET[search]%' and identify='1'";
				$name_search_count_result = mysql_query($name_search_count_query, $connect);
				$name_search_count_total = mysql_fetch_row($name_search_count_result);

				// 아이디로 검색 되었을 때
				while ($id_search_row = mysql_fetch_array($id_search_result)){
					if (!empty($id_search_row[original])) $user_name = "({$id_search_row[original]})";
					else $user_name = "";
					?>
					<div class="user_name_search">
						<font color="green"><?=$id_search_row[name]?></font><?=$user_name?>&nbsp;<a href="search.php?no=0&amp;type=name&amp;search=<?=$id_search_row[name]?>"><img src="images/user_file_search.jpg" border="0" width="10" height="10" alt="검색" /></a>
						<a href="#" onclick="friends_add('<?=$id_search_row[name]?>'); return false;"><img src="images/frame_multi_add.jpg" border="0" width="10" height="10" alt="친구추가" /></a>
					</div>
					<?
				}
				// 이름으로 검색 되었을 때
				while ($name_search_row = mysql_fetch_array($name_search_result)){
					?>
					<div class="user_name_search"><?=$name_search_row[name]?>(<font color="green"><?=$name_search_row[original]?></font>)&nbsp;<a href="search.php?no=0&amp;type=name&amp;search=<?=$name_search_row[name]?>"><img src="images/user_file_search.jpg" border="0" width="10" height="10" alt="검색" /></a>
					<a href="#" onclick="friends_add('<?=$name_search_row[name]?>'); return false;"><img src="images/frame_multi_add.jpg" border="0" width="10" height="10" alt="친구추가" /></a></div>
					<?
				}
				// 결과가 없을 때
				if ($id_search_count_total[0] == "0" && $name_search_count_total[0] == "0"){
					?>
					<div class="user_name_search">검색된 결과가 없습니다.</div>
					<?
				}
			}
			?>
		</div>
		<!-- 내 친구 -->
		<div class="widget_frame_header">
			<h2>내 친구
			<?
			$friends_add_count = 150 - $friends_count_total[0];
			echo "+".$friends_add_count;
			?>
			</h2>
		</div>
		<div id="user_myfriend_box"></div>
		<div id="user_myfriend_view_box"></div>
	</div>
	<!-- 메인화면 footer -->
		<div id="footer">
		<img src="images/logo.png" border="0" align="middle" width="150" height="50" alt="위니플" /><br />
				<a href="author.php">제작자 &amp; 가이드</a>&nbsp;|&nbsp;<font color="#626262">&copy; WinyPle ALL RIGHTS RESERVED.</font>
	</div>
</div>
</body>
</html>