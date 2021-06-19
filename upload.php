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
<title> WinyPle ! :: Upload </title>
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
function page_click(){
		$("#wrap").hide();
		$("#loading").show();
	if ($("input[name=background]").val() == "custom" && $("input[name=type]").val() == "video"){
		var image_confirm = confirm("이미지 크기를 자동맞춤 해도 될까요?");
		if (image_confirm == true){
			$("input[name=image_size]").val("auto_size");
		}
		else{
			$("input[name=image_size]").val("default_size");
		}
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
				alert("아이디 또는 비밀번호를 천천히 다시 입력해 보세요~");
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
function form_cancel(){
	var cancel_value = confirm("입력을 취소 할까요?");
	if (cancel_value == true){
		$(document.location = "index.php");
	}
}
function contents_upload(){
	alert("아래에 정보를 입력해주시면 되요!");
}
$(document).ready(function(){
	$("#main_logo").mouseover(function(){
		$("#logo").attr("src", "images/logo_jquery.png");
	});
	$("#main_logo").mouseout(function(){
		$("#logo").attr("src", "images/logo.png");
	});
	$("input[name=background]").val("default");
	$("input[name=image_size]").val("default_size");
	$("#video_upload, #image_upload").hide();
	$(".backgrond_image").each(function(index, value){
		if (index == "0"){
			$(this).html("<img id='background_select_icon' src='images/background_select_icon.jpg' border='0' alt='' />");
		}
	});
	$("#type_image").click(function(){
		$("#default_upload, #video_upload").hide();
		$("#image_upload").show();
		$("input[name=type]").val("image");
		$("#type_video").removeClass("upload_type_jquery");
		$(this).addClass("upload_type_jquery");
	});
	$("#type_video").click(function(){
		$("#default_upload, #image_upload").hide();
		$("#video_upload").show();
		$("input[name=type]").val("video");
		$("#type_image").removeClass("upload_type_jquery");
		$(this).addClass("upload_type_jquery");
	});
	$("#image_type_background").click(function(){
		$("#image_type_background").hide();
		$("#background_upload_box").show();
		$(".backgrond_image").each(function(index, value){
			if ($.contains(this, document.getElementById("background_select_icon"))){
				$(this).html("");
			}
		});
		$("input[name=background]").val("custom");
	});
	$("#image_type_background_cancel").click(function(){
		$("#background_upload_box").hide();
		$("#image_type_background").show();
		$(".backgrond_image").each(function(index, value){
			if ($.contains(this, document.getElementById("background_select_icon"))){
				$(this).html("");
			}
			else if(index == "0"){
				$(this).html("<img id='background_select_icon' src='images/background_select_icon.jpg' border='0' alt='' />");
				$("input[name=background]").val("default");
			}
		});
	});
	$(".backgrond_image").click(function(){
		$(".backgrond_image").each(function(){
			if ($.contains(this, document.getElementById("background_select_icon"))){
				$(this).html("");
			}
		})
		$(this).html("<img id='background_select_icon' src='images/background_select_icon.jpg' border='0' alt='' />");
		$("#background_upload_box").hide();
		$("#image_type_background").show();
		$(".backgrond_image").each(function(index, value){
			if (index == "0" && $.contains(this, document.getElementById("background_select_icon"))){
				$("input[name=background]").val("default");
			}
			else if(index == "1" && $.contains(this, document.getElementById("background_select_icon"))){
				$("input[name=background]").val("mint");
			}
			else if(index == "2" && $.contains(this, document.getElementById("background_select_icon"))){
				$("input[name=background]").val("love");
			}
			else if(index == "3" && $.contains(this, document.getElementById("background_select_icon"))){
				$("input[name=background]").val("gray");
			}
			else if(index == "4" && $.contains(this, document.getElementById("background_select_icon"))){
				$("input[name=background]").val("user_background_image");
			}
		})
	});
	$("#upload_title").blur(function(event){
		var search_keyword = $(this).val();
		if (search_keyword != ""){
			$("#keword_search").html("<a href='https://www.google.co.kr/search?hl=ko&tbm=isch&q="+search_keyword+"' target='_blank'>Google 이미지 키워드 검색</a>");
		}
		else{
			$("#keword_search").html("");
		}
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
// 커스텀 이미지 파일 경로
$file_directory = "upload/background/$user_info_row[user_background]";

// 커스텀 이미지가 존재하지 않을 때
if(!empty($user_info_row[user_background]) && !file_exists($file_directory) && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
		echo "<script type='text/javascript'>
		window.alert('커스텀 이미지가 존재하지 않아 삭제됩니다!');
	location.replace('upload.php');
	</script>";
	$user_background_query = "update user set user_background='' where name='$_COOKIE[user]'";
	$user_background_result = mysql_query($user_background_query, $connect);
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
	<!-- 업로드 좌 -->
		<div id="upload_left">
		<div class="contents_frame_header"><h2>업로드</h2></div>
		<form action="upload_insert.php" method="post" enctype="multipart/form-data">
		<div class="upload_write">
			<img src="images/step1.jpg" alt="" border="0" /><br />
			<a href="#" onclick="return false;"><img src="images/upload_choose.jpg" id="image_type_background" alt="" border="0" /></a>
			<div class="user_image_upload_box" id="background_upload_box">
				<div class="user_image_upload">배경 이미지</div>
				<div class="user_image_upload_blank">
					<a href="#" onclick="return false;"><img src="images/frame_multi_cancel.jpg" border="0" width="12" height="12" id="image_type_background_cancel" align="bottom" alt="취소" /></a>
				</div>
				<div class="input_image" id="type_background_custom">
					<input type="file" name="background_file" />
				</div>
			</div>
			<img src="images/step2.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<input type="text" name="title" class="input_source" id="upload_title" />
			<img src="images/step3.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<div class="upload_type_select_image" id="type_image">이미지</div>
			<div class="upload_type_select_video" id="type_video">동영상</div>
			<div class="upload_type_select_blank"></div>
			<div class="upload_type_default" id="default_upload">업로드 타입을 선택해 주세요</div>
			<div id="image_upload" class="input_image">
				<input type="file" name="img_file" />
			</div>
			<div id="video_upload">
				<input type="text" class="input_source" name="video_src" />
			</div>
			<img src="images/step4.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<input type="text" id="upload_artist" name="upload_artist" class="main_input" />
			<img src="images/step5.jpg" style="margin-top: 10px;" alt="" border="0" /><br />
			<textarea name="contents" style="width: 510px; height: 90px;" class="input_contents" cols="" rows=""></textarea>
			<input type="hidden" name="background" />
			<input type="hidden" name="upload_check" value="error" />
			<input type="hidden" name="type" />
			<input type="hidden" name="image_size" />
		</div>
		<input type="button" class="frame_button" value="취소" onclick="form_cancel(); return false;" /><input type="submit" onclick="page_click()" class="frame_button" value="확인" style="margin-right: 15px;" />
		</form>
		</div>
	<!-- 업로드 우 -->
		<div id="upload_right">
		<div class="widget_frame_header"><h2>배경 이미지</h2></div>
		<div class="upload_background">
			<div class="backgrond_image" style="position: relative; float: left; width: 68px; height: 68px; background-image: url(upload/background/default_ico.jpg); margin-right: 14px; cursor: pointer;"></div>
			<div class="backgrond_image" style="position: relative; float: left; width: 68px; height: 68px; background-image: url(upload/background/mint_ico.jpg); margin-right: 14px; cursor: pointer;"></div>
			<div class="backgrond_image" style="position: relative; float: left; width: 68px; height: 68px; background-image: url(upload/background/love_ico.jpg); cursor: pointer;"></div>
			<div class="backgrond_image" style="position: relative; float: left; width: 68px; height: 68px; background-image: url(upload/background/gray_ico.jpg); margin-right: 14px; margin-top: 10px; cursor: pointer;"></div>
			<?
			if (!empty($user_info_row[user_background])){
				?>
				<div class="backgrond_image" style="position: relative; float: left; width: 68px; height: 68px; background-image: url(upload/background/custom_ico.jpg); margin-right: 18px; margin-top: 10px; cursor: pointer;"></div>
				<?
			}
			?>
			<div style="clear: both; padding-top: 10px;">※ HTML 태그는 그대로 보여집니다.<br />※ 적절하지 못한 게시물은 삭제됩니다.<br />※ 배경이미지는 수정될 수 있습니다.<br />※ 권장 크기 : 가로 680px 이상<br /><br /><a href="http://yuj7803.blog.me/20165956082" target="_blank">동영상 업로드 가이드</a><br />
			<span id="keword_search"></span></div>
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