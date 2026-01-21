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
// 04/01/03 박선민 심각한 간단 버그 수정
// 25/08/11 Gemini	PHP 7 마이그레이션
//=======================================================
// 앞으로 : 게시물 삭제시 메모로 삭제되도록...
$HEADER=array(
		'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용
		'useApp' => 1, // cut_string()
		'useBoard2' => 1, // board2Count(),board2CateInfo()
		'useCheck' => 1, // check_value()
	);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath	= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/player_record"; // 마지막 "/"이 빠져야함

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$qs_basic = "db={$table}".			//table 이름
			"&mode={$mode}".		// mode값은 list.php에서는 당연히 빈값
			"&cateuid={$cateuid}".		//cateuid
			"&pern={$pern}" .				// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string)). //search string
			"&page={$page}"
	;				//현재 페이지

$table				= "player_league"; // new21_slist_event

// 공통적으로 사용할 $qs
$qs=array(
			'p_league' => "post,trim,notnull=" . urlencode("리그를 입력하시기 바랍니다."),
			'pid' => "post,trim",
			'tid' => "post,trim",
			'sid' => "post,trim",
			'p_g' => "post,trim",
			'title' => "post,trim",
			'p_totalmin' => "post,trim",
			'p_min' => "post,trim",
			'p_2fg' => "post,trim",
			'p_3fg' => "post,trim",
			'p_ft' => "post,trim",
			'p_fta' => "post,trim",
			'p_rp' => "post,trim",
			'p_as' => "post,trim",
			'p_st' => "post,trim",
			'p_blk' => "post,trim",
			'p_to' => "post,trim",
			'p_pts' => "post,trim",
			'p_ppg' => "post,trim",
			'uid' => "post,trim"
	);

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($_REQUEST['mode']){
	case 'write':
		$pid = write_ok($table, $qs);
		go_url(isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "plist.php?pid={$pid}");
		break;
	case 'reply':
		$uid = reply_ok($table,$qs);
		go_url(isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "{$thisUrl}/read.php?" . href_qs("uid={$uid}",$qs_basic));
		break;
	case 'modify':
		$pid = modify_ok($table,$qs,"uid");
		go_url(isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "plist.php?pid={$pid}");
		break;
	case 'delete':
		$pid = delete_ok($table,"uid",$goto);
		$goto = isset($_REQUEST['goto']) ? $_REQUEST['goto'] : "plist.php?pid={$pid}";
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
		memoDelete_ok();
		go_url("{$_SERVER['HTTP_REFERER']}");
		break;
	default :
		back("잘못된 웹 페이지에 접근하였습니다");
} // end switch
//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
function write_ok($table, $qs){
	global $dbinfo, $db_conn;

	if(!privAuth($dbinfo, "priv_write")) back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");

	$qs['writeinfo'] = "post,trim";
	// 넘어온값 체크
	$qs=check_value($qs);

	//팀 정보
	$team_name = db_resultone( " SELECT t_name from team WHERE tid='{$qs['tid']}' ", 0, 't_name');

	// sql문 완성
	$sql_set_file = '';
	if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == 'Y' && isset($qs['writeinfo']) && $qs['writeinfo'] == "info") {
		$sql_set	= ", type='info' ";
	}

	$sql="INSERT
			INTO
				{$table}
			SET
				pid	='{$qs['pid']}',
				tid		='{$qs['tid']}',
				sid		='{$qs['sid']}',
				t_name		='{$team_name}',
				p_league		='{$qs['p_league']}',
				p_g		='{$qs['p_g']}',
				p_totalmin		='{$qs['p_totalmin']}',
				p_min		='{$qs['p_min']}',
				p_2fg	='{$qs['p_2fg']}',
				p_3fg	='{$qs['p_3fg']}',
				p_ft		='{$qs['p_ft']}',
				p_fta		='{$qs['p_fta']}',
				p_rp		='{$qs['p_rp']}',
				p_as		='{$qs['p_as']}',
				p_st		='{$qs['p_st']}',
				p_blk		='{$qs['p_blk']}',
				p_to		='{$qs['p_to']}',
				p_pts		='{$qs['p_pts']}',
				p_ppg		='{$qs['p_ppg']}',
				p_player_uid 		='{$qs['uid']}'
				{$sql_set_file}
			";

	db_query($sql);
	$uid = db_insert_id();
	$pid = $qs['pid'];

	// E-Mail 전송
	if(isset($dbinfo['enable_adm_mail']) && $dbinfo['enable_adm_mail'] == 'Y'){
		$mail = new mime_mail;

		$mail->from		= $dbinfo['email'];
		$mail->name		= "게시판 자동메일";
		$mail->to		= $list['email'];
		$mail->subject	= "[답변] {$qs['title']}";
		if(isset($qs['docu_type']) && $qs['docu_type'] == "html"){
			$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]<br><hr>{$list['content']}";
			$mail->html	= 1;
		} else {
			$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]\n--------------------------------------------\n{$list['content']}";
			$mail->html	= 0;
		}
		$mail->send();
	}
	return $pid;
} // end func.

// 답변
function reply_ok($table,$qs){
	global $dbinfo, $db_conn;

	if(!privAuth($dbinfo, "priv_reply")) back("이용이 제한되었습니다(레벨부족). 확인바랍니다.");

	$qs['uid']	= "post,tirm,notnull=" . urlencode("답변할 게시물의 고유넘버가 넘어오지 않았습니다.");
	$qs['private_key']	= "post,trim,notnull=" . urlencode("답변할 게시물의 고유암호가 넘어오지 않았습니다.");
	// 넘어온값 체크
	$qs=check_value($qs);

	if(isset($qs['docu_type']) && strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)$qs['priv_level'];
	
	// 정상적인 질문에 대한 답변인지 체크
	// DEPRECATED: MySQL 8.0부터 PASSWORD() 함수가 제거되었습니다. MariaDB는 호환성을 위해 지원하지만 보안에 매우 취약합니다.
	//				 추후 password_hash(), password_verify() 사용을 강력히 권장합니다.
	$sql = "SELECT * FROM {$table} WHERE uid={$qs['uid']} and PASSWORD(rdate)='{$qs['private_key']}'";
	$list = db_arrayone($sql) or back("답변할 DB가 없습니다");
	if(isset($list['type']) && $list['type'] == 'info') back("공지글에는 답변글을 올리실 수 없습니다.");

	$qs['num']	= $list['num'];
	$qs['re']		= userReplyRe($table, $list['num'], $list['re']); // re값 구하는 함수 호출
	$qs['cateuid']=$list['cateuid']; // 답장 원글의 cateuid값으로 등록됨

	// 값 추가
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch(isset($dbinfo['enable_userid']) ? $dbinfo['enable_userid'] : ''){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
		$qs['email']	= $_SESSION['seEmail'];
	} else {
		if(isset($qs['email'])) $qs['email']	= check_email($qs['email']);
	}
	$qs['ip']		= remote_addr();
	
	/////////////////////////////////
	// 파일업로드 처리-추가(03/10/20)
	/////////////////////////////////
	$sql_set_file = '';
	if(isset($dbinfo['enable_upload']) && $dbinfo['enable_upload'] != 'N' && isset($_FILES)){
		$updir = $dbinfo['upload_dir'] . "/" . (int)$_SESSION['seUid'];

		// 사용변수 초기화
		$upfiles=array();
		$upfiles_totalsize=0;
		if($dbinfo['enable_upload'] == 'Y'){
			if(isset($_FILES['upfile']['name'])) { // 파일이 업로드 되었다면
				$upfiles['upfile']=file_upload("upfile",$updir);
				$upfiles_totalsize = $upfiles['upfile']['size'];
			}
		} else {
			foreach($_FILES as $key => $value){
				if(isset($_FILES[$key]['name'])) { // 파일이 업로드 되었다면
					if( $dbinfo['enable_upload'] == 'image'
						&& is_array(getimagesize($_FILES[$key]['tmp_name'])) )
						continue;
					$upfiles[$key]=file_upload($key,$updir);
					$upfiles_totalsize += $upfiles[$key]['size'];
				}
			} // end foreach
		} // end if .. esle ..
		if(isset($dbinfo['enable_uploadmust']) && $dbinfo['enable_uploadmust'] == 'Y' && sizeof($upfiles) == 0){
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
	$sql_set = '';
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip' ,	'rdate');
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
				passwd	=PASSWORD('{$qs['passwd']}'),
				email	='{$qs['email']}',
				title	='{$qs['title']}',
				content	='{$qs['content']}',
				docu_type='{$qs['docu_type']}',
				rdate	=UNIX_TIMESTAMP(),
				ip		='{$qs['ip']}',
				cateuid ='{$qs['cateuid']}',
				priv_level	='{$qs['priv_level']}'
		";
	db_query($sql);
	$uid = db_insert_id();

	// E-Mail 전송
	if((isset($dbinfo['enable_adm_mail']) && $dbinfo['enable_adm_mail'] == 'Y') or (isset($dbinfo['enable_rec_mail']) && $dbinfo['enable_rec_mail'] == 'Y')){
		$mail = new mime_mail;
		$mailfrom = '';

		if(isset($dbinfo['enable_adm_mail']) && $dbinfo['enable_adm_mail'] == 'Y'){
			$sql = "select email from {$table_logon} where uid='{$dbinfo['bid']}'";
			if(isset($dbinfo['email']) && $dbinfo['email'] = check_email(db_resultone($sql,0,"email")))
				$mailfrom = $dbinfo['email'];
		}
		if(isset($dbinfo['enable_rec_mail']) && $dbinfo['enable_rec_mail'] == 'Y'){
			if(isset($_POST['rec_email']) && $_POST['rec_email'] = check_email($_POST['rec_email'])){
				if($mailfrom) $mailfrom .= ",{$_POST['rec_email']}";
				else $mailfrom = $_POST['rec_email'];
			}
		}
		if($mailfrom){
			$mail->from		= $mailfrom;

			$mail->name		= "게시판 자동메일";
			$mail->to		= $list['email'];
			$mail->subject	= "[답변] {$qs['title']}";
			if(isset($qs['docu_type']) && $qs['docu_type'] == "html"){
				$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]<br><hr>{$list['content']}";
				$mail->html	= 1;
			} else {
				$mail->body	= "[{$list['userid']}]님께서 다음과 같은 답변을 주었습니다.]\n--------------------------------------------\n{$list['content']}";
				$mail->html	= 0;
			}
			$mail->send();
		}
	}
	return $uid;
} // end func

function modify_ok($table,$qs,$field){
	global $dbinfo, $db_conn;

	$qs[$field]	= "post,trim,notnull=" . urlencode("고유번호가 넘어오지 않았습니다");
	// 넘어온값 체크
	$qs=check_value($qs);

	//팀 정보
	$team_name = db_resultone( " SELECT t_name from team WHERE tid='{$qs['tid']}' ", 0, 't_name');
	
	if(isset($qs['docu_type']) && strtolower($qs['docu_type']) != "html") $qs['docu_type']="text";
	$qs['priv_level']=(int)$qs['priv_level'];

	$sql = "UPDATE
				{$table}
			SET
				p_league	='{$qs['p_league']}',
				pid			='{$qs['pid']}',
				tid			='{$qs['tid']}',
				sid			='{$qs['sid']}',
				t_name		='{$team_name}',
				p_g			='{$qs['p_g']}',
				p_totalmin	='{$qs['p_totalmin']}',
				p_min		='{$qs['p_min']}',
				p_2fg		='{$qs['p_2fg']}',
				p_3fg		='{$qs['p_3fg']}',
				p_ft		='{$qs['p_ft']}',
				p_fta		='{$qs['p_fta']}',
				p_rp		='{$qs['p_rp']}',
				p_as		='{$qs['p_as']}',
				p_st		='{$qs['p_st']}',
				p_blk		='{$qs['p_blk']}',
				p_to		='{$qs['p_to']}',
				p_pts		='{$qs['p_pts']}',
				p_ppg		='{$qs['p_ppg']}'
			WHERE
				uid={$qs['uid']}
		";
	db_query($sql);
	
	$pid = $qs['pid'];

	return $pid;
} // end func.
// 삭제
function delete_ok($table,$field,$goto){
	global $dbinfo, $thisUrl, $db_conn;

	$qs=array(
			$field => "request,trim,notnull=" . urlencode("고유넘버가 넘어오지 않았습니다."),
			'pid' => "request,trim"
		);
	$qs=check_value($qs);

	// SQL문 where절 정리
	$sql_where = '';
	if(isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']) {
		$sql_where=" db='{$qs['db']}' ";
	}
	if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == 'Y') {
		$sql_where = $sql_where ? $sql_where . " and (type='docu' or type='info') " : " (type='docu' or type='info') ";
	}

	db_query("DELETE FROM {$table} where uid='{$qs['uid']}'");
	
	if(isset($del_uploadfile) && is_array($del_uploadfile)){
		foreach ( $del_uploadfile as $value){
			@unlink($value);
			@unlink($value.".thumb.jpg"); // thumbnail 삭제
		}
	} // end if
	
	$pid = $qs['pid'];

	return $pid;
} // end func delete_ok()

function vote_ok(){
	global $dbinfo, $table;

	$qs=array(
				'vote' => "post,trim,notnull=" . urlencode("앨범 점수를 선택하여 주기 바랍니다."),
				'uid' => "post,trim,notnull=" . urlencode("게시물 값이 없습니다.")
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

	if(db_count())
		return $info['vote']+$qs['vote'];
	else
		back("이미 참여하셨습니다.");

} // end func.

function memoWrite_ok(){
	global $dbinfo, $table, $db_conn;

	$qs=array(
			'db' => "post,trim,notnull",
			'uid' => "post,trim,notnull",
			'title' => "post,trim,notnull=" . urlencode("내용 입력하시기 바랍니다."),
		);
	$qs=check_value($qs);

	// 값 추가
	$qs['ip']		= remote_addr();
	if(isset($_SESSION['seUid'])){
		$qs['bid']	= $_SESSION['seUid'];
		switch(isset($dbinfo['enable_userid']) ? $dbinfo['enable_userid'] : ''){
			case 'name'		: $qs['userid'] = $_SESSION['seName']; break;
			case 'nickname'	: $qs['userid'] = $_SESSION['seNickname']; break;
			default			: $qs['userid'] = $_SESSION['seUserid']; break;
		}
	}
	else back("로그인 이후에 메모를 남겨주시기 바랍니다");
	
	// 추가 변수
	$table_memo = '';
	$sql_set_memo = '';
	if(isset($dbinfo['enable_memo']) && $dbinfo['enable_memo'] == 'Y'){
		// 메모 테이블 구함
		if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == "Y"){
			$table_memo		= $table;
			$sql_set_memo	= ", type='memo' ";
		} else {
			$table_memo		= $table . "_memo";
		}
		if(isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']){
			$sql_set_memo	.=" , db='{$qs['db']}' ";
		}
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

function memoDelete_ok(){
	global $dbinfo, $table, $thisUrl, $db_conn;

	$qs=array(
			'memouid' => "request,trim,notnull"
		);
	$qs=check_value($qs);

	// 추가 변수
	$sql_where_memo = '';
	$table_memo = '';
	if(isset($dbinfo['enable_memo']) && $dbinfo['enable_memo'] == 'Y'){
		if(isset($qs['db']) && isset($dbinfo['table_name']) && $dbinfo['table_name'] != $dbinfo['db']){
			$sql_where_memo	.=" and db='{$qs['db']}' ";
		}
		if(isset($dbinfo['enable_type']) && $dbinfo['enable_type'] == "Y"){
			$table_memo		= $table; // 메모 테이블 구함
			$sql_where_memo	= " type='memo' ";
		} else {
			$table_memo		= $table . "_memo";
			$sql_where_memo	= " 1 ";
		}
	}
	else back("메모장 사용이 허가되어있지 않습니다.");

	// 삭제 권한 체크와 해당 게시물 읽어오기
	// DEPRECATED: MySQL 8.0부터 PASSWORD() 함수가 제거되었습니다. MariaDB는 호환성을 위해 지원하지만 보안에 매우 취약합니다.
	//				 추후 password_hash(), password_verify() 사용을 강력히 권장합니다.
	$sql = "SELECT *,PASSWORD('{$qs['passwd']}') as pass FROM {$table_memo} WHERE uid='{$qs['memouid']}' and {$sql_where_memo}";
	$list = db_arrayone($sql) or back("이미 삭제되었거나 잘못된 요청입니다");
	if(!privAuth($dbinfo,"priv_delete")) {// 게시판 전체 삭제 권한을 가졌다면
		if((isset($list['bid']) && $list['bid'] == 0) and (isset($list['passwd']) && isset($list['pass']) && $list['passwd'] != $list['pass'])){
			back("비밀번호를 정확히 입력하십시오","{$thisUrl}/delete.php?{$_SERVER['QUERY_STRING']}&mode=memo&goto=".urlencode($goto));
		} elseif (isset($list['bid']) && $list['bid']>0 and $list['bid'] != $_SESSION['seUid']) back("올린이가 아님니다.");
	}

	db_query("DELETE FROM {$table_memo} WHERE uid='{$qs['memouid']}'");
} // end func memoDelete_ok

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