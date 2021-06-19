<?
include "db_info.php";

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> WinyPle ! :: Sign Up </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
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
				alert("아이디 또는 비밀번호를 확인하셔야죠!");
			}
			else{
				$("#main_login").html("<div class='login_user'>"+login_result+"&nbsp;님<a href='user.php'><img src='images/user_modify.png' border='0' align='top' width='16' height='16' alt='설정' /></a>&nbsp;&nbsp;<input type='button' value='Upload' class='login_menu_button' onclick='contents_upload(); return false;' />&nbsp;&nbsp;<input type='button' value='로그아웃' class='login_menu_button' onclick='logout_submit(); return false;' /></div>");
				return view_setting('friends');
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
	return false;
}
function member_insert(){
	var member_id = encodeURIComponent($("#member_id").val());
	var member_passwd = $("#member_passwd").val();
	var member_passwd_check = $("#member_passwd_check").val();
	var member_email = encodeURIComponent($("#member_email").val());
	var member_check = $("#member_check").val();
	$.ajax({
		type: "POST",
		data: "member_id=" +member_id+ "&member_passwd=" +member_passwd+ "&member_passwd_check=" +member_passwd_check+ "&member_email=" +member_email+ "&member_check=" +member_check,
		url: "member_insert.php",
		success: function(member_result){
			if (member_result == "id_korean_error"){
				alert("아이디를 영어와 숫자로만 입력해주셔야 되요.");
			}
			else if (member_result == "id_count_error"){
				alert("아이디를 5 Byte 이상 10 Byte 이하로!");
			}
			else if (member_result == "id_blank_error"){
				alert("공백없이 입력해주세요.");
			}
			else if (member_result == "id_query_error"){
				alert("생성할 수 없는 아이디예요!");
			}
			else if (member_result == "id_same_query_error"){
				alert("같은 아이디가 이미 존재하군요. 멋진 아이디로 만들어보세요!");
			}
			else if (member_result == "passwd_count_error"){
				alert("비밀번호를 8자 이상 18자 이하로!");
			}
			else if (member_result == "passwd_check_error"){
				alert("비밀번호가 일치하지 않네요. 천천히 입력해보세요 ^^");
			}
			else if (member_result == "email_blank_error"){
				alert("공백없이 입력해주세요.");
			}
			else if (member_result == "email_check_error"){
				alert("이메일을 다시 입력해주세요. 대충 입력하시려는 것은 아니시죠? ^^");
			}
			else{
				alert("회원이 되셨어요! 환영합니다!");
				$(document.location = "author.php");
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
function form_cancel(){
	var cancel_value = confirm("입력을 취소 할까요?");
	if (cancel_value == true){
		$(document.location = "index.php");
	}
}
function member_sign(){
	alert("아래에 정보를 입력해주시면 되요!");
}
function contents_upload(){
	alert("어..? 어떻게 들어왔어요?");
	$(document.location = "upload.php");
}
$(document).ready(function(){
	$("#main_logo").mouseover(function(){
		$("#logo").attr("src", "images/logo_jquery.png");
	});
	$("#main_logo").mouseout(function(){
		$("#logo").attr("src", "images/logo.png");
	});
});
//]]>
</script>
</head>

<body>
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
				<input type="submit" onclick="return login_submit()" value="로그인" class="main_menu_button" style="margin-right: 10px;" />
				<input type="button" value="회원가입" class="main_menu_button" onclick="member_sign(); return false;" />
			</form>
			<?
		}
		?>
	</div>
	<!-- 회원가입 좌 -->
		<div id="member_left">
		<div class="contents_frame_header"><h2>회원가입</h2></div>
		<form action="member_insert.php" method="post">
		<div class="member_signup">
			<img src="images/member_step1.jpg" alt="" border="0" /><br />
			<input type="text" size="34" name="member_id" id="member_id" class="main_input" />
			<img src="images/member_step2.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<input type="password" size="36" name="member_passwd" id="member_passwd" class="main_input" />
			<img src="images/member_step3.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<input type="password" size="36" name="member_passwd_check" id="member_passwd_check" class="main_input" />
			<img src="images/member_step4.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<input type="text" size="54" name="member_email" id="member_email" class="main_input_text" />
			<input type="hidden" name="member_check" id="member_check" value="error" />
		</div>
		<input type="button" class="frame_button" value="취소" onclick="form_cancel(); return false;" />
		<input type="submit" onclick="return member_insert()" class="frame_button" value="확인" style="margin-right: 15px;" />
		</form>
		</div>
	<!-- 회원가입 우 -->
		<div id="member_right">
		<div class="widget_frame_header"><h2>알림</h2></div>
		<div class="member_notice">
			위니플에 오신 것을 환영합니다!<br />
			<p>Talk Messenger에서 내 생각을<br />
			담고 프랜드톡을 통해 친구와<br />
			비공개적인 대화를 나눌 수 있습니다.<br />
			지금 Winyple에 가입하세요!</p>
		</div>
		</div>
	<!-- 메인화면 footer -->
		<div id="footer">
		<img src="images/logo.png" border="0" align="middle" width="150" height="50" alt="위니플" /><br />
				<a href="author.php">제작자 &amp; 가이드</a>&nbsp;|&nbsp;<font color="#626262">&copy; WinyPle ALL RIGHTS RESERVED.</font>
	</div>
</div>
</body>
</html>