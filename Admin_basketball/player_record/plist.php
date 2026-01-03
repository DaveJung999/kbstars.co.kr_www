
<?php
$HEADER=array(
		'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
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

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 넘오온값 체크
$table_player = "`savers_secret`.player";
$table_team = "`savers_secret`.team";

if (!($_GET['pid'] ?? '')) {
	// 해당 선수 정보
	$sql = "SELECT * from {$table_player} where tid=13 and p_gubun ='현역'	order by p_name limit 0, 1 " ;
	$player=db_arrayone($sql);
	$_GET['pid'] = $player['uid'];
}

// 선수명단
$strOpt = '';
$pList = array();
$sql = "SELECT * from {$table_player} where tid =13 and p_gubun ='현역' order by p_name";
$rs=db_query($sql);

while($list=db_array($rs)){
	$pList[$list['uid']] = $list['title'];
	if ($_GET['pid'] == $list['uid'] )
		$strOpt .= "<option value='{$list['uid']}' selected>{$list['p_name']} [{$list['p_position']}]</option>";
	else
		$strOpt .= "<option value='{$list['uid']}'>{$list['p_name']} [{$list['p_position']}]</option>";
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
.style1 {font-weight: bold}
-->
</style>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<script type="text/JavaScript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}
//-->
</script>

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>선수 종합기록 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td><select name="cmchange" size="1"
													onchange="location.href='?pid='+this.value">
				<option>선수종합기록</option>
				<?php echo $strOpt ; ?>
				</select></td>
			<td height="40" align="right"><input name="back4" type="button" class="CCbox04" id="back4" onclick="location.href='pwrite.php?pid=<?php echo $_GET['pid'] ; ?>&mode=write'" value=" 기록등록 "/></td>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
			<tr>
			<td height="30" align="center" bgcolor="#D2BF7E"><strong>소속팀</strong></td> 
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>시즌</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>G</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"><strong>Total<br />
			MIN</strong></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>MIN</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>3FG</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>2FG</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"><strong>FT</strong></td>
			<td align="center" bgcolor="#D2BF7E"><strong>FTA</strong></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>FT%</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>RP</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>AS</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>ST</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>BLK</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>PTS</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"> <p align="center"><strong>PPG</strong></p></td>
			<td align="center" bgcolor="#D2BF7E"><strong>수정</strong></td>
			<td align="center" bgcolor="#D2BF7E"><strong>삭제</strong></td>
		</tr>
<?php
$rs_league = db_query("SELECT * FROM `savers_secret`.player_league WHERE pid = '{$_GET['pid']}' ORDER BY uid DESC");
$total = db_count($rs_league);

$i=0;
if($total){
	for($i = 0 ; $i < $total ; $i++)	{
		$plist = db_array($rs_league);
		$tplist = [];
		
		// 값 없으면.... 0 으로 세팅...........
		$plist['p_totalmin'] = $plist['p_totalmin'] ? $plist['p_totalmin'] : "0:0";
		$plist['p_min'] = $plist['p_min'] ? $plist['p_min'] : "0:0";
		$plist['p_3fg'] = $plist['p_3fg'] ? $plist['p_3fg'] : "0/0";
		$plist['p_2fg'] = $plist['p_2fg'] ? $plist['p_2fg'] : "0/0";
		$plist['p_ft'] = $plist['p_ft'] ? $plist['p_ft'] : 0;
		$plist['p_fta'] = $plist['p_fta'] ? $plist['p_fta'] : 0;
		$plist['p_rp'] = $plist['p_rp'] ? $plist['p_rp'] : 0;
		$plist['p_as'] = $plist['p_as'] ? $plist['p_as'] : 0;
		$plist['p_st'] = $plist['p_st'] ? $plist['p_st'] : 0;
		$plist['p_blk'] = $plist['p_blk'] ? $plist['p_blk'] : 0;
		$plist['p_pts'] = $plist['p_pts'] ? $plist['p_pts'] : 0;
		$plist['p_ppg'] = $plist['p_ppg'] ? $plist['p_ppg'] : "0.0";

		$tmp_total = explode(":",$plist['p_totalmin']);
		$tplist['p_totalmin1'] = 	$tplist['p_totalmin1'] + $tmp_total[0];
		$tplist['p_totalmin2'] = 	$tplist['p_totalmin2'] + $tmp_total[1];
		
		$tmp = explode(":",$plist['p_min']);
		$tplist['p_min1'] = 	$tplist['p_min1'] + $tmp[0];
		$tplist['p_min2'] = 	$tplist['p_min2'] + $tmp[1];
		
		$tplist['p_g'] 	= 	$tplist['p_g'] + $plist['p_g'];
		$tplist['p_2fg'] = 	$tplist['p_2fg'] + $plist['p_2fg'];
		$tplist['p_3fg'] = 	$tplist['p_3fg'] + $plist['p_3fg'];
		$tplist['p_ft'] = 	$tplist['p_ft'] + $plist['p_ft'];
		$tplist['p_fta'] = 	$tplist['p_fta'] + $plist['p_fta'];
		
		$p_fta = $plist['p_fta'];
		if($plist['p_fta'] == 0 ) $p_fta = 1;
		$plist['p_ftpct'] = number_format($plist['p_ft'] / $p_fta*100, 1);
		
		$tplist['p_rp'] = 	$tplist['p_rp'] + $plist['p_rp'];
		$tplist['p_as'] = 	$tplist['p_as'] + $plist['p_as'];
		$tplist['p_st'] = 	$tplist['p_st'] + $plist['p_st'];
		$tplist['p_blk'] = 	$tplist['p_blk'] + $plist['p_blk'];
		$tplist['p_to'] = 	$tplist['p_to'] + $plist['p_to'];
		$tplist['p_pts'] = 	$tplist['p_pts'] + $plist['p_pts'];
		$tplist['p_ppg'] = 	$tplist['p_ppg'] + $plist['p_ppg']; 
?>
		
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"><strong>
			<?php echo ($plist['t_name'])? $plist['t_name']." (".$plist['tid'].")": $player['t_name']." (".$player['tid'].")" ; ?>
			</strong></td>
			<td align="center"> <?php echo $plist['p_league'] ; ?></td>
			<td align="center"> <?php echo $plist['p_g'] ; ?></td>
			<td align="center"> <?php echo $plist['p_totalmin'] ; ?></td>
			<td align="center"> <?php echo $plist['p_min'] ; ?></td>
			<td align="center"> <?php echo $plist['p_3fg'] ; ?></td>
			<td align="center"> <?php echo $plist['p_2fg'] ; ?></td>
			<td align="center"> <?php echo $plist['p_ft'] ; ?></td>
			<td align="center"> <?php echo $plist['p_fta'] ; ?></td>
			<td align="center"> <?php echo $plist['p_ftpct'] ; ?></td>
			<td align="center"> <?php echo $plist['p_rp'] ; ?></td>
			<td align="center"> <?php echo $plist['p_as'] ; ?></td>
			<td align="center"> <?php echo $plist['p_st'] ; ?></td>
			<td align="center"> <?php echo $plist['p_blk'] ; ?></td>
			<td align="center"> <?php echo $plist['p_pts'] ; ?></td>
			<td align="center"> <?php echo $plist['p_ppg'] ; ?></td>
			<td align="center"><input name="back" type="button" class="CCboxw" id="back" onclick="location.href='pwrite.php?uid=<?php echo $plist['uid'] ; ?>&pid=<?php echo $_GET['pid'] ; ?>&mode=modify'" value=" 수정 "/></td>
			<td height="25" align="center">
				<input name="back2" type="button" class="CCboxw" id="back2" onclick="javascript:if(confirm('기록을 삭제하시겠습니까?')) location.href='pok.php?uid=<?php echo $plist['uid'] ; ?>&amp;pid=<?php echo $_GET['pid'] ; ?>&amp;mode=delete' " value=" 삭제 "/></td>
		</tr>
<?php
}//end for
	} else { 
?>
		<tr>
			<td height="50" colspan="18" align="center" bgcolor="#F8F8EA"> <p align="center">해당선수에 대한 종합기록이 없습니다. </p> </td> 
		</tr>
<?php
}//end if 

	// 나누는 값이 0 이면 안 되므로.....davej..............2007-04-10
	if ($i == 0) $i = 1;
	
	// 0으로 나누면 에러
	$gametotal = $tplist['p_g'] ? $tplist['p_g'] : 1;
	$fta = $tplist['p_fta'] ? $tplist['p_fta'] : 1;
	
	// 토탈게임
	$imsi_total = $tplist['p_totalmin1']*60 + $tplist['p_totalmin2'] ;
	$imsi_total_div_game = ceil($imsi_total / $gametotal) ;
	$imsi_total_min = floor($imsi_total_div_game / 60);
	$imsi_total_sec = $imsi_total_div_game % 60 ; 
?>
			<tr>
				<td height="30" colspan="2" align="center" bgcolor="#D2BF7E"><span class="style1">합계(평균)</span></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_g']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_totalmin1']); ?> : <?php echo number_format( $tplist['p_totalmin2']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($imsi_total_min); ?> : <?php echo number_format($imsi_total_sec); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_3fg']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_2fg']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_ft']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_fta']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_ft']/$fta*100, 1); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_rp']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_as']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_st']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_blk']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_pts']); ?></td>
				<td align="center" bgcolor="#F0EBD6"><?php echo number_format($tplist['p_pts'] /$gametotal , 2); ?></td>
				<td align="center" bgcolor="#F0EBD6"></td>
				<td align="center" bgcolor="#F0EBD6"></td>
			</tr>	
</table>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr align="right">
				<td height="40"><input name="back3" type="button" class="CCbox04" id="back3" onclick="location.href='pwrite.php?pid=<?php echo $_GET['pid'] ; ?>&mode=write'" value=" 기록등록 "/></td>
			</tr>
		</table></td>
	</tr>
</table>