<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[upload_check])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 유저 정보 가져오기
$user_info_query = "select * from user where name='$_COOKIE[user]'";
$user_info_result = mysql_query($user_info_query, $connect);
$user_info_row = mysql_fetch_array($user_info_result);

// 커스텀 이미지 공유
if (!empty($_POST[custom_image_query])){
	if(empty($_COOKIE[sid]) || empty($_COOKIE[user]) || $_COOKIE[sid] != $user_info_row[passwd]){
		echo "login_fail";
	}
	else{
		$custom_image_query = "update user set user_background='$_POST[custom_image_query]' where name='$_COOKIE[user]'";
		$custom_image_result = mysql_query($custom_image_query, $connect);
	}
	exit;
}
// 입력정보
$title_value = trim($_POST[title]);
$youtube_view_source = strip_tags($_POST[video_src]);
$artist_value = trim($_POST[upload_artist]);

// 제목을 입력하지 않았을 때
if (empty($title_value)){
	echo "<script type='text/javascript'>
	window.alert('제목을 입력해주셔야죠!');
	location.replace('upload.php');
	</script>";
	exit;
}
// 제목 58Byte를 초과 하였을 때
if (strlen($title_value) > "58"){
	echo "<script type='text/javascript'>
	window.alert('제목은 58 Byte를 넘길 수 없다구요!');
	location.replace('upload.php');
	</script>";
	exit;
}
// 내용 1600Byte를 초과 하였을 때
if (strlen($_POST[text]) > "1600"){
	echo "<script type='text/javascript'>
	window.alert('내용을 1600 Byte 이하로 입력해주세요! 너무 길어요~');
	location.replace('upload.php');
	</script>";
	exit;
}
// 업로드 타입이 없을 경우
if (empty($_POST[type])){
	echo "<script type='text/javascript'>
	window.alert('업로드 타입을 선택하세요!');
	location.replace('upload.php');
	</script>";
	exit;
}
// 파일을 선택하지 않았을 때
if (empty($_FILES["img_file"]["name"]) && $_POST[type] == "image"){
	echo "<script type='text/javascript'>
	window.alert('파일을 선택해 주셔야죠!');
	location.replace('upload.php');
	</script>";
	exit;
}
// 아티스트 필터링
if (strlen($artist_value) > "20"){
	echo "<script type='text/javascript'>
	window.alert('아티스트 이름이 너무 긴데요..?');
	location.replace('upload.php');
	</script>";
	exit;
}
// 파일 확장자
$name = trim($_FILES["img_file"]["name"]);
$img_name = array(".jpg", ".png", ".gif");
$str = strlen($name) - 4;
$check_name = strtolower(substr($name, $str, 4));
$cut_name = str_replace($img_name, "", $name);
$board_id = time();

// 아이디 & 비밀번호 검색
$user_query = "select * from user where name='$_COOKIE[user]'";
$user_result = mysql_query($user_query, $connect);
$user_row = mysql_fetch_array($user_result);

// 아티스트 정보
$artist_info_query = "select count(*) from artist where name='$_COOKIE[user]' and people='$artist_value'";
$artist_info_result = mysql_query($artist_info_query, $connect);
$artist_info_row = mysql_fetch_row($artist_info_result);

// 파일 이름명 해시값으로 변환
$string = md5($cut_name);
$img_source = "{$string}_{$board_id}{$check_name}";

// 이미지 처리
if ($_POST[type] == "image"){
	if ($check_name == $img_name[0] || $check_name == $img_name[1] || $check_name == $img_name[2]){
		// 이미지 임시 저장
		move_uploaded_file($_FILES["img_file"]["tmp_name"], "upload/temporary/".$img_source);
		$original_path = "upload/temporary/$img_source";
		$unlink_img = "upload/temporary/$img_source";
		$img_info = getimagesize($original_path);

		// 이미지 썸네일 생성
		if ($img_info[0] > "500"){
			$resize_rate = 500 / $img_info[0];
			$resize_height = round($resize_rate*$img_info[1]);
			$resize_img = imagecreatetruecolor(500, $resize_height);
			if ($img_info[2] == "1"){
				$original_resize_img = imagecreatefromgif($original_path);
			}
			else if ($img_info[2] == "2"){
				$original_resize_img = imagecreatefromjpeg($original_path);
			}
			else if ($img_info[2] == "3"){
				$original_resize_img = imagecreatefrompng($original_path);
			}
			imagecopyresampled($resize_img, $original_resize_img, 0, 0, 0, 0, 500, $resize_height, $img_info[0], $img_info[1]);
			$save_resize_path = "upload/images/$img_source";
			if ($img_info[2] == "1"){
				imagegif($resize_img, $save_resize_path);
			}
			else if ($img_info[2] == "2"){
				imagejpeg($resize_img, $save_resize_path);
			}
			else if ($img_info[2] == "3"){
				imagepng($resize_img, $save_resize_path);
			}
			unlink($unlink_img);
		}
		else{
			$copy_img_original_source = "upload/temporary/$img_source";
			$copy_img_source = "upload/images/$img_source";
			copy($copy_img_original_source, $copy_img_source);
			unlink($unlink_img);
		}
		$original_path = "upload/images/$img_source";
		$img_info = getimagesize($original_path);
		$thumbnail_resize_rate = 90/$img_info[1];
		$thumbnail_resize_width = round($thumbnail_resize_rate*$img_info[0]);
		if ($thumbnail_resize_width < "60"){
			$new_img = imagecreatetruecolor(60, 90);
			$width="60";
			$height="90";
		}
		else if ($img_info[0] == $img_info[1]){
			$new_img = imagecreatetruecolor(90, 90);
			$width="90";
			$height="90";
		}
		else if ("130" < $thumbnail_resize_width){
			$new_img = imagecreatetruecolor(130, 90);
			$width="130";
			$height="90";
		}
		else if ("59" < $thumbnail_resize_width && $thumbnail_resize_width < "131"){
			$new_img = imagecreatetruecolor($thumbnail_resize_width, 90);
			$width="$thumbnail_resize_width";
			$height="90";
		}
		if ($img_info[2] == "1"){
			$original_img = imagecreatefromgif($original_path);
		}
		else if ($img_info[2] == "2"){
			$original_img = imagecreatefromjpeg($original_path);
		}
		else if ($img_info[2] == "3"){
			$original_img = imagecreatefrompng($original_path);
		}
		imagecopyresampled($new_img, $original_img, 0, 0, 0, 0, $width, $height, $img_info[0], $img_info[1]);
		$save_path = "upload/thumbnail/$img_source";
		if ($img_info[2] == "1"){
			imagegif($new_img, $save_path);
		}
		else if ($img_info[2] == "2"){
			imagejpeg($new_img, $save_path);
		}
		else if ($img_info[2] == "3"){
			imagepng($new_img, $save_path);
		}
	}
	else{
		echo "<script type='text/javascript'>
		window.alert('JPG GIF PNG 확장자의 이미지만 업로드해주세요! 다른 것은 안받아요!');
		location.replace('upload.php');
		</script>";
		exit;
	}
}
// 동영상 처리
if ($_POST[type] == "video"){
	if (strpos($youtube_view_source, "http://youtu.be/") == "true"){
		// 유투브 소스 처리
		$video_source_check = strstr($youtube_view_source, "?");
		$video_alter_source = str_replace($video_source_check, "", $youtube_view_source);
		$youtube_source_query = "[{$video_alter_source}]";
		$youtube_alter_query = "/\[http:\/\/youtu\.be\/([A-Za-z0-9_-]{11,11})\]/";

		// 유투브 결과
		$youtube_image_source = preg_replace($youtube_alter_query, "http://img.youtube.com/vi/$1/default.jpg", $youtube_source_query);
		$youtube_video_source = preg_replace($youtube_alter_query, "<iframe id=\"contents_view\" width=\"500\" height=\"280\" src=\"http://www.youtube.com/embed/$1?wmode=opaque\" frameborder=\"0\"></iframe>", $youtube_source_query);
	}
	else{
		echo "<script type='text/javascript'>
		window.alert('동영상 주소를 확인해 주세요. 주소가 이상하네요!');
		location.replace('upload.php');
		</script>";
		exit;
	}
}
// 배경 이미지 처리
switch ($_POST[background]){
	case "default":
	$bg_img_source = "default.jpg";
	break;
	case "mint":
	$bg_img_source = "mint.jpg";
	break;
	case "love":
	$bg_img_source = "love.jpg";
	break;
	case "gray":
	$bg_img_source = "gray.jpg";
	break;
	case "user_background_image":
	if (file_exists("upload/background/$user_row[user_background]")){
		$bg_img_source = "$user_row[user_background]";
	}
	else{
		echo "<script type='text/javascript'>
		window.alert('커스텀 이미지가 존재하지 않네요. 기본으로 설정할께요!');
		</script>";
		$bg_img_source = "default.jpg";
	}
	break;
	case "custom":
	if (empty($_FILES["background_file"]["name"])){
		echo "<script type='text/javascript'>
		window.alert('배경으로 올려주신 파일이 없네요. 기본으로 설정할께요!');
		</script>";
		$bg_img_source = "default.jpg";
	}
	else{
		$bg_name = trim($_FILES["background_file"]["name"]);
		$bg_str = strlen($bg_name) - 4;
		$bg_check_name = strtolower(substr($bg_name, $bg_str, 4));
		$bg_cut_name = str_replace($img_name, "", $bg_name);
		$bg_string = md5($bg_cut_name);
		$bg_img_source = "{$bg_string}_{$board_id}{$bg_check_name}";

		if ($bg_check_name == $img_name[0] || $bg_check_name == $img_name[1] || $bg_check_name == $img_name[2]){
			// 배경이미지 임시저장
			move_uploaded_file($_FILES["background_file"]["tmp_name"], "upload/temporary/".$bg_img_source);
			$bg_original_path = "upload/temporary/$bg_img_source";
			$bg_unlink_img = "upload/temporary/$bg_img_source";
			$bg_img_info = getimagesize($bg_original_path);

			// 배경이미지 썸네일 생성
			if ($bg_img_info[0] > "680"){
				$bg_resize_rate = 680 / $bg_img_info[0];
				// 이미지 자동 맞춤 판단
				$bg_img_height = round($bg_resize_rate*$bg_img_info[1]);
				if ($_POST["image_size"] == "auto_size" && $bg_img_height < "400"){
					$bg_resize_height = "400";
				}
				else{
					$bg_resize_height = round($bg_resize_rate*$bg_img_info[1]);
				}
				$bg_resize_img = imagecreatetruecolor(680, $bg_resize_height);
				if ($bg_img_info[2] == "1"){
					$bg_original_resize_img = imagecreatefromgif($bg_original_path);
				}
				else if ($bg_img_info[2] == "2"){
					$bg_original_resize_img = imagecreatefromjpeg($bg_original_path);
				}
				else if ($bg_img_info[2] == "3"){
					$bg_original_resize_img = imagecreatefrompng($bg_original_path);
				}
				imagecopyresampled($bg_resize_img, $bg_original_resize_img, 0, 0, 0, 0, 680, $bg_resize_height, $bg_img_info[0], $bg_img_info[1]);
				$bg_save_resize_path = "upload/background/$bg_img_source";
				if ($bg_img_info[2] == "1"){
					imagegif($bg_resize_img, $bg_save_resize_path);
				}
				else if ($bg_img_info[2] == "2"){
					imagejpeg($bg_resize_img, $bg_save_resize_path);
				}
				else if ($bg_img_info[2] == "3"){
					imagepng($bg_resize_img, $bg_save_resize_path);
				}
				unlink($bg_unlink_img);
			}
			else{
				$copy_bgimg_original_source = "upload/temporary/$bg_img_source";
				$copy_bgimg_source = "upload/background/$bg_img_source";
				copy($copy_bgimg_original_source, $copy_bgimg_source);
				unlink($bg_unlink_img);
			}
		}
		else{
			echo "<script type='text/javascript'>
			window.alert('배경으로 올려주신 파일이 이상해요. 기본으로 설정할께요!');
			</script>";
			$bg_img_source = "default.jpg";
		}
	}
	break;
	default:
	$bg_img_source = "default.jpg";
	break;
}
if ($_POST[type] == "video"){
	// 비디오 타입 게시물 등록
	$board_query = "insert into board (id, name, title, contents, wdate, view, img, bgimg, ip, total, today, commentupdate, stickerupdate, messenger, video, type, artist, feel_good, feel_soso, feel_bad, feel_view, feel_day, feel_type) values ('', '$user_row[name]', '$title_value', '$_POST[contents]', now(), '0', '$youtube_image_source', '$bg_img_source', '$_SERVER[REMOTE_ADDR]', '0', '1', '0', '0', '0', '$video_alter_source', 'video', '$artist_value', '0', '0', '0', '0', '', '')";
	$board_result = mysql_query($board_query, $connect);
}
else{
	// 이미지 타입 게시물 등록
	$board_query = "insert into board (id, name, title, contents, wdate, view, img, bgimg, ip, total, today, commentupdate, stickerupdate, messenger, video, type, artist, feel_good, feel_soso, feel_bad, feel_view, feel_day, feel_type) values ('', '$user_row[name]', '$title_value', '$_POST[contents]', now(), '0', '$img_source', '$bg_img_source', '$_SERVER[REMOTE_ADDR]', '0', '1', '0', '0', '0', '', 'image', '$artist_value', '0', '0', '0', '0', '', '')";
	$board_result = mysql_query($board_query, $connect);
}
// 아티스트 처리
if (!empty($artist_value) && $artist_info_row[0] == "0"){
	$artist_insert_query = "insert into artist (id, name, people) values ('', '$user_row[name]', '$artist_value')";
	$artist_insert_result = mysql_query($artist_insert_query, $connect);
}
// 프랜드톡 알림 등록
$social_query = "select * from board where name='$_COOKIE[user]' order by id desc limit 0, 1";
$social_result = mysql_query($social_query, $connect);
$social_row = mysql_fetch_array($social_result);
$friendtalk_query = "insert into talkupdate (id, bid, name, talkup) values ('', '$social_row[id]', '$_COOKIE[user]', '0')";
$friendtalk_result = mysql_query($friendtalk_query, $connect);

// 게시물 알림 등록
$board_update_query = "update friends set boardup='1' where mate='$_COOKIE[user]' and identify='2'";
$board_update_result = mysql_query($board_update_query, $connect);

echo "<script type='text/javascript'>
window.alert('인상적입니다!');
location.replace('index.php');
</script>";

mysql_close($connect);
?>