<?php
//=======================================================
// 설	명 : 게시판 처리(ok.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/01/03
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/03/06 박선민 delete_ok() 버그 수정
// 03/11/13 박선민 마지막 수정
// 03/12/08 박선민 추가 필드, userGetAppendFields()
// 04/01/03 박선민 심각한 간단 버그수정
// 25/08/14 Gemini AI PHP 7+ 호환성, 함수 호출 방식 등 수정
//=======================================================
// 앞으로 : 게시물 삭제시 메모로 삭제되도록...

// $HEADER를 하나의 배열로 만듭니다.
$HEADER = array(
	'priv' => "운영자,뉴스관리자", // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useCheck' => 1, // check_email()
	'useBoard2' => 1, // 보드관련 함수 포함
	'useApp' => 1,
	'useImage' => 1, // thumbnail()
	'useClassSendmail' => 1,
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath	= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/player"; // 마지막 "/"이 빠져야함
include_once("{$_SERVER['DOCUMENT_ROOT']}/Admin_basketball/player/dbinfo.php"); // $dbinfo, $table 값 정의

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? '') .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes($_REQUEST['sc_string'] ?? '')) . //search string
			"&page=" . ($_REQUEST['page'] ?? '');

$upload_path = !empty(trim($dbinfo['upload_dir'] ?? '')) ? trim($dbinfo['upload_dir']) : dirname(__FILE__) . "/upload";
$dbinfo['upload_dir'] = $upload_path . "/{$table}";

// 공통적으로 사용할 $qs
$qs = array(
	'userid' => "post,trim",
	'passwd' => "post,trim",
	'title' => "post,trim",
	'p_name' => "post,trim,notnull=" . urlencode("이름을 입력하시기 바랍니다."),
	'p_position' => "post,trim,notnull=" . urlencode("포지션을 입력하시기 바랍니다."),
	'p_num' => "post,trim=" . urlencode("백넘버를 입력하시기 바랍니다."),
	'tid' => "post,trim,notnull=" . urlencode("소속팀을 입력하시기 바랍니다."),
	'p_gubun' => "post,trim,notnull=" . urlencode("선수구분을 입력하시기 바랍니다.")
);

$_REQUEST['goto'] = "{$thisUrl}/list.php?team=" . ($_REQUEST['tid'] ?? '');
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch ($_REQUEST['mode'] ?? '') {
	case 'write':
		$uid = write_ok($table, $qs);
		go_url($_REQUEST['goto'] ?? "{$thisUrl}/list.php?" . href_qs("team=" . ($_REQUEST['tid'] ?? ''), $qs_basic));
		break;
	case 'reply':
		$uid = reply_ok($table, $qs);
		go_url($_REQUEST['goto'] ?? "{$thisUrl}/list.php?" . href_qs("team=" . ($_REQUEST['tid'] ?? ''), $qs_basic));
		break;
	case 'modify':
		modify_ok($table, $qs, "uid");
		go_url($_REQUEST['goto'] ?? "{$thisUrl}/list.php?" . href_qs("team=" . ($_REQUEST['tid'] ?? ''), $qs_basic));
		break;
	case 'delete':
		$goto = $_REQUEST['goto'] ?? "{$thisUrl}/list.php?" . href_qs("team=" . ($_REQUEST['tid'] ?? ''), $qs_basic);
		delete_ok($table, "uid", $goto);
		go_url($goto);
		break;
	// VOTE
	case 'vote':
		$vote = vote_ok();
		back("현재 {$vote}점입니다.", $_SERVER['HTTP_REFERER'] ?? $thisUrl); // HTTP_REFERER가 없는 경우 처리

		break;	
	// 메모
	case 'memowrite':
		memoWrite_ok();
		go_url($_SERVER['HTTP_REFERER'] ?? $thisUrl); // HTTP_REFERER가 없는 경우 처리
		break;
	case 'memodelete':
		memodelete_ok();
		go_url($_SERVER['HTTP_REFERER'] ?? $thisUrl); // HTTP_REFERER가 없는 경우 처리
		break;
	default:
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch
//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
function write_ok($table, $qs){
	global $dbinfo, $db_conn;
	if (!privAuth($dbinfo, "priv_write")) {
		back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");
	}

	$qs['writeinfo'] = "post,trim";
	// 넘어온값 체크
	$qs = check_value($qs);

	if (isset($qs['docu_type']) && strtolower($qs['docu_type']) != "html") {
		$qs['docu_type'] = "text";
	}
	$qs['priv_level'] = (int)($qs['priv_level'] ?? 0);
	if (isset($qs['catelist'])) {
		$qs['cateuid'] = $qs['catelist'];
	}

	// 값 추가
	if (isset($_SESSION['seUid'])) {
		$qs['bid'] = $_SESSION['seUid'];
		switch ($dbinfo['enable_userid'] ?? '') {
			case 'name':
				$qs['userid'] = $_SESSION['seName'];
				break;
			case 'nickname':
				$qs['userid'] = $_SESSION['seNickname'];
				break;
			default:
				$qs['userid'] = $_SESSION['seUserid'];
				break;
		}
		$qs['email'] = $_SESSION['seEmail'];
	} else {
		$qs['email'] = check_email($qs['email'] ?? '');
	}
	$qs['ip'] = remote_addr();
	// - num의 최대값 구함
	$sql_where = '1';
	if (isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']) {
		$sql_where = " db='" . db_escape($dbinfo['db'] ?? '') . "' ";
	}
	
	$sql = "SELECT max(num) AS max_num FROM {$table} WHERE $sql_where ";

	$qs['num'] = (db_resultone($sql, 0, "max_num") ?? 0) + 1;

	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = '';
	if (($dbinfo['enable_upload'] ?? 'N') != 'N' && !empty($_FILES)) { // empty 체크 추가
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles = array();
		$upfiles_totalsize = 0;
		if (($dbinfo['enable_upload'] ?? '') == 'Y') {
			if (!empty($_FILES['upfile']['name'])) { // empty 체크 추가
				$upfiles['upfile'] = file_upload("upfile", $updir);
				$upfiles_totalsize = $upfiles['upfile']['size'] ?? 0;
			}
		} else {
			foreach ($_FILES as $key => $value) {
				if (!empty($value['name'])) { // empty 체크 추가
					if (($dbinfo['enable_upload'] ?? '') == 'image' && !is_array(@getimagesize($value['tmp_name']))) {
						continue;
					}
					$upfiles[$key] = file_upload($key, $updir);
					$upfiles_totalsize += ($upfiles[$key]['size'] ?? 0);
				}
			}
		}
		if (($dbinfo['enable_uploadmust'] ?? '') == 'Y' and count($upfiles) == 0) {
			back(($dbinfo['enable_upload'] ?? '') == 'image' ? "이미지파일을 선택하여 업로드하여 주시기 바랍니다" : "파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='" . db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	}
	/////////////////////////////////

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if ($fieldlist = userGetAppendFields($table, $skip_fields)) {
		foreach ($fieldlist as $value) {
			if (isset($_POST[$value])) {
				$sql_set .= ", `{$value}` = '" . db_escape($_POST[$value]) . "' ";
			}
		}
	}
	////////////////////////////////
	
	// sql문 완성
	if (($dbinfo['enable_type'] ?? '') == 'Y' and ($qs['writeinfo'] ?? '') == "info") {
		$sql_set .= ", type='info' ";
	}

	$sql = "INSERT INTO {$table} SET
				db = '" . db_escape($qs['db'] ?? ($dbinfo['db'] ?? '')) . "',
				num = '" . (int)$qs['num'] . "',
				bid = '" . (int)($qs['bid'] ?? 0) . "',
				userid = '" . db_escape($qs['userid'] ?? '') . "',
				title = '" . db_escape($qs['title'] ?? '') . "',
				rdate = UNIX_TIMESTAMP(),
				ip = '" . db_escape($qs['ip'] ?? '') . "'
				{$sql_set}
				{$sql_set_file}
			";
	
	$result = db_query($sql);
	
	$uid = db_insert_id();

	return $uid;
} // end func.

// 답변
function reply_ok($table, $qs){
	global $dbinfo, $db_conn, $table_logon;
	if (!privAuth($dbinfo, "priv_reply")) {
		back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");
	}

	$qs['uid'] = "post,trim,notnull=" . urlencode("답변할 게시물의 고유넘버가 넘어오지 않았습니다.");
	$qs['private_key'] = "post,trim,notnull=" . urlencode("답변할 게시물의 고유암호가 넘어오지 않았습니다.");
	// 넘어온값 체크
	$qs = check_value($qs);

	if (isset($qs['docu_type']) && strtolower($qs['docu_type']) != "html") {
		$qs['docu_type'] = "text";
	}
	$qs['priv_level'] = (int)($qs['priv_level'] ?? 0);

	// 정상적인 질문에 대한 답변인지 체크
	$sql = "SELECT * FROM {$table} WHERE uid='" . (int)$qs['uid'] . "' and passwd=password('" . db_escape($qs['private_key'] ?? '') . "')"; // password(rdate) -> passwd로 수정 추정
	$list = db_arrayone($sql) or back("답변할 DB가 없습니다");
	if (($list['type'] ?? '') == 'info') {
		back("공지글에는 답변글을 올리실 수 없습니다.");
	}

	$qs['num'] = $list['num'] ?? 0;
	$qs['re'] = userReplyRe($table, $list['num'] ?? 0, $list['re'] ?? ''); // re값 구하는 함수 호출
	$qs['cateuid'] = $list['cateuid'] ?? 0; // 답장 원글의 cateuid값으로 등록됨

	// 값 추가
	if (isset($_SESSION['seUid'])) {
		$qs['bid'] = $_SESSION['seUid'];
		switch ($dbinfo['enable_userid'] ?? '') {
			case 'name':
				$qs['userid'] = $_SESSION['seName'];
				break;
			case 'nickname':
				$qs['userid'] = $_SESSION['seNickname'];
				break;
			default:
				$qs['userid'] = $_SESSION['seUserid'];
				break;
		}
		$qs['email'] = $_SESSION['seEmail'];
	} else {
		$qs['email'] = check_email($qs['email'] ?? '');
	}
	$qs['ip'] = remote_addr();

	/////////////////////////////////
	// 파일업로드 처리
	$sql_set_file = '';
	if (($dbinfo['enable_upload'] ?? 'N') != 'N' && !empty($_FILES)) {
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);
		$upfiles = array();
		$upfiles_totalsize = 0;
		if (($dbinfo['enable_upload'] ?? '') == 'Y') {
			if (!empty($_FILES['upfile']['name'])) {
				$upfiles['upfile'] = file_upload("upfile", $updir);
				$upfiles_totalsize = $upfiles['upfile']['size'] ?? 0;
			}
		} else {
			foreach ($_FILES as $key => $value) {
				if (!empty($value['name'])) {
					if (($dbinfo['enable_upload'] ?? '') == 'image' && !is_array(@getimagesize($value['tmp_name']))) {
						continue;
					}
					$upfiles[$key] = file_upload($key, $updir);
					$upfiles_totalsize += ($upfiles[$key]['size'] ?? 0);
				}
			}
		}
		if (($dbinfo['enable_uploadmust'] ?? '') == 'Y' and count($upfiles) == 0) {
			back(($dbinfo['enable_upload'] ?? '') == 'image' ? "이미지파일을 선택하여 업로드하여 주시기 바랍니다" : "파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='" . db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	}
	/////////////////////////////////

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	$sql_set = '';
	if ($fieldlist = userGetAppendFields($table, $skip_fields)) {
		foreach ($fieldlist as $value) {
			if (isset($_POST[$value])) {
				$sql_set .= ", `{$value}` = '" . db_escape($_POST[$value]) . "' ";
			}
		}
	}
	////////////////////////////////

	// sql문 완성
	$sql = "INSERT INTO {$table} SET
				db = '" . db_escape($qs['db'] ?? ($dbinfo['db'] ?? '')) . "',
				num = '" . (int)$qs['num'] . "',
				re = '" . db_escape($qs['re'] ?? '') . "',
				bid = '" . (int)($qs['bid'] ?? 0) . "',
				userid = '" . db_escape($qs['userid'] ?? '') . "',
				passwd = password('" . db_escape($qs['passwd'] ?? '') . "'),
				email = '" . db_escape($qs['email'] ?? '') . "',
				title = '" . db_escape($qs['title'] ?? '') . "',
				content = '" . db_escape($qs['content'] ?? '') . "',
				docu_type = '" . db_escape($qs['docu_type'] ?? '') . "',
				rdate = UNIX_TIMESTAMP(),
				ip = '" . db_escape($qs['ip'] ?? '') . "',
				cateuid = '" . (int)($qs['cateuid'] ?? 0) . "',
				priv_level = '" . (int)($qs['priv_level'] ?? 0) . "'
				{$sql_set_file}
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	// E-Mail 전송
	if (($dbinfo['enable_adm_mail'] ?? 'N') == 'Y' or ($dbinfo['enable_rec_mail'] ?? 'N') == 'Y') {
		$mail = new mime_mail;
		$mailfrom = '';

		if (($dbinfo['enable_adm_mail'] ?? 'N') == 'Y') {
			$sql = "select email from {$table_logon} where uid='" . (int)($dbinfo['bid'] ?? 0) . "'";
			$admin_email = check_email(db_resultone($sql, 0, "email"));
			if ($admin_email) {
				$mailfrom = $admin_email;
			}
		}
		if (($dbinfo['enable_rec_mail'] ?? 'N') == 'Y') {
			$rec_email = check_email($_POST['rec_email'] ?? '');
			if ($rec_email) {
				$mailfrom .= ($mailfrom ? ',' : '') . $rec_email;
			}
		}
		if ($mailfrom && ($list['email'] ?? '')) {
			$mail->from = $mailfrom;
			$mail->name = "게시판 자동메일";
			$mail->to = $list['email'];
			$mail->subject = "[답변] {$qs['title']}";
			if (($qs['docu_type'] ?? '') == "html") {
				$mail->body = "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]<br><hr>{$qs['content']}";
				$mail->html = 1;
			} else {
				$mail->body = "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]\n--------------------------------------------\n{$qs['content']}";
				$mail->html = 0;
			}
			$mail->send();
		}
	}
	return $uid;
} // end func

function modify_ok($table, $qs, $field){
	global $dbinfo, $thisUrl;
	$qs[$field] = "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	$qs = check_value($qs);

	if (isset($qs['docu_type']) && strtolower($qs['docu_type']) != "html") {
		$qs['docu_type'] = "text";
	}
	$qs['priv_level'] = (int)($qs['priv_level'] ?? 0);

	// 수정 권한 체크와 해당 게시물 읽어오기
	$list = false;
	if (privAuth($dbinfo, "priv_delete")) {
		$sql = "SELECT * FROM {$table} WHERE uid='" . (int)$qs['uid'] . "'";
		$list = db_arrayone($sql);
	} elseif (isset($_SESSION['seUid'])) {
		$sql = "SELECT * FROM {$table} WHERE uid='" . (int)$qs['uid'] . "' and bid='" . (int)$_SESSION['seUid'] . "'";
		$list = db_arrayone($sql);
	} else {
		$sql = "SELECT * FROM {$table} WHERE uid='" . (int)$qs['uid'] . "' and passwd=password('" . db_escape($qs['passwd'] ?? '') . "')";
		$list = db_arrayone($sql);
	}

	if (!$list) {
		back("게시물이 없거나 수정할 권한이 없습니다");
	}

	// 값 추가
	if (($list['bid'] ?? 0) == ($_SESSION['seUid'] ?? 0)) {
		switch ($dbinfo['enable_userid'] ?? '') {
			case 'name':
				$qs['userid'] = $_SESSION['seName'];
				break;
			case 'nickname':
				$qs['userid'] = $_SESSION['seNickname'];
				break;
			default:
				$qs['userid'] = $_SESSION['seUserid'];
				break;
		}
		$qs['email'] = $_SESSION['seEmail'];
	} else {
		$qs['userid'] = $list['userid'] ?? '';
		$qs['email'] = isset($qs['email']) ? check_email($qs['email']) : ($list['email'] ?? '');
	}
	$qs['ip'] = remote_addr();
	$qs['cateuid'] = (isset($qs['catelist']) && strlen($list['re'] ?? '') == 0) ? $qs['catelist'] : ($list['cateuid'] ?? 0);

	///////////////////////////////
	// 파일 업로드 - 변경(03/10/20)
	///////////////////////////////
	$sql_set_file = '';
	if (($dbinfo['enable_upload'] ?? 'N') != 'N') {
		$updir = $dbinfo['upload_dir'] . "/" . (int)($list['bid'] ?? 0);
		$upfiles = @unserialize($list['upfiles'] ?? '');
		if (!is_array($upfiles)) {
			$upfiles = array();
			if (isset($list['upfiles'])) {
				$upfiles['upfile']['name'] = $list['upfiles'];
				$upfiles['upfile']['size'] = (int)($list['upfiles_totalsize'] ?? 0);
			}
		}
		$upfiles_totalsize = (int)($list['upfiles_totalsize'] ?? 0);

		if (is_array($upfiles) && count($upfiles) > 0) {
			foreach ($upfiles as $key => $value) {
				if (isset($_REQUEST["del_{$key}"])) {
					$file_path = $dbinfo['upload_dir'] . "/{$list['bid']}/{$value['name']}";
					if (!is_file($file_path)) {
						$file_path = $dbinfo['upload_dir'] . "/{$value['name']}";
					}
					if (is_file($file_path)) {
						@unlink($file_path);
						@unlink("{$file_path}.thumb.jpg");
					}
					$upfiles_totalsize -= ($value['size'] ?? 0);
					unset($upfiles[$key]);
				}
			}
		}

		if (!empty($_FILES)) {
			if (($dbinfo['enable_upload'] ?? '') == 'Y') {
				if (!empty($_FILES['upfile']['name'])) {
					$upfiles_tmp = file_upload("upfile", $updir);
					if (isset($upfiles['upfile']['name'])) {
						$old_file_path = $dbinfo['upload_dir'] . "/{$list['bid']}/{$upfiles['upfile']['name']}";
						if (!is_file($old_file_path)) $old_file_path = $dbinfo['upload_dir'] . "/{$upfiles['upfile']['name']}";
						@unlink($old_file_path);
						@unlink("{$old_file_path}.thumb.jpg");
					}
					$upfiles_totalsize = ($upfiles_tmp['size'] ?? 0); // 기존 파일 삭제 후 새 파일 사이즈만 반영
					$upfiles['upfile'] = $upfiles_tmp;
				}
			} else {
				foreach ($_FILES as $key => $value) {
					if (!empty($value['name'])) {
						if (($dbinfo['enable_upload'] ?? '') == 'image' && !is_array(@getimagesize($value['tmp_name']))) {
							continue;
						}
						$upfiles_tmp = file_upload($key, $updir);
						if (isset($upfiles[$key]['name'])) {
							// *** 원본 오류 수정: $upfiles[{$key}] -> $upfiles[$key] ***
							$old_file_path = $dbinfo['upload_dir'] . "/{$list['bid']}/{$upfiles[$key]['name']}";
							if (!is_file($old_file_path)) $old_file_path = $dbinfo['upload_dir'] . "/{$upfiles[$key]['name']}";
							@unlink($old_file_path);
							@unlink("{$old_file_path}.thumb.jpg");
						}
						// 파일 크기 업데이트는 기존 파일이 있을 경우에만 빼주는 로직이 필요. 없으면 0으로 계산.
						$upfiles_totalsize = $upfiles_totalsize - ($upfiles[$key]['size'] ?? 0) + ($upfiles_tmp['size'] ?? 0);
						$upfiles[$key] = $upfiles_tmp;
					}
				}
			}
		}

		if (($dbinfo['enable_uploadmust'] ?? '') == 'Y' and count($upfiles) == 0) {
			back(($dbinfo['enable_upload'] ?? '') == 'image' ? "이미지파일을 선택하여 업로드하여 주시기 바랍니다" : "파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='" . db_escape(serialize($upfiles)) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
	}
	///////////////////////////////

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'rdate');
	$sql_set = '';
	if ($fieldlist = userGetAppendFields($table, $skip_fields)) {
		foreach ($fieldlist as $value) {
			if (isset($_POST[$value])) {
				$sql_set .= ", `{$value}` = '" . db_escape($_POST[$value]) . "' ";
			}
		}
	}
	////////////////////////////////
	$sql = "UPDATE {$table} SET
				userid = '" . db_escape($qs['userid'] ?? '') . "',
				email = '" . db_escape($qs['email'] ?? '') . "',
				title = '" . db_escape($qs['title'] ?? '') . "'
				{$sql_set}
				{$sql_set_file}
			WHERE uid='" . (int)$qs['uid'] . "'";
	db_query($sql);

	if (($qs['cateuid'] ?? '') != ($list['cateuid'] ?? '')) {
		db_query("UPDATE {$table} set cateuid='" . (int)$qs['cateuid'] . "' where db='" . db_escape($list['db'] ?? '') . "' and type='" . db_escape($list['type'] ?? '') . "' and num='" . (int)($list['num'] ?? 0) . "'");
	}

	return true;
} // end func.

// 삭제
function delete_ok($table, $field, $goto){
	global $dbinfo, $thisUrl, $db_conn;
	
	$qs = array(
		"{$field}" => "request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
		'passwd' => "request,trim"
	);
	$qs = check_value($qs);
	
	$sql_where = '1';
	if (isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']) {
		$sql_where = " db='" . db_escape($dbinfo['db'] ?? '') . "' ";
	}
	if (($dbinfo['enable_type'] ?? '') == 'Y') {
		$sql_where .= ($sql_where == '1' ? '' : ' AND ') . " (type='docu' or type='info') ";
	}
	
	// $sql_where를 안전하게 쿼리 문자열에 삽입
	$sql = "SELECT *,password('" . db_escape($qs['passwd'] ?? '') . "') as pass FROM {$table} WHERE uid='" . (int)$qs['uid'] . "' and {$sql_where} "; 
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");

	if (!privAuth($dbinfo, "priv_delete")) {
		if (($list['bid'] ?? 0) == 0 && ($list['passwd'] ?? '') != ($list['pass'] ?? '')) {
			back("비밀번호를 정확히 입력하십시오", "{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}");
		} elseif (($list['bid'] ?? 0) > 0 && ($list['bid'] ?? '') != ($_SESSION['seUid'] ?? '')) {
			back("올린이가 아닙니다.");
		}
	}

	///////////////////////////////
	// 파일 삭제
	if (isset($list['upfiles'])) {
		$upfiles = @unserialize($list['upfiles'] ?? '');
		if (!is_array($upfiles)) {
			$upfiles = array('upfile' => array('name' => $list['upfiles'] ?? ''));
		}
		foreach ($upfiles as $key => $value) {
			if (isset($value['name'])) {
				$file_path = $dbinfo['upload_dir'] . "/{$list['bid']}/{$value['name']}";
				if (!is_file($file_path)) $file_path = $dbinfo['upload_dir'] . "/{$value['name']}";
				if (is_file($file_path)) {
					@unlink($file_path);
					@unlink("{$file_path}.thumb.jpg");
				}
			}
		}
	}
	///////////////////////////////

	$del_uploadfile = [];
	$rs_subre = db_query("SELECT * FROM {$table} WHERE {$sql_where} and num='" . (int)($list['num'] ?? 0) . "' AND length(re) > length('" . db_escape($list['re'] ?? '') . "') AND locate('" . db_escape($list['re'] ?? '') . "',re) = 1");
	while ($row = db_array($rs_subre)) {
		if (isset($row['upfiles'])) {
			$upfiles = @unserialize($row['upfiles']);
			if (!is_array($upfiles)) {
				$upfiles = array('upfile' => array('name' => $row['upfiles']));
			}
			foreach ($upfiles as $key => $value) {
				if (isset($value['name'])) {
					$file_path = $dbinfo['upload_dir'] . "/{$row['bid']}/{$value['name']}";
					if (!is_file($file_path)) $file_path = $dbinfo['upload_dir'] . "/{$value['name']}";
					$del_uploadfile[] = $file_path;
				}
			}
		}
	}
	db_free($rs_subre);
	
	db_query("DELETE FROM {$table} WHERE {$sql_where} and num='" . (int)($list['num'] ?? 0) . "' AND length(re) > length('" . db_escape($list['re'] ?? '') . "') AND locate('" . db_escape($list['re'] ?? '') . "',re) = 1");
	db_query("DELETE FROM {$table} where {$sql_where} and uid='" . (int)($list['uid'] ?? 0) . "'");
	
	foreach ($del_uploadfile as $value) {
		@unlink($value);
		@unlink("{$value}.thumb.jpg");
	}
	
	return true;
}

function vote_ok(){
	global $dbinfo, $table, $db_conn;
	$qs = array(
		'vote' => "post,trim,notnull=" . urlencode("앨범 점수를 선택하여 주기 바랍니다."),
		'uid' => "post,trim,notnull=" . urlencode("게시물 값이 없습니다.")
	);
	$qs = check_value($qs);

	$qs['vote'] = (int)$qs['vote'];
	if ($qs['vote'] > 5) $qs['vote'] = 5;
	if ($qs['vote'] < -5) $qs['vote'] = -5;
	
	db_query("UPDATE {$table} SET
					vote = vote + " . $qs['vote'] . ",
					voteip = '" . db_escape($_SERVER['REMOTE_ADDR']) . "'
				WHERE
					uid='" . (int)$qs['uid'] . "'
				AND
					voteip <> '" . db_escape($_SERVER['REMOTE_ADDR']) . "'
				LIMIT 1
				");

	if (db_count()) {
		$info = db_arrayone("SELECT vote FROM {$table} WHERE uid='" . (int)$qs['uid'] . "'");
		return $info['vote'] ?? 0;
	} else {
		back("이미 참여하셨습니다.");
	}
}

function memoWrite_ok(){
	global $dbinfo, $table, $db_conn;

	$qs = array(
		'db' => "post,trim,notnull",
		'uid' => "post,trim,notnull",
		'title' => "post,trim,notnull=" . urlencode("내용 입력하시기 바랍니다.")
	);
	$qs = check_value($qs);

	$qs['ip'] = remote_addr();
	if (isset($_SESSION['seUid'])) {
		$qs['bid'] = $_SESSION['seUid'];
		switch ($dbinfo['enable_userid'] ?? '') {
			case 'name':
				$qs['userid'] = $_SESSION['seName'];
				break;
			case 'nickname':
				$qs['userid'] = $_SESSION['seNickname'];
				break;
			default:
				$qs['userid'] = $_SESSION['seUserid'];
				break;
		}
	} else {
		back("로그인 이후에 메모를 남겨주시기 바랍니다");
	}
	
	$sql_set_memo = '';
	if (($dbinfo['enable_memo'] ?? '') == 'Y') {
		if (($dbinfo['enable_type'] ?? '') == "Y") {
			$table_memo = $table;
			$sql_set_memo = ", type='memo' ";
		} else {
			$table_memo = "{$table}_memo";
		}
		if (isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']) {
			$sql_set_memo .= " , db='" . db_escape($qs['db'] ?? '') . "' ";
		}
	} else {
		back("메모장 사용이 허가되어있지 않습니다.");
	}

	$sql = "INSERT INTO {$table_memo} SET
				bid = '" . (int)($qs['bid'] ?? 0) . "',
				userid = '" . db_escape($qs['userid'] ?? '') . "',
				num = '" . (int)($qs['uid'] ?? 0) . "',
				title = '" . db_escape($qs['title'] ?? '') . "',
				rdate = UNIX_TIMESTAMP(),
				ip = '" . db_escape($qs['ip'] ?? '') . "'
				{$sql_set_memo}
		";

	db_query($sql);
	return db_insert_id();
}

function memodelete_ok(){
	global $dbinfo, $table, $thisUrl, $db_conn;

	$qs = array( 'memouid' => "request,trim,notnull" );
	$qs = check_value($qs);
	
	$sql_where_memo = '1';
	$table_memo = '';
	if (($dbinfo['enable_memo'] ?? '') == 'Y') {
		if (isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']) {
			$sql_where_memo .= " and db='" . db_escape($qs['db'] ?? ($dbinfo['db'] ?? '')) . "' ";
		}
		if (($dbinfo['enable_type'] ?? '') == "Y") {
			$table_memo = $table;
			$sql_where_memo = " type='memo' ";
		} else {
			$table_memo = "{$table}_memo";
		}
	} else {
		back("메모장 사용이 허가되어있지 않습니다.");
	}

	$sql = "SELECT * FROM {$table_memo} WHERE uid='" . (int)$qs['memouid'] . "' and {$sql_where_memo}";
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");

	if (!privAuth($dbinfo, "priv_delete")) {
		if (($list['bid'] ?? 0) > 0 && ($list['bid'] ?? '') != ($_SESSION['seUid'] ?? '')) {
			back("올린이가 아닙니다.");
		}
	}

	db_query("DELETE FROM {$table_memo} WHERE uid='" . (int)$qs['memouid'] . "'");
}

function userReplyRe($table, $num, $re){
	global $dbinfo;

	// 한 table에 여러 게시판 생성의 경우
	$sql_where = '';
	if (($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) {
		$sql_where = " db='" . db_escape($dbinfo['db'] ?? '') . "' ";
	}
	if (($dbinfo['enable_type'] ?? '') == 'Y') {
		$sql_where = $sql_where ? $sql_where . " and type='docu' " : " type='docu' ";
	}
	if (!$sql_where) {
		$sql_where = " 1 ";
	}

	$sql = "SELECT re, right(re,1) AS last_char FROM {$table} WHERE {$sql_where} and num='" . (int)$num . "' AND length(re)=length('" . db_escape($re) . "')+1 AND locate('" . db_escape($re) . "', re)=1 ORDER BY re DESC LIMIT 1";
	$row = db_arrayone($sql);

	if ($row) {
		$ord_head = substr($row['re'], 0, -1);
		if (ord($row['last_char']) >= 255) {
			back("더이상 추가하실 수 없습니다");
		}
		$ord_foot = chr(ord($row['last_char']) + 1);
		$re = $ord_head . $ord_foot;
	} else {
		$re .= "1";
	}
	return $re;
}

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
/**
 * 추가 입력해야할 필드를 가져옵니다. (Modernized version)
 * @param string $table The table name.
 * @param array $skip_fields Fields to exclude.
 * @return array|false List of additional fields or false on failure.
 */
function userGetAppendFields(string $table, array $skip_fields = [])
{
	if (empty($table)) {
		return false;
	}

	$result = db_query("SHOW COLUMNS FROM {$table}");

	if (!$result) {
		return false;
	}

	$fieldlist = [];
	while($row = db_array($result)) {
		if(!in_array($row['Field'], $skip_fields)){
			$fieldlist[] = $row['Field'];
		}
	}
	db_free($result);

	return isset($fieldlist) ? $fieldlist : false;
}

?>