
<?php
$HEADER=array(
		'priv' => "운영자,뉴스관리자,사진관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '', // log_site 테이블에 지정한 키워드로 로그 남김
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
<script>
function sub(){
	//선수 기록 입력
	document.write.submit();
}
</script>
<?php
	if($mode != "modify" || !$gid || !$rid)	back_close('필요한 정보가 없습니다.', "/Admin_basketball/game/list.php");
	
	//경기 기본정보 가져오기
	$grs = db_query(" SELECT *, sid as s_id FROM `savers_secret`.game WHERE gid={$gid} ");
	$gct = db_count($grs);
	if($gct) {	
		$glist = db_array($grs);
		if($glist['g_start'] > 0)
			$start = date("Y.m.d H:i", $glist['g_start']);
		if($glist['g_end'] > 0)
			$end	= date("Y.m.d H:i", $glist['g_end']);
		
		//시즌 정보 가져오기
		$srs = db_query(" SELECT * FROM `savers_secret`.season WHERE sid={$glist['s_id']} ");
		$sct = db_count($srs);
		if($sct)
			$slist = db_array($srs);
		
		//선수 정보 가져오기
		$prs = db_query(" SELECT * FROM `savers_secret`.player WHERE uid={$pid} ");
		$pct = db_count($prs);
		if($pct)	{
			$plist = db_array($prs);
		}
		
		
		//팀 정보
		$trs = db_query( " SELECT * from `savers_secret`.team WHERE tid={$plist['tid']} ");
		$tct = db_count( $trs );
		if($tct)	{
			$tlist = db_array( $trs );
		}
	}
	
	$rsql = " SELECT * from `savers_secret`.record WHERE rid = {$rid} ";
	$rrs = db_query( $rsql );
	$rcnt = db_count( $rrs );
	
	if( $rcnt ){
		$rlist = db_array( $rrs );
		
		if($rlist['min']){
			$min2 = $rlist['min'] % 60;
			$min1 = ($rlist['min'] - $min2) / 60;		
		}
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
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center">
			<td width="15%" height="30" align="center" bgcolor="#D2BF7E"><strong><span class="style8">시&nbsp;&nbsp;&nbsp;&nbsp; 즌</span></strong></td>
			<td width="85%" height="15" align="left" bgcolor="#F8F8EA"><span class="style1">&nbsp;&nbsp;<?php echo $slist['s_name'] ; ?>
			</span></td>
			</tr>
			<tr align="center">
			<td height="30" align="center" bgcolor="#D2BF7E"><strong><span class="style8">경기구분</span></strong></td>
			<td height="15" align="left" bgcolor="#F8F8EA"><span class="style1">&nbsp;&nbsp;<?php echo $glist['g_division'] ; ?>
			</span></td>
			</tr>
			<tr align="center">
			<td height="30" align="center" bgcolor="#D2BF7E"><strong><span class="style8">경기번호</span></strong></td>
			<td height="15" align="left" bgcolor="#F8F8EA"><span class="style1">&nbsp;&nbsp;<?php echo $glist['gameno'] ; ?>
			</span></td>
			</tr>
			<tr align="center">
			<td height="30" align="center" bgcolor="#D2BF7E"><strong><span class="style8">경기일자</span></strong></td>
			<td height="15" align="left" bgcolor="#F8F8EA"><span class="style1">&nbsp;&nbsp;<?php echo $start ; ?> ~ <?php echo $end ; ?>
			</span></td>
			</tr>
			<tr align="center">
			<td height="30" align="center" bgcolor="#D2BF7E"><strong><span class="style8">경 기 장</span></strong></td>
			<td height="15" align="left" bgcolor="#F8F8EA"><span class="style1">&nbsp;&nbsp;<?php echo $glist['g_ground'] ; ?>
			</span></td>
			</tr>
			<tr align="center">
			<td height="30" colspan="2" align="center" bgcolor="#F8F8EA">&nbsp;&nbsp;<span class="style2"><strong>[입력시 주의]</strong>모든 필드가 기록되어야 합니다. 값이 없는 곳은 0으로	입력.</span></td>
			</tr>
		</table>
		<form action="ok.php" method="post" name="write" id="write">
			<input name="rid" type="hidden" value="<?php echo $rid ; ?>" />
			<input name="gid" type="hidden" value="<?php echo $gid ; ?>" />
			<input name="s_id" type="hidden" value="<?php echo $glist['s_id'] ; ?>" />
			<input name="mode" type="hidden" value="<?php echo $mode ; ?>" />
			<input name="season" type="hidden" value="<?php echo $season ; ?>" />
			<!--------------------- 기록 시작 ------------------------------------->
			<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center">
				<td width="100" height="30" rowspan="2" bgcolor="#D2BF7E"><strong><?php echo $tlist['t_name']." (".$tlist['tid'].")" ; ?>
				</strong></td>
				<td colspan="7" bgcolor="#D2BF7E"><strong>Scoring</strong></td>
				<td width="80" rowspan="2" bgcolor="#D2BF7E"><strong>Min</strong></td>
				<td colspan="2" bgcolor="#D2BF7E"><strong>3P</strong></td>
				<td colspan="2" bgcolor="#D2BF7E"><strong>2P</strong></td>
				<td colspan="2" bgcolor="#D2BF7E"><strong>FT</strong></td>
				<td colspan="2" bgcolor="#D2BF7E"><strong>REBOUNDS</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>Ast</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>Stl</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>GD</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>BS</strong></td>
				<td colspan="2" bgcolor="#D2BF7E"><strong>PF</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>TO</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>ldf</strong></td>
				<td width="40" rowspan="2" bgcolor="#D2BF7E"><strong>TF</strong></td>
			</tr>
			<tr align="center">
				<td width="40" bgcolor="#D2BF7E"><strong>1Q</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>2Q</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>3Q</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>4Q</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>E1</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>E2</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>E3</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>M</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>A</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>M</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>A</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>M</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>A</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>Off</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>Def</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>w/FT</strong></td>
				<td width="40" bgcolor="#D2BF7E"><strong>w/oFT</strong></td>
			</tr>
			<tr align="center" bgcolor="#F8F8EA">
				<td height="30" nowrap="nowrap"><input type="checkbox" name="start" value="1" <?php	if($rlist['start']) echo 'checked'; ?> />
				<?php echo $plist['p_name'] ; ?>
				<br />
				NO:
				<input name="pback" type="text" size="2" maxlength="3"	value="<?php echo $rlist['pback'] ; ?>" required hname="No를 입력 해 주세요."/></td>
				<td><input name="qs1" type="text" size="2" maxlength="3" value="<?php echo $rlist['1qs'] ; ?>" required hname="1Q Scoring을 입력 해 주세요." /></td>
				<td><input name="qs2" type="text" size="2" maxlength="3" value="<?php echo $rlist['2qs'] ; ?>" required hname="2Q Scoring을 입력 해 주세요." /></td>
				<td><input name="qs3" type="text" size="2" maxlength="3" value="<?php echo $rlist['3qs'] ; ?>" required hname="3Q Scoring을 입력 해 주세요." /></td>
				<td><input name="qs4" type="text" size="2" maxlength="3" value="<?php echo $rlist['4qs'] ; ?>" required hname="4Q Scoring을 입력 해 주세요." /></td>
				<td><input name="e1s" type="text" size="2" maxlength="3" value="<?php echo $rlist['e1s'] ; ?>" required hname="E1 Scoring을 입력 해 주세요." /></td>
				<td><input name="e2s" type="text" size="2" maxlength="3" value="<?php echo $rlist['e2s'] ; ?>" required hname="E2 Scoring을 입력 해 주세요." /></td>
				<td><input name="e3s" type="text" size="2" maxlength="3" value="<?php echo $rlist['e3s'] ; ?>" required hname="E3 Scoring을 입력 해 주세요." /></td>
				<td><input name="min1" type="text" size="2" maxlength="3" value="<?php echo $min1 ; ?>" required hname="Min 값을 입력 해 주세요." /> :<input name="min2" type="text" size="2" maxlength="2" value="<?php echo $min2 ; ?>" required hname="Min 값을 입력 해 주세요." /></td>
				<td><input name="m3" type="text" size="2" maxlength="3" value="<?php echo $rlist['3p_m'] ; ?>" /></td>
				<td><input name="a3" type="text" size="2" maxlength="3" value="<?php echo $rlist['3p_a'] ; ?>" /></td>
				<td><input name="m2" type="text" size="2" maxlength="3" value="<?php echo $rlist['2p_m'] ; ?>" /></td>
				<td><input name="a2" type="text" size="2" maxlength="3" value="<?php echo $rlist['2p_a'] ; ?>" /></td>
				<td><input name="mft" type="text" size="2" maxlength="3" value="<?php echo $rlist['ft_m'] ; ?>" /></td>
				<td><input name="aft" type="text" size="2" maxlength="3" value="<?php echo $rlist['ft_a'] ; ?>" /></td>
				<td><input name="re_off" type="text" size="2" maxlength="3" value="<?php echo $rlist['re_off'] ; ?>" /></td>
				<td><input name="re_def" type="text" size="2" maxlength="3" value="<?php echo $rlist['re_def'] ; ?>" /></td>
				<td><input name="ast" type="text" size="2" maxlength="3" value="<?php echo $rlist['ast'] ; ?>" /></td>
				<td><input name="stl" type="text" size="2" maxlength="3" value="<?php echo $rlist['stl'] ; ?>" /></td>
				<td><input name="gd" type="text" size="2" maxlength="3" value="<?php echo $rlist['gd'] ; ?>" /></td>
				<td><input name="bs" type="text" size="2" maxlength="3" value="<?php echo $rlist['bs'] ; ?>" /></td>
				<td><input name="w_ft" type="text" size="2" maxlength="3" value="<?php echo $rlist['w_ft'] ; ?>" /></td>
				<td><input name="w_oft" type="text" size="2" maxlength="3" value="<?php echo $rlist['w_oft'] ; ?>" /></td>
				<td><input name="tover" type="text" size="2" maxlength="3" value="<?php echo $rlist['tover'] ; ?>" /></td>
				<td><input name="ldf" type="text" size="2" maxlength="3" value="<?php echo $rlist['ldf'] ; ?>" /></td>
				<td><input name="tf" type="text" size="2" maxlength="3" value="<?php echo $rlist['tf'] ; ?>" /></td>
			</tr>
			</table>
			<!--------------------- 기록 끝 ---------------------------------->
		</form>
		<table width="97%"	border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td height="45" align="center">&nbsp;
				<input name="submit" type="button" class="CCbox04" onclick="sub();" value=" 기록입력 " />
				&nbsp;
			<input name="Submit2" type="button" class="CCbox04" value=" 뒤 로 " onclick="javascript:history.back();" /></td>
			</tr>
		</table></td>
	</tr>
</table>
<br>
<?php echo $SITE['tail']; ?>