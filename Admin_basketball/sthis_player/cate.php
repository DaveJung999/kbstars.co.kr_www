<?php
//=======================================================
// 설	명 : 게시판 카테고리 관리리스트(cate.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 02/08/10
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 02/08/10 박선민 마지막 수정
// 03/12/16 박선민 소스 개선
// 2025-01-XX PHP 업그레이드: $PHP_SELF, $QUERY_STRING를 $_SERVER 변수로 교체
//=======================================================
$HEADER=array(
		'priv'		=> "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2'	=>1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp'	=>1,
		'useBoard'=>1,
		'html_echo'	=>0	 // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$db			= $_REQUEST['db'];
	$cateuid	= $_REQUEST['cateuid'];

	$table_dbinfo = "{$SITE['th']}boardinfo";

	// boardinfo 테이블 정보 가져와서 $dbinfo로 저장
	if($db) {
		$sql = "SELECT * from {$table_dbinfo} WHERE db='{$db}'";
		$dbinfo = db_arrayone($sql) or back("사용하지 않은 DB입니다.");
		
		$table = $SITE['th'] . "board_" . $dbinfo['table_name']; // 테이블이름 가져오기

		// 인증 체크
		if(!privAuth($dbinfo, "priv_catemanage")) back("이용이 제한되었습니다.(레벨부족)");
	}
	else back("DB 값이 없습니다");

	// 카테고리 테이블 구함
	if($dbinfo['enable_cate']=='Y') {
		// 카테고리 테이블 이름과 where절 구함 
		if($dbinfo['enable_type']=='Y') {
			$table_cate=$table;
			$sql_where_cate=" type='cate' "; //{$sql_where_cate}사용 시작
		}
		else {
			$table_cate=$table . "_cate";
			$sql_where_cate=" 1 ";
		}
	}
	else back("카테고리를 지원하지 않습니다.");

	// 해당 카테고리 네비케이션 구하기
	if($cateuid) {
		$cate_nevi = "<a href='{$_SERVER['PHP_SELF']}
?db={$db}'>Top</a> > ";
		$rs_cateinfo = db_query("SELECT * from {$table_cate} WHERE {$sql_where_cate} and uid={$cateuid}");
		if(db_count()) {
			$cateinfo = db_array($rs_cateinfo);
			if(strlen($cateinfo['re'])) {
				// ( re='' or re='a' or re='ac' ) 만들기, re='aca"일때
				$sql_where_cate_tmp = " (re='' ";
				for($i=0;$i<strlen($cateinfo['re'])-1;$i++) {
						$sql_where_cate_tmp .= " or re='" . substr($cateinfo['re'],0,$i+1) ."' ";			
				}
				$sql_where_cate_tmp .= " ) ";
				// 	카테고리 네비게이션 만들기
				$rs = db_query("SELECT * from {$table_cate} WHERE {$sql_where_cate} and num={$cateinfo['num']} and {$sql_where_cate_tmp} order by re");
				while($row=db_array($rs)) {
					$cate_nevi .= $row['title'] . " > ";
				}
			} // end if	
			if($mode == "catemodify"){
				$list=$cateinfo;
				$html['submitvalue']="수정";
			}
			else {
				$cate_nevi .= "$cateinfo['title'] > ";		
				$html['submitvalue']="추가";
			} // end if
		} // end if(db_count)
	} // end if($cateuid)

	$mode = $mode ? $mode : "catewrite";

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<STYLE TYPE='text/css'>
<!--
A:link {font-size:10pt ; text-decoration:none; color:#000000;}
A:visited {font-size:10pt ; text-decoration:none; color:#000000;}
A:active {font-size:10pt ; text-decoration:none;}
A:hover {font-size:10pt ; text-underline:none;}

body, table, tr, td, select
{
	font-family: '굴림', '굴림체', 'seoul', 'arial',;
	font-size: 9pt;
}
input, textarea, select
{
	background-color: #E0F2F3;
	color: #29686B;
	border-style: solid;
	border-width: 1;
}
-->
</STYLE>
<body bgcolor="white" background="">
<form name="form1" method="post" action="cateok.php">
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="db" value="<?php echo $db; ?>">
	<input type="hidden" name="cateuid" value="<?=$cateuid?>">

	<table border=0 cellspacing='1' cellpadding='3' bgcolor='black'>
		<tr> 
			<td bgcolor='#CCFFCC' colspan=2>메뉴 <?=$html['submitvalue']?></td>
		</tr>
		<tr> 
			<td bgcolor='#CCFFCC'>메뉴 이름</td>
			<td bgcolor='#EDFEE'>
				<?=$cate_nevi?>
			<input type="text" name="title" value="<?=htmlspecialchars($list['title'],ENT_QUOTES) ?>">
			</td>
		</tr>
		<tr>
			<td bgcolor='#CCFFCC'>&nbsp;</td>
			<td bgcolor='#EDFEE'>
			<input type="submit" name="Submit" value=" 메뉴 <?=$html['submitvalue']?> ">
			</td>
		</tr>
		</table>
		<br>
</form>



<form name="form3" method="post" action="">
	<table border=0 cellspacing='1' cellpadding='3' bgcolor='black'>
	<tr> 
		<td bgcolor='#CCFFCC' colspan=8><b><?php echo $title; ?></b> 
		메뉴 현황</td>
	</tr>
	<tr bgcolor='#CCFFCC'> 
		<td width="48" > <div align="center">cateuid=?</div></td>
		<td width="48" > <div align="center">num</div></td>
		<td width="145" > <div align="center">카테고리 제목</div></td>
		<td	colspan=4 nowrap> <div align="center">메뉴</div></td>
		<td nowrap >메뉴수</td>
	</tr>
<?php
$rs_catelist = db_query("SELECT * from {$table_cate} WHERE {$sql_where_cate} ORDER BY num, re");		
$total = db_count();
for($i=0; $i<$total; $i++){
	$list = db_array($rs_catelist);
	$list['rede']=strlen($list['re']);
	if($list['rede'])
		$list['title']= str_repeat("&nbsp;&nbsp;&nbsp;", $list['rede']) . " ↘ " . $list['title']; 

	// 해당 카테고리수의 db수 구하기
	//$list['dbcount']=db_result(db_query("select count(*) as count from {$SITE['th']}shop_{$db} WHERE $sql_where cateuid='{$list['uid']}'"),0,"count");

	// URL Link..
	$href['catewrite']="{$_SERVER['PHP_SELF']}
?db={$db}";
	$href['catereply']="{$PHP_SELP}?db={$db}&cateuid={$list['uid']}";
	$href['catemodify']="{$PHP_SELP}?db={$db}&mode=catemodify&cateuid={$list['uid']}";
	$href['catesort']="./catesort.php?db={$db}&cateuid={$list['uid']}";
	$href['catedirectmodify']="/admin/myadmin224/tbl_change.php?table={$table_cate}&pos=0&session_max_rows=30&disp_direction=horizontal&repeat_cells=100&dontlimitchars=&primary_key=+%60uid%60+%3D+%27{$list['uid']}%27&goto=" . urlencode($_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
	$href['catedelete']="./cateok.php?db={$db}&mode=catedelete&cateuid={$list['uid']}";
	$href["list"]="/sboard/list.php?db={$db}&cateuid={$list['uid']}";
?>
	<tr bgcolor='#EDFEE'> 
		<TD align="center" nowrap > 
		<?=$list['uid']?>
		</TD>
		<TD align="center" nowrap > 
		<?=$list['rede'] ? "" : $list['num']?>
		</TD>
		<TD nowrap> 
		<?=$list['title']?>(<?=$list['re']?>) - [<a href="<?=$href["list"]?>" target=_blank>보기</a>]
		</TD>
		<td nowrap> <div align="center"><a href="javascript: return false" onClick="window.open('<?={$href['catesort']}?>','_blank','toolbar=no,location=no,status=no,menubar=no,scrollbars=auto,resizable=no,width=350,height=100,top=30 left=30')">순서변경</a></div></td>
		<td align="center" nowrap><a href="<?=$href['catereply']?>">서브추가</a></td>
		<td align="center" nowrap> <a href="<?=$href['catemodify']?>">수정</a> </td>
		<td align=center nowrap> <a href="<?=$href['catedelete']?>" onclick="return confirm('서브카테고리있다면 서비카테고리까지 삭제됩니다.\n정말 삭제 하시겠습니까?')">삭제</a> 
		</td>
		<td align="center" nowrap> 
		<?=$menucount?>
		</td>
	</tr>
<?php
} // end for
?>
	</table>
</form>
</body>
</html>