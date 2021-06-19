<?
header("Content-Type: text/plain");
header("Content-Type: text/html; charset=euc-kr");
include "db_info.php";

// 값 태그 삭제
$id_value = iconv("UTF-8", "CP949", rawurldecode(strip_tags($_POST[member_id])));
$email_value = iconv("UTF-8", "CP949", rawurldecode(strip_tags($_POST[member_email])));

// 아이디 중복 검색
$query = "select * from user where name ='$id_value'";
$result = mysql_query($query, $connect);
$row = mysql_fetch_array($result);

// 회원정보 입력 확인
$id_check = strlen($id_value);
$id_word_check = strstr($id_value, " ");
$passwd_check = strlen($_POST[member_passwd]);
$email_check = strstr($email_value, "@");
$email_word_check = strstr($email_value, " ");
$id_string_check = array("관리", "관리자", "운영자", "운영팀", "위니플", "어드민", "운영", "winyple", "admin", "administrator");

// 잘못된 경로로 접근하였을 때
if (empty($_POST[member_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
if(!ereg("^[a-zA-Z0-9]+$", $id_value)){
   echo "id_korean_error";
   exit();
}
// 아이디 필터링
if ($id_check < "5" || $id_check > "10"){
	echo "id_count_error";
	exit;
}
// 아이디에 공백이 있을 경우
if (!empty($id_word_check)){
	echo "id_blank_error";
	exit;
}
// 생성할 수 없는 아이디 입력시
if (in_array($id_value, $id_string_check)){
	echo "id_query_error";
	exit;
}
// 같은 아이디가 존재할 때
if ($row[name] == $id_value){
	echo "id_same_query_error";
	exit;
}
// 비밀번호 필터링
if ($passwd_check < "8" || $passwd_check > "19"){
	echo "passwd_count_error";
	exit;
}
// 비밀번호 확인
if ($_POST[member_passwd] != $_POST[member_passwd_check]){
	echo "passwd_check_error";
	exit;
}
// 이메일에 공백이 있을 경우
if (!empty($email_word_check)){
	echo "email_blank_error";
	exit;
}
// 잘못된 이메일 형식 일 때
if (empty($email_check)){
	echo "email_check_error";
	exit;
}
// 비밀번호 암호화
$passwd_value = md5($_POST[member_passwd]);

// 회원가입 입력
$member_query = "insert into user (id, name, passwd, email, ip, original, identify, view_id, view_total, view_like, user_background, advertise, today_feel, feel_day) values ('', '$id_value', '$passwd_value', '$email_value', '$_SERVER[REMOTE_ADDR]', '', '1', '', '0', '', '', '0', '', '')";
$member_result = mysql_query ($member_query);

// Talk Messenger 등록
$board_query = "insert into board (id, name, title, contents, wdate, view, img, bgimg, ip, total, today, commentupdate, stickerupdate, messenger, video, type, artist, feel_good, feel_soso, feel_bad, feel_view, feel_day, feel_type) values ('', '$id_value', 'Talk Messenger', '', now(), '0', 'messenger.png', 'default.jpg', '$_SERVER[REMOTE_ADDR]', '0', '1', '0', '0', '1', '', 'image', '', '0', '0', '0', '0', '', '')";
$board_result = mysql_query($board_query, $connect);

// 프랜드톡 알림 등록
$social_query = "select * from board where name='$id_value' order by id desc limit 0, 1";
$social_result = mysql_query($social_query, $connect);
$social_row = mysql_fetch_array($social_result);
$friendtalk_query = "insert into talkupdate (id, bid, name, talkup) values ('', '$social_row[id]', '$id_value', '0')";
$friendtalk_result = mysql_query($friendtalk_query, $connect);

mysql_close($connect);
?>