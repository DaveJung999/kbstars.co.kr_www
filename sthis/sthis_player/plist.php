
<?php
$HEADER=array(
		'priv' => '운영자', // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		header => 1, // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		useUtil => 1,
		html => "contribution", // html header 파일(/stpl/basic/index_$HEADER['html'].php 파일을 읽음)
		log => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
	if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
		$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
		$_SESSION['seHTTP_REFERER'] = $seHTTP_REFERER;
	}
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================

if ($_GET['team'] == "") $_GET['team'] = 13;
//팀별로 보기
$tsql = 'SELECT tid, t_name FROM team ';
$trs = db_query($tsql);
$tcnt = db_count($trs);
$tselect = "<option value=plist.php?team => :::: 팀선택 ::::</option><option value=plist.php?team=0>전체</option>";
if($tcnt){
	for($i = 0 ; $i < $tcnt ; $i++){
		$tlist = db_array($trs);
		if ($tlist['tid'] == $_GET['team']) $sel = "selected";
		${'t' . $tlist['tid'] . '_name'} = $tlist['t_name'];
		$tselect .= "<option value=plist.php?team={$tlist['tid']} {$sel} >{$tlist['t_name']}</option>";
		$sel = "";
	}
} 

?>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}

function del(){
	var answer=confirm("삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}
//-->
</script>
<style type="text/css">
<!--
.style4 {color: #CC3300;
	font-weight: bold;
}
-->
</style>

<br>
<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolorlight="#cccccc">
	<tr>
	<form name="form1">
		<td><select name="team" onChange="MM_jumpMenu('parent',this,0)">
<?php echo $tselect ; ?></select></td>
		<td align="right"><a href="write.php?mode=write&goto=plist.php"><strong>[선수등록]</strong></a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;</td>
	</form>
	</tr>
</table>
<br />
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">
	<tr>
	<td height="40" align="center" bgcolor="#FFFFFF"><span class="style4">선수정보</span></td>
	</tr>
</table>
<br>
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#BDC8BD">
	<tr align="center" bgcolor="#e6eae6">
	<td width="5%">번호</td>
	<td width="16%" height="25">팀</td>
	<td width="16%">선수명</td>
	<td width="13%">포지션</td>
	<td width="13%">백넘버</td>
	<td width="13%">선수구분</td>
	<td width="12%">순서</td>
	<td width="12%">수정/삭제</td>
	</tr>
<?php
if($team = $_GET['team'])	
		$sql_where = " WHERE tid = {$team} ";
	else 
		$sql_where = " WHERE 1 ";
	
	$sql = " SELECT * FROM player ".$sql_where."ORDER BY tid, p_name ";
	$rs = db_query($sql);
	$cnt = db_count($rs);
	if($cnt)	{
		for($i = 0 ; $i < $cnt ; $i++)	{
			$list = db_array($rs); ?>
	<tr align="center" bgcolor="#FFFFFF">
	<td height="25">
<?php echo $i+1 ; ?>
	</td>
	<td><?php echo ${'t' . $tlist['tid'] . '_name'} ; ?>
	</td>
	<td><?php echo $list['p_name'] ; ?>
	</td>
	<td><?php echo $list['p_position'] ; ?>
	</td>
	<td><?php echo $list['p_num'] ; ?>
	</td>
	<td><?php echo $list['p_gubun'] ; ?>
	</td>
	<td><?php echo $list['p_seq'] ; ?> </td>
	<td><a href="write.php?mode=modify&team=<?php echo $_GET['team'] ; ?>&pid=<?php echo $list['uid'] ; ?>&uid=<?php echo $list['uid'] ; ?>&num=<?php echo $list['num'] ; ?>&goto=plist.php?team=<?php echo $_GET['team'] ; ?>">수정</a> <a onclick="return del()" href="ok.php?mode=delete&pid=<?php echo $list['uid'] ; ?>&uid=<?php echo $list['uid'] ; ?>&goto=plist.php">삭제</a></td>
	</tr>
<?php
}
	} else {
			echo "<tr align=center><td colspan=6 height=30>&nbsp;등록된 선수가 없습니다.</td></tr>";
	} 

?>
</table>
<table width="95%" border="0" align="center">
	<tr align="right">
		<td height="25"><a href="write.php?mode=write&goto=plist.php"><strong>[선수등록]</strong></a>&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;</td>
	</tr>
</table>
<?php echo $SITE['tail']; ?>
