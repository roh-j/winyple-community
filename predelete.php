<?
include "db_info.php";

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 어드민 정보 가져오기
$admin_info_query = "select * from user where name='admin'";
$admin_info_result = mysql_query($admin_info_query, $connect);
$admin_info_row = mysql_fetch_array($admin_info_result);

// 잘못된 경로로 접근하였을 때
if (empty($_GET[page])){
		echo "<script type='text/javascript'>
		window.alert('잘못된 접근입니다.');
		location.replace('index.php');
	</script>";
		exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> WinyPle ! Delete </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="author" content="Noh Jae hee" />
<meta name="keywords" content="위니플, winyple, 이미지, image, video, 사진, 동영상, 유머, 게임, 뮤직비디오" />
<meta name="description" content="인상적인 모든 것을 공유하세요!" />
<link href="global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
//<![CDATA[
function login_check(type){
	if (type == "id" && document.login_form.userid.value == "아이디") document.login_form.userid.value = "";
	else if (type == "password" && document.login_form.userpasswd.value == "비밀번호") document.login_form.userpasswd.value = "";
	else if (type == "id_return" && document.login_form.userid.value == "") document.login_form.userid.value = "아이디";
	else if (type == "password_return" && document.login_form.userpasswd.value == "") document.login_form.userpasswd.value = "비밀번호";
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
				alert("아이디 또는 비밀번호를 확인해 주세요.");
			}
			else{
				location.reload();
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function board_delete_submit(){
	var userpasswd = $("#userpasswd").val();
	var page = $("#page").val();
	var board_delete_check = $("#board_delete_check").val();
	$.ajax({
		type: "POST",
		data: "userpasswd=" +userpasswd+ "&page=" +page+ "&board_delete_check=" +board_delete_check,
		url: "delete.php",
		success: function(board_delete_result){
			if (board_delete_result == "talkmessenger_delete_error"){
				alert("Talk Messenger는 삭제할 수 없습니다!");
			}
			else if (board_delete_result == "passwd_error"){
				alert("비밀번호가 일치하지 않습니다.");
			}
			else{
				location.replace("index.php");
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
//]]>
</script>
</head>

<body>
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
// 관리자 권한이 있을 때 바로 삭제
if ($_COOKIE[user] == "admin" && $_COOKIE[sid] == $admin_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
		// 게시물 검색
		$query = "select * from board where id='$_GET[page]'";
		$result = mysql_query($query, $connect);
		$row = mysql_fetch_array($result);

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
				$delete_query = "delete from board where id='$_GET[page]'";
				$delete_result = mysql_query($delete_query, $connect);

		// 댓글 삭제
				$comment_delete_query = "delete from comment where bid='$_GET[page]'";
				$comment_delete_result = mysql_query($comment_delete_query, $connect);

		// 스티커 삭제
				$sticker_delete_query = "delete from board_sticker where bid='$_GET[page]'";
				$sticker_delete_result = mysql_query($sticker_delete_query, $connect);

		// 프랜드톡 삭제
				$friendtalk_delete_query = "delete from friendtalk where bid='$_GET[page]'";
				$friendtalk_delete_result = mysql_query($friendtalk_delete_query, $connect);

		// 프랜드톡 알림 삭제
				$talkupdate_delete_query = "delete from talkupdate where bid='$_GET[page]'";
				$talkupdate_delete_result = mysql_query($talkupdate_delete_query, $connect);
		echo "<script type='text/javascript'>
		location.replace('index.php');
		</script>";
				exit;
		}
		else{
				echo "<script type='text/javascript' language='javascript'>
				window.alert('Talk Messenger는 삭제할 수 없어요!');
		location.replace('index.php');
		</script>";
				exit;
		}
}
?>
<div id="frame_delete">
	<form action="delete.php" method="post">
	비밀번호를 입력해 주세요<br /><br />
	<input type="password" id="userpasswd" name="userpasswd" class="main_input" />
	<input type="hidden" id="page" name="page" value="<?=$_GET[page]?>" />
	<input type="hidden" id="board_delete_check" name="board_delete_check" value="error" />
	<input type="submit" value="입력" class="main_menu_button" onclick="return board_delete_submit()" />
	</form>
</div>
</body>
</html>