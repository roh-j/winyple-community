<?
include "db_info.php";

// 잘못된 경로로 접근하였을 때
if (empty($_POST[modify_check]) && empty($_POST[type])){
	echo "<script type='text/javascript'>
	window.alert('잘못된 접근입니다.');
	location.replace('index.php');
	</script>";
	exit;
}
// 입력 Value
$name_value = strip_tags($_POST[name]);
$original_length = strlen($name_value);
$name_word_check = strstr($name_value, " ");
$source_check_array = array("name", "sticker", "update", "feel");

if (in_array($_POST[type], $source_check_array)){
	if ($_POST[type] == "feel"){
		$today = getdate();
		$today_value = $today[year] + $today[mon] + $today[mday];
		$feel_update_query = "update user set today_feel='$_POST[feel_type]', feel_day='$today_value' where name='$_COOKIE[user]'";
		$feel_update_result = mysql_query($feel_update_query);
		echo "feel_update";
		exit;
	}
	// choose가 name이고 전달된 값이 없을때
	if (empty($name_value) && $_POST[type] == "name"){
		echo "<script type='text/javascript'>
		window.alert('이름을 입력하셔야죠!');
		location.replace('user.php');
		</script>";
		exit;
	}
	// 스티커 수정 or 등록 썸네일 처리
	if ($_POST[type] == "update" || $_POST[type] == "sticker"){
		// 이미지 파일이 선택되지 않았을 때
		if (empty($_FILES["sticker_file"]["name"])){
			echo "<script type='text/javascript'>
			window.alert('파일을 선택해주세요.');
			location.replace('user.php');
			</script>";
			exit;
		}
		// 파일 확장자
		$name = trim($_FILES["sticker_file"]["name"]);
		$img_name = array(".jpg", ".png", ".gif");
		$str = strlen($name) - 4;
		$check_name = strtolower(substr($name, $str, 4));
		$cut_name = str_replace($img_name, "", $name);
		$sticker_id = time();

		// 파일 이름명 해시값으로 변환
		$string = md5($cut_name);
		$img_source = "{$string}_{$sticker_id}{$check_name}";

		// choose가 update 일때 스티커 이미지 삭제
		if ($_POST[type] == "update" && $check_name == $img_name[0] || $check_name == $img_name[1] || $check_name == $img_name[2]){
			if ($check_name == $img_name[0] || $check_name == $img_name[1] || $check_name == $img_name[2]){
				$unlink_sticker_query = "select * from user_sticker where name='$_COOKIE[user]'";
				$unlink_sticker_result = mysql_query($unlink_sticker_query, $connect);
				$unlink_sticker_row = mysql_fetch_array($unlink_sticker_result);
				$unlink_sticker_src = "upload/sticker/$unlink_sticker_row[sticker]";
				unlink($unlink_sticker_src);
			}
		}
		// 이미지 확장자일때 실행
		if ($check_name == $img_name[0] || $check_name == $img_name[1] || $check_name == $img_name[2]){
			// 이미지 임시 저장
			move_uploaded_file($_FILES["sticker_file"]["tmp_name"], "upload/temporary/".$img_source);
			$original_path = "upload/temporary/$img_source";
			$unlink_img = "upload/temporary/$img_source";
			$img_info = getimagesize($original_path);
			if ($img_info[0] > "70" || $img_info[0] < "70" || $img_info[1] > "70" || $img_info[1] < "70"){
				$resize_img = imagecreatetruecolor(70, 70);
				if ($img_info[2] == "1"){
					$original_resize_img = imagecreatefromgif($original_path);
				}
				else if ($img_info[2] == "2"){
					$original_resize_img = imagecreatefromjpeg($original_path);
				}
				else if ($img_info[2] == "3"){
					$original_resize_img = imagecreatefrompng($original_path);
				}
				imagecopyresampled($resize_img, $original_resize_img, 0, 0, 0, 0, 70, 70, $img_info[0], $img_info[1]);
				$save_resize_path = "upload/sticker/$img_source";
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
				$copy_original_source = "upload/temporary/$img_source";
				$copy_source = "upload/sticker/$img_source";
				copy($copy_original_source, $copy_source);
				unlink($unlink_img);
			}
		}
		else{
			echo "<script type='text/javascript'>
			window.alert('JPG GIF PNG 확장자의 이미지만 업로드 가능하답니다!');
			location.replace('user.php');
			</script>";
			exit;
		}
		// 스티커 이미지 업데이트 선택 시
		if ($_POST[type] == "update"){
			$sticker_modify_query = "update user_sticker set sticker='$img_source', wdate=now() where name='$_COOKIE[user]'";
			$sticker_modify_result = mysql_query($sticker_modify_query, $connect);

			// 추천한 전체 게시물 이미지 수정
			$sticker_board_modify_query = "update board_sticker set sticker='$img_source' where name='$_COOKIE[user]'";
			$sticker_board_modify_result = mysql_query($sticker_board_modify_query, $connect);

			echo "<script type='text/javascript'>
			window.alert('더 멋진 스티커네요!');
			location.replace('user.php');
			</script>";
			exit;
		}
		// 스티커 이미지 업로드 선택 시
		if ($_POST[type] == "sticker"){
			$sticker_insert_query = "insert into user_sticker (id, name, sticker, wdate) values ('', '$_COOKIE[user]', '$img_source', now())";
			$sticker_insert_result = mysql_query($sticker_insert_query, $connect);

			echo "<script type='text/javascript'>
			window.alert('멋진 스티커군요!');
			location.replace('user.php');
			</script>";
			exit;
		}
	}
	// 이름 수정
	if ($_POST[type] == "name"){
		if ($original_length > "10"){
			echo "<script type='text/javascript'>
			window.alert('10 Byte 이하로 입력해 주셔야 되요.');
			location.replace('user.php');
			</script>";
			exit;
		}
		if (!empty($name_word_check)){
			echo "<script type='text/javascript'>
			window.alert('공백없이 입력!');
			location.replace('user.php');
			</script>";
			exit;
		}
		$original_query = "update user set original='$name_value' where name='$_COOKIE[user]'";
		$original_result = mysql_query($original_query, $connect);

		echo "<script type='text/javascript'>
		window.alert('이름이 등록되었습니다!');
		location.replace('user.php');
		</script>";
		exit;
	}
}
// 전달된 값이 없을때 알림 표시
else{
	echo "<script type='text/javascript'>
	window.alert('수정내용을 확인해 보세요!');
	location.replace('user.php');
	</script>";
	exit;
}
mysql_close($connect);
?>