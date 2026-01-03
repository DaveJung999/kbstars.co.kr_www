
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

function contribute1($score, $stl, $bs, $re_def, $re_off, $ast, $gd, $min){
	$con1 = ($score + $stl + $bs + $re_def) * 1.0 + ($re_off + $ast + $gd) * 1.5 + $min/4;
	return $con1;
}
function contribute2($tover, $f2, $f3, $fft){
	$con2 = ($tover*1.5 + $f2*1.0 + $f3*0.9 + $fft*0.8);
	return $con2;
} 

?>
<style type="text/css">
<!--
.style1 {color: #666666}
.style2 {color: #CC3300;
	font-weight: bold;
}
-->
</style>
<?php
if($html == "print")	$print = "";
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('t_record.php?html=print&season={$season}&mode={$mode}');\">";

//시즌정보
$sql = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if(!$_GET['season']){
			$_GET['season'] = $list['s_id'];
			$season = $_GET['season'];
		}
		if($season == $list['s_id'])
			$sselect .= "<option value=t_record.php?tid={$tid}&season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=t_record.php?tid={$tid}&season={$list['s_id']}>{$list['s_name']}</option>";
	}		
}	

$t_rs = db_query(" select * FROM `savers_secret`.team order by tid");
$t_cnt = db_count($t_rs);

if($t_cnt)	{
	for($i=0 ; $i<$t_cnt ; $i++){
		$t_list = db_array($t_rs);
		
		if($tid == $t_list['tid'])
			$t_select .= "<option value=t_record.php?tid={$t_list['tid']}&season={$season} selected>{$t_list['t_name']}</option>";
		else
			$t_select .= "<option value=t_record.php?tid={$t_list['tid']}&season={$season}>{$t_list['t_name']}</option>";
	}
}

if($_GET['season']){
	$tuid = time();
	$sql_team = " SELECT * FROM `savers_secret`.team ORDER BY tid ";
	$rs_team = db_query($sql_team);
	$cnt_team = db_count($rs_team);
	
	if($cnt_team)	{
		for($i=0 ; $i<$cnt_team ; $i++){
			$list_team = db_array($rs_team);
			
			$sql_game_h = " SELECT * FROM `savers_secret`.game where g_home = {$list_team['tid']} ORDER BY gid ASC ";
			$rs_game_h = db_query($sql_game_h);
			$cnt_game_h = db_count($rs_game_h);
			
			if($cnt_game_h){
				for($j=0 ; $j<$cnt_game_h ; $j++){
					$list_game_h = db_array($rs_game_h);
					$sql_record_h = " SELECT 
						sum(min) as min,
						sum(2p_a) as 2p_a,
						sum(2p_m) as 2p_m,
						sum(2p_a - 2p_m) 2p_f,
						sum(3p_a) as 3p_a,
						sum(3p_m) as 3p_m,
						sum(3p_a - 3p_m) 3p_f,
						sum(ft_a) as ft_a,
						sum(ft_m) as ft_m,
						sum(ft_a - ft_m) ft_f,
						sum(re_off) as re_off,
						sum(re_def) as re_def,
						sum(ast) as ast,
						sum(stl) as stl,
						sum(bs) as bs,
						sum(gd) as gd,
						sum(tover) as tover,
						sum(w_ft + w_oft + tf) as pf,
						sum(tover) as tov,
						sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as score
					FROM `savers_secret`.record
					WHERE sid = {$_GET['season']} and gid = {$list_game_h['gid']} and tid = {$list_team['tid']}";
					
					$rs_record_h = db_query($sql_record_h);
					$cnt_record_h = db_count($rs_record_h);
					
					if($cnt_record_h){
						for($k=0; $k<$cnt_record_h; $k++){
							$list_record_h = db_array($rs_record_h);
							if($list_record_h['min']){
								$list_sum['tmp_count']++;
								$list_sum['score'] = $list_sum['score'] + $list_record_h['score'];
								$list_sum['p2a'] = $list_sum['p2a'] + $list_record_h['2p_a'];
								$list_sum['p2f'] = $list_sum['p2f'] + $list_record_h['2p_f'];
								$list_sum['p2'] = number_format(($list_sum['p2a'] - $list_sum['p2f']) / $list_sum['p2a'] * 100, 1);
								$list_sum['p3a'] = $list_sum['p3a'] + $list_record_h['3p_a'];
								$list_sum['p3f'] = $list_sum['p3f'] + $list_record_h['3p_f'];								
								$list_sum['p3'] = number_format(($list_sum['p3a'] - $list_sum['p3f']) / $list_sum['p3a'] * 100, 1);
								$list_sum['fpa'] = $list_sum['fpa'] + $list_record_h['ft_a'];
								$list_sum['fpf'] = $list_sum['fpf'] + $list_record_h['ft_f'];								
								$list_sum['fp'] = number_format(($list_sum['fpa'] - $list_sum['fpf']) / $list_sum['fpa'] * 100, 1);
								$list_sum['ast'] = $list_sum['ast'] + $list_record_h['ast'];
								$list_sum['reo'] = $list_sum['reo'] + $list_record_h['re_off'];
								$list_sum['red'] = $list_sum['red'] + $list_record_h['re_def'];
								$list_sum['bs'] = $list_sum['bs'] + $list_record_h['bs'];
								$list_sum['stl'] = $list_sum['stl'] + $list_record_h['stl'];
								$list_sum['pf'] = $list_sum['pf'] + $list_record_h['pf'];
								$list_sum['tov'] = $list_sum['tov'] + $list_record_h['tov'];
							}
						}
					}
				}
				
				if ($list_sum['tmp_count'] == 0 ) $list_sum['tmp_count'] = 1;
				
				$list_sum['score'] 	= number_format($list_sum['score'] / $list_sum['tmp_count'],1);
				$list_sum['ast'] 		= number_format($list_sum['ast'] / $list_sum['tmp_count'],1);
				$list_sum['rea'] 		= number_format(($list_sum['reo'] + $list_sum['red']) / $list_sum['tmp_count'],1);
				$list_sum['bs'] 		= number_format($list_sum['bs'] / $list_sum['tmp_count'],1);
				$list_sum['stl']		= number_format($list_sum['stl'] / $list_sum['tmp_count'],1);
				$list_sum['pf'] 		= number_format($list_sum['pf'] / $list_sum['tmp_count'],1);
				$list_sum['tov'] 		= number_format($list_sum['tov'] / $list_sum['tmp_count'],1);

					
				$tmp_sql_h ="INSERT 
					INTO 
						record_tmp2
					SET
						tuid	= '$tuid',
						tid		= '{$list_team['tid']}',
						t_name	= '{$list_team['t_name']}',
						g_mode	= 1,
						score	= '{$list_sum['score']}',
						g_num	= '{$list_sum['tmp_count']}',
						p2		= '{$list_sum['p2']}',
						p3		= '{$list_sum['p3']}',
						fp		= '{$list_sum['fp']}',
						ast		= '{$list_sum['ast']}',
						rea		= '{$list_sum['rea']}',
						bs		= '{$list_sum['bs']}',
						stl		= '{$list_sum['stl']}',
						pf		= '{$list_sum['pf']}',
						tov		= '{$list_sum['tov']}'
						
					";
					db_query($tmp_sql_h);
				
				unset($list_sum);
			}

						
			
			$sql_game_a = " SELECT * FROM `savers_secret`.game where g_away = {$list_team['tid']} ORDER BY gid ASC ";
			$rs_game_a = db_query($sql_game_a);
			$cnt_game_a = db_count($rs_game_a);
			
			if($cnt_game_a){
				for($j=0 ; $j<$cnt_game_a ; $j++){
					$list_game_a = db_array($rs_game_a);
					$sql_record_a = " SELECT 
						sum(min) as min,
						sum(2p_a) as 2p_a,
						sum(2p_m) as 2p_m,
						sum(2p_a - 2p_m) 2p_f,
						sum(3p_a) as 3p_a,
						sum(3p_m) as 3p_m,
						sum(3p_a - 3p_m) 3p_f,
						sum(ft_a) as ft_a,
						sum(ft_m) as ft_m,
						sum(ft_a - ft_m) ft_f,
						sum(re_off) as re_off,
						sum(re_def) as re_def,
						sum(ast) as ast,
						sum(stl) as stl,
						sum(bs) as bs,
						sum(gd) as gd,
						sum(tover) as tover,
						sum(w_ft + w_oft + tf) as pf,
						sum(tover) as tov,
						sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as score
					FROM `savers_secret`.record
					WHERE sid = {$_GET['season']} and gid = {$list_game_a['gid']} and tid = {$list_team['tid']}";
					
					$rs_record_a = db_query($sql_record_a);
					$cnt_record_a = db_count($rs_record_a);
					
					if($cnt_record_a){
						for($k=0; $k<$cnt_record_a; $k++){
							$list_record_a = db_array($rs_record_a);
							if($list_record_a['min']){
								$list_sum['tmp_count']++;
								$list_sum['score'] = $list_sum['score'] + $list_record_a['score'];
								$list_sum['p2a'] = $list_sum['p2a'] + $list_record_a['2p_a'];
								$list_sum['p2f'] = $list_sum['p2f'] + $list_record_a['2p_f'];
								$list_sum['p2'] = number_format(($list_sum['p2a'] - $list_sum['p2f']) / $list_sum['p2a'] * 100, 1);
								$list_sum['p3a'] = $list_sum['p3a'] + $list_record_a['3p_a'];
								$list_sum['p3f'] = $list_sum['p3f'] + $list_record_a['3p_f'];								
								$list_sum['p3'] = number_format(($list_sum['p3a'] - $list_sum['p3f']) / $list_sum['p3a'] * 100, 1);
								$list_sum['fpa'] = $list_sum['fpa'] + $list_record_a['ft_a'];
								$list_sum['fpf'] = $list_sum['fpf'] + $list_record_a['ft_f'];								
								$list_sum['fp'] = number_format(($list_sum['fpa'] - $list_sum['fpf']) / $list_sum['fpa'] * 100, 1);
								$list_sum['ast'] = $list_sum['ast'] + $list_record_a['ast'];
								$list_sum['reo'] = $list_sum['reo'] + $list_record_a['re_off'];
								$list_sum['red'] = $list_sum['red'] + $list_record_a['re_def'];
								$list_sum['bs'] = $list_sum['bs'] + $list_record_a['bs'];
								$list_sum['stl'] = $list_sum['stl'] + $list_record_a['stl'];
								$list_sum['pf'] = $list_sum['pf'] + $list_record_a['pf'];
								$list_sum['tov'] = $list_sum['tov'] + $list_record_a['tov'];
							}
						}
					}
				}
				
				if ($list_sum['tmp_count'] == 0 ) $list_sum['tmp_count'] = 1;
				$list_sum['score'] = number_format($list_sum['score'] / $list_sum['tmp_count'],1);
				$list_sum['ast'] = number_format($list_sum['ast'] / $list_sum['tmp_count'],1);
				$list_sum['rea'] = number_format(($list_sum['reo'] + $list_sum['red']) / $list_sum['tmp_count'],1);
				$list_sum['bs'] = number_format($list_sum['bs'] / $list_sum['tmp_count'],1);
				$list_sum['stl'] = number_format($list_sum['stl'] / $list_sum['tmp_count'],1);
				$list_sum['pf'] = number_format($list_sum['pf'] / $list_sum['tmp_count'],1);
				$list_sum['tov'] = number_format($list_sum['tov'] / $list_sum['tmp_count'],1);
					
				$tmp_sql_h ="INSERT 
					INTO 
						record_tmp3
					SET
						tuid2	= '$tuid',
						tid		= '{$list_team['tid']}',
						t_name2	= '{$list_team['t_name']}',
						g_mode2	= 2,
						score2	= '{$list_sum['score']}',
						g_num2	= '{$list_sum['tmp_count']}',
						p22		= '{$list_sum['p2']}',
						p32		= '{$list_sum['p3']}',
						fp2		= '{$list_sum['fp']}',					
						ast2	= '{$list_sum['ast']}',
						rea2	= '{$list_sum['rea']}',
						bs2		= '{$list_sum['bs']}',
						stl2	= '{$list_sum['stl']}',
						pf2		= '{$list_sum['pf']}',
						tov2	= '{$list_sum['tov']}'
					";
				db_query($tmp_sql_h);
				
				unset($list_sum);
			}
		}
	}			
}//if($_GET['season'])

$sel[$_GET['mode']]="selected"; 
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
		<td background="/images/admin/tbox_bg.gif"><strong>팀기록 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td height="30"><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='t_record.php'>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="mode" onchange="MM_jumpMenu('this',this,0)">
					<option value='t_record.php?season=<?php echo $_GET['season'] ; ?>&amp;mode=1' <?php echo $sel['1']	; ?>>공격력</option>
					<option value='t_record.php?season=<?php echo $_GET['season'] ; ?>&amp;mode=2' <?php echo $sel['2']	; ?>>수비력</option>
					<option value='t_record.php?season=<?php echo $_GET['season'] ; ?>&amp;mode=3' <?php echo $sel['3']	; ?>>실격/퇴장</option>
					</select></td>
				<td width="70" align="right">
				<?php echo $print ; ?></td>
			</form>
			</tr>
		</table>
	<br /></td>
	</tr>
</table>

<br>
<?php
include("t_record_inc.php");

echo $_GET['mode'];
if($_GET['season']){
	switch($_GET['mode']){
		case '1':
			go1();
		break;
		case '2':
			go2();
		break;
		case '3':
			go3();
		break;
		case '4':
			go4();
		break;
		default :
			go1();
	}

	db_query("DELETE FROM `savers_secret`.record_tmp2 WHERE tuid = {$tuid}");
	db_query("DELETE FROM `savers_secret`.record_tmp3 WHERE tuid2 = {$tuid}");
}

echo $SITE['tail']; 
?>
