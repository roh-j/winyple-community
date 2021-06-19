<?
include "db_info.php";

// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 광고 권환 유저 가져오기
$advertise_user_info_query = "select * from user where advertise='1'";
$advertise_user_info_result = mysql_query($advertise_user_info_query, $connect);
$advertise_user_info_row = mysql_fetch_array($advertise_user_info_result);

// 광고 유저 Load
$advertise_count_user_query = "select count(*) from user where advertise='1'";
$advertise_count_user_result = mysql_query($advertise_count_user_query, $connect);
$advertise_count_user_row = mysql_fetch_row($advertise_count_user_result);

// 광고 게시물 Load
$advertise_count_info_query = "select count(*) from board where name='$advertise_user_info_row[name]' and messenger='0'";
$advertise_count_info_result = mysql_query($advertise_count_info_query, $connect);
$advertise_count_info_row = mysql_fetch_row($advertise_count_info_result);

// AD 게시물 확인
$advertise_board_info_query = "select * from board where name='$advertise_user_info_row[name]' and messenger='0' order by id desc limit 0, 1";
$advertise_board_info_result = mysql_query($advertise_board_info_query, $connect);
$advertise_board_info_row = mysql_fetch_array($advertise_board_info_result);

// AD 게시물 댓글
$advertise_comment_info_query = "select count(*) from comment where bid='$advertise_board_info_row[id]'";
$advertise_comment_info_result = mysql_query($advertise_comment_info_query, $connect);
$advertise_comment_info_row = mysql_fetch_row($advertise_comment_info_result);

// 입력 타입 확인
$cookie_check_array = array("friends", "update");

// 게시물 뷰 세팅
if (empty($_COOKIE[view_setting])  || !in_array($_COOKIE[view_setting], $cookie_check_array) || empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
	setcookie("view_setting", "update", 0, "/");
	$_COOKIE[view_setting] = "update";
}
// 초기값
if (empty($_GET[page]) || $_GET[page] < "0" ){
	$_GET[page] = "0";
}
// 입력 처리
if (!empty($_POST[view_setting])){
	// 타입이 최신글 보기 일때
	if ($_POST[view_setting] == "update"){
		// 쿠키 발급
		setcookie("view_setting", "update", 0, "/");
		echo "success";
		exit;
	}
	// 타입이 관심글 보기 일 때
	else if($_POST[view_setting] == "friends" && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
		// 쿠키 발급
		setcookie("view_setting", "friends", 0, "/");

		// 게시물 뷰 초기값
		$user_view_setting_value_query = "update user set view_id='', view_total='0', view_like='' where name='$_COOKIE[user]'";
		$user_view_setting_value_result = mysql_query($user_view_setting_value_query, $connect);

		// 친구 표시
		$friends_query = "select * from friends where name='$_COOKIE[user]' and identify='2' order by intimacy desc";
		$friends_result = mysql_query($friends_query, $connect);

		// 친구 결과 & 친밀도
		while ($friends_row = mysql_fetch_array($friends_result)){
			// 친구와 댓글 상호 작용
			$comment_intimacy_query = "select count(*) from comment where writer='$friends_row[mate]' and name='$_COOKIE[user]'";
			$comment_intimacy_result = mysql_query($comment_intimacy_query, $connect);
			$comment_intimacy_row = mysql_fetch_row($comment_intimacy_result);

			// 친구와 프랜드톡 상호 작용
			$friendtalk_intimacy_query = "select count(*) from friendtalk where writer='$friends_row[mate]' and name='$_COOKIE[user]'";
			$friendtalk_intimacy_result = mysql_query($friendtalk_intimacy_query, $connect);
			$friendtalk_intimacy_row = mysql_fetch_row($friendtalk_intimacy_result);

			// 친구와 스티커 상호 작용
			$sticker_intimacy_query = "select count(*) from board_sticker where writer='$friends_row[mate]' and name='$_COOKIE[user]'";
			$sticker_intimacy_result = mysql_query($sticker_intimacy_query, $connect);
			$sticker_intimacy_row = mysql_fetch_row($sticker_intimacy_result);

			// 친밀도 계산
			$friends_intimacy_point = $comment_intimacy_row[0] + $friendtalk_intimacy_row[0] + $sticker_intimacy_row[0] + $friends_row[view];
			$friends_intimacy_count_query = "update friends set intimacy='$friends_intimacy_point' where mate='$friends_row[mate]' and name='$_COOKIE[user]'";
			$friends_intimacy_count_result = mysql_query($friends_intimacy_count_query, $connect);

			// 데이터 베이스에서 글 가져오기
			$board_info_query = "select distinct bid from board_sticker where name='$friends_row[mate]' order by id desc";
			$board_info_result = mysql_query($board_info_query, $connect);

			// 게시물 총 갯수 카운트
			$page_count_query = "select count(distinct bid) from board_sticker where name='$friends_row[mate]'";
			$page_count_result = mysql_query($page_count_query, $connect);
			$total_page_count = mysql_fetch_row($page_count_result);

			// 친구 관심글 갯수 등록
			$friends_board_count_query = "update user set view_total=view_total+$total_page_count[0] where name='$_COOKIE[user]'";
			$friends_board_count_result = mysql_query($friends_board_count_query, $connect);

			// 관심글 처리
			while ($board_info_row = mysql_fetch_array($board_info_result)){
				// 게시물 정보 가져오기
				$board_messenger_confirm_query = "select * from board where id='$board_info_row[bid]'";
				$board_messenger_confrim_result = mysql_query($board_messenger_confirm_query, $connect);
				$board_messenger_confrim_row = mysql_fetch_array($board_messenger_confrim_result);

				// 메신저가 아닐 때 배열로 저장
				if ($board_messenger_confrim_row[messenger] == "0"){
					$view_array_value[] = $board_info_row[bid];
					$view_like_array_value[] = $friends_row[mate];
				}
				// 메신저 일 때
				else{
					$friends_board_count_confrim_query = "update user set view_total=view_total-1 where name='$_COOKIE[user]'";
					$friends_board_count_confrim_result = mysql_query($friends_board_count_confrim_query, $connect);
				}
			}
		}
		// 관심글 검색결과 총 갯수
		$user_view_total_query = "select * from user where name='$_COOKIE[user]'";
		$user_view_total_result = mysql_query($user_view_total_query, $connect);
		$user_view_total_row = mysql_fetch_array($user_view_total_result);

		// 총 갯수가 0개가 아니고 결과 값 저장
		if ($user_view_total_row[view_total] != "0"){
			// 배열로 저장
			$view_value = implode(",", $view_array_value);
			$view_like_value = implode(",", $view_like_array_value);

			// 관심글 ID
			$view_update_query = "update user set view_id='$view_value' where name='$_COOKIE[user]'";
			$view_update_result = mysql_query($view_update_query, $connect);

			// 관심글 유저
			$view_like_update_query = "update user set view_like='$view_like_value' where name='$_COOKIE[user]'";
			$view_like_update_result = mysql_query($view_like_update_query, $connect);
		}
		echo "success";
		exit;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--


	o888o           o888o  o88o88o  88888o    o888  888o     o888
	 888             888     888    888 888    888   d88     88b
	 888     o8o     888     888    888  888   888     d8o o8b
		888   dY8Yb   888      888    888   888  888       8Y8
		 88o o88 88o o88       888    888    888 888       888       .o.
		 'V888V' 'V888V'     o88888o  888o    o88888       888       Y8P


-->
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> WinyPle ! :: 커뮤니티 </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<meta name="author" content="Noh Jae hee" />
<meta name="keywords" content="위니플, winyple, 이미지, image, video, 사진, 동영상, 유머, 게임, 뮤직비디오" />
<meta name="description" content="Winyple은 인상적인 영상이나 이미지를 담을 수 있는 사이트입니다. Winyple을 통해 친구와 자유롭게 대화하고 관심사를 공유하세요." />
<meta name="google-site-verification" content="YzuImYC1id1JlKXuNNBv750428_mYbXGmyyBhgQsaBU" />
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
function search_check(){
		if (document.search_form.search.value == ""){
				alert("검색어는 입력해 주셔야죠!");
				document.search_form.search.focus();
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
				$(document.location = "index.php");
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
function member_sign(){
	alert("회원가입하시려구요? 좋은 생각이예요!");
	$(document.location = "member.php");
}
function contents_upload(){
	alert("어떤 자료를 올리실지 궁금하네요!");
	$(document.location = "upload.php");
}
function call_winyple(type){
	if (type == "down"){
		$("#call_winyple_pop").slideDown(1500);
	}
	else{
		$("#call_winyple_pop").slideUp(1500);
	}
}
function feel_add(feel_type){
	var modify_check = "error";
	var type = "feel";
	$.ajax({
		type: "POST",
		data: "modify_check=" +modify_check+ "&type=" +type+ "&feel_type=" +feel_type,
		url: "user_modify.php",
		success: function(feel_result){
			if (feel_result == "feel_update"){
				alert("현재 기분이 등록되었어요!");
				location.reload();
			}
		},
		error: function(code, message, error){
			alert(code.status + " "+ message +" "+error);
		}
	})
}
$(document).ready(function(){
	$("#main_logo").mouseover(function(){
		$("#logo").attr("src", "images/logo_jquery.png");
	});
	$("#main_logo").mouseout(function(){
		$("#logo").attr("src", "images/logo.png");
	});
	$("#page_current").addClass("background_jquery");
	$("#search_type_subject, #select_search_menu_subject").show();
	$("input[name=type]").val("subject");
	$("#select_search_menu_subject").click(function(){
		$("#select_search_menu_subject, #search_type_subject, #search_type_name, #select_search_menu_name").show();
		$("input[name=type]").val("subject");
	});
	$("#select_search_menu_name").click(function(){
		$("#select_search_menu_subject, #search_type_subject, #search_type_name, #select_search_menu_name").show();
		$("input[name=type]").val("subject");
	});
	$("#search_type_subject").click(function(){
		$("#search_type_name, #select_search_menu_name").hide();
		$("input[name=type]").val("subject");
	});
	$("#search_type_name").click(function(){
		$("#search_type_subject, #select_search_menu_subject").hide();
		$("input[name=type]").val("name");
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
	<div id="call_winyple_pop">
		<a href="#" onclick="call_winyple('up'); return false;" id="call_winyple_contents_close">Close</a>
		<img src="images/winyple_call.jpg" alt="winyple_call" border="0" />
		<div class="call_winyple_container">
		<?
		// 게시물 추천 정보 가져오기
		if ($user_info_row[today_feel] == "feel_good"){
			$feel_user_type = "오늘은 기분이 좋으시군요!";
		}
		else if ($user_info_row[today_feel] == "feel_soso"){
			$feel_user_type = "평범한 날이라면 아래의 게시물을 확인해보세요~";
		}
		else{
			$feel_user_type = "힘내세요! 내일은 기분이 좋을거예요!";
		}
		// 오늘 날짜
		$today = getdate();
		$today_value = $today[year] + $today[mon] + $today[mday];

		// 추천 게시물 검색
		$feel_board_query = "select * from board where feel_type = '$user_info_row[today_feel]' and messenger = '0' and feel_day = '$today_value' order by feel_view desc limit 0, 6";
		$feel_board_result = mysql_query($feel_board_query, $connect);
		$feel_board_count_query = "select count(*) from board where feel_type = '$user_info_row[today_feel]' and messenger = '0' and feel_day = '$today_value'";
		$feel_board_count_result = mysql_query($feel_board_count_query, $connect);
		$feel_board_count_row = mysql_fetch_row($feel_board_count_result);

		if (!empty($user_info_row[today_feel]) && $feel_board_count_row[0] > "0"){
			echo "$feel_user_type<br /><br />";
			while($feel_board_row = mysql_fetch_array($feel_board_result)){
				?>
				♬ <a href="read.php?page=<?=$feel_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0"><?=$feel_board_row[title]?></a><br />
				<?
			}
		}
		else if (!empty($user_info_row[today_feel]) && $feel_board_count_row[0] == "0"){
			?>
			<span style="font-size: 16px;">
				아직 정보가 없어요!
			</span>
			<?
		}
		else if (empty($user_info_row[today_feel]) && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
			?>
			<div style="text-align: center;">
				오늘은 어떤 기분이신가요? 등록하시면 제가 게시물을 추천해 드릴께요!<br /><br />
				<a href="#" onclick="feel_add('feel_good'); return false;"><img src="images/feel_good.jpg" border="0" style="margin-right: 30px;" alt="좋아요!" /></a>
				<a href="#" onclick="feel_add('feel_soso'); return false;"><img src="images/feel_soso.jpg" border="0" alt="그럭저럭" /></a>
				<a href="#" onclick="feel_add('feel_bad'); return false;"><img src="images/feel_bad.jpg" border="0" alt="나빠요!" style="margin-left: 30px;" /></a>
			</div>
			<?
		}
		else{
			?>
			<span style="font-size: 16px;">
				오늘 하루 기분이 어떠셨나요?<br /><br />로그인 하신 후 기분을 등록하시면<br /><br />같은 기분을 느끼고 있는 다른 회원분들이 보신 게시물을 추천받으실 수 있어요!
			</span>
			<?
		}
		?>
		</div>
	</div>
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
	<div id="header_call_winyple"><a href="#" onclick="call_winyple('down'); return false;">Call Winyple!</a></div>
	<!-- 메인화면 좌 -->
	<div id="main_left">
		<!-- 메인화면 검색 -->
		<div id="main_search">
			<form method="get" action="search.php" name="search_form">
				<input type="hidden" name="no" value="0" />
				<input type="hidden" name="type" />
				<input type="text" name="search" class="main_input_text" /><input type="submit" class="search_button" value="Search" onclick="return search_check()" />
			</form>
		</div>
		<!-- 메인화면 검색 타입 -->
		<div id="main_search_type">
			<div id="search_type_subject" class="main_search_style">제목</div>
			<div id="select_search_menu_subject" class="main_search_box">▽</div>
			<div id="search_type_name" class="main_search_style">ID</div>
			<div id="select_search_menu_name" class="main_search_box">▽</div>
				</div>
		<?
		// 관심글 처리
		if ($_COOKIE[view_setting] == "friends"){
			?>
			<!-- 게시물 뷰 타입 -->
			<div class="board_header"><h2>관심글</h2></div>
			<div class="board_view_info">
			<?
			if ($_COOKIE[view_setting] == "update"){
				echo "새로 등록된 게시물 입니다.";
			}
			else{
				echo "친구가 관심있어 하는 게시물 입니다.";
			}
			?>
			&nbsp;&nbsp;&nbsp;<span id="update_view_setting"><a href="#" onclick="return view_setting('update')">최신글</a></span>&nbsp;|
			<?
			if (empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
				?>
				<span id="friends_view_setting"><font color="gray">관심글</font></span>
				<?
			}
			else{
				?>
				<span id="friends_view_setting"><a href="#" onclick="return view_setting('friends')">관심글</a></span>
				<?
			}
			?>
			</div>
			<?
			// 한 페이지에 보여질 게시물
			$page_size = "10";
			$page_list_size = "8";

			// 총 페이지
			if ($user_info_row[view_total] <= "0") $user_info_row[view_total] = "0";
			$total_page = ceil($user_info_row[view_total] / $page_size);

			// 현재 페이지
			$current_page = ceil(($_GET[page] + 1)/$page_size);
			$start_page = floor(($current_page - 1) / $page_list_size)*$page_list_size + 1;
			$end_page = $start_page + $page_list_size - 1;
			if ($total_page < $end_page) $end_page = $total_page;

			// 뷰 가져오기
			$board_view_value = explode(",", $user_info_row[view_id]);
			$board_like_value = explode(",", $user_info_row[view_like]);

			$view_page_count = $_GET[page] + $page_size;

			// 관심글 뷰 페이징
			if ($view_page_count > $user_info_row[view_total]) $view_page = $user_info_row[view_total];
			else if ($view_page_count <= $user_info_row[view_total]) $view_page = $view_page_count;

			for ($i = $_GET[page] ; $i < $view_page ; $i++){
				// 게시물 Load
				$board_view_query = "select * from board where id='$board_view_value[$i]'";
				$board_view_result = mysql_query($board_view_query, $connect);
				$board_view_row = mysql_fetch_array($board_view_result);

				// Comment Load (Main)
				$board_comment_count = mysql_query("select count(*) from comment where bid='$board_view_value[$i]'", $connect);
				$board_comment_row = mysql_fetch_row($board_comment_count);
				$board_comment_total = $board_comment_row[0];

				?>
				<div class="interest_view_user"><?=$board_like_value[$i]?>님이 관심있어 합니다!</div>
				<?
				if ($board_view_row[type] == "image"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$board_view_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_view_row[title])?>"><img src="upload/thumbnail/<?=$board_view_row[img]?>" border="0" alt="" /></a>
					</div>
					<?
				}
				if ($board_view_row[type] == "video"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$board_view_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_view_row[title])?>"><img src="<?=$board_view_row[img]?>" alt="" border="0" /></a>
					</div>
					<?
				}
				?>
				<div class="title">
					<a href="read.php?page=<?=$board_view_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_view_row[title])?>"><?=htmlspecialchars($board_view_row[title])?></a>
				</div>
				<div class="user">
					작성자&nbsp;<span><font color="#7195af"><?=$board_view_row[name]?></font></span>
					<a href="search.php?no=0&amp;type=name&amp;search=<?=$board_view_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
					<?
					if ($board_view_row[type] == "video"){
						?>
						<img src="images/youtube_play.jpg" width="12" height="9" alt="비디오" border="0" />
						<?
					}
					?>
				</div>
				<div class="view">
					조회수&nbsp;<span><?=$board_view_row[view]?></span>&nbsp;&nbsp;|&nbsp;&nbsp;댓글&nbsp;<span><?=$board_comment_total?></span>
				</div>
				<?
			}
			if (empty($user_info_row[view_total])){
				?>
				<div id="frame_result">관심게시물 정보가 없습니다.</div>
				<?
			}
			if (!empty($user_info_row[view_total])){
				?>
				<div class="page">
				<?
				$current_page_color = ($_GET[page] / $page_size) + 1;
				if ($start_page >= $page_list_size){
					$prev_list = ($start_page - 2) * $page_size;
					?>
					<div class="page_select">
						<a href="index.php?page=<?=$prev_list?>">이전</a>
					</div>
					<?
				}
				else{
					?>
					<div class="page_select"></div>
					<?
				}
				for ($i=$start_page ; $i <= $end_page ; $i++){
					$page = ($i - 1) * $page_size;
					if ($current_page_color == $i){
						?>
						<div class="page_select" id="page_current">
							<span><?=$i?></span>
						</div>
						<?
					}
					else{
						?>
						<div class="page_select">
							<a href="index.php?page=<?=$page?>"><?=$i?></a>
						</div>
						<?
					}
				}
				if ($total_page > $end_page){
					$next_list = $end_page * $page_size;
					?>
					<div class="page_select">
						<a href="index.php?page=<?=$next_list?>">다음</a>
					</div>
					<?
				}
				else{
					?>
					<div class="page_select"></div>
					<?
				}
				?>
				</div>
				<?
			}
		}
		else{
			?>
			<div class="board_header"><h2>최신글</h2></div>
			<div class="board_view_info">
			<?
			if ($_COOKIE[view_setting] == "update"){
				echo "새로 등록된 게시물 입니다.";
			}
			else{
				echo "친구가 관심있어 하는 게시물 입니다.";
			}
			?>
			&nbsp;&nbsp;&nbsp;<span id="update_view_setting"><a href="#" onclick="return view_setting('update')">최신글</a></span>&nbsp;|
			<?
			if (empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
				?>
				<span id="friends_view_setting"><font color="gray">관심글</font></span>
				<?
			}
			else{
				?>
				<span id="friends_view_setting"><a href="#" onclick="return view_setting('friends')">관심글</a></span>
				<?
			}
			?>
			</div>
			<?
			// 한 페이지에 보여질 게시물
			$page_size = "10";
			$page_list_size = "8";

			// 게시물 Load
			$board_info_query = "select * from board where messenger='0' order by id desc limit $_GET[page], $page_size";
			$board_info_result = mysql_query($board_info_query, $connect);

			$page_count_query = "select count(*) from board where messenger='0'";
			$page_count_result = mysql_query($page_count_query, $connect);
			$total_page_count = mysql_fetch_row($page_count_result);

			// 총 페이지
			if ($total_page_count[0] <= "0") $total_page_count[0] = "0";
			$total_page = ceil($total_page_count[0] / $page_size);

			// 현재 페이지
			$current_page = ceil(($_GET[page] + 1)/$page_size);
			$start_page = floor(($current_page - 1) / $page_list_size) * $page_list_size + 1;
			$end_page = $start_page + $page_list_size - 1;
			if ($total_page < $end_page) $end_page = $total_page;

			while ($board_info_row = mysql_fetch_array($board_info_result)){
				// Comment Load (Main)
				$board_comment_count = mysql_query("select count(*) from comment where bid='$board_info_row[id]'", $connect);
				$board_comment_row = mysql_fetch_row($board_comment_count);
				$board_comment_total = $board_comment_row[0];

				if ($board_info_row[type] == "image"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_info_row[title])?>"><img src="upload/thumbnail/<?=$board_info_row[img]?>" alt="" border="0" /></a>
					</div>
					<?
				}
				if ($board_info_row[type] == "video"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_info_row[title])?>"><img src="<?=$board_info_row[img]?>" alt="" border="0" /></a>
					</div>
					<?
				}
				?>
				<div class="title">
					<a href="read.php?page=<?=$board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($board_info_row[title])?>"><?=htmlspecialchars($board_info_row[title])?></a>
				</div>
				<div class="user">
					작성자&nbsp;<span><font color="#7195af"><?=$board_info_row[name]?></font></span>
					<a href="search.php?no=0&amp;type=name&amp;search=<?=$board_info_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
					<?
					if ($board_info_row[type] == "video"){
						?>
						<img src="images/youtube_play.jpg" width="12" height="9" alt="비디오" border="0" />
						<?
					}
					?>
				</div>
				<div class="view">
					조회수&nbsp;<span><?=$board_info_row[view]?></span>&nbsp;&nbsp;|&nbsp;&nbsp;댓글&nbsp;<span><?=$board_comment_total?></span>
				</div>
				<?
			}
			?>
			<div class="page">
			<?
			$current_page_color = ($_GET[page] / $page_size) + 1;
			if ($start_page >= $page_list_size){
				$prev_list = ($start_page - 2) * $page_size;
				?>
				<div class="page_select">
					<a href="index.php?page=<?=$prev_list?>">이전</a>
				</div>
				<?
			}
			else{
				?>
				<div class="page_select"></div>
				<?
			}
			for ($i=$start_page ; $i <= $end_page ; $i++){
				$page = ($i - 1) * $page_size;
				if ($current_page_color == $i){
					?>
					<div class="page_select" id="page_current">
						<span><?=$i?></span>
					</div>
					<?
				}
				else{
					?>
					<div class="page_select">
						<a href="index.php?page=<?=$page?>"><?=$i?></a>
					</div>
					<?
				}
			}
			if ($total_page > $end_page){
				$next_list = $end_page * $page_size;
				?>
				<div class="page_select">
					<a href="index.php?page=<?=$next_list?>">다음</a>
				</div>
				<?
			}
			else{
				?>
				<div class="page_select"></div>
				<?
			}
			?>
			</div>
			<?
		}
		?>
	</div>
	<!-- 메인화면 우 -->
	<div id="main_right">
		<div id="main_information"></div>
				<div class="board_header"><h2>Best Top 5</h2></div>
		<div class="board_view_info">인기 있는 게시물 입니다.</div>
				<?
		// AD Board
		if ($advertise_count_info_row[0] != "0" && $advertise_count_user_row[0] != "0"){
			if ($advertise_board_info_row[type] == "image"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$advertise_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($advertise_board_info_row[title])?>"><img src="upload/thumbnail/<?=$advertise_board_info_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
			if ($advertise_board_info_row[type] == "video"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$advertise_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($advertise_board_info_row[title])?>"><img src="<?=$advertise_board_info_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
			?>
			<div class="title">
				<a href="read.php?page=<?=$advertise_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($advertise_board_info_row[title])?>"><?=htmlspecialchars($advertise_board_info_row[title])?></a>
			</div>
			<div class="user">
				작성자&nbsp;<span><font color="red"><?=$advertise_board_info_row[name]?></font></span>
				<a href="search.php?no=0&amp;type=name&amp;search=<?=$advertise_board_info_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
				<?
				if ($advertise_board_info_row[type] == "video"){
					?>
					<img src="images/youtube_play.jpg" width="12" height="9" alt="비디오" border="0" />
					<?
				}
				?>
				<font color="red">[AD]</font>
			</div>
			<div class="view">
				조회수&nbsp;<span><?=$advertise_board_info_row[view]?></span>&nbsp;&nbsp;|&nbsp;&nbsp;댓글&nbsp;<span><?=$advertise_comment_info_row[0]?></span>
			</div>
			<?
		}

		// Best Board
				$best_board_query = "select * from board where today='1' and messenger='0' order by total desc limit 0, 5";
				$best_board_result = mysql_query($best_board_query, $connect);

				while ($best_board_row = mysql_fetch_array($best_board_result)){
						// Comment Load (Best)
						$best_comment_count = mysql_query("select count(*) from comment where bid='$best_board_row[id]'", $connect);
						$best_comment_row = mysql_fetch_row($best_comment_count);
						$best_comment_total = $best_comment_row[0];

			if ($best_board_row[type] == "image"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$best_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($best_board_row[title])?>"><img src="upload/thumbnail/<?=$best_board_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
			if ($best_board_row[type] == "video"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$best_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($best_board_row[title])?>"><img src="<?=$best_board_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
				?>
				<div class="title">
				<a href="read.php?page=<?=$best_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($best_board_row[title])?>"><?=htmlspecialchars($best_board_row[title])?></a>
			</div>
				<div class="user">
				작성자&nbsp;<span><font color="#7195af"><?=$best_board_row[name]?></font></span>
				<a href="search.php?no=0&amp;type=name&amp;search=<?=$best_board_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
				<?
				if ($best_board_row[type] == "video"){
					?>
					<img src="images/youtube_play.jpg" width="12" height="9" alt="비디오" border="0" />
					<?
				}
				?>
			</div>
				<div class="view">
				조회수&nbsp;<span><?=$best_board_row[view]?></span>&nbsp;&nbsp;|&nbsp;&nbsp;댓글&nbsp;<span><?=$best_comment_total?></span>
			</div>
				<?
		}
		?>
		<!-- 사이드바 -->
		<img src="images/side_bar.jpg" usemap="#sidebar" alt="사이드바" border="0" />
		<map name="sidebar" id="sidebar">
		<area shape="rect" coords="0, 30, 140, 0" href="member.php" alt="회원가입" />
		<area shape="rect" coords="140, 30, 280, 0" href="upload.php" alt="파일올리기" />
		<area shape="rect" coords="280, 30, 420, 0" href="user.php" alt="스티커설정" />
		</map>
	</div>
	<!-- 메인화면 footer -->
		<div id="footer">
		<img src="images/logo.png" border="0" align="middle" width="150" height="50" alt="위니플" /><br />
				<a href="author.php">제작자 &amp; 가이드</a>&nbsp;|&nbsp;<font color="#626262">&copy; WinyPle ALL RIGHTS RESERVED.</font>
	</div>
</div>
</body>
</html>