<?php
//=======================================================
// 설	명 : 게시판 목록보기(list.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/01/31
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 04/01/14 박선민 $list['enable_new'] 만듦
// 04/01/27 박선민 카테고리 개선
// 04/01/31 박선민 서치부분 개선
// 25/08/12 Gemini	PHP 7.x 버전 마이그레이션 및 문법 수정
//=======================================================
$HEADER=array(
	'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useSkin' =>	1, // 템플릿 사용
	'useBoard2' => 1, // privAuth()
	'useApp' => 1
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_player_teamhistory"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];

$qs_basic = "db={$db}".					//table 이름
			"&mode=".					// mode값은 list.php에서는 당연히 빈값
			"&cateuid={$cateuid}".		//cateuid
			"&pern={$pern}" .	// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string)). //search string
			"&page={$page}";				//현재 페이지

if(isset($_GET['skin']) && $_GET['skin'] == ""){	
	include_once("./dbinfo.php"); // $dbinfo, $table 값 정의
} else {
	$table = isset($_REQUEST['db']) ? $_REQUEST['db'] : '';
	$dbinfo['skin'] = isset($_GET['skin']) ? $_GET['skin'] : '';
	$dbinfo['orderby'] = "win_go ";	
}


$totla_result=array(
		"tr_game" =>	"0",
		"tr_win" =>	"0",
		"tr_loss" =>	"0",
		"tr_score" =>	"0",
		"tr_2p1" =>	"0",
		"tr_2p2" =>	"0",
		"tr_3p1" =>	"0",
		"tr_3p2" =>	"0",
		"tr_free1" =>	"0",
		"tr_free2" =>	"0",
		"tr_re" =>	"0",
		"tr_as" =>	"0",
		"tr_st" =>	"0",
		"tr_blk" =>	"0",
		"tr_to" =>	"0",
		"tr_po" =>	"0"
	);

// 인증 체크
if(!privAuth($dbinfo, "priv_list",1)) back("이용이 제한되었습니다.(레벨부족)");

// 넘어온 값에 따라 $dbinfo값 변경
if(isset($dbinfo['enable_getinfo']) && $dbinfo['enable_getinfo'] == 'Y'){
	if(isset($_GET['cut_length']))	$dbinfo['cut_length']	= $_GET['cut_length'];
	if(isset($_GET['pern']))			$dbinfo['pern']		= $_GET['pern'];

	// skin관련
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	if( isset($_GET['html_headtpl']) and preg_match("/^[_a-z0-9]+$/i",$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )	
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	if( isset($_GET['skin']) and preg_match("/^[_a-z0-9]+$/i",$_GET['skin'])
		and is_dir("{$thisPath}/stpl/{$_GET['skin']}") )
		$dbinfo['skin']	= $_GET['skin'];
}

//===================
// SQL문 where절 정리
//===================
if(!$sql_where) $sql_where= " 1 ";

// pid 파라미터가 있으면 필터링
if(isset($pid) && $pid) {
	$pid = db_escape($pid);
	$sql_where .= " and A.pid = '{$pid}' ";
}

//============================
// SQL문 order by..부분 만들기
//============================
switch(isset($_GET['sort']) ? $_GET['sort'] : ''){
	case "title": $sql_orderby = "title"; break;
	case "!title":$sql_orderby = "title DESC"; break;
	case "rdate": $sql_orderby = "rdate DESC"; break;
	case "!rdate":$sql_orderby = "rdate"; break;
	case "hit" : $sql_orderby = "hit DESC";	break;
	default :
		$sql_orderby = isset($dbinfo['orderby']) ? $dbinfo['orderby'] : "	num DESC, re ";
}

//=====
// misc
//=====
// 페이지 나눔등 각종 카운트 구하기
$count['total']=db_resultone("SELECT count(*) FROM {$table} WHERE  $sql_where ", 0, "count(*)"); // 전체 게시물 수
// 게시물 일부만 본다면
if(isset($_GET['limitrows'])) $dbinfo['pern'] = $count['total'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$count=board2Count($count['total'],$page,$dbinfo['pern'],$dbinfo['page_pern']); // 각종 카운트 구하기
$count['today']=db_resultone("SELECT count(*) FROM {$table} WHERE (rdate > unix_timestamp(curdate())) and $sql_where " , 0, "count(*)");

// 서치 폼의 hidden 필드 모두!!
$form_search =" action='{$_SERVER['PHP_SELF']}' method='get'>";
$form_search .= substr(href_qs("",$qs_basic,1),0,-1);

// URL Link...
$href['list']	= "{$thisUrl}/list.php?db={$dbinfo['db']}";
$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);	// 글씨기
if($count['nowpage'] > 1) { // 처음, 이전 페이지
	$href['firstpage']="{$_SERVER['PHP_SELF']}?" . href_qs("page=1",$qs_basic);
	$href['prevpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage']-1),$qs_basic);
} else {
	$href['firstpage']="javascript: void(0)";
	$href['prevpage']	="javascript: void(0)";
}
if($count['nowpage'] < $count['totalpage']){ // 다음, 마지막 페이지
	$href['nextpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage']+1),$qs_basic);
	$href['lastpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=".$count['totalpage'],$qs_basic);
} else {
	$href['nextpage']	="javascript: void(0)";
	$href['lastpage'] ="javascript: void(0)";
}
$href['prevblock']= ($count['nowblock']>1) ? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['firstpage']-1) ,$qs_basic): "javascript: void(0)";// 이전 페이지 블럭
$href['nextblock']= ($count['totalpage'] > $count['lastpage'])? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['lastpage'] +1),$qs_basic) : "javascript: void(0)";// 다음 페이지 블럭

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
if( !is_file("{$thisPath}/stpl/{$dbinfo['skin']}/list.htm") ) $dbinfo['skin']="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo['skin']}/list.htm",TPL_BLOCK);
// Limit로 필요한 게시물만 읽음.
$limitno	= isset($_GET['limitno']) ? $_GET['limitno'] : $count['firstno'];
$limitrows	= isset($_GET['limitrows']) ? $_GET['limitrows'] : $count['pern'];
$sql = "SELECT A.* FROM {$table} as A, {$table_season} as B WHERE $sql_where and A.sid = B.sid ORDER BY	B.s_start desc LIMIT {$limitno},{$limitrows}";

$rs_list = db_query($sql);

if(!$total=db_count($rs_list)) {	// 게시물이 하나도 없다면...
	if(isset($_GET['sc_string'])) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($_GET['sc_string']),ENT_QUOTES));
		$tpl->process('LIST', 'nosearch');
	}
	else // 게시물이 없다면..
		$tpl->process('LIST', 'nolist');
} else {
	if(!(isset($dbinfo['row_pern']) && $dbinfo['row_pern']<1)) $dbinfo['row_pern']=1; // 한줄에 여러값 출력이 아닌 경우
	for($i=0; $i<$total; $i+=$dbinfo['row_pern']){
		if(isset($dbinfo['row_pern']) && $dbinfo['row_pern'] >= 1) $tpl->set_var('CELL',"");
		
		for($j=$i; ($j-$i < $dbinfo['row_pern']) && ($j < $total); $j++) { // 한줄에 여러값 출력시 루틴
			if( $j>=$total ){
				if(isset($dbinfo['row_pern']) && $dbinfo['row_pern'] > 1) $tpl->process('CELL','nocell',TPL_APPEND);
				continue;
			}
			$list		= db_array($rs_list);
			$list['no']	= $count['lastnum'];
			$list['rede']	= strlen($list['re']);
			
			if($i == 0){
				$new_list = $list;
				$tpl->set_var('new_list'			, $new_list);
				$tpl->process('NEW_LIST','new_list',TPL_APPEND);
			}
			
			$total_result['tr_game'] = $total_result['tr_game'] + $list['tr_game'];
			$total_result['tr_win'] = $total_result['tr_win'] + $list['tr_win'];
			$total_result['tr_loss'] = $total_result['tr_loss'] + $list['tr_loss'];
			$total_result['tr_score'] = $total_result['tr_score'] + $list['tr_score'];
			$total_result['tr_2p1'] = $total_result['tr_2p1'] + $list['tr_2p1'];
			$total_result['tr_2p2'] = $total_result['tr_2p2'] + $list['tr_2p2'];
			$total_result['tr_3p1'] = $total_result['tr_3p1'] + $list['tr_3p1'];
			$total_result['tr_3p2'] = $total_result['tr_3p2'] + $list['tr_3p2'];
			$total_result['tr_free1'] = $total_result['tr_free1'] + $list['tr_free1'];
			$total_result['tr_free2'] = $total_result['tr_free2'] + $list['tr_free2'];
			$total_result['tr_re'] = $total_result['tr_re'] + $list['tr_re'];
			$total_result['tr_as'] = $total_result['tr_as'] + $list['tr_as'];
			$total_result['tr_st'] = $total_result['tr_st'] + $list['tr_st'];
			$total_result['tr_blk'] = $total_result['tr_blk'] + $list['tr_blk'];
			$total_result['tr_to'] = $total_result['tr_to'] + $list['tr_to'];
			$total_result['tr_po'] = $total_result['tr_po'] + $list['tr_po'];

			$list['rdate']= $list['rdate'] ? date("Y.m.d", $list['rdate']) : "";	//	날짜 변환
			if(!$list['title']) $list['title'] = "제목없음…";
		
			//답변이 있을 경우 자리는 길이를 더 줄임
			$cut_length = isset($list['rede']) && $list['rede'] ? $dbinfo['cut_length'] - $list['rede'] -3 : $dbinfo['cut_length'];
			$list['cut_title'] = cut_string($list['title'], $cut_length);

			//	Search 단어 색깔 표시
			if(isset($_GET['sc_string'])){
				$sc_string = $_GET['sc_string'];
				$pattern = '/' . preg_quote($sc_string, '/') . '/i';
				if(isset($_GET['sc_column'])){
					if($_GET['sc_column'] == "title")
						$list['cut_title'] = preg_replace($pattern, "<font color=darkred>\\0</font>",	$list['cut_title']);
					else
						$list[$_GET['sc_column']]	= preg_replace($pattern, "<font color='darkred'>\\0</font>", $list[$_GET['sc_column']]);
				} else {
					$list['userid']	= preg_replace($pattern, "<font color=darkred>\\0</font>", $list['userid']);
					$list['cut_title']= preg_replace($pattern, "<font color=darkred>\\0</font>",	$list['cut_title']);
				}
			}

			// URL Link...
			$href['download']	= "{$thisUrl}/download.php?db={$dbinfo['db']}&uid={$list['uid']}";
			$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid={$list['uid']}",$qs_basic);
			$href['modify']	= "{$thisUrl}/write.php?" . href_qs("mode=modify&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);
			$href['delete']	= "{$thisUrl}/ok.php?" . href_qs("mode=delete&uid={$list['uid']}&num={$list['num']}&time=".time(),$qs_basic);

/*			$trs1 = substr($list['tr_season'], 0, 2);	
			$trs2 = substr($list['tr_season'], 2);	
			
			$list['tr_season'] = $trs1."<br>".$trs2;
*/		
			
			$tpl->set_var('href.modify'		, $href['modify']);
			$tpl->set_var('href.delete'		, $href['delete']);
			$tpl->set_var('href.read'		, $href['read']);
			$tpl->set_var('href.download'	, $href['download']);
			$tpl->set_var('list'			, $list);
			
			$count['lastnum']--;
			
			if(isset($dbinfo['row_pern']) && $dbinfo['row_pern'] >= 1){
				if($j == 0) $tpl->drop_var('blockloop');
				else $tpl->set_var('blockloop',true);
				$tpl->process('CELL','cell',TPL_APPEND);
			}
		} // end for (j)
		
		$tpl->process('LIST','list',TPL_OPTIONAL|TPL_APPEND);
		$tpl->set_var('blockloop',true);
	} // end for (i)
	$tpl->drop_var('blockloop');
	$tpl->drop_var('href.read'); unset($href['read']);
} // end if (게시물이 있다면...)

$time = time();
$list['wdat'] =	date("m/d");
$list['wdat'] =	$list['wdat']." WKBL 종합순위";

$tpl->set_var('total_result'			, $total_result);
$tpl->process('LIST','total',TPL_APPEND);

$tpl->set_var('list.wdat'			,isset($list['wdat']) ? $list['wdat'] : '');// dbinfo 정보 변수
// 템플릿 마무리 할당
$tpl->set_var('dbinfo'			,$dbinfo);// dbinfo 정보 변수
$tpl->set_var('cateinfo.uid'	,isset($cateinfo['uid']) ? $cateinfo['uid'] : '');
$tpl->set_var('cateinfo.title'	,isset($cateinfo['title']) ? $cateinfo['title'] : '');
$tpl->set_var('count'			,$count);	// 게시판 각종 카운트
$tpl->set_var('href'			,$href);	// 게시판 각종 링크
$tpl->set_var('sc_string'		,htmlspecialchars(stripslashes(isset($_REQUEST['sc_string']) ? $_REQUEST['sc_string'] : ''),ENT_QUOTES));	// 서치 단어
$tpl->set_var('form_search'		,$form_search);	// form actions, hidden fileds

if(!isset($_GET['limitrows'])) { // 게시물 일부 보기에서는 카테고리, 블럭이 필요 없을 것임
	// 블럭 : 카테고리(상위, 동일, 서브) 생성
	if(isset($dbinfo['enable_cate']) && $dbinfo['enable_cate'] == 'Y'){
		if(isset($cateinfo['catelist'])){
			$tpl->set_var('cateinfo.catelist',$cateinfo['catelist']);
			$tpl->process('CATELIST','catelist',TPL_APPEND);
		}

		if(is_array(isset($cateinfo['highcate']) ? $cateinfo['highcate'] : null)){
			foreach($cateinfo['highcate'] as $key =>	$value){
				$tpl->set_var('href.highcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid={$key}",$qs_basic));
				$tpl->set_var('highcate.uid',$key);
				$tpl->set_var('highcate.title',$value);
				$tpl->process('HIGHCATE','highcate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if(is_array(isset($cateinfo['samecate']) ? $cateinfo['samecate'] : null)){
			foreach($cateinfo['samecate'] as $key =>	$value){
				if(isset($cateinfo['uid']) && $key == $cateinfo['uid'])
					$tpl->set_var('samecate.selected'," selected ");
				else
					$tpl->set_var('samecate.selected',"");
				$tpl->set_var('href.samecate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid={$key}",$qs_basic));
				$tpl->set_var('samecate.uid',$key);
				$tpl->set_var('samecate.title',$value);
				$tpl->process('SAMECATE','samecate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if(is_array(isset($cateinfo['subcate']) ? $cateinfo['subcate'] : null)){
			foreach($cateinfo['subcate'] as $key =>	$value){
				// subsubcate...
				$tpl->drop_var('SUBSUBCATE');
				if(is_array(isset($cateinfo['subsubcate'][$key]) ? $cateinfo['subsubcate'][$key] : null)){
					$blockloop = $tpl->get_var('blockloop');
					$tpl->drop_var('blockloop');
					foreach($cateinfo['subsubcate'][$key] as $subkey =>	$subvalue){
						$tpl->set_var('href.subsubcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid={$subkey}",$qs_basic));
						$tpl->set_var('subsubcate.uid',$subkey);
						$tpl->set_var('subsubcate.title',$subvalue);
						$tpl->process('SUBSUBCATE','subsubcate',TPL_OPTIONAL|TPL_APPEND);
						$tpl->set_var('blockloop',true);
					}
					$tpl->set_var('blockloop',$blockloop);
				} // end if

				$tpl->set_var('href.subcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid={$key}",$qs_basic));
				$tpl->set_var('subcate.uid',$key);
				$tpl->set_var('subcate.title',$value);
				$tpl->process('SUBCATE','subcate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
	} // end if

	// 블럭 : 첫페이지, 이전페이지
	if($count['nowpage'] > 1){
		$tpl->process('FIRSTPAGE','firstpage');
		$tpl->process('PREVPAGE','prevpage');
	} else {
		$tpl->process('FIRSTPAGE','nofirstpage');
		$tpl->process('PREVPAGE','noprevpage');
	}

	// 블럭 : 페이지 블럭 표시
		// <-- (이전블럭) 부분
		if ($count['nowblock']>1) $tpl->process('PREVBLOCK','prevblock');
		else $tpl->process('PREVBLOCK','noprevblock');
		// 1 2 3 4 5 부분
		for ($i=$count['firstpage'];$i<=$count['lastpage'];$i++) {
			$tpl->set_var('blockcount',$i);
			if($i == $count['nowpage'])
				$tpl->process('BLOCK','noblock',TPL_APPEND);
			else {
				$tpl->set_var('href.blockcount', "{$_SERVER['PHP_SELF']}?" . href_qs("page=".$i,$qs_basic) );
				$tpl->process('BLOCK','block',TPL_APPEND);
			}
		} // end for
		// --> (다음블럭) 부분
		if ($count['totalpage'] > $count['lastpage']	) $tpl->process('NEXTBLOCK','nextblock');
		else $tpl->process('NEXTBLOCK','nonextblock');

	// 블럭 : 다음페이지, 마지막 페이지
	if($count['nowpage'] < $count['totalpage']){
		$tpl->process('NEXTPAGE','nextpage');
		$tpl->process('LASTPAGE','lastpage');
	} else {
		$tpl->process('NEXTPAGE','nonextpage');
		$tpl->process('LASTPAGE','nolastpage');
	}
} // end if

// 블럭 : 글쓰기
if(privAuth($dbinfo, "priv_write")) $tpl->process('WRITE','write');
else $tpl->process('WRITE','nowrite');

// 마무리
$val="\\1{$thisUrl}/stpl/{$dbinfo['skin']}/images/";
// - 사이트 템플릿 읽어오기
// ereg() 대신 preg_match() 사용
if(preg_match("/^(ht|h|t)$/",isset($dbinfo['html_headpattern']) ? $dbinfo['html_headpattern'] : '')){
	$HEADER['header'] == 2;
	if( (isset($dbinfo['html_headtpl']) ? $dbinfo['html_headtpl'] : '') != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php") )
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$dbinfo['html_headtpl']}.php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
switch(isset($dbinfo['html_headpattern']) ? $dbinfo['html_headpattern'] : ''){
	case "ht":
		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", $val, $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "h":
		echo $SITE['head'] . $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", $val, $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'];
		break;
	case "t":
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", $val, $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'] . $SITE['tail'];
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", $val, $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo $dbinfo['html_head'];
		echo preg_replace("/([\"|\'])images\//", $val, $tpl->process('', 'html', TPL_OPTIONAL));
		echo $dbinfo['html_tail'];
} // end switch
?>
