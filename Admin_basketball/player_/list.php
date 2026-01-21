<?php
$HEADER=array(
	'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
// session_register 함수는 PHP 5.4.0부터 삭제
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) === false ){
	$_SESSION['seHTTP_REFERER']=$_SERVER['HTTP_REFERER'];
}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//팀별로 보기
$tsql = 'SELECT tid, t_name FROM team order by tid ';
$trs = db_query($tsql);
$tcnt = db_count($trs);
$tselect = "<option value=list.php>:::: 팀선택 ::::</option><option value=list.php>전체</option>";
$tname_map = [];
if($tcnt){
	while($tlist = db_array($trs)){
		// davej 2024-10-09
		$display_name = $tlist['t_name']." (".$tlist['tid'].")";
		$tname_map[$tlist['tid']] = $display_name;

		$sel = "";
		if (isset($_GET['team']) && $tlist['tid'] == $_GET['team']) {
			$sel = "selected";
		}

		$tselect .= "<option value=list.php?team={$tlist['tid']} {$sel} >{$display_name}</option>";
	}
}

?>
<script language="JavaScript" type="text/JavaScript">
<!--

function del(){
	var answer=confirm("삭제하시겠습니까?");
	if(answer)
		return true;
	else
		return false;
}
//-->
</script>
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
<script type="text/javascript">
function MM_jumpMenu(targ,selObj,restore){ //v3.0
  eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
  if (restore) selObj.selectedIndex=0;
}
</script>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
			<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
			<td background="/images/admin/tbox_bg.gif"><strong>선수정보 </strong></td>
			<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td width="62%" align="left"><form name="form1" id="form1"><select name="team" onchange="MM_jumpMenu('this',this,0)">
					<?php echo $tselect ; ?>
				</select></form></td>
				<td width="38%" height="40"><input name="back3" type="button" class="CCbox04" id="back3" onclick="location.href='write.php?mode=write'" value=" 선수등록 "/></td>
			</tr>
	</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#e6eae6">
				<td height="30" bgcolor="#D2BF7E"><strong>번호</strong></td>
				<td bgcolor="#D2BF7E"><strong>팀</strong></td>
				<td bgcolor="#D2BF7E"><strong>고유번호</strong></td>
				<td bgcolor="#D2BF7E"><strong>선수명</strong></td>
				<td bgcolor="#D2BF7E"><strong>포지션</strong></td>
				<td bgcolor="#D2BF7E"><strong>백넘버</strong></td>
				<td bgcolor="#D2BF7E"><strong>선수구분</strong></td>
				<td bgcolor="#D2BF7E"><strong>순서</strong></td>
				<td bgcolor="#D2BF7E"><strong>수정</strong></td>
				<td bgcolor="#D2BF7E"><strong>삭제</strong></td>
			</tr>
<?php
	$sql_where = " WHERE 1 ";
	if(isset($_GET['team'])) {
		$team = $_GET['team'];
		$sql_where = " WHERE tid = {$team} ";
	}

	$sql = " SELECT * FROM player ".$sql_where."ORDER BY p_num ";

	$rs = db_query($sql);
	$cnt = db_count($rs);
	if($cnt)	{
		for($i = 0 ; $i < $cnt ; $i++)	{
			$list = db_array($rs);
?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30"><?php echo $i+1 ; ?></td>
				<td><?php echo isset($tname_map[$list['tid']]) ? htmlspecialchars($tname_map[$list['tid']]) : ''; ?></td>
				<td><?php echo htmlspecialchars($list['uid']) ; ?> </td>
				<td><?php echo htmlspecialchars($list['p_name']) ; ?></td>
				<td><?php echo htmlspecialchars($list['p_position']) ; ?></td>
				<td><?php echo htmlspecialchars($list['p_num']) ; ?></td>
				<td><?php echo htmlspecialchars($list['p_gubun']) ; ?></td>
				<td><?php echo htmlspecialchars($list['p_seq']) ; ?></td>
				<td><input name="back" type="button" class="CCboxw" id="back" onclick="location.href='write.php?mode=modify&tid=<?php echo htmlspecialchars($_GET['team']) ; ?>&pid=<?php echo htmlspecialchars($list['uid']) ; ?>'" value=" 수정 "/></td>
				<td><input name="back2" type="button" class="CCboxw" id="back2" onclick="javascript:if(del()) location.href='ok.php?mode=delete&tid=<?php echo htmlspecialchars($_GET['team']) ; ?>&pid=<?php echo $list['uid'] ; ?>&uid=<?php echo $list['uid'] ; ?>' " value=" 삭제 "/></td>
			</tr>
<?php
		}
	} else {
		echo "<tr align=center bgcolor='#F8F8EA'><td colspan=10 height=90>&nbsp;등록된 선수가 없습니다.</td></tr>";
	}
?>
	</table>
		<table width="97%" border="0" align="center">
			<tr align="right">
			<td height="40"><input name="back4" type="button" class="CCbox04" id="back4" onclick="location.href='write.php?mode=write'" value=" 선수등록 "/></td>
			</tr>
		</table></td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>