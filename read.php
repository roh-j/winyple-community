<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_GET[page]) && empty($_POST[contents_type])){
		echo "<script type='text/javascript' language='javascript'>
		window.alert('잘못된 접근입니다.');
		location.replace('index.php');
	</script>";
		exit;
}
// 컨텐츠 내용 업데이트
if (!empty($_POST[contents_type])){
	// 게시물 정보
	$contents_read_query = "select * from board where id='$_POST[board_id]'";
	$contents_read_result = mysql_query($contents_read_query, $connect);
	$contents_read_row = mysql_fetch_array($contents_read_result);

	// 수정 처리
	$contents_update = iconv("UTF-8", "CP949", rawurldecode($_POST[contents_update]));
	$contents_update_query = "update board set contents='$contents_update' where id='$_POST[board_id]'";
	$contnets_update_result = mysql_query($contents_update_query, $connect);
	echo "success";
	exit;
}
// 새로고침시 조회수 방지
if ($_COOKIE[view] != md5($_GET[page])){
	setcookie("view", md5($_GET[page]), 0, "/");
		$read_update_query = "update board set view = view+1 where id='$_GET[page]'";
		$read_update_result = mysql_query($read_update_query, $connect);
}
// 초기 값
if (empty($_GET[no]) || $_GET[no] < "0" ){
	$_GET[no] = "0";
}
if (empty($_GET[sticker]) || $_GET[sticker] < "0" ){
	$_GET[sticker] = "0";
}
if (empty($_GET[friendtalk]) || $_GET[friendtalk] < "0" ){
	$_GET[friendtalk] = "0";
}
// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 어드민 정보 가져오기
$admin_info_query = "select * from user where name='admin'";
$admin_info_result = mysql_query($admin_info_query, $connect);
$admin_info_row = mysql_fetch_array($admin_info_result);

// 게시물 검색
$read_query = "select * from board where id='$_GET[page]'";
$read_result = mysql_query($read_query, $connect);
$read_row = mysql_fetch_array($read_result);

// 게시물 작성자 권한 확인
$read_writer_info_query = "select * from user where name='$read_row[name]'";
$read_writer_info_result = mysql_query($read_writer_info_query, $connect);
$read_writer_info_row = mysql_fetch_array($read_writer_info_result);

// 글이 존재하지 않을 경우
if(empty($read_row[title])){
		echo "<script type='text/javascript' language='javascript'>
		window.alert('글이 존재하지 않아요! 메인에서 멋진 게시물을 찾아보세요!');
		location.replace('index.php');
	</script>";
		exit;
}
// 배경이미지 확인 배열
$custom_check_array = array("default.jpg", "mint.jpg", "love.jpg", "gray.jpg");

// 게시물 기분 분석
if ($_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user]) && !empty($user_info_row[today_feel]) && $_COOKIE[feel] != md5($_GET[page])){
	setcookie("feel", md5($_GET[page]), 0, "/");
	// 현재 날짜
	$today = getdate();
	$today_value = $today[year] + $today[mon] + $today[mday];

	// 날짜가 다를 경우
	if ($read_row[feel_day] != $today_value){
		$feel_today_reset_query = "update board set feel_good='0', feel_soso='0', feel_bad='0', feel_view='0', feel_day='', feel_type='' where id='$_GET[page]'";
		$feel_today_reset_result = mysql_query($feel_today_reset_query, $connect);
	}
	// 유저의 오늘 기분 & 날짜
	$feel_type = $user_info_row[today_feel];
	$feel_board_query = "update board set $feel_type=$feel_type+1, feel_day=$today_value where id='$_GET[page]'";
	$feel_board_result = mysql_query($feel_board_query);

	$feel_type_query = "select greatest(feel_good, feel_soso, feel_bad) from board where id='$_GET[page]'";
	$feel_type_reuslt = mysql_query($feel_type_query);
	$feel_type_row = mysql_fetch_array($feel_type_reuslt);
	$feel_type = $feel_type_row['greatest(feel_good, feel_soso, feel_bad)'];

	// 기분 최대값
	$feel_view_query = "update board set feel_view='$feel_type' where id='$_GET[page]'";
	$feel_view_result = mysql_query($feel_view_query, $connect);

	// 게시물 기분 검색
	$read_feel_query = "select * from board where id='$_GET[page]'";
	$read_feel_result = mysql_query($read_feel_query, $connect);
	$read_feel_row = mysql_fetch_array($read_feel_result);

	// 기분 비교
	if ($read_feel_row[feel_good] == $feel_type){
		$feel_board_update_query = "update board set feel_type='feel_good' where id='$_GET[page]'";
		$feel_board_update_result = mysql_query($feel_board_update_query, $connect);
	}
	else if ($read_feel_row[feel_soso] == $feel_type){
		$feel_board_update_query = "update board set feel_type='feel_soso' where id='$_GET[page]'";
		$feel_board_update_result = mysql_query($feel_board_update_query, $connect);
	}
	else{
		$feel_board_update_query = "update board set feel_type='feel_bad' where id='$_GET[page]'";
		$feel_board_update_result = mysql_query($feel_board_update_query, $connect);
	}
}
// 스티커 이미지 검색
$sticker_image_query = "select * from user_sticker where name='$read_row[name]'";
$sticker_image_result = mysql_query($sticker_image_query, $connect);
$sticker_image_row = mysql_fetch_array($sticker_image_result);

// 댓글 수 검색
$page_comment_count = mysql_query("select count(*) from comment where bid='$_GET[page]'", $connect);
$page_comment_row = mysql_fetch_row($page_comment_count);
$page_total_comment = $page_comment_row[0];

// 스티커 총 갯수
$sticker_total_query = "select count(*) from board_sticker where bid='$_GET[page]'";
$sticker_total_result = mysql_query($sticker_total_query, $connect);
$sticker_total_page = mysql_fetch_row($sticker_total_result);

// 베스트 게시물 집계
$best_board_view = ($read_row[view] * 0.3)+($page_total_comment * 0.3)+($sticker_total_page[0] * 0.4);
$best_board_query = "update board set total='$best_board_view' where id='$_GET[page]'";
$best_board_result = mysql_query($best_board_query, $connect);

// 친구 권한 확인
$friend_query = "select count(*) from friends where name='$_COOKIE[user]' and mate='$read_row[name]' and identify='2'";
$friend_result = mysql_query($friend_query, $connect);
$friend_row = mysql_fetch_row($friend_result);

// Talk Messenger 보안
if ($read_row[messenger] == "1" && $friend_row[0] == "0" || $read_row[messenger] == "1" && empty($_COOKIE[user])){
		// 본인일 경우
		if ($_COOKIE[user] != $read_row[name]){
		// 관리자가 아닐 경우
				if ($_COOKIE[user] != "admin" || $_COOKIE[sid] != $admin_info_row[passwd]){
						echo "<script type='text/javascript' language='javascript'>
						window.alert('Talk Messenger는 친구만 볼 수 있다구요!');
						location.replace('index.php');
			</script>";
						exit;
				}
		}
}
// comment 알림 목록 삭제
if ($read_row[name] == $_COOKIE[user] && $_GET[check] == "comment"){
		$board_comment_update_query = "update board set commentupdate='0' where id='$_GET[page]'";
		$board_comment_update_result = mysql_query($board_comment_update_query, $connect);
}
// sticker 알림 목록 삭제
if ($read_row[name] == $_COOKIE[user] && $_GET[check] == "sticker"){
		$board_sticker_update_query = "update board set stickerupdate='0' where id='$_GET[page]'";
		$board_sticker_update_result = mysql_query($board_sticker_update_query, $connect);
}
// friendtalk 알림 목록 삭제
if ($_GET[check] == "friendtalk"){
	$board_friendtalk_update_query = "update talkupdate set talkup='0' where bid='$_GET[page]' and name='$_COOKIE[user]'";
		$board_friendtalk_update_result = mysql_query($board_friendtalk_update_query, $connect);
}
// reply 알림 목록 삭제
if ($_GET[check] == "reply"){
	$board_commentupdate_query = "update comment set reply='0' where bid='$_GET[page]' and name='$_COOKIE[user]'";
		$board_commentupdate_result = mysql_query($board_commentupdate_query, $connect);
}
// My Talk 알림 목록 삭제
if ($_GET[check] == "talk" && $read_row[messenger] == "1"){
		$mytalk_update_query = "update friends set talkup='0' where name='$_COOKIE[user]'";
		$mytalk_update_result = mysql_query($mytalk_update_query);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> <?=$read_row[title]?> - WinyPle ! </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<link href="global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
//<![CDATA[
var board_type = "<?=$read_row[type]?>";
var artist_presence = "<?=$read_row[artist]?>";
var view_user = "<?=$_COOKIE[user]?>";
var board_writer = "<?=$read_row[name]?>";
var friend_confirm = "<?=$friend_row[0]?>";
var talk_type= "<?=$read_row[messenger]?>";
var writer_ad_info = "<?=$read_writer_info_row[advertise]?>";

fetch_unix_timestamp = function(){
		return parseInt(new Date().getTime().toString().substring(0, 10))
}
function login_check(type){
	if (type == "id" && document.login_form.userid.value == "아이디") document.login_form.userid.value = "";
	else if (type == "password" && document.login_form.userpasswd.value == "비밀번호") document.login_form.userpasswd.value = "";
	else if (type == "id_return" && document.login_form.userid.value == "") document.login_form.userid.value = "아이디";
	else if (type == "password_return" && document.login_form.userpasswd.value == "") document.login_form.userpasswd.value = "비밀번호";
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
function friends_add(friend){
	var friends = friend;
	var type = "friends_query";
	$.ajax({
		type: "POST",
		data: "friends=" +friends+ "&type=" +type,
		url: "friends_insert.php",
		success: function(friends_result){
			if (friends_result == "login_fail"){
				alert("로그인이 필요하답니다.");
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
function board_load(page, no, sticker, friendtalk){
	var timestamp = fetch_unix_timestamp();
	$("#read_comment").load("comment.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
	$("#read_sticker").load("sticker.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
	$("#read_friendtalk").load("friendtalk.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
}
function sticker_image_submit(board_id, sticker, no, friendtalk){
	var timestamp = fetch_unix_timestamp();
	var sticker_check = $("#sticker_check").val();
	$.ajax({
		type: "POST",
		data: "board_id=" +board_id+ "&sticker_check=" +sticker_check,
		url: "sticker_insert.php",
		success: function(sticker_result){
			if (sticker_result == "sticker_insert_error_1"){
				alert("자신이 작성한 게시물에는 스티커를 주실 수 없어요!");
			}
			else if (sticker_result == "sticker_insert_error_2"){
				alert("이미 스티커를 주셨다고 되어있네요~");
			}
			else{
				$("#read_sticker").load("sticker.php", "page="+board_id+"&sticker="+sticker+"&no="+no+"&friendtalk="+friendtalk+"&view_time="+timestamp);
				$("#message_pop").slideDown(1500).text("멋진 스티커네요! 받은 사람도 좋아할 거예요!").delay(2000).slideUp(1500);
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function friendtalk_submit(board_id, friendtalk, no, sticker){
	var timestamp = fetch_unix_timestamp();
	var friend_comment = encodeURIComponent($("#friend_comment").val());
	var friendtalk_check = $("#friendtalk_check").val();
	$.ajax({
		type: "POST",
		data: "board_id=" +board_id+ "&friend_comment=" +escape(friend_comment)+ "&friendtalk_check=" +friendtalk_check,
		url: "friendtalk_insert.php",
		success: function(friendtalk_result){
			if (friendtalk_result == "friendtalk_empty_error"){
				alert("내용은 입력하셔야죠!");
				document.friendtalk_form.friend_comment.focus();
			}
			else if (friendtalk_result == "friendtalk_count_error"){
				alert("1000 Byte 이하로 입력해 주셔야 되요!");
			}
			else{
				$("#read_friendtalk").load("friendtalk.php", "page="+board_id+"&friendtalk="+friendtalk+"&no="+no+"&sticker="+sticker+"&view_time="+timestamp);
				$("#message_pop").slideDown(1500).text("프랜드톡을 남겼어요! 친구도 좋아하겠는데요?").delay(2000).slideUp(1500);
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function reply_value(reply, reply_id, user){
	if (user == ""){
		alert("로그인하시고 답글 남겨보세요!");
	}
	$("input[name=reply]").val(reply);
	$("input[name=reply_id]").val(reply_id);
	$("#comment").val("@"+reply+" ");
}
function comment_submit(board_id, no, sticker, friendtalk){
	var timestamp = fetch_unix_timestamp();
	var comment = encodeURIComponent($("#comment").val());
	var comment_check = $("#comment_check").val();
	var reply = encodeURIComponent($("#reply").val());
		var reply_id = $("#reply_id").val();
	if ($("#reply").val() == "comment"){
		$.ajax({
			type: "POST",
			data: "board_id=" +board_id+ "&comment=" +escape(comment)+ "&comment_check=" +comment_check,
			url: "comment_insert.php",
			success: function(comment_result){
				if (comment_result == "comment_empty_error"){
					alert("내용입력하신 거 맞아요?");
					document.comment_form.comment.focus();
				}
				else if (comment_result == "mytalk_insert_error"){
					alert("Talk은 본인만 작성할 수 있다구요!");
				}
				else if (comment_result == "mytalk_count_error"){
					alert("My Talk을 1000 Byte 이하로 입력해주세요~");
				}
				else if (comment_result == "comment_count_error"){
					alert("1000 Byte 이하로 입력해주셔야죠! 너무 길어요.");
				}
				else{
					$("#read_comment").load("comment.php", "page="+board_id+"&no="+no+"&friendtalk="+friendtalk+"&sticker="+sticker+"&view_time="+timestamp);
					if (comment_result == "0"){
						$("#message_pop").slideDown(1500).text("댓글을 남겼어요! 빨리 "+board_writer+"님에게 알려줄께요!").delay(2000).slideUp(1500);
					}
					else{
						$("#message_pop").slideDown(1500).text("마이톡을 남겼어요. 친구도 분명 궁금할거예요!").delay(2000).slideUp(1500);
					}
				}
			},
			error: function(code, message, error){
				alert(code.status + " "+ message +" "+error);
			}
		})
	}
	else{
		$.ajax({
			type: "POST",
			data: "board_id=" +board_id+ "&comment=" +escape(comment)+ "&comment_check=" +comment_check+ "&reply=" +reply+ "&reply_id=" +reply_id,
			url: "comment_insert.php",
			success: function(reply_result){
				if (reply_result == "comment_empty_error"){
					alert("내용은요?");
					document.comment_form.comment.focus();
				}
				else if (reply_result == "comment_count_error"){
					alert("1000 Byte 이하로!");
				}
				else{
					$("#read_comment").load("comment.php", "page="+board_id+"&no="+no+"&friendtalk="+friendtalk+"&sticker="+sticker+"&view_time="+timestamp);
					$("#message_pop").slideDown(1500).text("답글을 저장했어요!").delay(2000).slideUp(1500);
				}
			},
			error: function(code, message, error){
				alert(code.status + " "+ message +" "+error);
			}
		})
	}
	return false;
}
function login_submit(page, no, sticker, friendtalk){
	var timestamp = fetch_unix_timestamp();
	var userid = $("#userid").val();
	var userpasswd = $("#userpasswd").val();
	var contents_writer = "<?=$read_row[name]?>";
	$.ajax({
		type: "POST",
		data: "userid=" +userid+ "&userpasswd=" +userpasswd,
		url: "login_insert.php",
		success: function(login_result){
			if (login_result == "login_fail"){
				alert("아이디 또는 비밀번호를 확인해 보세요! 잘못되었다고 나오네요.");
			}
			else{
				$("#main_login").html("<div class='login_user'>"+login_result+"&nbsp;님<a href='user.php'><img src='images/user_modify.png' border='0' align='top' width='16' height='16' alt='설정' /></a>&nbsp;&nbsp;<input type='button' value='Upload' class='login_menu_button' onclick='contents_upload(); return false;' />&nbsp;&nbsp;<input type='button' value='로그아웃' class='login_menu_button' onclick='logout_submit(); return false;' /></div>");
				$("#read_comment").load("comment.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
				$("#read_sticker").load("sticker.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
				$("#read_friendtalk").load("friendtalk.php", "page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk+"&view_time="+timestamp);
				$("#message_pop").slideDown(1500).text(login_result+"님 반가워요!").delay(2000).slideUp(1500);
				if (contents_writer == login_result){
					$("#contents_update").html("<a href='#' onclick='return board_contents_update()'>수정</a>");
				}
				return view_setting('friends');
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function view_setting(setting){
	var view_setting = setting;
	$.ajax({
		type: "POST",
		data: "view_setting=" +view_setting,
		url: "index.php",
		success: function(setting_result){
			if (setting_result != "success"){
				alert("관심게시물을 불러오던 중 오류가 났어요!");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function board_contents_update(){
	var board_id = "<?=$_GET[page]?>";
	$(".read_contents").html("<textarea name='contents_area' id='contents_area' rows='' cols='' class='board_update_area'></textarea>");
	$("#contents_update").html("<a href='#' onclick='return contents_update_submit("+board_id+")'>수정완료</a>");
	return false;
}
function contents_update_submit(board_id){
	var contents_update = encodeURIComponent($("#contents_area").val());
	if (contents_update == ""){
		var contents_confirm = confirm("내용없이 수정해도 될까요?");
		if (contents_confirm == true){
			return contents_update_query(board_id);
		}
		else{
			location.reload();
		}
	}
	else{
		return contents_update_query(board_id);
	}
	return false;
}
function contents_update_query(board_id){
	var contents_type = "contents_update";
	var contents_update = encodeURIComponent($("#contents_area").val());
	$.ajax({
		type: "POST",
		data: "contents_type=" +contents_type+ "&contents_update=" +escape(contents_update)+ "&board_id=" +board_id,
		url: "read.php",
		success: function(contents_result){
			if (contents_result == "success"){
				location.reload();
			}
			else{
				alert("내용 수정중 오류가 났네요!");
				location.reload();
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function message_random(){
	var message_count = Math.floor((Math.random()*6) + 1);
	message_type(message_count);
}
function message_type(message_count){
	if (message_count == "1"){
		if (artist_presence != "" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*3) + 1);
			if (message_no == "1"){
				var message_value = "님의 멋진 작품입니다!";
			}
			else if (message_no == "2"){
				var message_value = "님이 왜 인기가 많은지 알겠어요!";
			}
			else if (message_no == "3"){
				var message_value = "님의 인기가 하늘을 찌르는군요!";
			}
			$("#message_pop").slideDown(1500).text(artist_presence+message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
	else if (message_count == "2"){
		if (board_type == "video" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*2) + 1);
			if (message_no == "1"){
				var message_value = "어떤 영상이길래 인기가 많죠?";
			}
			else if (message_no == "2"){
				var message_value = "멋진 영상인데요?";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
	else if (message_count == "3"){
		if (board_type == "image" && talk_type == "0" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*3) + 1);
			if (message_no == "1"){
				var message_value = "역시 위니플에 올린 이미지는 다르군요!";
			}
			else if (message_no == "2"){
				var message_value = "인상적이예요!";
			}
			else if (message_no == "3"){
				var message_value = "이미지가 예술인데요?";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
	else if (message_count == "4"){
		if (view_user != "" && friend_confirm == "1" && talk_type == "0" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*4) + 1);
			if (message_no == "1"){
				var message_value = "친구가 올린 게시물이군요! 프랜드톡 남겨보시는 것은 어떠세요?";
			}
			else if (message_no == "2"){
				var message_value = "친구가 올린 게시물이 궁금하셨군요?";
			}
			else if (message_no == "3"){
				var message_value = "친구가 관심있어 하는 정보네요!";
			}
			else if (message_no == "4"){
				var message_value = board_writer+"님과 친하시나봐요!";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
	else if (message_count == "5"){
		if (view_user != "" && friend_confirm == "1" && talk_type == "1" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*2) + 1);
			if (message_no == "1"){
				var message_value = board_writer+"님이 남긴 톡이예요!";
			}
			else if (message_no == "2"){
				var message_value = board_writer+"님의 지금 생각을 확인해보세요!";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else if (view_user == board_writer && talk_type == "1" && writer_ad_info == "0"){
			var message_no = Math.floor((Math.random()*2) + 1);
			if (message_no == "1"){
				var message_value = "지금 생각을 남겨보세요. 친구도 궁금할거예요!";
			}
			else if (message_no == "2"){
				var message_value = "지금 어떤 생각을 하고 계시나요?";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
	else if (message_count == "6"){
		if (writer_ad_info == "1"){
			var message_no = Math.floor((Math.random()*2) + 1);
			if (message_no == "1"){
				var message_value = artist_presence+"님의 게시물은 환상적이예요!";
			}
			else if (message_no == "2"){
				var message_value = artist_presence+"님의 게시물을 추천합니다!";
			}
			$("#message_pop").slideDown(1500).text(message_value).delay(2000).slideUp(1500);
		}
		else{
			message_random();
		}
	}
}
function member_sign(){
	alert("회원가입하시려구요? 좋은 생각이예요!");
	$(document.location = "member.php");
}
function contents_upload(){
	alert("어떤 자료를 올리실지 궁금하네요!");
	$(document.location = "upload.php");
}
function sticker_image_over(){
	$("#sticker_submit_image").attr("src", "images/sticker_submit_jquery.jpg");
}
function sticker_image_out(){
	$("#sticker_submit_image").attr("src", "images/sticker_submit.jpg");
}
function custom_img_add(img_source){
	var upload_check = "error";
	var custom_img_check = confirm("커스텀이미지로 등록할까요?");
	if (custom_img_check == true){
		$.ajax({
			type: "POST",
			data: "upload_check=" +upload_check+ "&custom_image_query=" +img_source,
			url: "upload_insert.php",
			success: function(custom_img_result){
				if (custom_img_result == "login_fail"){
					alert("로그인 해보세요!");
				}
				else{
					alert("커스텀이미지가 등록되었어요!");
				}
			},
			error: function(code, message, error){
				alert(code.status + " "+ message +" "+error);
			}
		})
	}
}
function artist_add(artist){
	var friends = encodeURIComponent(artist);
	var type = "artist_query";
	var artist_check = confirm("이 아티스트에 관심이 있으신가요?");
	if (artist_check == true){
		$.ajax({
			type: "POST",
			data: "friends=" +escape(friends)+ "&type=" +type,
			url: "friends_insert.php",
			success: function(aritst_result){
				if (aritst_result == "login_fail"){
					alert("로그인! 잊으신 것은 아니시죠?");
				}
				else if (aritst_result == "artist_fail_1"){
					alert("이미 등록되어 있네요 ^^ 많이 좋아하시나봐요!");
				}
				else{
					alert(artist+"님을 관심있어 하는군요!");
				}
			},
			error: function(code, message, error){
				alert(code.status + " "+ message +" "+error);
			}
		})
	}
}
$(document).ready(function(){
	message_random();
	$("#main_logo").mouseover(function(){
		$("#logo").attr("src", "images/logo_jquery.png");
	});
	$("#main_logo").mouseout(function(){
		$("#logo").attr("src", "images/logo.png");
	});
	$("#background_view").click(function(){
		var board_height = $("#board_view").height();
		if ($("#contents_view").css("display") == "none"){
			$("#contents_view").fadeIn(1000);
			$(this).html("<a href='#' onclick='return false;'>배경</a>");
			return false;
		}
			$("#board_view").height(board_height);
			$("#contents_view").css("display", "none");
			$(this).html("<a href='#' onclick='return false;'>내용</a>");
	});
});
//]]>
</script>
</head>

<body onload="board_load('<?=$_GET[page]?>', '<?=$_GET[no]?>', '<?=$_GET[sticker]?>', '<?=$_GET[friendtalk]?>')">
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
<div id="message_pop"></div>
<div id="wrap">
	<!-- 위니플 로고 -->
	<div id="main_logo">
		<h1><a href="/"><img src="images/logo.png" alt="위니플" border="0" id="logo" /></a></h1>
	</div>
	<!-- 로그인 -->
	<div id="main_login">
		<?
		if ($_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
			?>
			<div class="login_user">
				<?=$_COOKIE[user]?> 님<a href="user.php"><img src="images/user_modify.png" border="0" align="top" width="16" height="16" alt="설정" /></a>&nbsp;
				<input type="button" value="Upload" class="login_menu_button" onclick="contents_upload(); return false;" />&nbsp;
				<input type="button" value="로그아웃" class="login_menu_button" onclick="logout_submit(); return false;" />
			</div>
			<?
		}
		else{
			?>
			<form method="post" action="login_insert.php" name="login_form">
				<input type="text" name="userid" value="아이디" id="userid" onfocus="login_check('id')" onblur="login_check('id_return')" class="main_input" />
				<input type="password" name="userpasswd" id="userpasswd" onfocus="login_check('password')" value="비밀번호" onblur="login_check('password_return')" class="main_input" />
				<input type="submit" onclick="return login_submit('<?=$_GET[page]?>', '<?=$_GET[no]?>', '<?=$_GET[sticker]?>', '<?=$_GET[friendtalk]?>')" value="로그인" class="main_menu_button" style="margin-right: 10px;" />
				<input type="button" value="회원가입" class="main_menu_button" onclick="member_sign(); return false;" />
			</form>
			<?
		}
		?>
	</div>
	<!-- 게시물 좌 -->
		<div id="read_left">
		<?
				// 게시물 글 분석 HTML 태그 삭제 & 띄어쓰기
				$str_title = trim($read_row[title]);
				$board_string = nl2br(str_replace(" ", "&nbsp;", htmlspecialchars($read_row[contents])));
		$board_contents = preg_replace("/([^\"\'\=\>])(http|https|HTTP|HTTPS)\:\/\/(.[^ \n\<\"\']+)/", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", " ".$board_string);

				// 배경화면 처리
				$bg_img ="background-image: url(upload/background/$read_row[bgimg]);";
				?>
		<div class="read_title"><h2><?=htmlspecialchars($str_title)?></h2></div>
				<div id="board_view" class="read_image" style="<?=$bg_img?>">
		<?
				// 동영상 처리
				$youtube_source_query = "[{$read_row[video]}]";
				$youtube_alter_query = "/\[http:\/\/youtu\.be\/([A-Za-z0-9_-]{11,11})\]/";

		// 동영상 source
		$youtube_image_source = preg_replace($youtube_alter_query, "http://img.youtube.com/vi/$1/0.jpg", $youtube_source_query);
				$youtube_video_source = preg_replace($youtube_alter_query, "<iframe id=\"contents_view\" width=\"500\" height=\"280\" src=\"http://www.youtube.com/embed/$1?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe>", $youtube_source_query);

				if ($read_row[type] == "image"){
			?>
			<img id="contents_view" src="upload/images/<?=$read_row[img]?>" alt="<?=$str_title?>" />
			<?
		}
				if ($read_row[type] == "video"){
			?>
			<?=$youtube_video_source?>
			<?
		}
		?>
		</div>
		<?
				if (!empty($read_row[contents]) || $read_row[type] == "video"){
			?>
			<div class="read_contents">
				<img src="images/contents_write.jpg" border="0" alt="포스트" /><br /><br />
				<div id="contents">
				<?
				if (!empty($read_row[contents])){
					echo "$board_contents";
				}
				else{
					echo "등록된 포스트가 없어요!";
				}
				if ($read_row[type] == "video"){
					echo "<br /><br />";
					?>
					<div align="right">Link : <a href="<?=$read_row[video]?>" target="_blank"><?=$read_row[video]?></a></div>
					<?
				}
				?>
				</div>
			</div>
			<?
		}
		else{
			?>
			<div class="read_contents">
				<img src="images/contents_write.jpg" border="0" alt="포스트" /><br /><br />
				<div id="contents">등록된 포스트가 없어요!</div>
			</div>
			<?
		}
				if (empty($sticker_image_row[sticker])){
			$sticker_image_row[sticker] = "winyple.jpg";
		}
		?>
				<div class="read_contents_user_image">
			<img src="upload/sticker/<?=$sticker_image_row[sticker]?>" border="0" width="70" height="70" alt="" />
		</div>
				<div class="read_contents_user_name">
			<?=$read_row[name]?>님&nbsp;<a href="search.php?no=0&amp;type=name&amp;search=<?=$read_row[name]?>"><img src="images/contents_user_search.jpg" border="0" width="10" height="10" alt="검색" /></a>
			<a href="#" onclick="friends_add('<?=$read_row[name]?>'); return false;"><img src="images/contents_user_add.jpg" border="0" width="10" height="10" alt="친구추가" /></a>
			<?
			if (!empty($read_row[artist])){
				?>
				<a href="#" onclick="artist_add('<?=$read_row[artist]?>'); return false;" class="read_info_tag">#<?=$read_row[artist]?></a>
				<?
			}
			if (!in_array($read_row[bgimg], $custom_check_array)){
				?>
				<a href="#" onclick="custom_img_add('<?=$read_row[bgimg]?>'); return false;" class="read_info_tag">커스텀 이미지로 등록</a>
				<?
			}
			?>
		</div>
				<div class="read_contents_user_info">
			작성일 : <?=$read_row[wdate]?>&nbsp;&nbsp;|&nbsp;
			조회수 : <?=$read_row[view]?>&nbsp;&nbsp;|&nbsp;
			<span id="background_view"><a href="#" onclick="return false;">배경</a></span>&nbsp;&nbsp;|&nbsp;
			<?
			if ($read_row[name] == $_COOKIE[user] && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
				?>
				<span id="contents_update"><a href="#" onclick="return board_contents_update()">수정</a></span>
				<?
			}
			else{
				?>
				<span id="contents_update"><font color="gray">수정</font></span>
				<?
			}
			?>
			&nbsp;|&nbsp;&nbsp;<a href="predelete.php?page=<?=$_GET[page]?>&amp;no=<?=$_GET[no]?>&amp;sticker=<?=$_GET[sticker]?>&amp;friendtalk=<?=$_GET[friendtalk]?>">삭제</a>
		</div>
				<div id="read_comment">
			<center><img src="images/loading.gif" border="0" alt="로딩" /></center>
				</div>
	</div>
	<!-- 게시물 우 -->
		<div id="read_right">
		<?
				if ($read_row[type] == "image"){
						$img_src = "upload/images/$read_row[img]";
						$img_info = getimagesize($img_src);
			?>
						<div id="read_file_info">
				<br /><span><font color="#7195af">가로 :&nbsp;<?=$img_info[0]?></font></span><br /><br />
				<span><font color="#7195af">세로 :&nbsp;<?=$img_info[1]?></font></span><br /><br />
				<font color="gray">※ 가로 500 이상 이미지는<br />&nbsp;&nbsp; 썸네일로 생성됩니다.</font>
			</div>
			<?
				}
				if ($read_row[type] == "video"){
			?>
			<div id="read_file_info">
				<br /><span><font color="#7195af">가로 :&nbsp;500</font></span><br /><br />
				<span><font color="#7195af">세로 :&nbsp;280</font></span><br /><br />
				<font color="gray">※ YouTube 동영상은<br />&nbsp;&nbsp; 500 X 280으로 보여집니다.</font>
			</div>
			<?
				}
				?>
		<!-- 스티커 -->
				<div class="widget_header">
			<h3>스티커
			<?
			echo "+".$sticker_total_page[0];
			?>
			</h3>
		</div>
				<div id="read_sticker">
			<div class="loading_contents">
				<img src="images/loading.gif" border="0" alt="로딩" />
			</div>
				</div>
		<!-- 프랜드톡 -->
				<div class="widget_header"><h3>프랜드톡</h3></div>
				<div id="read_friendtalk">
			<div class="loading_contents">
				<img src="images/loading.gif" border="0" alt="로딩" />
			</div>
				</div>
		</div>
	<!-- 게시물 footer -->
		<div id="footer">
		<img src="images/logo.png" border="0" align="middle" width="150" height="50" alt="위니플" /><br />
				<a href="author.php">제작자 &amp; 가이드</a>&nbsp;|&nbsp;<font color="#626262">&copy; WinyPle ALL RIGHTS RESERVED.</font>
	</div>
</div>
</body>
</html>