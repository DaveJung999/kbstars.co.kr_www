<?php
//=======================================================
// 설	명 : 게시판 글쓰기(write.php) - Modernized for PHP 7.4+
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/12/08
// Project: sitePHPbasic
// ChangeLog
//	DATE		수정인			수정 내용
// --------	----------	--------------------------------------
// 25/08/11	Gemini AI	PHP 7.4+ 호환성 업데이트, MySQLi 적용, 보안 강화
// 03/09/15	박선민		$HEADER['nocache'] -> $HEADER['private']
// 03/10/14	박선민		마지막 수정
// 03/12/08	박선민		bugfix - replay 부분
//=======================================================
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>	1, // 템플릿 사용
		'useBoard2' => 1, // 보드관련 함수 포함
		'useApp' => 1,
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST'] ?? '');
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_player_teamhistory"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

// 기본 URL QueryString
$table_dbinfo	= isset($dbinfo['table']) ? $dbinfo['table'] : '';

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'pname', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$qs_basic = "db=".($db ?? '').					//table 이름
			"&mode=".($mode ?? '').					// mode값은 list.php에서는 당연히 빈값
			"&cateuid=".($cateuid ?? '').		//cateuid
			"&pern=".($pern ?? '').				// 페이지당 표시될 게시물 수
			"&sc_column=".($sc_column ?? '').	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string ?? '')). //search string
			"&m_category=5".
			"&m_bcode=1".
			"&pid=".($pid ?? '').
			"&pname=".urlencode($pname ?? '').
			"&page=".($page ?? '');				//현재 페이지

$qs_basic		= href_qs($qs_basic); // 해당값 초기화

if (empty($pid) || empty($pname)) back_close("선수정보가 넘어오지 않았습니다.");

//해당 선수정보만 가져오기............davej............2007-04-11
if (isset($pid)){
	$dbinfo['pid'] = $pid;
	$dbinfo['pname'] = $pname;
}
//===================
// SQL문 where절 정리
//===================
if(empty($sql_where)) $sql_where= " 1 ";

//===================//
$list = [];
// 글 수정하기/ 글 답변하기라면...
if($mode === "modify" || $mode === "reply"){
	// WARNING: $sql_where 변수를 직접 쿼리에 포함하는 것은 SQL 인젝션에 매우 취약합니다.
	//			이 변수는 신뢰할 수 있는 소스에서만 생성되어야 합니다.
	$uid = db_escape($uid ?? '');
	$num = db_escape($_GET['num'] ?? '');
	$sql = "SELECT *, password(rdate) as private_key FROM {$dbinfo['table']} WHERE $sql_where and uid='{$uid}' and num='{$num}'";
	$list = db_arrayone($sql);
	if(!$list) back("게시물의 정보가 없습니다");

	// 비공개글 제외시킴
	if(($dbinfo['enable_level'] ?? 'N') === 'Y' and !privAuth($list, "priv_level",1)){
		back("이용이 제한되었습니다. 게시물 설정 권한을 확인바랍니다.");
	}

	$list['title']	= htmlspecialchars($list['title'],ENT_QUOTES, 'UTF-8');
	$list['content']	= htmlspecialchars($list['content'],ENT_QUOTES, 'UTF-8');
	/////////////////////////////////
	// 추가되어 있는 테이블 필드 포함
	$skip_fields = array('uid', 'bid', 'userid', 'email', 'passwd', 'db', 'cateuid', 'num', 're', 'title', 'content', 'upfiles', 'upfiles_totalsize', 'docu_type', 'type', 'priv_level', 'ip', 'hit', 'hitip', 'hitdownload', 'vote', 'voteip' ,	'rdate');
	if($fieldlist = userGetAppendFields($dbinfo['table'], $skip_fields)){
		foreach($fieldlist as $value){
			$value	= htmlspecialchars($value,ENT_QUOTES, 'UTF-8');
		}
	}
	////////////////////////////////
	

	if($mode === "modify"){
		// 인증 체크
		if( ($list['bid'] ?? 0) == 0 or ($list['bid'] ?? null) == ($_SESSION['seUid'] ?? null) or privAuth($dbinfo, "priv_delete", 1) ){
			// nothing...
		}
		else back("글쓴이가 아니면 수정을 하실수 없습니다.");

		$list['name'] = htmlspecialchars($list['name'],ENT_QUOTES, 'UTF-8');
		$list['docu_type_checked'] = (strtoupper($list['docu_type']) === "HTML") ? "checked" : "";
		//$list['writeinfo_checked'] = ($list['type'] == "info") ? "checked" : "";
	}
	elseif($mode === "reply"){
		// 인증 체크
		if(!privAuth($dbinfo, "priv_reply", 1)) back("이용이 제한되었습니다.(레벨부족)");
		$qs_basic .= "&rec_email=".urlencode($list['email']);

		$list['content'] = preg_replace("/\n/i", "\n ", $list['content']);
		$list['content'] = preg_replace("/^/i", "\n\n\n[ {$list['userid']} ]님이 작성하신 글입니다\n---------------------------------------\n ", $list['content']);
		/* 혹은 글 앞에 ":"붙이기
		$list['content'] = preg_replace("/<([^<>\n]+)\n([^\n<>]+)>/i", "<\\1 \\2>", $list['content']); // 테그 붙이기
		$list['content'] = preg_replace("/^/", ": ", $list['text']);
		$list['content'] = preg_replace("/\n/", "\n: ", $list['text']);
		$list['content'] = htmlspecialchars($list['text']);
		*/
	}
} else {
	// 인증 체크
	if(!privAuth($dbinfo, "priv_write",1)) back("이용이 제한되었습니다.(레벨부족)");
}

$form_write = " method='post' action='{$thisUrl}/ok.php' ENCTYPE='multipart/form-data'>";
$form_write .= substr(href_qs("mode={$mode}&uid=" . ($list['uid'] ?? '') . "&private_key=" . ($list['private_key'] ?? ''),$qs_basic,1),0,-1);

// URL Link...
$href["list"] = "./list.php?" . href_qs("",$qs_basic);

//팀명, 팀아이디 가져오기
$tsql = " SELECT * FROM team ORDER BY tid ASC ";
$trs = db_query($tsql);
$tcnt = db_count($trs);
$tselect = '';

if($tcnt){
	for($i = 0 ; $i < $tcnt ; $i++)	{
		$tlist = db_array($trs);
		$teamid = $tlist['tid'];
		$t_name = $tlist['t_name'];
		$tsel="";
		//저장된 팀 항목 셀렉트
		if(isset($list['tid']) && $list['tid'] == $teamid){
			$tsel = "selected";
			$tselect .= "<option value='{$teamid}' {$tsel}>{$t_name}</option>";
		} else {
			$tselect .= "<option value='{$teamid}'>{$t_name}</option>";
		}
	}
}

//시즌 가져오기
$sql_season = " SELECT * FROM season ORDER BY s_start desc ";
$rs_season = db_query($sql_season);
$cnt_season = db_count($rs_season);
$sname = '';

if($cnt_season){
	for($i = 0 ; $i < $cnt_season ; $i++)	{
		$list_season = db_array($rs_season);
		$sel ="";

		//저장된 항목 셀렉트
		if(isset($list['sid']) && $list['sid'] == $list_season['sid']){
			$sel = "selected";
			$sname .= "<option value='{$list_season['sid']}' {$sel}>{$list_season['s_name']}</option>";
		} else {
			$sname .= "<option value='{$list_season['sid']}'>{$list_season['s_name']}</option>";
		}
	}
}
//선수 포지션 저장된 항목 셀렉트
if (isset($list['pposition'])) {
	${"{$list['pposition']}_selected"} = "selected";
}

//===================================
// 카테고리 정보 가져와 콤보박스 넣기
//===================================
// 카테고리 테이블과{$sql_where_cate}구함
if(($dbinfo['enable_cate'] ?? 'N') === 'Y' and empty($list['re'])){
	$table_cate	= (($dbinfo['enable_type'] ?? 'N') === 'Y') ? $dbinfo['table'] : $dbinfo['table'] . "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount 		= trim($sc_string) ? 0 : 1;
	$string_firsttotal	= isset($dbinfo['cate_depth']) ? 0 : "(전체)";
	$tmp_cateuid		= $list['cateuid'] ?? ($_GET['cateuid'] ?? null);
	$cateinfo			= boardCateInfo($dbinfo, $table_cate, $tmp_cateuid, 'Y', 1,1,$tmp_itemcount,$string_firsttotal);
	$list['catelist']		= $cateinfo['catelist'];
	unset($cateinfo);
} // end if
//===================================//

// 넘어온 값에 따라 $dbinfo값 변경
if(($dbinfo['enable_getinfo'] ?? 'N') === 'Y'){
	if(isset($_GET['cut_length']))	$dbinfo['cut_length']	= $_GET['cut_length'];
	if(isset($_GET['pern']))			$dbinfo['pern']		= $_GET['pern'];

	// skin관련
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	if( isset($_GET['html_headtpl']) and preg_match("/^[_a-z0-9]+$/",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )	
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	if( isset($_GET['skin']) and preg_match("/^[_a-z0-9]+$/",$_GET['skin'])
		and is_dir("{$thisPath}/stpl/{$_GET['skin']}") )
		$dbinfo['skin']	= $_GET['skin'];
}

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
$dbinfo['skin'] = isset($dbinfo['skin']) ? $dbinfo['skin'] : 'basic';
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/write.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/write.htm",TPL_BLOCK);

// 템플릿 마무리 할당
if( !($mode === "modify" and ($list['bid'] ?? null) != ($_SESSION['seUid'] ?? null)) ){
	switch($dbinfo['enable_userid'] ?? ''){
		case 'name'		: {$list['userid']} = $_SESSION['seName'] ?? ''; break;
		case 'nickname'	: {$list['userid']} = $_SESSION['seNickname'] ?? ''; break;
		default			: {$list['userid']} = $_SESSION['seUserid'] ?? ''; break;
	}
	$list['email']	= ($_SESSION['seEmail'] ?? null) ? $_SESSION['seEmail'] : ($email ?? '');
}

$tpl->set_var('list',$list);
$tpl->set_var('dbinfo',$dbinfo);
$tpl->set_var('href',$href);
$tpl->set_var('form_write',$form_write);
$tpl->set_var('tselect',$tselect);
$tpl->set_var('sname',$sname);
if (isset($list['pposition'])) {
	$tpl->set_var("{$list['pposition']}_selected", ${"{$list['pposition']}_selected"});
}

// 블럭 : 공지글 선택(글을 쓰때만 유효함)
if(($dbinfo['enable_writeinfo'] ?? 'N') === 'Y' and $mode === "write" and privAuth($dbinfo, "priv_writeinfo")) $tpl->process('IFWRITEINFO','ifwriteinfo');

// 블럭 : 사용자 정보
if(isset($_SESSION['seUid'])) $tpl->process('USERINFO','userinfo');
else $tpl->process('USERINFO','nouserinfo');

// 블럭 : 카테고리 정보 가져와 콤보박스 넣기
if(($dbinfo['enable_cate'] ?? 'N') === 'Y' and empty($list['re']) and isset($list['catelist'])) $tpl->process('CATELIST','catelist');

// 블럭 : 레벨 입력 부분
if(($dbinfo['enable_level'] ?? 'N') === 'Y')	$tpl->process('LEVEL','level');

// 블럭 : 파일 업로드
if(($dbinfo['enable_upload'] ?? 'N') === 'Y' or ($dbinfo['enable_upload'] ?? '') === 'multi')	
	$tpl->process('UPLOAD','upload',TPL_OPTIONAL);

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
$html_output = $tpl->process('', 'html', TPL_OPTIONAL);

switch($dbinfo['html_headpattern'] ?? ''){
	case "ht":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", $val, $html_output);	
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "h":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", $val, $html_output);	
		echo ($dbinfo['html_tail'] ?? '');
		break;
	case "t":
		// 전체 홈페이지 템플릿 읽어오기
		$HEADER['header'] = 2;
		if( isset($dbinfo['html_headtpl']) and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
		else
			@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");

		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", $val, $html_output);
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", $val, $html_output);
		break;
	default:
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", $val, $html_output);	
		echo ($dbinfo['html_tail'] ?? '');
} // end switch

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
