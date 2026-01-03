<?php
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
	$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
	$_SESSION['seHTTP_REFERER'] = $seHTTP_REFERER;
}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<script>
function check_form(){
	var form = document.write;
	if(form.t_name.value.length < 1){
		alert("팀명을 입력하세요.");		
		return false;
	} else {		
		form.submit();
		return true;
	}
}	
</script>
<?php

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

if($mode == "modify" && $tid)	{
	$sql = " SELECT t_name FROM `savers_secret`.team WHERE tid = {$tid} ";
	$rs = db_query($sql);
	$cnt = db_count($rs);
	
	if($cnt)
		$list = db_array($rs);
		$t_name = $list['t_name'];
} 

?>
<link href="/css/basic_text.css" rel="stylesheet" type="text/css">
<link href="/css/link01.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
body {
	margin-left: 5px;
	margin-top: 15px;
	margin-right: 5px;
	margin-bottom: 5px;
	background-color:F8F8EA;
}
-->
</style>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<form name="write" method="post" action="ok.php">

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
			<td background="/images/admin/tbox_bg.gif"><strong>팀정보 </strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
			</tr>
		</table>
		<br>
		<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bordercolorlight="#cccccc" bgcolor="#666666">
			<tr>
			<td width="25%" height="40" bgcolor="#D2BF7E" align="center"><strong>팀 명</strong></td>
			<td width="74%" bgcolor="#F8F8EA">&nbsp;&nbsp;
				<input name="t_name" type="text" size="50" value="<?php echo $t_name ; ?>" />
				&nbsp; <input name="button" type="button" class="CCbox03" onclick="check_form();" value=" 등 록 " />
				<input name="tid" type="hidden" value="<?php echo $tid ; ?>" />
				<input name="mode" type="hidden" value="<?php echo $mode ; ?>" />
			<input name="Submit2" type="button" class="CCbox03" value=" 뒤 로 " onclick="javascript:history.back();" /></td>
			</tr>
		</table></td>
	</tr>
</table>

	<br />
	<br>	
</form>
<?php echo $SITE['tail']; ?>
