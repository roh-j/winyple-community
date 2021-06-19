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
if (empty($_GET[page]) || empty($_GET[friendtalk_id])){
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
function friendtalk_delete_submit(page, no, sticker, friendtalk){
	var userpasswd = $("#userpasswd").val();
	var page = $("#page").val();
	var friendtalk_id = $("#friendtalk_id").val();
	var friendtalk_delete_check = $("#friendtalk_delete_check").val();
	$.ajax({
		type: "POST",
		data: "userpasswd=" +userpasswd+ "&page=" +page+ "&friendtalk_id=" +friendtalk_id+ "&friendtalk_delete_check=" +friendtalk_delete_check,
		url: "friendtalk_delete.php",
		success: function (friendtalk_delete_result){
			if (friendtalk_delete_result == "passwd_error"){
				alert("비밀번호를 확인해 주세요");
			}
			else{
				location.replace("read.php?page="+page+"&no="+no+"&sticker="+sticker+"&friendtalk="+friendtalk);
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
// 관리자 권한
if($_COOKIE[user] == "admin" && $_COOKIE[sid] == $admin_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
		$friendtalk_query = "select * from friendtalk where bid='$_GET[page]' and id='$_GET[friendtalk_id]'";
		$friendtalk_result = mysql_query($friendtalk_query, $connect);
		$friendtalk_row = mysql_fetch_array($friendtalk_result);
		$talkupdate_user_query = "select * from board where id='$_GET[page]'";
		$talkupdate_user_result = mysql_query($talkupdate_user_query, $connect);
		$talkupdate_user_row = mysql_fetch_array($talkupdate_user_result);

		// 프랜드톡 삭제
		$delete_query = "delete from friendtalk where bid='$_GET[page]' and id='$_GET[friendtalk_id]'";
		$delete_result = mysql_query($delete_query, $connect);

	// 작성자의 프랜드톡 알림 가져오기
		$talkupdate_count_query = "select count(*) from friendtalk where bid='$_GET[page]' and name='$friendtalk_row[name]'";
		$talkupdate_count_result = mysql_query($talkupdate_count_query, $connect);
		$talkupdate_count_row = mysql_fetch_row($talkupdate_count_result);

	// 프랜드톡 알림 삭제
		if ($talkupdate_count_row[0] == "0" && $talkupdate_user_row[name] != $friendtalk_row[name]){
				$talkupdate_delete_query = "delete from talkupdate where bid='$_GET[page]' and name='$friendtalk_row[name]'";
				$talkupdate_delete_result= mysql_query($talkupdate_delete_query, $connect);
		}
	echo "<script type='text/javascript'>
		location.replace('read.php?page=$_GET[page]&no=$_GET[no]&sticker=$_GET[sticker]&friendtalk=$_GET[friendtalk]');
	</script>";
		exit;
}
?>
<div id="frame_delete">
	<form action="friendtalk_delete.php" method="post">비밀번호를 입력해 주세요<br /><br />
	<input type="password" id="userpasswd" name="userpasswd" class="main_input" />
	<input type="hidden" id="page" name="page" value="<?=$_GET[page]?>" />
	<input type="hidden" id="friendtalk_id" name="friendtalk_id" value="<?=$_GET[friendtalk_id]?>" />
	<input type="hidden" id="friendtalk_delete_check" name="friendtalk_delete_check" value="error" />
	<input type="submit" value="입력" class="main_menu_button" onclick="return friendtalk_delete_submit('<?=$_GET[page]?>', '<?=$_GET[no]?>', '<?=$_GET[sticker]?>', '<?=$_GET[friendtalk]?>')" />
	</form>
</div>
</body>
</html>