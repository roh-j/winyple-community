<?
include "db_info.php";

// 초기값
if (empty($_GET[page]) || $_GET[page] < "0" ){
	$_GET[page] = "0";
}
if (empty($_GET[type])){
	$_GET[type] = "subject";
}
// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 어드민 정보 가져오기
$admin_info_query = "select * from user where name='admin'";
$admin_info_result = mysql_query($admin_info_query, $connect);
$admin_info_row = mysql_fetch_array($admin_info_result);

// 친구 권한 확인
$friend_query = "select count(*) from friends where name='$_COOKIE[user]' and mate='$_GET[search]' and identify='2'";
$friend_result = mysql_query($friend_query, $connect);
$friend_row = mysql_fetch_row($friend_result);

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

// 게시물 알림 확인
if ($_GET[type] == "name" && $_COOKIE[sid] == $user_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
	// 친구 검색
		$board_friends_confirm_query = "select count(*) from friends where mate='$_GET[search]' and name='$_COOKIE[user]' and identify='2'";
		$board_friends_confirm_result = mysql_query($board_friends_confirm_query, $connect);
		$board_friends_confirm_row = mysql_fetch_row($board_friends_confirm_result);

	// 정보 업데이트
		if ($board_friends_confirm_row[0] > "0"){
		// 게시물 알림 확인
				$board_update_query = "update friends set boardup='0' where mate='$_GET[search]' and name='$_COOKIE[user]'";
				$board_update_result = mysql_query($board_update_query, $connect);

		// 친밀도 계산
		$friends_intimacy_query = "update friends set view= view+1 where mate='$_GET[search]' and name='$_COOKIE[user]'";
		$friends_intimacy_result = mysql_query($friends_intimacy_query, $connect);
		}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
<head>
<title> <?=$_GET[search]?> - WinyPle ! :: Search </title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<link href="global.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
//<![CDATA[
var view_user = "<?=$_COOKIE[user]?>";
var friend_confirm = "<?=$friend_row[0]?>";
var search_value = "<?=$_GET[search]?>";
var search_type = "<?=$_GET[type]?>";
var advertise_info = "<?=$advertise_user_info_row[name]?>";

function login_check(type){
	if (type == "id" && document.login_form.userid.value == "아이디") document.login_form.userid.value = "";
	else if (type == "password" && document.login_form.userpasswd.value == "비밀번호") document.login_form.userpasswd.value = "";
	else if (type == "id_return" && document.login_form.userid.value == "") document.login_form.userid.value = "아이디";
	else if (type == "password_return" && document.login_form.userpasswd.value == "") document.login_form.userpasswd.value = "비밀번호";
}
function search_check(){
		if (document.search_form.search.value == ""){
				alert("검색어를 입력해 주셔야죠! 성격도 급하셔라~ ^^");
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
				alert("아이디 또는 비밀번호가 잘못되었네요! 천천히~");
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
function member_sign(){
	alert("회원가입하시려구요? 좋은 생각이예요!");
	$(document.location = "member.php");
}
function contents_upload(){
	alert("어떤 자료를 올리실지 궁금하네요!");
	$(document.location = "upload.php");
}
$(document).ready(function(){
	if (search_value == advertise_info && search_type == "name"){
		$("#message_pop").slideDown(1500).text(search_value+"님의 인상적인 광고입니다!").delay(2000).slideUp(1500);
	}
	else if (view_user != "" && friend_confirm == "1" && search_type == "name"){
		$("#message_pop").slideDown(1500).text("친구가 올린 게시물과 톡을 확인해보세요!").delay(2000).slideUp(1500);
	}
	else if (view_user == search_value && search_type == "name"){
		$("#message_pop").slideDown(1500).text(search_value+"님이 올려주신 자료예요!").delay(2000).slideUp(1500);
	}
	else{
		$("#message_pop").slideDown(1500).text("원하시는 검색결과가 있나요?").delay(2000).slideUp(1500);
	}
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
<div id="message_pop"></div>
<div id="wrap">
	<!-- 위니플 로고 -->
	<div id="main_logo">
		<h1><a href="/"><img src="images/logo.png" border="0" alt="위니플" id="logo" /></a></h1>
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
	<div id="header_call_winyple"></div>
	<!-- 검색결과 좌측 -->
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
				<div class="board_header"><h2>검색 결과</h2></div>
		<div class="board_view_info">검색 결과입니다.</div>
				<?
				// 한 페이지에 보여질 게시물
				$page_size = "10";
				$page_list_size = "8";

				// 검색 처리
				switch ($_GET[type]){
						case "name":
						if ($friend_row[0] > "0" || $_COOKIE[user] == $_GET[search] ||  $_COOKIE[user] == "admin" && $_COOKIE[sid] == $admin_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
				// 친구 게시물 검색 결과 가져오기
								$search_board_info_query = "select * from board where name='$_GET[search]' order by id desc limit $_GET[page], $page_size";
								$search_board_info_result = mysql_query($search_board_info_query, $connect);
								$page_count_query = "select count(*) from board where name='$_GET[search]'";
								$page_count_result = mysql_query($page_count_query, $connect);
								$total_page_count = mysql_fetch_row($page_count_result);
								break;
						}
						else{
				// 게시물 검색 가져오기
								$search_board_info_query = "select * from board where messenger='0' and name='$_GET[search]' order by id desc limit $_GET[page], $page_size";
								$search_board_info_result = mysql_query($search_board_info_query, $connect);
								$page_count_query = "select count(*) from board where messenger='0' and name='$_GET[search]'";
								$page_count_result = mysql_query($page_count_query, $connect);
								$total_page_count = mysql_fetch_row($page_count_result);
								break;
						}
						default:
			// 게시물 제목 검색 가져오기
						$search_board_info_query = "select * from board where messenger='0' and title like '%$_GET[search]%' order by id desc limit $_GET[page], $page_size";
						$search_board_info_result = mysql_query($search_board_info_query, $connect);
						$page_count_query = "select count(*) from board where messenger='0' and title like '%$_GET[search]%'";
						$page_count_result = mysql_query($page_count_query, $connect);
						$total_page_count = mysql_fetch_row($page_count_result);
						break;
				}
				// 총 페이지
				if ($total_page_count[0] <= "0") $total_page_count[0] = "0";
				$total_page = ceil($total_page_count[0] / $page_size);

				// 현재 페이지
				$current_page = ceil(($_GET[page] + 1)/$page_size);
				$start_page = floor(($current_page - 1) / $page_list_size) * $page_list_size + 1;
				$end_page = $start_page + $page_list_size - 1;

				if ($total_page < $end_page) $end_page = $total_page;
				if ($total_page_count[0] == "0"){
			?>
			<div id="frame_result">검색 결과가 없습니다.</div>
			<?
		}
		while ($search_board_info_row = mysql_fetch_array($search_board_info_result)){
			// Comment Load (Search)
			$search_board_comment_count = mysql_query("select count(*) from comment where bid='$search_board_info_row[id]'", $connect);
			$search_board_comment_row = mysql_fetch_row($search_board_comment_count);
			$search_board_comment_total = $search_board_comment_row[0];

			if ($search_board_info_row[type] == "image"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$search_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($search_board_info_row[title])?>"><img src="upload/thumbnail/<?=$search_board_info_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
			if ($search_board_info_row[type] == "video"){
				?>
				<div class="image">
					<a href="read.php?page=<?=$search_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($search_board_info_row[title])?>"><img src="<?=$search_board_info_row[img]?>" alt="" border="0" /></a>
				</div>
				<?
			}
			?>
			<div class="title">
				<a href="read.php?page=<?=$search_board_info_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($search_board_info_row[title])?>"><?=htmlspecialchars($search_board_info_row[title])?></a>
			</div>
			<div class="user">
				작성자&nbsp;<span><font color="#7195af"><?=$search_board_info_row[name]?></font></span>
				<a href="search.php?page=0&amp;type=name&amp;search=<?=$search_board_info_row[name]?>"><img src="images/user_file_search.jpg" border="0" width="9" height="9" alt="검색" /></a>
				<?
				if ($search_board_info_row[type] == "video"){
					?>
					<img src="images/youtube_play.jpg" border="0" width="12" height="9" alt="비디오" />
					<?
				}
				?>
			</div>
			<div class="view">
				조회수&nbsp;<span><?=$search_board_info_row[view]?></span>&nbsp;&nbsp;|&nbsp;
				<?
				if ($search_board_info_row[messenger] == "1"){
					echo "Talk";
				}
				else{
					echo "댓글";
				}
				?>
				<span><?=$search_board_comment_total?></span>
			</div>
			<?
		}
		if ($total_page_count[0] != "0"){
			?>
			<div class="page">
			<?
			$current_page_color = ($_GET[page] / $page_size) + 1;
			if ($start_page >= $page_list_size){
				$prev_list = ($start_page - 2) * $page_size;
				?>
				<div class="page_select">
					<a href="search.php?page=<?=$prev_list?>&amp;type=<?=$_GET[type]?>&amp;search=<?=$_GET[search]?>">이전</a>
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
						<a href="search.php?page=<?=$page?>&amp;type=<?=$_GET[type]?>&amp;search=<?=$_GET[search]?>"><?=$i?></a>
					</div>
					<?
				}
			}
			if ($total_page > $end_page){
				$next_list = $end_page * $page_size;
				?>
				<div class="page_select">
					<a href="search.php?page=<?=$next_list?>&amp;type=<?=$_GET[type]?>&amp;search=<?=$_GET[search]?>">다음</a>
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
		<div id="main_information"><img src="images/introduce.jpg" alt="" border="0" /></div>
		<?
		if ($total_page_count[0] == "0"){
			?>
			<div class="board_header"><h2>Best Top 5</h2></div>
			<div class="board_view_info">인기 있는 게시물 입니다.</div>
			<?
		}
		else{
			?>
			<div class="board_header"><h2>추천 게시물</h2></div>
			<div class="board_view_info">검색 결과 중 추천하는 게시물 입니다.</div>
			<?
		}
				// 추천 게시물 Load
				switch ($_GET[type]){
						case "name":
				if ($friend_row[0] > "0" || $_COOKIE[user] == $_GET[search] || $_COOKIE[user] == "admin" && $_COOKIE[sid] == $admin_info_row[passwd] && !empty($_COOKIE[sid]) && !empty($_COOKIE[user])){
					$recommend_board_query = "select * from board where name='$_GET[search]' order by total desc limit 0, 5";
					$recommend_board_result = mysql_query($recommend_board_query, $connect);
					break;
				}
				else{
					$recommend_board_query = "select * from board where messenger='0' and name='$_GET[search]' order by total desc limit 0, 5";
					$recommend_board_result = mysql_query($recommend_board_query, $connect);
					break;
				}
						default:
				$recommend_board_query = "select * from board where messenger='0' and title like '%$_GET[search]%' order by total desc limit 0, 5";
				$recommend_board_result = mysql_query($recommend_board_query, $connect);
						break;
				}
				if ($total_page_count[0] == "0"){
			$best_board_query = "select * from board where today='1' and messenger='0' order by total desc limit 0, 5";
			$best_board_result = mysql_query($best_board_query, $connect);

			// AD 게시물
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
					<a href="search.php?page=0&amp;type=name&amp;search=<?=$advertise_board_info_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
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
			// 베스트 게시물 Load
			while ($best_board_row = mysql_fetch_array($best_board_result)){
				// Comment Load (Best)
				$best_comment_count = mysql_query("select count(*) from comment where bid='$best_info_row[id]'", $connect);
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
					<a href="search.php?page=0&amp;type=name&amp;search=<?=$best_board_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
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
		}
		else{
			// AD Load
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
					<a href="search.php?page=0&amp;type=name&amp;search=<?=$advertise_board_info_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
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
			while ($recommend_board_row = mysql_fetch_array($recommend_board_result)){
				// Comment Load (Recommend)
				$recommend_comment_count = mysql_query("select count(*) from comment where bid='$recommend_board_row[id]'", $connect);
				$recommend_comment_row = mysql_fetch_row($recommend_comment_count);
				$recommend_total_comment = $recommend_comment_row[0];

				if ($recommend_board_row[type] == "image"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$recommend_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($recommend_board_row[title])?>"><img src="upload/thumbnail/<?=$recommend_board_row[img]?>" alt="" border="0" /></a>
					</div>
					<?
				}
				if ($recommend_board_row[type] == "video"){
					?>
					<div class="image">
						<a href="read.php?page=<?=$recommend_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($recommend_board_row[title])?>"><img src="<?=$recommend_board_row[img]?>" alt="" border="0" /></a>
					</div>
					<?
				}
				?>
				<div class="title">
					<a href="read.php?page=<?=$recommend_board_row[id]?>&amp;no=0&amp;sticker=0&amp;friendtalk=0" title="<?=htmlspecialchars($recommend_board_row[title])?>"><?=htmlspecialchars($recommend_board_row[title])?></a>
				</div>
				<div class="user">
					작성자&nbsp;<span><?=$recommend_board_row[name]?></span>
					<a href="search.php?page=0&amp;type=name&amp;search=<?=$recommend_board_row[name]?>"><img src="images/user_file_search.jpg" width="9" height="9" alt="검색" border="0" /></a>
					<?
					if ($recommend_board_row[type] == "video"){
						?>
						<img src="images/youtube_play.jpg" width="12" height="9" alt="비디오" border="0" />
						<?
					}
					?>
				</div>
				<div class="view">
					조회수&nbsp;<span><?=$recommend_board_row[view]?></span>&nbsp;&nbsp;|&nbsp;
					<?
					if ($recommend_board_row[messenger] == "1"){
						echo "Talk";
					}
					else{
						echo "댓글";
					}
					?>
					<span><?=$recommend_total_comment?></span>
				</div>
				<?
			}
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