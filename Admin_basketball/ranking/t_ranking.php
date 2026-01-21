
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

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//=================================================== 
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
var hideBuf;
function hideBeforePrint(){
	hideBuf = document.body.innerHTML;
	document.body.innerHTML = ppp.innerHTML;
}
function showAfterPrint(){
	document.body.innerHTML = hideBuf;
}
window.onbeforeprint = hideBeforePrint;
window.onafterprint = showAfterPrint;

function PrintPage(){
	window.print();
}

function MM_jumpMenu(targ,selObj,restore){ //v3.0
	eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
	if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<?php
if($html == "print")	$print = "";
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('t_ranking.php?html=print&season');\">";

//시즌정보
$sql = " SELECT *, sid as s_id FROM season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if(!$_GET['season']){
			$_GET['season'] = $list['s_id'];
			$season = $_GET['season'];
		}
		if($season == $list['s_id']){
			$sselect .= "<option value=t_ranking.php?season={$list['s_id']} selected>{$list['s_name']}</option>";
			$sname = $list['s_name'];
		} else {
			$sselect .= "<option value=t_ranking.php?season={$list['s_id']}>{$list['s_name']}</option>";
			if($i == 0) $sname = $list['s_name'];
		}
	}		
}	

	$s_id = $_GET['season'];
	
	//시즌정보
	$s_sql = " SELECT *, sid as s_id FROM season where sid = {$s_id} ORDER BY s_start DESC ";
	$s_rs	= db_query($s_sql);
	$s_cnt = db_count($s_rs);
	if($s_cnt)	{
		for($i=0 ; $i<$s_cnt ; $i++){
			$s_list = db_array($s_rs);
			if(!$s_id && $i == 0)
				$s_id = $s_list['s_id'];
		}
	}

	//팀정보
	$t_sql = "SELECT * FROM team order by tid";
	$t_rs	= db_query($t_sql);
	$t_cnt = db_count($t_rs);
	if($t_cnt)	{
		for($i=0 ; $i<$t_cnt ; $i++)	{
			$t_list = db_array($t_rs);
			$tid[] = $t_list['tid'];
			$t_name[] = $t_list['t_name']." (".$t_list['tid'].")";
			$team[$t_list['tid']][0] = 0; //승
			$team[$t_list['tid']][1] = 0; //패
		}
	}
	
	//게임정보
	$g_sql = " SELECT * FROM game WHERE sid = {$s_id} and g_division = '정규시즌' ORDER BY g_start DESC ";
	$g_rs	= db_query($g_sql);
	$g_cnt = db_count($g_rs);
	$ah = 1;
	$aa = 1;
	if($g_cnt)	{
		for($i=0 ; $i<$g_cnt ; $i++)	{
			$g_list = db_array($g_rs);
			
		//해당 시즌 경기기록				
		//============ >			선수개인기록에서 가져오던 것을 경기정보에서 가져오는걸로 바꿈			davej.
			//홈팀 스코어
			
			$h_list['sum'] = $g_list['home_1q'] + $g_list['home_2q'] + $g_list['home_3q'] + $g_list['home_4q'] + $g_list['home_eq'];
/*				$h_sql = " SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum FROM record WHERE gid = {$g_list['gid']} and tid = {$g_list['g_home']} ";
			$h_rs	= db_query($h_sql);
			$h_cnt = db_count($h_rs);
			if($h_cnt)	{
				$h_list = db_array($h_rs);				
			}
*/

			//어웨이팀 스코어
			$a_list['sum'] = $g_list['away_1q'] + $g_list['away_2q'] + $g_list['away_3q'] + $g_list['away_4q'] + $g_list['away_eq'];
			
			
/*			$a_sql = " SELECT sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as sum FROM record WHERE gid = {$g_list['gid']} and tid = {$g_list['g_away']} ";
			$a_rs	= db_query($a_sql);
			$a_cnt = db_count($a_rs);
			if($a_cnt)	{
				$a_list = db_array($a_rs);
			}
*/			

			
			//승패
			if($h_list['sum'] > $a_list['sum']) {		
				$team[$g_list['g_home']][0]++; //승
				$team[$g_list['g_away']][1]++; //패
				
				$ah = count($win_loss[$g_list['g_home']]) + 1;
				$aa = count($win_loss[$g_list['g_away']]) + 1;
		
				$win_loss[$g_list['g_home']][$ah] = 1;
				$win_loss[$g_list['g_away']][$aa] = 0;
			}else if($a_list['sum'] > $h_list['sum']){
				$team[$g_list['g_away']][0]++;	
				$team[$g_list['g_home']][1]++;
				
				$ah = count($win_loss[$g_list['g_home']]) + 1;
				$aa = count($win_loss[$g_list['g_away']]) + 1;
				
				$win_loss[$g_list['g_home']][$ah] = 0;
				$win_loss[$g_list['g_away']][$aa] = 1;
			}
		}
	}
	
	$sql_team = " SELECT * FROM team ORDER BY tid ";
	$rs_team = db_query($sql_team);
	$cnt_team = db_count($rs_team);
	
	if($cnt_team)	{
		for($i=0 ; $i<$cnt_team ; $i++){
			$list_team = db_array($rs_team);
			
			$sql_game_h = " SELECT g_start FROM game where g_home = {$list_team['tid']} and g_division = '정규시즌' ORDER BY g_start ASC ";
			
			$list_start_home = db_arrayone($sql_game_h);
			
			$sql_game_a = " SELECT g_start FROM game where g_away = {$list_team['tid']} and g_division = '정규시즌' ORDER BY g_start ASC ";
			$list_start_away = db_arrayone($sql_game_a);
			
			if($list_start_home['g_start'] < $list_start_away['g_start'])
				$g_start[$list_team['tid']] = date("Y-m-d", $list_start_home['g_start']);
			else
				$g_start[$list_team['tid']] = date("Y-m-d", $list_start_away['g_start']);
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
		<td background="/images/admin/tbox_bg.gif"><strong>팀순위(계산) </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
	<br><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
			<form name="form1">
		<td>
				<select name="season" onChange="MM_jumpMenu('this',this,0)">
					<option value='t_ranking.php'>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
		</td>
			<td width="70" align="right">
			<?php echo $print ; ?></td>
			</form>
		</tr>
	</table>
	<table width="97%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#666666">
	<tr>
	<td height="40" align="center" bgcolor="#F8F8EA"><span class="style2">[
	<?php echo $sname ; ?>] &nbsp; 팀순위(계산)</span></td>
	</tr>
</table>
<div id="ppp">
	<table width="97%"	border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
	<tr align="center" bgcolor="#D2BF7E">
		<td width="5%" height="30" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">순위</span></strong></td>
		<td width="17%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">팀 명</span></strong></td>
		<td width="8%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">경기수</span></strong></td>
		<td width="5%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">승</span></strong></td>
		<td width="5%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">패</span></strong></td>
		<td width="5%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">승 률</span></strong></td>
		<td width="5%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">승 차</span></strong></td>
		<td colspan="2" bgcolor="#D2BF7E"><strong><span class="style1">연 속</span></strong></td>
		<td width="16%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">시작일자</span></strong></td>
		<td colspan="2" bgcolor="#D2BF7E"><strong><span class="style1">최 다</span></strong></td>
		<td width="10%" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">최종</span></strong></td>
	</tr>
	<tr bgcolor="#D2BF7E">
		<td width="5%" align="center" bgcolor="#D2BF7E"><strong><span class="style1">승</span></strong></td>
		<td width="5%" align="center" bgcolor="#D2BF7E"><strong><span class="style1">패</span></strong></td>
		<td width="7%" align="center" bgcolor="#D2BF7E"><strong><span class="style1">연속승</span></strong></td>
		<td width="7%" align="center" bgcolor="#D2BF7E"><strong><span class="style1">연속패</span></strong></td>
	</tr>
<?php
	for($j=0 ; $j<count($tid) ; $j++){		
			
			$game_cnt[$j] = $team[$tid[$j]][0] + $team[$tid[$j]][1];
			
			if($game_cnt[$j] > 0){
				$win_per[$j]	= number_format($team[$tid[$j]][0] / ($team[$tid[$j]][0] + $team[$tid[$j]][1]), 2, '', '');	
			} else {
				$win_per[$j] = "";
			}
	
		}
	
		for($i=0 ; $i<count($win_per); $i++){
			for($k=0 ; $k<=count($win_per); $k++){
				if($win_per[$k] >= $win_per[$i]){
					$tmp_count[$i]= $tmp_count[$i]+1;
				}
			}	
			
			if(!$real_count[$tmp_count[$i]]){
				$real_count[$tmp_count[$i]] = $win_per[$i];
				$real_count2[$tmp_count[$i]]= $i;
			} else {
				$real_count[$tmp_count[$i]-1] = $win_per[$i];
				$real_count2[$tmp_count[$i]-1]= $i;
			}
		}		
	
		for($l=1;$l<=count($tmp_count);$l++){
			
			$win_loss_count = count($win_loss[$tid[$real_count2[$l]]]);
			$win_end = 1;
			$loss_end = 1;
			
						
			for($u=1; $u<=$win_loss_count; $u++){
				
				if($u == 1){
					if($win_loss[$tid[$real_count2[$l]]][$u]){
						$win_lost_last[0] = $win_loss[$tid[$real_count2[$l]]][$u];
						$tmp_win = 1;
						$tmp_loss = 0;
						$win_last_count[$tid[$real_count2[$l]]] = 1;
						$loss_last_count[$tid[$real_count2[$l]]] = 0;
						$go_win[$tid[$real_count2[$l]]]=0;
						$go_loss[$tid[$real_count2[$l]]]=0;
						$tmp_go_win = 1;
						$tmp_go_loss = 1;
					} else {
						$win_lost_last[1] = $win_loss[$tid[$real_count2[$l]]][$u];
						$tmp_loss = 1;
						$tmp_win = 0;
						$win_last_count[$tid[$real_count2[$l]]] = 0;
						$loss_last_count[$tid[$real_count2[$l]]] = 1;
						$go_win[$tid[$real_count2[$l]]]=0;
						$go_loss[$tid[$real_count2[$l]]]=0;
						$tmp_go_win = 1;
						$tmp_go_loss = 1;
					}
				} else {
					if($tmp_win){ 
						if($win_end){
							if($win_lost_last[0] == $win_loss[$tid[$real_count2[$l]]][$u]){
								$win_last_count[$tid[$real_count2[$l]]]++;
							} else {
								$win_end = 0;
							}//if($win_lost_last[0] == $win_loss[$tid[$real_count2[$l]]][$u])
						}//if($win_end == 1)						
					}
					
					if($tmp_loss){ 
						if($loss_end){
							if($win_lost_last[1] == $win_loss[$tid[$real_count2[$l]]][$u]){
								$loss_last_count[$tid[$real_count2[$l]]]++;
							} else {
								$loss_end = 0;
							}//if($win_lost_last[1] == $win_loss[$tid[$real_count2[$l]]][$u]
						}//if($loss_end == 1)						
					}
					
					if($win_loss[$tid[$real_count2[$l]]][$u]){
						if($win_loss[$tid[$real_count2[$l]]][$u-1] == $win_loss[$tid[$real_count2[$l]]][$u]){
							$tmp_go_win++;
							if($go_win[$tid[$real_count2[$l]]] < $tmp_go_win){
								$go_win[$tid[$real_count2[$l]]] =	$tmp_go_win;
							}
						} else {
							$tmp_go_win = 1;
						}						
					} else {
						if($win_loss[$tid[$real_count2[$l]]][$u-1] == $win_loss[$tid[$real_count2[$l]]][$u]){
							$tmp_go_loss++;
							if($go_loss[$tid[$real_count2[$l]]] < $tmp_go_loss){
								$go_loss[$tid[$real_count2[$l]]] =	$tmp_go_loss;
							}
						} else {
							$tmp_go_loss = 1;
						}			
					}					
				}
			}//for($u=$win_loss_count; $u>=1; $u--)
			
			
			if($go_win[$tid[$real_count2[$l]]] < $tmp_go_win)
					$go_win[$tid[$real_count2[$l]]] =	$tmp_go_win;
			
			if($go_loss[$tid[$real_count2[$l]]] < $tmp_go_loss)
					$go_loss[$tid[$real_count2[$l]]] =	$tmp_go_loss;
			
		
			$num_count = $l;					
			if($real_count[$l] == $real_count[$l-1])
				$num_count = $l-1;
				
			if($l>1){
				$win_num = $team[$tid[$real_count2[1]]][0] - $team[$tid[$real_count2[$l]]][0];
				$los_num = $team[$tid[$real_count2[1]]][1] - $team[$tid[$real_count2[$l]]][1];
				
				$total_num = (abs($win_num) + abs($los_num)) / 2;
				$total_num = number_format($total_num, 1, '','');				
			}else
				$total_num = "-";
				
			if(!$win_last_count[$tid[$real_count2[$l]]]) $win_last_count[$tid[$real_count2[$l]]] = "-";
			if(!$loss_last_count[$tid[$real_count2[$l]]]) $loss_last_count[$tid[$real_count2[$l]]] = "-";
			
			if($s_list["1st"] == $tid[$real_count2[$l]])
				$po_view = "우승";
			if($s_list["2nd"] == $tid[$real_count2[$l]])
				$po_view = "준우승";
			if($s_list["3rd"] == $tid[$real_count2[$l]])
				$po_view = "PO";
			if($s_list["4th"] == $tid[$real_count2[$l]])
				$po_view = "PO";
			
			// davej.................	자료 없는 데이터 지나가기
			if (strlen($real_count2[$l]) <= 0) 
				continue; 
?>
		<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td height="30" align="center"> <?php echo $num_count; ?></td>
			<td align="center"> <?php echo $t_name[$real_count2[$l]] ; ?></td>
			<td align="center">&nbsp;<?php echo $game_cnt[$real_count2[$l]] ; ?></td>
			<td align="center">&nbsp;<?php echo $team[$tid[$real_count2[$l]]][0] ; ?></td>
			<td align="center">&nbsp;<?php echo $team[$tid[$real_count2[$l]]][1] ; ?></td>
			<td align="center">&nbsp;<?php echo $real_count[$l] ; ?></td>
			<td align="center">&nbsp;<?php echo $total_num; ?></td>
			<td align="center">&nbsp;<?php echo $win_last_count[$tid[$real_count2[$l]]] ; ?></td>
			<td align="center">&nbsp;<?php echo $loss_last_count[$tid[$real_count2[$l]]] ; ?></td>
			<td align="center">&nbsp;<?php echo $g_start[$tid[$real_count2[$l]]] ; ?></td>
			<td align="center">&nbsp;<?php echo $go_win[$tid[$real_count2[$l]]] ; ?></td>
			<td align="center">&nbsp;<?php echo $go_loss[$tid[$real_count2[$l]]] ; ?></td>
			<td align="center">&nbsp;<?php echo $po_view; ?></td>
		</tr>
<?php
		$po_view = "";
	} 

?>
</table>
</div>
	</td>
	</tr>
</table>
<br>
<?php echo $SITE['tail']; ?>
