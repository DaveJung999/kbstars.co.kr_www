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
// 24/05/20 Gemini PHP 7 마이그레이션
//=======================================================
$HEADER=array(
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useSkin' =>  1, // 템플릿 사용
		'useBoard2' => 1, // privAuth()
		'useApp' => 1
		);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath	= dirname(__FILE__);
$thisUrl	= "/spoll"; // 마지막 "/"이 빠져야함

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text', 'html_headtpl'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$qs_basic = "db={$db}".					//table 이름
			"&mode=".					// mode값은 list.php에서는 당연히 빈값
			"&cateuid={$cateuid}".		//cateuid
			"&team={$team}".		//team
			"&pern={$pern}" .	// 페이지당 표시될 게시물 수
			"&sc_column={$sc_column}".	//search column
			"&sc_string=" . urlencode(stripslashes($sc_string)) . //search string
			"&html_headtpl={$html_headtpl}".
			"&m_category=5".
			"&m_bcode=1".
			"&page={$page}";				//현재 페이지

include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

// 인증 체크
if(!privAuth($dbinfo, "priv_list",1)) back("이용이 제한되었습니다.(레벨부족)");

//===================
// 카테고리 정보 구함
//===================
// PHP 7 호환성: 배열 키에 따옴표 사용
if(($dbinfo['enable_cate'] ?? null) == 'Y'){
	$table_cate	= (($dbinfo['enable_type'] ?? null) == 'Y') ? ($table ?? '') : ($table ?? '') . "_cate";

	// 카테고리정보구함 (dbinfo, table_cate, cateuid, $enable_catelist='Y', sw_topcatetitles, sw_notitems, sw_itemcount,string_firsttotal)
	// highcate[], samecate[], subcate[], subsubcate[], subcateuid[], catelist
	$tmp_itemcount = trim($sc_string) ? 0 : 1;
	$cateinfo=boardCateInfo(($dbinfo ?? null), $table_cate, ($cateuid ?? null), 'Y', 1,1,$tmp_itemcount,"(종합)");

	if(!($cateuid ?? null)){
		$cateinfo['uid']		= "{$_SERVER['PHP_SELF']}?" . href_qs("",$qs_basic);
		$cateinfo['title']	= "전체";
	}
} // end if

// 넘어온 값에 따라 $dbinfo값 변경
if(($dbinfo['enable_getinfo'] ?? null) == 'Y'){
	// PHP 7 호환성: isset() 체크 및 (int) 형변환
	if(isset($_GET['cut_length']))	$dbinfo['cut_length']	= $_GET['cut_length'];
	if(isset($_GET['pern']))			$dbinfo['pern']		= $_GET['pern'];

	// skin관련
	if(isset($_GET['html_headpattern']))	$dbinfo['html_headpattern'] = $_GET['html_headpattern'];
	// PHP 7 호환성: eregi()를 preg_match()로 대체
	if( isset($_GET['html_headtpl']) and preg_match('/^[_a-z0-9]+$/i',$_GET['html_headtpl'])
		and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_{$_GET['html_headtpl']}.php") )
		$dbinfo['html_headtpl'] = $_GET['html_headtpl'];
	// PHP 7 호환성: eregi()를 preg_match()로 대체
	if( isset($_GET['skin']) and preg_match('/^[_a-z0-9]+$/i',$_GET['skin'])
		and is_dir("{$thisPath}/stpl/{$_GET['skin']}") )
		$dbinfo['skin']	= $_GET['skin'];
}

//===================
// SQL문 where절 정리
//===================
$sql_where = ''; // init
// 한 table에 여러 게시판 생성의 경우
if(($dbinfo['table_name'] ?? '') != ($dbinfo['db'] ?? '')) $sql_where=" db='".db_escape($dbinfo['db']) . "' "; // $sql_where 사용 시작
// PHP 7 호환성: 배열 키에 따옴표 사용
if(($dbinfo['enable_type'] ?? null) == 'Y') $sql_where = $sql_where ? $sql_where	. " and type='docu' " : " type='docu' ";
// 해당 카테고리만 볼려면
if(is_array($cateinfo['subcate_uid'] ?? null) and sizeof($cateinfo['subcate_uid'])>0 ) $sql_where = $sql_where ? $sql_where	. " and ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) " : " ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) ";

$team = $_GET['team'] ?? '';
if(trim($team)) $sql_where= "	tid = ".db_escape($team) . " ";

if(!$sql_where) $sql_where= "	tid = ".db_escape($team) . " ";

// 서치 게시물만..
if(trim($sc_string)){
	if($sql_where) $sql_where .= " and ";
	if($sc_column)
		if(in_array($sc_column,array("bid","uid")))
			$sql_where .=" ({$sc_column}='".db_escape($sc_string) . "') ";
		else
			$sql_where .=" ({$sc_column} like '%".db_escape($sc_string) . "%') ";
	else
		$sql_where .=" ((userid like '%".db_escape($sc_string) . "%') or (title like '%".db_escape($sc_string) . "%') or (content like '%".db_escape($sc_string) . "%')) ";
}
// 답변글 안보이기, 서치시에는 답변글 무조건 보이기 위해 서치의 elseif 씀

// 비공개글 제외시킴
if(($dbinfo['enable_level'] ?? null) == 'Y'){
	if($sql_where) $sql_where .= " and ";
	if($_SESSION['seUid'] ?? null){
		$priv_level	= ($dbinfo['gid'] ?? null) ? (int)($_SESSION['seGroup'][$dbinfo['gid']] ?? 0) : (int)($_SESSION['sePriv']['level'] ?? 0);
		$sql_where .=" ( priv_level<={$priv_level} or bid='".db_escape($_SESSION['seUid']) . "' ) ";
	}
	else $sql_where .="	priv_level=0 ";
} // end if

if(!$sql_where) $sql_where= " 1 ";

//============================
// SQL문 order by..부분 만들기
//============================
$sort = $_GET['sort'] ?? null;
switch($sort){
	case "title": $sql_orderby = "title"; break;
	case "!title":$sql_orderby = "title DESC"; break;
	case "rdate": $sql_orderby = "rdate DESC"; break;
	case "!rdate":$sql_orderby = "rdate"; break;
	case "hit" : $sql_orderby = "hit DESC";	break;
	default :
		$sql_orderby = ($dbinfo['orderby'] ?? null) ? $dbinfo['orderby'] : "	num DESC, re ";
}

//=====
// misc
//=====
// 페이지 나눔등 각종 카운트 구하기
$table = $table ?? '';
$count['total']=db_resultone("SELECT count(*) FROM {$table} WHERE  $sql_where ", 0, "count(*)"); // 전체 게시물 수
// 게시물 일부만 본다면
if($_GET['limitrows'] ?? null) $dbinfo['pern'] = $count['total'];
$dbinfo_pern = $dbinfo['pern'] ?? 10;
$dbinfo_page_pern = $dbinfo['page_pern'] ?? 5;
$count=board2Count($count['total'],($page ?? 1),$dbinfo_pern,$dbinfo_page_pern); // 각종 카운트 구하기
$count['today']=db_resultone("SELECT count(*) FROM {$table} WHERE (rdate > unix_timestamp(curdate())) and $sql_where " , 0, "count(*)");

//-------------spemin
if(!($dbinfo['pern'] ?? null)) $dbinfo['pern']		= $count['total'];
if(!($dbinfo['row_pern'] ?? null)) $dbinfo['row_pern']		= $count['total'];
//-------------------

// 서치 폼의 hidden 필드 모두!!
$form_search =" action='{$_SERVER['PHP_SELF']}' method='get'>";
$form_search .= substr(href_qs("",$qs_basic,1),0,-1);

// URL Link...
$href['list']	= "{$thisUrl}/list.php?db=" . ($dbinfo['db'] ?? '');
$href['write']	= "{$thisUrl}/write.php?" . href_qs("mode=write&time=".time(),$qs_basic);	// 글씨기
if(($count['nowpage'] ?? 1) > 1) { // 처음, 이전 페이지
	$href['firstpage']="{$_SERVER['PHP_SELF']}?" . href_qs("page=1",$qs_basic);
	$href['prevpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage']-1),$qs_basic);
} else {
	$href['firstpage']="javascript: void(0)";
	$href['prevpage']	="javascript: void(0)";
}
if(($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)){ // 다음, 마지막 페이지
	$href['nextpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['nowpage']+1),$qs_basic);
	$href['lastpage']	="{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['totalpage'] ?? 1),$qs_basic);
} else {
	$href['nextpage']	="javascript: void(0)";
	$href['lastpage'] ="javascript: void(0)";
}
$href['prevblock']= (($count['nowblock'] ?? 1)>1)					? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['firstpage']-1) ,$qs_basic): "javascript: void(0)";// 이전 페이지 블럭
$href['nextblock']= (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1))? "{$_SERVER['PHP_SELF']}?" . href_qs("page=" . ($count['lastpage'] +1),$qs_basic) : "javascript: void(0)";// 다음 페이지 블럭

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// 템플릿 기반 웹 페이지 제작
$tpl = new phemplate("","remove_nonjs");
$dbinfo_skin = $dbinfo['skin'] ?? 'board_basic';
if( !is_file("{$thisPath}/stpl/{$dbinfo_skin}/list.htm") ) $dbinfo_skin="board_basic";
$tpl->set_file('html',"{$thisPath}/stpl/{$dbinfo_skin}/list.htm",TPL_BLOCK);

/////////////////////////////
// 게시판 맨 위에 무조건 공지글(type필드에 info인 것) 읽어오기
if(($dbinfo['enable_writeinfo'] ?? null) == 'Y' and ($dbinfo['enable_type'] ?? null) == 'Y' and ($dbinfo['row_pern'] ?? 0)<2 and strlen($sc_string) == 0 and ($_GET['limitrows'] ?? 0)<1){
	
	// 공지글은 검색시, iframe으로 일부만 볼때, 그리고 한줄에 여러줄 출력할때는 안보인다.
	if(strlen($sc_string) == 0 and strlen($_GET['skin'] ?? '') == 0 and ($dbinfo['row_pern'] ?? 0)<2 ){
		$sql_where_info = " db='".db_escape($dbinfo['db']) . "' and type='info' ";
		// 공지도 해당 카테고리만
		if(is_array($cateinfo['subcate_uid'] ?? null) and sizeof($cateinfo['subcate_uid'])>0 ) $sql_where_info = $sql_where_info ? $sql_where_info	. " and ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) " : " ( cateuid in ( " . implode(",",$cateinfo['subcate_uid']) . ") ) ";
		if(!$sql_where_info) $sql_where_info = " 1 ";
		$sql = "SELECT * FROM {$table} WHERE {$sql_where_info} ORDER BY num DESC, re";
		$rs_list_writeinfo = db_query($sql);
		$total_writeinfo=db_count($rs_list_writeinfo);
		for($i=0;$i<$total_writeinfo;$i++){
			$list		= db_array($rs_list_writeinfo);
			$list['no']	= $total_writeinfo - $i;
			$list['rede']	= strlen($list['re'] ?? '');

			// new image넣을 수 있게 <opt name="enable_new">..
			if(($list['rdate'] ?? 0) > time()-3600*24) $list['enable_new']="Y";
			$list['rdate']= ($list['rdate'] ?? null) ? date("y/m/d", $list['rdate']) : "";	//	날짜 변환
			if(!($list['title'] ?? null)) $list['title'] = "제목없음…";

			//답변이 있을 경우 자르는 길이를 더 줄임
			$cut_length = ($list['rede'] ?? 0) ? ($dbinfo['cut_length'] ?? 50) - ($list['rede'] ?? 0) -3 : ($dbinfo['cut_length'] ?? 50);
			$list['cut_title'] = cut_string($list['title'], $cut_length);

			//	답변 게시물 답변 아이콘 표시
			if(($list['rede'] ?? 0) > 0){
				$list['cut_title'] = "<img src='/scommon/spacer.gif' width='" . (($list['rede'] ?? 0)-1)*8	. "' border=0><img src='/scommon/re.gif' align='absmiddle' border=0> {$list['cut_title']}";
			}

			// 업로드파일 처리
			if((($dbinfo['enable_upload'] ?? null) != 'N') and ($list['upfiles'] ?? null)){
				$upfiles=unserialize($list['upfiles']);
				if(!is_array($upfiles)) { // 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile']['name']=$list['upfiles'] ?? '';
					$upfiles['upfile']['size']=(int)($list['upfiles_totalsize'] ?? 0);
				}
				foreach($upfiles as $key =>  $value){
					if($value['name'] ?? null)
						$upfiles[$key]['href']="download.php?" . href_qs("uid={$list['uid']}&upfile={$key}",$qs_basic);
				} // end foreach
				$list['upfiles']=$upfiles;
				unset($upfiles);
			} // end if 업로드파일 처리

			// URL Link...
			$href['read']		= "{$thisUrl}/read.php?" . href_qs("uid=" . ($list['uid'] ?? ''),$qs_basic);
			$href['download']	= "{$thisUrl}/download.php?db=" . ($dbinfo['db'] ?? '') . "&uid=" . ($list['uid'] ?? '');

			// 템플릿 YESRESULT 값들 입력
			$tpl->set_var('href.read'		,$href['read']);
			$tpl->set_var('href.download'	,$href['download']);
			$tpl->set_var('list'			,$list);

			$tpl->process('INFO','info',TPL_OPTIONAL|TPL_APPEND);
			$tpl->set_var('blockloop',true);
		} // end for
		$tpl->drop_var('blockloop'); // 공지글이기에 다음 게시물을 위해서 주석처리
	} // end if
} // end if
///////////////////////////////////

// Limit로 필요한 게시물만 읽음.
$limitno	= $_GET['limitno'] ?? ($count['firstno'] ?? 0);
$limitrows	= $_GET['limitrows'] ?? ($count['pern'] ?? 10);
$sql = "SELECT * FROM {$table} WHERE $sql_where ORDER BY {$sql_orderby} LIMIT {$limitno},{$limitrows}";
$rs_list = db_query($sql);

if(!($total=db_count($rs_list))) {	// 게시물이 하나도 없다면...
	if(trim($sc_string)) { // 서치시 게시물이 없다면..
		$tpl->set_var('sc_string',htmlspecialchars(stripslashes($sc_string),ENT_QUOTES));
		$tpl->process('LIST', 'nosearch');
	}
	else // 게시물이 없다면. .
		$tpl->process('LIST', 'nolist');
} else {
	if($dbinfo['row_pern']<1) $dbinfo['row_pern']=1; // 한줄에 여러값 출력이 아닌 경우
	for($i=0; $i<$total; $i+=$dbinfo['row_pern']){
		if($dbinfo['row_pern'] >= 1) $tpl->set_var('CELL',"");

		for($j=$i; ($j-$i < $dbinfo['row_pern']) && ($j < $total); $j++) { // 한줄에 여러값 출력시 루틴
			if( $j>=$total ){
				if($dbinfo['row_pern'] > 1) $tpl->process('CELL','nocell',TPL_APPEND);
				continue;
			}
			$list		= db_array($rs_list);
			$count_lastnum = (isset($count['lastnum']) ? $count['lastnum']-- : $total--);
			$list['no']	= $count_lastnum;
			$list['rede']	= strlen($list['re'] ?? '');

			// new image넣을 수 있게 <opt name="enable_new">..
			if(($list['rdate'] ?? 0)>time()-3600*24) $list['enable_new']="<img src='/images/icon_new.gif' width='30' height='15' border='0'>";

			// 업로드파일 처리
			if((($dbinfo['enable_upload'] ?? null) != 'N') and ($list['upfiles'] ?? null)){
				$upfiles=unserialize($list['upfiles']);
				if(!is_array($upfiles)) {
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile']['name']=$list['upfiles'] ?? '';
					$upfiles['upfile']['size']=(int)($list['upfiles_totalsize'] ?? 0);
				}
				foreach($upfiles as $key =>  $value){
					if($value['name'] ?? null)
						$upfiles[$key]['href']="{$thisUrl}/download.php?" . href_qs("uid=" . ($list['uid'] ?? '') . "&upfile={$key}",$qs_basic);
				} // end foreach
				$list['upfiles']=$upfiles;
				unset($upfiles);
			} // end if 업로드파일 처리

			// URL Link...
			$href['download']	= "{$thisUrl}/download.php?db=" . ($dbinfo['db'] ?? '') . "&uid=" . ($list['uid'] ?? '');

			$href['read']		= "{$thisUrl}/read.php?" . href_qs("db=" . ($dbinfo['db'] ?? '') . "&uid=" . ($list['uid'] ?? ''),$qs_basic);

			$href['openview'] = "onclick=javascript:window.open('{$thisUrl}/download.php?mode=allimages&db=" . ($dbinfo['db'] ?? '') . "&uid=" . ($list['uid'] ?? '') . "','이미지','width=100,height=100,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0')";

			// 템플릿 YESRESULT 값들 입력
			$tpl->set_var('href.openview'		, $href['openview']);
			$tpl->set_var('href.read'		, $href['read']);
			$tpl->set_var('href.download'	, $href['download']);
			$tpl->set_var('list'			, $list);

			$count['lastnum']--;
			
			if($dbinfo['row_pern'] >= 1){
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

$db = $db ?? '';
switch($db){
	case 'photo':
		$list['sub_title'] = "<a href='/d06_data/index.php' class='white'>세이버스 룸</a> >>";
		$list['m_title'] = "<a href='/d06_data/index.php' class='white'>포토갤러리</a>";
		break;
	case 'movie':
		$list['sub_title'] = "<a href='/d06_data/index.php' class='white'>세이버스 룸</a> >>";
		$list['m_title'] = "<a href='/d06_data/movie.php' class='white'>동영상갤러리</a>";
		break;
	case 'free_board':
		$list['sub_title'] = "커뮤니티</a> >>";
		$list['m_title'] = "<a href='/d05_supporters/index.php' class='white'>자유게시판</a>";
		break;
	case 'kbplayer':
		$list['sub_title'] = "커뮤니티</a> >>";
		$list['m_title'] = "<a href='/d05_supporters/kbplayer.php' class='white'>KB선수단 게시판</a>";
		break;
	case 'event_board':
		$list['sub_title'] = "커뮤니티</a> >>";
		$list['m_title'] = "<a href='/d05_supporters/event.php' class='white'>이벤트</a>";
		break;
	case 'news':
		$list['sub_title'] = "NEWS</a> >>";
		$list['m_title'] = "<a href='/d04_news/index.php' class='white'>SAVERS NOTICE</a>";
		break;
	case 'briefing':
		$list['sub_title'] = "NEWS</a> >>";
		$list['m_title'] = "<a href='/d04_news/news.php' class='white'>MEDIA CENTER</a>";
	break;
	case 'hot_focus':
		$list['sub_title'] = "NEWS</a> >>";
		$list['m_title'] = "<a href='/d04_news/hotfocus.php' class='white'>HOT FOCUS</a>";
	break;
	
} // end switch
		
$tpl->set_var('list.sub_title'			,($list['sub_title'] ?? ''));// dbinfo 정보 변수
$tpl->set_var('list.m_title'			,($list['m_title'] ?? ''));// dbinfo 정보 변수
		

// 템플릿 마무리 할당
$tpl->set_var('dbinfo'			, $dbinfo ?? []); // dbinfo 정보 변수
$tpl->set_var('cateinfo.uid'	, ($cateinfo['uid'] ?? ''));
$tpl->set_var('cateinfo.title'	, ($cateinfo['title'] ?? ''));
$tpl->set_var('count'			, $count ?? []);	// 게시판 각종 카운트
$tpl->set_var('href'			, $href ?? []);	// 게시판 각종 링크
$tpl->set_var('sc_string'		,htmlspecialchars(stripslashes($sc_string ?? ''),ENT_QUOTES));	// 서치 단어
$tpl->set_var('form_search'		,$form_search);	// form actions, hidden fileds

if(!($_GET['limitrows'] ?? null)) { // 게시물 일부 보기에서는 카테고리, 블럭이 필요 없을 것임
	// 블럭 : 카테고리(상위, 동일, 서브) 생성
	if(($dbinfo['enable_cate'] ?? null) == 'Y'){
		if(($cateinfo['catelist'] ?? null)){
			$tpl->set_var('cateinfo.catelist',$cateinfo['catelist']);
			$tpl->process('CATELIST','catelist',TPL_APPEND);
		}

		if(is_array($cateinfo['highcate'] ?? null)){
			foreach($cateinfo['highcate'] as $key =>  $value){
				$tpl->set_var('href.highcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
				$tpl->set_var('highcate.uid',$key);
				$tpl->set_var('highcate.title',$value);
				$tpl->process('HIGHCATE','highcate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if(is_array($cateinfo['samecate'] ?? null)){
			foreach($cateinfo['samecate'] as $key =>  $value){
				if($key == ($cateinfo['uid'] ?? null))
					$tpl->set_var('samecate.selected'," selected ");
				else
					$tpl->set_var('samecate.selected',"");
				$tpl->set_var('href.samecate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
				$tpl->set_var('samecate.uid',$key);
				$tpl->set_var('samecate.title',$value);
				$tpl->process('SAMECATE','samecate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
		if(is_array($cateinfo['subcate'] ?? null)){
			foreach($cateinfo['subcate'] as $key =>  $value){
				// subsubcate...
				$tpl->drop_var('SUBSUBCATE');
				if(is_array($cateinfo['subsubcate'][$key] ?? null)){
					$blockloop = $tpl->get_var('blockloop');
					$tpl->drop_var('blockloop');
					foreach($cateinfo['subsubcate'][$key] as $subkey =>  $subvalue){
						$tpl->set_var('href.subsubcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$subkey,$qs_basic));
						$tpl->set_var('subsubcate.uid',$subkey);
						$tpl->set_var('subsubcate.title',$subvalue);
						$tpl->process('SUBSUBCATE','subsubcate',TPL_OPTIONAL|TPL_APPEND);
						$tpl->set_var('blockloop',true);
					}
					$tpl->set_var('blockloop',$blockloop);
				} // end if

				$tpl->set_var('href.subcate',"{$_SERVER['PHP_SELF']}?" . href_qs("cateuid=".$key,$qs_basic));
				$tpl->set_var('subcate.uid',$key);
				$tpl->set_var('subcate.title',$value);
				$tpl->process('SUBCATE','subcate',TPL_OPTIONAL|TPL_APPEND);
				$tpl->set_var('blockloop',true);
			}
			$tpl->drop_var('blockloop');
		} // end if
	} // end if

	// 블럭 : 첫페이지, 이전페이지
	if(($count['nowpage'] ?? 1) > 1){
		$tpl->process('FIRSTPAGE','firstpage');
		$tpl->process('PREVPAGE','prevpage');
	} else {
		$tpl->process('FIRSTPAGE','nofirstpage');
		$tpl->process('PREVPAGE','noprevpage');
	}

	// 블럭 : 페이지 블럭 표시
		// <-- (이전블럭) 부분
		if (($count['nowblock'] ?? 1)>1) $tpl->process('PREVBLOCK','prevblock');
		else $tpl->process('PREVBLOCK','noprevblock');
		// 1 2 3 4 5 부분
		for ($i=($count['firstpage'] ?? 1);$i<=($count['lastpage'] ?? 1);$i++) {
			$tpl->set_var('blockcount',$i);
			if($i == ($count['nowpage'] ?? 1))
				$tpl->process('BLOCK','noblock',TPL_APPEND);
			else {
				$tpl->set_var('href.blockcount', "{$_SERVER['PHP_SELF']}?" . href_qs("page=".$i,$qs_basic) );
				$tpl->process('BLOCK','block',TPL_APPEND);
			}
		} // end for
		// --> (다음블럭) 부분
		if (($count['totalpage'] ?? 1) > ($count['lastpage'] ?? 1)) $tpl->process('NEXTBLOCK','nextblock');
		else $tpl->process('NEXTBLOCK','nonextblock');

	// 블럭 : 다음페이지, 마지막 페이지
	if(($count['nowpage'] ?? 1) < ($count['totalpage'] ?? 1)){
		$tpl->process('NEXTPAGE','nextpage');
		$tpl->process('LASTPAGE','lastpage');
	} else {
		$tpl->process('NEXTPAGE','nonextpage');
		$tpl->process('LASTPAGE','nolastpage');
	}
} // end if

// 블럭 : 글쓰기
if(privAuth(($dbinfo ?? null), "priv_write")) $tpl->process('WRITE','write');
//else $tpl->process('WRITE','nowrite');

// 마무리
$val="\\1{$thisUrl}/stpl/" . ($dbinfo['skin'] ?? 'board_basic') . "/images/";
// - 사이트 템플릿 읽어오기
if(preg_match('/^(ht|h|t)$/',($dbinfo['html_headpattern'] ?? ''))){
	$HEADER['header'] == 2;
	if( ($dbinfo['html_headtpl'] ?? '') != "" and is_file("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_" . ($dbinfo['html_headtpl'] ?? '') . ".php") )
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_" . ($dbinfo['html_headtpl'] ?? '') . ".php");
	else
		@include("{$_SERVER['DOCUMENT_ROOT']}/stpl/basic/index_basic.php");
}
switch($dbinfo['html_headpattern'] ?? ''){
	case "ht":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "h":
		echo ($SITE['head'] ?? '') . ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '');
		break;
	case "t":
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '') . ($SITE['tail'] ?? '');
		break;
	case "no":
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		break;
	default:
		echo ($dbinfo['html_head'] ?? '');
		echo preg_replace("/([\"|\'])images\//", "{$val}", $tpl->process('', 'html', TPL_OPTIONAL));
		echo ($dbinfo['html_tail'] ?? '');
} // end switch
?>
