
<?php
$HEADER=array(
		'priv' => "운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================
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
<script language="JavaScript" type="text/JavaScript">
<!--

function del(){
	var answer=confirm("해당 경기의 기록까지 모두 삭제됩니다.\n삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}

function putSettings() 
{ 
	with(factory.printing)
	{
		header = ''; // 머릿말
		footer = ''; // 꼬릿말
		portrait = false; // true이면 세로 인쇄, false이면 가로 인쇄.
		leftMargin = 0; // 왼쪽 여백
		rightMargin = 1; // 오른쪽 여백
		topMargin = 0; // 윗쪽 여백
		bottomMargin = 0; // 아랫쪽 여백
	} 
}

function doPrint(frame){
	putSettings();
	factory.printing.Print(false, frame);
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<object id=factory style="display:none;" classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814" viewastext codebase="http://www.meadroid.com/scriptx/ScriptX.cab#Version=6,1,429,14"></object>
<?php
//시즌정보
$sql = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if($season == $list['s_id'])
			$sselect .= "<option value=result.php?season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=result.php?season={$list['s_id']}>{$list['s_name']}</option>";
	}		
}	

$t_rs = db_query(" select * from `savers_secret`.team order by tid");
$t_cnt = db_count($t_rs);

if($t_cnt)	{
	for($i=0 ; $i<$t_cnt ; $i++){
		$t_list = db_array($t_rs);
			
		// davej 2024-10-09
		$t_list['t_name'] = $t_list['t_name']." (".$t_list['tid'].")";

		if($tid == $t_list['tid']){
			$t_select .= "<option value=result.php?tid={$t_list['tid']}&season={$season} selected>{$t_list['t_name']}</option>";
		} else {
			$t_select .= "<option value=result.php?tid={$t_list['tid']}&season={$season}>{$t_list['t_name']}</option>";
		}

	}
} 

?>
<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>한 경기 종합기록 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='result.php?season='>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="team" onchange="MM_jumpMenu('this',this,0)">
					<option value='result.php?tid='>팀선택</option>
					<?php echo $t_select ; ?>
					</select></td>
			</form>
			</tr>
		</table>
		<br />
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center" bgcolor="#D2BF7E">
			<td width="15%" height="30"><strong><span class="style2">경기일</span></strong></td>
			<td width="20%"><strong><span class="style2">홈팀</span></strong></td>
			<td width="20%"><strong><span class="style2">어웨이팀</span></strong></td>
			<td width="25%"><strong><span class="style2">지역</span></strong></td>
			<td width="10%"><strong><span class="style2">경기결과</span></strong></td>
			</tr>
			<!-- 경기일정/결과 정보 반복 시작 -->
<?php
	
	//경기 정보 가져오기
	$gsql = " select * FROM `savers_secret`.game ";
	$sql_where = " where ";
	if($season) 
		$sql_where .= " sid = {$season} ";
	if($season && $tid)
		$sql_where .= " and (g_home = {$tid} or g_away = {$tid})";
	if(!$season && $tid)
		$sql_where .= " g_home = {$tid} or g_away = {$tid}";
	if(!$season && !$tid)
		$sql_where .= " 1 ";
			
	$orderby = " ORDER BY g_start desc ";
			
	$gsql = $gsql.$sql_where.$orderby;
	$grs = db_query($gsql);
	$gcnt = db_count($grs);
	
	if($gcnt){
		for($i = 0 ; $i < $gcnt ; $i++)	{
			$glist = db_array($grs);
			$glist['g_start'] = date("Y-m-d", $glist['g_start']);			
			
			//팀아이디를 팀이름으로 변경
			$trs = db_query("select * from `savers_secret`.team order by tid");
			$tcnt = db_count($trs);
			for($j=0 ; $j < $tcnt ; $j++){
				$tlist = db_array($trs);
				if($glist['g_home'] == $tlist['tid'])	{
					$glist['g_home'] = $tlist['t_name']." (".$tlist['tid'].")";
					$glist['home_tid'] = $tlist['tid'];
					if ($tlist['tid'] == '13') $glist['g_home'] = "<b>".$glist['g_home']."</b>";
				}
				if($glist['g_away'] == $tlist['tid']){
					$glist['g_away'] = $tlist['t_name']." (".$tlist['tid'].")";
					$glist['away_tid'] = $tlist['tid'];
					if ($tlist['tid'] == '13') $glist['g_away'] = "<b>".$glist['g_away']."</b>";
				}
			}
			
			$rrs = db_query(" select count(rid) as cnt from `savers_secret`.record where gid={$glist['gid']} ");
			$rcount = db_array($rrs);
			if($rcount['cnt'] > 0){
				$href_read = "<a href='/Admin_basketball/record/result_read.php?gid={$glist['gid']}'>{$glist['g_start']}</a>";
			} else {
				$href_read = "$glist['g_start']";
			}
			
			//홈팀 경기 결과
			$home = "SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum from `savers_secret`.record WHERE gid = {$glist['gid']} and tid = {$glist['home_tid']}";			
			$home_rs = db_query($home);
			$home_cnt = db_count($home_rs);
			if($home_cnt)	{
				$home_score = db_array($home_rs);
				
			}
			
			//어웨이팀 경기결과
			$away = "SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum from `savers_secret`.record WHERE gid = {$glist['gid']} and tid = {$glist['away_tid']}";			
			$away_rs = db_query($away);
			$away_cnt = db_count($away_rs);
			if($away_cnt)	{
				$away_score = db_array($away_rs);
			}
			
			// 한경기종합기록에 자료가 없으면 게임정보에서 자료 가져옴 davej................................
			
			if($home_score['sum'] && $away_score['sum'])
				$score[$i] = $home_score['sum']." : ".$away_score['sum'];
			else if ($glist['home_score'] > 0 && $glist['away_score'] > 0)
				$score[$i] = $glist['home_score']." : ".$glist['away_score'];
				
			if ($glist['home_tid'] == '13' || $glist['away_tid'] == '13'){
				$score[$i] = "<b>".$score[$i]."</b>";
				$bgcolor = " bgcolor = '#FDF2FD'";
			} else {
				$bgcolor = " bgcolor = '#F8F8EA'";
			} 

?>
			<tr align="center" <?php echo $bgcolor ; ?>>
			<td height="30" bgcolor="#F8F8EA"><span class="style3"><?php echo $href_read ; ?></span></td>
			<td bgcolor="#F8F8EA"><span class="style3"><?php echo $glist['g_home'] ; ?></span></td>
			<td bgcolor="#F8F8EA"><span class="style3"><?php echo $glist['g_away'] ; ?></span></td>
			<td bgcolor="#F8F8EA"><span class="style3"><?php echo $glist['g_ground'] ; ?></span></td>
			<td bgcolor="#F8F8EA"><span class="style3">&nbsp;<?php echo $score[$i] ; ?></span></td>
			</tr>
<?php
}			
	} else {
			echo "<tr align=center><td colspan=5 height=80 bgcolor = '#F8F8EA'>&nbsp;등록된 경기가 없습니다.</td></tr>";
	} 

?>
			<!-- 경기일정/결과 정보 반복 끝	-->
	</table></td>
	</tr>
</table>

<br />
<br />
<?php echo $SITE['tail']; ?>
