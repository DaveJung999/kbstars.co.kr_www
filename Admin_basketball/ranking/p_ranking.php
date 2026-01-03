
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

//공헌도
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
.style1 {color: #333333}
.style2 {
	color: #7A7749;
	font-weight: bold;
}
.style3 {color: #CC3300;
	font-weight: bold;
}
-->
</style>
<?php
if($html == "print")	$print = "";
else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('p_ranking.php?html=print&season={$season}&mode={$mode}');\">";

if ($season) $season = $season;

//시즌정보
$sql = " SELECT *, sid as s_id FROM `savers_secret`.season ORDER BY s_start DESC ";
$rs = db_query($sql);
$cnt = db_count($rs);

if($cnt)	{
	for($i = 0 ; $i < $cnt ; $i++)	{
		$list = db_array($rs);
		if(!$season && $i == 0) $season = $list['s_id'];
		if($season == $list['s_id'])
			$sselect .= "<option value=p_ranking.php?tid={$tid}&season={$list['s_id']} selected>{$list['s_name']}</option>";
		else
			$sselect .= "<option value=p_ranking.php?tid={$tid}&season={$list['s_id']}>{$list['s_name']}</option>";
	}		
}	

$t_rs = db_query(" select * FROM `savers_secret`.team order by tid");
$t_cnt = db_count($t_rs);

if($t_cnt)	{
	for($i=0 ; $i<$t_cnt ; $i++){
		$t_list = db_array($t_rs);
					
		// davej 2024-10-09
		$t_list['t_name'] = $t_list['t_name']." (".$t_list['tid'].")";
		if (!$tid) $tid	= 6;
		if($tid == $t_list['tid'])
			$t_select .= "<option value=p_ranking.php?tid={$t_list['tid']}&season={$season} selected>{$t_list['t_name']}</option>";
		else
			$t_select .= "<option value=p_ranking.php?tid={$t_list['tid']}&season={$season}>{$t_list['t_name']}</option>";
	}
}

if($season){
	$tuid = time();
	/*				davej.......................
	$sql_player = " SELECT * FROM `savers_secret`.player ORDER BY uid DESC ";
	*/
	$sql_player = " SELECT * FROM `savers_secret`.player_teamhistory where sid={$season} ORDER BY pid DESC ";
	
	$rs_player = db_query($sql_player);
	$cnt_player = db_count($rs_player);
	
	if($cnt_player)	{
		for($i=0 ; $i<$cnt_player ; $i++){
		
			$list_player = db_array($rs_player);
	
			//선수 평균/누적 성적 시작
			$sql = " SELECT 
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
					sum(w_oft) as w_oft,
					sum(1qs + 2qs + 3qs + 4qs + e1s + e2s + e3s) as score,
					count(pid) as cnt
				FROM `savers_secret`.record
				WHERE pid = {$list_player['pid']} and sid = {$season} ";
			$rs = db_query($sql);
			$cnt = db_count($rs);
			
			$sql_tot = "SELECT * FROM `savers_secret`.record WHERE pid = {$list_player['pid']} and sid = {$season}";
			$rs_tot = db_query($sql_tot);
			$cnt_tot = db_count($rs_tot);
			
			$t_rs = db_arrayone(" select * FROM `savers_secret`.team where tid = {$list_player['tid']}");
		
			if($cnt){
				$list = db_array($rs);
			
				//출전시간
				if($list['min']){
					$min1 = $list['min'] % 60;
					$min2 = ($list['min'] - $min1) / 60;
					if($min1 < 10)	$min1 = "0".$min1;
					if($min2 < 10)	$min2 = "0".$min2;
					$list['min2'] = $min2.":".$min1;
					$cont_min = $min2.".".$min1;
				}
				//2득점 성공률
				if($list['2p_a']){
					$list['2p_p'] = number_format(($list['2p_a'] - $list['2p_f']) / $list['2p_a'] * 100, 1);
					if($list['2p_p'] == 0.0)	$list['2p_p'] = 0;
				} else {
					$list['2p_p'] = 0;
				}
				//3득점 성공률
				if($list['3p_a']){
					$list['3p_p'] = number_format(($list['3p_a'] - $list['3p_f']) / $list['3p_a'] * 100, 1);
					if($list['3p_p'] == 0.0)	$list['3p_p'] = 0;
				} else {
					$list['2p_p'] = 0;
				}

				//FT 성공률
				if($list['ft_a']){
					$list['ft_p'] = number_format(($list['ft_a'] - $list['ft_f']) / $list['ft_a'] * 100, 1);
					if($list['fp_p'] == 0.0)	$list['fp_p'] = 0;
				} else {
					$list['fp_p'] = 0;
				}
				//평균득점
				if($list['score'])
					$list['avg'] = number_format($list['score'] / $list['cnt']);
					
					$list['re'] = $list['re_off'] + $list['re_def'];
				
				//공헌도
				$con1 = contribute1($list['score'], $list['stl'], $list['bs'], $list['re_def'], $list['re_off'], $list['ast'], $list['gd'], $cont_min);
				$con2 = contribute2($list['tover'], $list['2p_f'], $list['3p_f'], $list['ft_f']);
				if($list['cnt'] > 0)
					$list['cont'] = number_format($con1 - $con2, 2);					
	
			} //if($cnt)
			
			$list['3p'] = $list['3p_a'] - $list['3p_f'];
	
			$tmp_sql ="INSERT INTO record_tmp
						SET
							tuid	= {$tuid},
							p_name	= '{$list_player['pname']}',
							t_name	= '{$t_rs['t_name']}',
							score	= '{$list['score']}',
							re		= '{$list['re']}',
							ast		= '{$list['ast']}',
							stl		= '{$list['stl']}',
							bs		= '{$list['bs']}',
							2pp		= '".$list['2p_p']."',
							3p		= ".$list['3p'].",
							3pp		= '".$list['3p_p']."',
							fp		= '{$list['ft_p']}',
							cont	= '{$list['cont']}',
							g_tot 	= '{$cnt_tot}'
						";
							
			db_query($tmp_sql);
		}//for($i=0 ; $i<$cnt_player ; $i++)
	}//if($cnt_player)
}//if($season)

switch($_GET['mode']){
	case '1':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY cont DESC limit 0, 30";
		$view_name = "공헌도";
	break;
	case '2':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY score DESC limit 0, 30";
		$view_name = "득점";
	break;
	case '3':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY re DESC limit 0, 30";
		$view_name = "리바운드";
	break;
	case '4':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY ast DESC limit 0, 30";
		$view_name = "어시스트";
	break;
	case '5':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY stl DESC limit 0, 30";
		$view_name = "스틸";
	break;
	case '6':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY bs DESC limit 0, 30";
		$view_name = "불록";
	break;
	case '7':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY 2pp DESC limit 0, 30";
		$view_name = "2점성공율";
	break;
	case '8':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY 3p DESC limit 0, 30";
		$view_name = "3득점(개수)";
	break;
	case '9':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY 3pp DESC limit 0, 30";
		$view_name = "3득점성공율";
	break;
	case '10':
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY fp DESC limit 0, 30";
		$view_name = "자유투성공율";
	break;
	default :
		$_GET['mode'] = "1";
		$r_sql = " SELECT * FROM `savers_secret`.record_tmp where tuid = {$tuid} ORDER BY cont DESC limit 0, 30";
		$view_name = "공헌도";
}

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
		<td background="/images/admin/tbox_bg.gif"><strong>선수기록(순위) </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<form name="form1" id="form1">
				<td><select name="season" onchange="MM_jumpMenu('this',this,0)">
					<option value='p_ranking.php'>시즌선택</option>
					<?php echo $sselect ; ?>
				</select>
					<select name="mode" onchange="MM_jumpMenu('this',this,0)">
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=1' <?php echo $sel['1']	; ?>>공헌도</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=2' <?php echo $sel['2']	; ?>>득점</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=3' <?php echo $sel['3']	; ?>>리바운드</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=4' <?php echo $sel['4']	; ?>>어시스트</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=5' <?php echo $sel['5']	; ?>>스틸</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=6' <?php echo $sel['6']	; ?>>불록</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=7' <?php echo $sel['7']	; ?>>2점성공율</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=8' <?php echo $sel['8']	; ?>>3득점(개수)</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=9' <?php echo $sel['9']	; ?>>3득점성공율</option>
					<option value='p_ranking.php?season=<?php echo $season; ?>&amp;mode=10' <?php echo $sel['10']	; ?>>자유투성공율</option>
					</select></td>
				<td width="70" align="right"> <?php echo $print ; ?></td>
			</form>
			</tr>
		</table>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666" height="30">
			<tr align="center" bgcolor="#D2BF7E">
			<td width="10%" height="30" bgcolor="#D2BF7E"><strong>순위</strong></td>
			<td width="20%" bgcolor="#D2BF7E"><strong>선수이름</strong></td>
			<td width="30%"><strong>소속구단</strong></td>
			<td width="20%"><strong>출전경기</strong></td>
			<td width="20%" bgcolor="#D2BF7E"><strong><?php echo $view_name ; ?>
			</strong></td>
			</tr>
<?php
if($season){
	
	$r_rs = db_query($r_sql);
	$r_cnt = db_count($r_rs);
	
	for($i=1 ; $i<=$r_cnt ; $i++){
		$list = db_array($r_rs);
		
		switch($_REQUEST['mode']){
			case '1':
				$view_point = "$list['cont']";
				break;
			case '2':
				$view_point = "$list['score']";
				break;
			case '3':
				$view_point = "$list['re']";
				break;
			case '4':
				$view_point = "$list['ast']";
				break;
			case '5':
				$view_point = "$list['stl']";
				break;
			case '6':
				$view_point = "$list['bs']";
				break;
			case '7':
				$view_point = $list['2pp']."%";
				break;
			case '8':
				$view_point = $list['3p']."";
				break;
			case '9':
				$view_point = $list['3pp']."%";
				break;
			case '10':
				$view_point = "$list['fp']"."%";
				break;
			default :
				$view_point = "$list['cont']";
		} 

?>
			<tr align="center" bgcolor="#F8F8EA" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
			<td width="10%" height="30"><strong><?php echo $i ; ?>
			</strong></td>
			<td width="20%"><?php echo $list['p_name'] ; ?></td>
			<td width="30%"><?php echo $list['t_name'] ; ?></td>
			<td width="20%"><?php echo $list['g_tot'] ; ?></td>
			<td width='20%' bgcolor='#F8F8EA'><?php echo $view_point; ?></td>
			</tr>
<?php
	} 
	db_query("DELETE FROM `savers_secret`.record_tmp WHERE tuid = {$tuid}");
} 

?>
	</table></td>
	</tr>
</table>

<br />
<br />
<?php echo $SITE['tail']; ?>
