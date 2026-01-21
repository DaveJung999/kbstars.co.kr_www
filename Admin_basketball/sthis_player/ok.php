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
//=======================================================
// 앞으로 : 게시물 삭제시 메모로 삭제되도록...
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useCheck' => 1, // check_email()
	'useBoard2' => 1, // 보드관련 함수 포함
	'useApp' => 1,
	'useImage' => 1, // thumbnail()
	'useClassSendmail' =>	1,
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_player"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$qs_basic = "db=" . ($_REQUEST['db'] ?? $table) .			//table 이름
			"&mode=" . ($_REQUEST['mode'] ?? '') .		// mode값은 list.php에서는 당연히 빈값
			"&cateuid=" . ($_REQUEST['cateuid'] ?? '') .		//cateuid
			"&team=" . ($_REQUEST['team'] ?? '') .				// 페이지당 표시될 게시물 수
			"&pern=" . ($_REQUEST['pern'] ?? '') .				// 페이지당 표시될 게시물 수
			"&sc_column=" . ($_REQUEST['sc_column'] ?? '') .	//search column
			"&sc_string=" . urlencode(stripslashes(isset($sc_string) ? $sc_string : '')) . //search string
			"&team=" . ($_REQUEST['team'] ?? '').
			"&html_headtpl=" . (isset($html_headtpl) ? $html_headtpl : '').
			"&page=" . ($_REQUEST['page'] ?? '');

$qs_basic		= href_qs($qs_basic); // 해당값 초기화

// 업로드 디렉토리 설정
// - 실제 저장 경로는 다음과 같아야 한다.
//   {DOCROOT}/sthis/sthis_player/upload/player/{bid}/{파일명}
// - 여기서는 base 디렉토리까지만 설정하고, 아래에서 bid를 붙인다.
if (empty($dbinfo['upload_dir'])) {
	$dbinfo['upload_dir'] = rtrim("{$_SERVER['DOCUMENT_ROOT']}/sthis/sthis_player/upload/player", "/");
} else {
	$dbinfo['upload_dir'] = rtrim($dbinfo['upload_dir'], "/");
}

// 공통적으로 사용할 $qs
$qs=array(
			'userid' =>	"post,trim",
			'passwd' =>	"post,trim",
			'title' =>	"post,trim",
			'p_num' =>	"post,trim",
			'p_name' =>	"post,trim",
			'p_position' =>	"post,trim",
			'p_bdate' =>	"post,trim",
			'p_ddate' =>	"post,trim",
			'p_school' =>	"post,trim",
			'p_pro' =>	"post,trim",
			'p_height' =>	"post,trim",
			'p_oab' =>	"post,trim",
			'p_marry' =>	"post,trim",
			'p_family' =>	"post,trim",
			'p_cm' =>	"post,trim",
			'p_hobby' =>	"post,trim",
			'p_mplayer' =>	"post,trim",
			'p_nickname' =>	"post,trim",
			'p_spo' =>	"post,trim",
			'p_email' =>	"post,trim",
			'p_homepage' =>	"post,trim",
			'p_start' =>	"post,trim",
			'p_sin' =>	"post,trim",
			'p_end' =>	"post,trim",
			'p_lee' =>	"post,trim",
			'p_stress' =>	"post,trim",
			'p_music' =>	"post,trim",
			'p_food' =>	"post,trim",
			'p_trade' =>	"post,trim"
	);
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($_REQUEST['mode']){
	case 'write':
		$uid = write_ok($table, $qs);
		go_url($_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/read.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'reply':
		$uid = reply_ok($table,$qs);
		go_url($_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/read.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'modify':
		$uid = $_POST['uid'] ?? $uid;
		modify_ok($table,$qs,"uid");
		go_url($_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/read.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'delete':
		$goto = $_REQUEST['goto'] ? $_REQUEST['goto'] : "{$thisUrl}/list.php?" . href_qs("",$qs_basic);
		delete_ok($table,"uid",$goto);
		go_url($goto);
		break;
	// VOTE
	case 'vote' :
		$vote=vote_ok();
		back("현재 {$vote}점입니다.","{$_SERVER['HTTP_REFERER']}");
		break;
	// 메모
	case 'memowrite':
		memoWrite_ok();
		go_url("{$_SERVER['HTTP_REFERER']}");
		break;
	case 'memodelete':
		memodelete_ok();
		go_url("{$_SERVER['HTTP_REFERER']}");
		break;
	default :
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch
//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
function write_ok($table, $qs){
	global $dbinfo, $db_conn, $_SESSION;
	if(!privAuth($dbinfo, "priv_write")) back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");

	$qs['writeinfo'] = "post,trim";
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) and strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)($qs['priv_level'] ?? 0);
	if(isset($qs['catelist'])) $qs['cateuid'] = $qs['catelist'];

	// 값 추가
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		$qs['email']	= check_email($qs['email'] ?? '');
	}
	$qs['ip']		= remote_addr();
	// - num의 최대값 구함
	$sql_where = '';
	if($dbinfo['table_name'] != $dbinfo['db']) $sql_where=" db='{$dbinfo['db']}' "; // $sql_where 사용 시작
	if(empty($sql_where)) $sql_where= " 1 ";
	$sql = "SELECT max(num) FROM {$table} WHERE  $sql_where ";
	$qs['num'] = db_resultone($sql,0,"max(num)") + 1;

	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = ''; // 변수 초기화
	if($dbinfo['enable_upload'] != 'N' and isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				$upfiles['upfile']=file_upload("upfile",$updir);
				$upfiles_totalsize = $upfiles['upfile']['size'];
			}
		} else {
			foreach($_FILES as $key =>	$value){
				if(isset($_FILES[$key]['name'])) { // 파일이 업로드 되었다면
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if .. esle ..
		if($dbinfo['enable_uploadmust'] == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".serialize($upfiles) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	} // end if
	/////////////////////////////////

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$sql_set = ''; // 변수 초기화
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if(isset($_POST[$value])) $sql_set .= ", {$value} = '" . $_POST[$value] . "' ";
		}
	}
	////////////////////////////////
	
	// sql문 완성
	if($dbinfo['enable_type'] == 'Y' and ($qs['writeinfo'] ?? '') == "info") $sql_set	.= ", type='info' ";// $sql_set 시작
	$sql="INSERT
			INTO
				{$table}
			SET
				db		='{$qs['db']}',
				num		='{$qs['num']}',
				bid		='{$qs['bid']}',
				userid	='{$qs['userid']}',
				title	='{$qs['title']}',
				rdate	= UNIX_TIMESTAMP(),
				ip		='{$qs['ip']}',
				p_num		='{$qs['p_num']}',
				p_name		='{$qs['p_name']}',
				p_position		='{$qs['p_position']}',
				p_bdate		='{$qs['p_bdate']}',
				p_ddate		='{$qs['p_ddate']}',
				p_school		='{$qs['p_school']}',
				p_pro		='{$qs['p_pro']}',
				p_height		='{$qs['p_height']}',
				p_oab		='{$qs['p_oab']}',
				p_marry		='{$qs['p_marry']}',
				p_family		='{$qs['p_family']}',
				p_cm		='{$qs['p_cm']}',
				p_hobby		='{$qs['p_hobby']}',
				p_mplayer		='{$qs['p_mplayer']}',
				p_nickname		='{$qs['p_nickname']}',
				p_spo		='{$qs['p_spo']}',
				p_email		='{$qs['p_email']}',
				p_homepage		='{$qs['p_homepage']}',
				p_start		='{$qs['p_start']}',
				p_sin		='{$qs['p_sin']}',
				p_end		='{$qs['p_end']}',
				p_lee		='{$qs['p_lee']}',
				p_stress		='{$qs['p_stress']}',
				p_music		='{$qs['p_music']}',
				p_food		='{$qs['p_food']}',
				p_trade		='{$qs['p_trade']}'
				{$sql_set_file}
				{$sql_set}
			";

	db_query($sql);
	$uid = db_insert_id();

	return $uid;
} // end func.

// 답변
function reply_ok($table,$qs){
	global $dbinfo, $db_conn, $_SESSION;
	if(!privAuth($dbinfo, "priv_reply")) back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");

	$qs['uid']	= "post,tirm,notnull=" . urlencode("답변할 게시물의 고유넘버가 넘어오지 않았습니다.");
	$qs['private_key']	= "post,trim,notnull=" . urlencode("답변할 게시물의 고유암호가 넘어오지 않았습니다.");
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) and strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)($qs['priv_level'] ?? 0);
	
	// 정상적인 질문에 대한 답변인지 체크
	$sql = "SELECT * FROM {$table} WHERE uid={$qs['uid']} and password(rdate)='{$qs['private_key']}'";
	$list = db_arrayone($sql) or back("답변할 DB가 없습니다");
	if($list['type'] == 'info') back("공지글에는 답변글을 올리실 수 없습니다.");

	$qs['num']	= $list['num'];
	$qs['re']		= userReplyRe($table, $list['num'], $list['re']); // re값 구하는 함수 호출
	$qs['cateuid']=$list['cateuid']; // 답장 원글의 cateuid값으로 등록됨

	// 값 추가
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		$qs['email']	= check_email($qs['email'] ?? '');
	}
	$qs['ip']		= remote_addr();
	
	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = ''; // 변수 초기화
	if($dbinfo['enable_upload'] != 'N' and isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)($_SESSION['seUid'] ?? 0);

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				$upfiles['upfile']=file_upload("upfile",$updir);
				$upfiles_totalsize = $upfiles['upfile']['size'];
			}
		} else {
			foreach($_FILES as $key =>	$value){
				if(isset($_FILES[$key]['name'])) { // 파일이 업로드 되었다면
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if .. esle ..
		if($dbinfo['enable_uploadmust'] == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".serialize($upfiles) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
		unset($upfiles);
	} // end if
	/////////////////////////////////

	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$sql_set = ''; // 변수 초기화
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if(isset($_POST[$value])) $sql_set .= ", {$value} = '" . $_POST[$value] . "' ";
		}
	}
	////////////////////////////////

	// sql문 완성
	$sql="INSERT
			INTO
				{$table}
			SET
				db		='{$qs['db']}',
				num		='{$qs['num']}',
				re		='{$qs['re']}',
				bid		='{$qs['bid']}',
				userid	='{$qs['userid']}',
				passwd	=password('{$qs['passwd']}'),
				email	='{$qs['email']}',
				title	='{$qs['title']}',
				content	='{$qs['content']}',
				docu_type='{$qs['docu_type']}',
				rdate	=UNIX_TIMESTAMP(),
				ip		='{$qs['ip']}',
				cateuid ='{$qs['cateuid']}',
				priv_level	='{$qs['priv_level']}'
				{$sql_set_file}
				{$sql_set}
		";
	db_query($sql);
	$uid = db_insert_id();

	// E-Mail 전송
	if( $dbinfo['enable_adm_mail'] == 'Y' or $dbinfo['enable_rec_mail'] == 'Y'){
		$mail = new mime_mail;
		$mailfrom = '';
		
		if($dbinfo['enable_adm_mail'] == 'Y'){
			$sql = "select email from {$table_logon} where uid='{$dbinfo['bid']}'";
			if($dbinfo['email'] = check_email(db_resultone($sql,0,"email")))
				$mailfrom = $dbinfo['email'];
		}
		if($dbinfo['enable_rec_mail'] == 'Y'){
			if($_POST['rec_email'] = check_email($_POST['rec_email'])){
				if(isset($mailfrom)) $mailfrom .= ",{$_POST['rec_email']}";
				else $mailfrom = $_POST['rec_email'];
			}
		}
		if(isset($mailfrom)){
			$mail->from		= $mailfrom;

			$mail->name		= "게시판 자동메일";
			$mail->to		= $list['email'];
			$mail->subject	= "[답변] {$qs['title']}";
			if($qs['docu_type'] == "html"){
				$mail->body	= "[".$list['userid']."]님께서 다음과 같은 답변을 주었습니다.]<br><hr>{$list['content']}";
				$mail->html	= 1;
			} else {
				$mail->body	= "[".$list['userid']."]님께서 다음과 같은 답변을 주었습니다.]\n--------------------------------------------\n{$list['content']}";
				$mail->html	= 0;
			}
			$mail->send();
		}
	}
	return $uid;
} // end func
function modify_ok($table,$qs,$field){
	global $dbinfo, $_SESSION;
	$qs["{$field}"]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) and strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)($qs['priv_level'] ?? 0);

	// 수정 권한 체크와 해당 게시물 읽어오기
	if(privAuth($dbinfo,"priv_delete")) // 게시판 전체 삭제 권한을 가졌다면 수정 권한 무조건 부여
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}'";
	elseif(isset($_SESSION['seUid'])) // 회원의 글이라면,
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}' and bid='{$_SESSION['seUid']}'";
	else { // 비회원의 글이라면 (비회원의 글에 패스워드가 없을 경우 누구든지 수정 가능, 실수로 안 입력했을 경우 수정가능하게)
		$sql = "SELECT * FROM {$table} WHERE uid='{$qs['uid']}' and passwd=password('{$qs['passwd']}')";
	} // end if
	if(!$list=db_arrayone($sql)) back("게시물이 없거나 수정할 권한이 없습니다");
		
	// 값 추가
	if($list['bid'] == $_SESSION['seUid']){
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		$qs['userid']	= $list['userid'];
		$qs['email']	= isset($qs['email']) ? check_email($qs['email']): $list['email']; // email값이 넘어오면 수정하고 아니면 그대로 유지
	}
	$qs['ip']		= remote_addr();
	$qs['cateuid']= ( isset($qs['catelist']) and strlen($list['re']) == 0 ) ? $qs['catelist'] : $list['cateuid']; // 답변이 아닌 경우에만 카테고리 수정 가능
	
	///////////////////////////////
	// 파일 업로드 - 변경(03/10/20)
	///////////////////////////////
	$sql_set_file = ''; // 변수 초기화
	if( $dbinfo['enable_upload'] != 'N' and isset($_FILES) ){
		// 파일 업로드 드렉토리
		$updir = $dbinfo['upload_dir'] . "/" . (int)$list['bid'];

		// 기존 업로드 파일 정보 읽어오기
		$upfiles=unserialize($list['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles['upfile']['name']=$list['upfiles'];
			$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
		}
		$upfiles_totalsize=$list['upfiles_totalsize'];

		// 파일을 올리지 않고, 해당 파일을 삭제하고자 하였을때
		if(is_array($upfiles) and count($upfiles)>0){
			foreach($upfiles as $key =>	$value){
				if(isset($_REQUEST["del_{$key}"])) {
						// 해당 파일 삭제
						if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']) ){
							@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']);
							@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
						}
						elseif( is_file($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']) ){
							@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']);
							@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
						}

						$upfiles_totalsize -= $upfiles[$key]['size'];
						unset($upfiles[$key]);
				}
			}
		}

		// 업로드 파일 처리
		if($dbinfo['enable_upload'] == 'Y') { // 파일 하나 업로드라면
			if(isset($_FILES['upfile']['name'])) {	// 파일이 업로드 되었다면
				$upfiles_tmp=file_upload("upfile",$updir);

				// 기존 업로드 파일이 있다면 삭제
				if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . ($upfiles['upfile']['name'] ?? '')) ){
					@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles['upfile']['name']);
					@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles['upfile']['name'].".thumb.jpg"); // thumbnail 삭제
				}
				elseif( is_file($dbinfo['upload_dir'] . "/" . ($upfiles['upfile']['name'] ?? '')) ){
					@unlink($dbinfo['upload_dir'] . "/" . $upfiles['upfile']['name']);
					@unlink($dbinfo['upload_dir'] . "/" . $upfiles['upfile']['name'].".thumb.jpg"); // thumbnail 삭제
				}

				$upfiles_totalsize	= $upfiles_tmp['size'];
				$upfiles['upfile']	= $upfiles_tmp;
				unset($upfiles_tmp);
			}
		} else { // 복수 업로드라면,
			foreach($_FILES as $key =>	$value){
				if(isset($_FILES[$key]['name'])) { // 파일이 업로드 되었다면
					if( $dbinfo['enable_upload'] == 'image'
						AND !is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles_tmp=file_upload($key,$updir);

					// 기존 업로드 파일이 있다면 삭제
					if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . ($upfiles[$key]['name'] ?? '')) ){
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name']);
						@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
					}
					elseif( is_file($dbinfo['upload_dir'] . "/" . ($upfiles[$key]['name'] ?? '')) ){
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name']);
						@unlink($dbinfo['upload_dir'] . "/" . $upfiles[$key]['name'].".thumb.jpg"); // thumbnail 삭제
					}

					$upfiles_totalsize = $upfiles_totalsize - ($upfiles[$key]['size'] ?? 0) + $upfiles_tmp['size'];
					$upfiles[$key]=$upfiles_tmp;
					unset($upfiles_tmp);
				}
			} // end foreach
		} // end if .. else ..
		if($dbinfo['enable_uploadmust'] == 'Y' and sizeof($upfiles) == 0){
			if( $dbinfo['enable_upload'] == 'image')
				back("이미지파일을 선택하여 업로드하여 주시기 바랍니다");
			else back("파일이 업로드 되지 않았습니다");
		}
		$sql_set_file = ", upfiles='".serialize($upfiles) . "', upfiles_totalsize='{$upfiles_totalsize}' ";
	} // end if
	///////////////////////////////
	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$sql_set = ''; // 변수 초기화
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip', 'rdate');
	if($fieldlist = userGetAppendFields($table, $skip_fields)){
		foreach($fieldlist as $value){
			if(isset($_POST[$value])) $sql_set .= ", {$value} = '" . $_POST[$value] . "' ";
		}
	}
	////////////////////////////////
	$sql = "UPDATE
				{$table}
			SET
				userid	='{$qs['userid']}',
				email	='{$qs['email']}',
				title	='{$qs['title']}',
				p_num		='{$qs['p_num']}',
				p_name		='{$qs['p_name']}',
				p_position		='{$qs['p_position']}',
				p_bdate		='{$qs['p_bdate']}',
				p_ddate		='{$qs['p_ddate']}',
				p_school		='{$qs['p_school']}',
				p_pro		='{$qs['p_pro']}',
				p_height		='{$qs['p_height']}',
				p_oab		='{$qs['p_oab']}',
				p_marry		='{$qs['p_marry']}',
				p_family		='{$qs['p_family']}',
				p_cm		='{$qs['p_cm']}',
				p_hobby		='{$qs['p_hobby']}',
				p_mplayer		='{$qs['p_mplayer']}',
				p_nickname		='{$qs['p_nickname']}',
				p_spo		='{$qs['p_spo']}',
				p_email		='{$qs['p_email']}',
				p_homepage		='{$qs['p_homepage']}',
				p_start		='{$qs['p_start']}',
				p_sin		='{$qs['p_sin']}',
				p_end		='{$qs['p_end']}',
				p_lee		='{$qs['p_lee']}',
				p_stress		='{$qs['p_stress']}',
				p_music		='{$qs['p_music']}',
				p_food		='{$qs['p_food']}',
				p_trade		='{$qs['p_trade']}'
				{$sql_set_file}
				{$sql_set}
			WHERE
				uid={$qs['uid']}
		";
	db_query($sql);

	// 만일 카테고리가 변경되었다면, 그 이하 답변글들 역시 cateuid값 변경함
	if( $qs['cateuid'] <> $list['cateuid'] ){
		db_query("update {$table} set cateuid='{$qs['cateuid']}' where db='{$list['db']}' and type='{$list['type']}' and num='{$list['num']}'");
	} // end if
	
	return true;
} // end func.
// 삭제
function delete_ok($table,$field,$goto){
	global $dbinfo,$thisUrl, $_SESSION;
	
	$qs=array(
			"$field" =>	"request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
			'passwd' =>	"request,trim"
		);
	
	$qs=check_value($qs);
	// SQL문 where절 정리
	// 한 table에 여러 게시판 생성의 경우
	if(!isset($sql_where)) $sql_where= " 1 ";

	// 한 table에 여러 게시판 생성의 경우
	if($dbinfo['table_name'] != $dbinfo['db']) $sql_where .= " and db='{$table}' "; // $sql_where 사용 시작
	if($dbinfo['enable_type'] == 'Y') $sql_where .= " and (type='docu' or type='info') ";

	// 삭제 권한 체크와 해당 게시물 읽어오기
	$sql = "SELECT *,password('{$qs['passwd']}') as pass FROM {$table} WHERE uid='{$qs['uid']}' and  $sql_where ";
	
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");
	if(!privAuth($dbinfo,"priv_delete")) {// 게시판 전체 삭제 권한을 가졌다면
		if($list['bid'] == 0 and $list['passwd'] != $list['pass']){
			if(isset($_SERVER['QUERY_STRING']))
				back("비밀번호를 입력하여 주십시오","{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}");
			else back("비밀번호를 정확히 입력하십시오");
		} elseif ($list['bid']>0 and $list['bid'] != $_SESSION['seUid']) back("올린이가 아님니다.");
	}

	///////////////////////////////
	// 파일 업로드 - 삭제(03/10/20)
	///////////////////////////////
	if(isset($list['upfiles'])){
		$upfiles=unserialize($list['upfiles']);
		if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles['upfile']['name']=$list['upfiles'];
			$upfiles['upfile']['size']=(int)$list['upfiles_totalsize'];
		}
		foreach($upfiles as $key =>	$value){
			if(isset($value['name'])){
				if( is_file($dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name']) ){
					@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name']);
					@unlink($dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name'].".thumb.jpg"); // thumbnail파일도
				}
				elseif( is_file($dbinfo['upload_dir'] . "/" . $value['name']) ){
					@unlink($dbinfo['upload_dir'] . "/" . $value['name']);
					@unlink($dbinfo['upload_dir'] . "/" . $value['name'].".thumb.jpg"); // thumbnail파일도
				}
			} // end if
		} // end foreach
	} // end if
	///////////////////////////////

	// 답변글과 파일도 삭제
	$del_uploadfile = array(); // 변수 초기화
	$rs_subre = db_query("SELECT * FROM {$table} WHERE $sql_where and num='{$list['num']}' AND length(re) > length('{$list['re']}') AND locate('{$list['re']}',re) = 1");
	while($row=db_array($rs_subre)){
		if(isset($row['upfiles'])){
			$upfiles=unserialize($row['upfiles']);
			if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
				$upfiles['upfile']['name']=$row['upfiles'];
				$upfiles['upfile']['size']=(int)$row['upfiles_totalsize'];
			}
			foreach($upfiles as $key =>	$value){
				if(isset($value['name'])){
					if( is_file($dbinfo['upload_dir'] . "/{$row['bid']}/" . $value['name']) )
						$del_uploadfile[] = $dbinfo['upload_dir'] . "/{$row['bid']}/" . $value['name'];
					elseif( is_file($dbinfo['upload_dir'] . "/" . $value['name']) )
						$del_uploadfile[] = $dbinfo['upload_dir'] . "/" . $value['name'];
				} // end if
			} // end foreach
		} // end if
	} // end while
	
	db_query("DELETE FROM {$table} WHERE $sql_where and num='{$list['num']}' AND length(re) > length('{$list['re']}') AND locate('{$list['re']}',re) = 1");
	db_query("DELETE FROM {$table} where $sql_where and uid='{$list['uid']}'");
	
	if(is_array($del_uploadfile)){
		foreach ( $del_uploadfile as $value){
			@unlink($value);
			@unlink($value.".thumb.jpg"); // thumbnail 삭제
		}
	} // end if
	
	return true;
} // end func delete_ok()

function vote_ok(){
	global $dbinfo, $table;
	$qs=array(
				'vote' =>	"post,trim,notnull=" . urlencode("앨범 점수를 선택하여 주기 바랍니다."),
				'uid' =>	"post,trim,notnull=" . urlencode("게시물 값이 없습니다.")
		);
	$qs=check_value($qs);

	// 점수 한계선 설정
	if($qs['vote']>5) $qs['vote']=5;
	if($qs['vote']<-5) $qs['vote']=-5;

	// 조회수 증가
	db_query("UPDATE {$table} SET
					vote	=vote +{$qs['vote']},
					voteip	='{$_SERVER['REMOTE_ADDR']}'
				WHERE
					uid='{$qs['uid']}'
				AND
					voteip<>'{$_SERVER['REMOTE_ADDR']}'
				LIMIT 1
				");

	if(db_count()){
		$info = db_arrayone("SELECT vote FROM {$table} WHERE uid='{$qs['uid']}'");
		return $info['vote'];
	}
	else
		back("이미 참여하셨습니다.");

} // end func.

function memoWrite_ok(){
	global $dbinfo, $table, $db_conn, $_SESSION ;

	$qs=array(
			'db' =>	"post,trim,notnull",
			'uid' =>	"post,trim,notnull",
			'title' =>	"post,trim,notnull=" . urlencode("내용 입력하시기 바랍니다."),
		);
	$qs=check_value($qs);

	// 값 추가
	$qs['ip']		= remote_addr();
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch($dbinfo['enable_userid']){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
	}
	else back("로그인 이후에 메모를 남겨주시기 바랍니다");
	
	// 추가 변수
	$sql_set_memo = ''; // 변수 초기화
	if($dbinfo['enable_memo'] == 'Y'){
		// 메모 테이블 구함
		if($dbinfo['enable_type'] == "Y"){
			$table_memo		= $table;
			$sql_set_memo	= ", type='memo' "; // $sql_set_memo 사용 시작
		} else {
			$table_memo		= $table . "_memo";
		} // end if
		if($dbinfo['table_name'] != $dbinfo['db']){
			$sql_set_memo	.=" , db='{$qs['db']}' ";
		} // end if

	}
	else back("메모장 사용이 허가되어있지 않습니다.");

	$sql="INSERT
			INTO
				{$table_memo}
			SET
				bid		='{$qs['bid']}',
				userid	='{$qs['userid']}',
				num		='{$qs['uid']}',
				title	='{$qs['title']}',
				rdate	=UNIX_TIMESTAMP(),
				ip		='{$qs['ip']}'
				{$sql_set_memo}
		";

	db_query($sql);
	return db_insert_id();
} // end func memoWrite_ok

function memodelete_ok(){
	global $dbinfo, $table, $thisUrl, $_SESSION;

	$qs=array(
			'memouid' =>	"request,trim,notnull"
		);
	$qs=check_value($qs);

	// 추가 변수
	$sql_where_memo = ''; // 변수 초기화
	if($dbinfo['enable_memo'] == 'Y'){
		if($dbinfo['table_name'] != $dbinfo['db']){
			$sql_where_memo	.=" and db='{$qs['db']}' "; // $sql_where_memo 사용 시작
		} // end if
		if($dbinfo['enable_type'] == "Y"){
			$table_memo		= $table; // 메모 테이블 구함
			$sql_where_memo	= " type='memo' ";
		} else {
			$table_memo		= $table . "_memo";
			$sql_where_memo	= " 1 ";
		} // end if

	}
	else back("메모장 사용이 허가되어있지 않습니다.");

	// 삭제 권한 체크와 해당 게시물 읽어오기
	$sql = "SELECT *,password('{$qs['passwd']}') as pass FROM {$table_memo} WHERE uid='{$qs['memouid']}' and {$sql_where_memo}";
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");
	if(!privAuth($dbinfo,"priv_delete")) {// 게시판 전체 삭제 권한을 가졌다면
		if($list['bid'] == 0 and $list['passwd'] != $list['pass']){
			back("비밀번호를 정확히 입력하십시오","{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}&mode=memo&goto=".urlencode($goto));
		} elseif ($list['bid']>0 and $list['bid'] != $_SESSION['seUid']) back("올린이가 아님니다.");
	}

	db_query("DELETE FROM {$table_memo} WHERE uid='{$qs['memouid']}'");
} // end func memodelete_ok

// 카테고리 새서브 RE값 구함
// 03/10/12
function userReplyRe($table, $num, $re){
	global $dbinfo;

	// 한 table에 여러 게시판 생성의 경우
	$sql_where = '';
	if (($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) {
		$sql_where = " db='{$dbinfo['db']}' ";
	}
	if (($dbinfo['enable_type'] ?? '') == 'Y') {
		$sql_where = $sql_where ? $sql_where . " and type='docu' " : " type='docu' ";
	}
	if (!$sql_where) {
		$sql_where = " 1 ";
	}

	$sql = "SELECT re, right(re,1) FROM {$table} WHERE $sql_where and num='{$num}' AND length(re)=length('{$re}')+1 AND locate('{$re}', re)=1 ORDER BY re DESC LIMIT 1";
	$row = db_arrayone($sql);

	if ($row) {
		$ord_head = substr($row['re'], 0, -1);
		if (ord($row['right(re,1)']) >= 255) {
			back("더이상 추가하실 수 없습니다");
		}
		$ord_foot = chr(ord($row['right(re,1)']) + 1);
		$re = $ord_head . $ord_foot;
	} else {
		$re .= "1";
	}
	return $re;
} // end func userReplyRe($table, $num, $re)

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
