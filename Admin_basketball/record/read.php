
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
<style type="text/css">
<!--
.style1 {color: #333333}
.button { border:1x solid #dadada; background-Color:#f7f7f7; font:11px tahoma; color:#555555; }
.style4 {color: #CC3300;
	font-weight: bold;
}
.style5 {font-weight: bold}
.style6 {font-weight: bold}

-->
</style>

<script>
function sub(){
	//선수 기록 입력
	document.write.submit();
}

function del(){
	var answer=confirm("삭제하시겠습니까?");

	if(answer)
		return true;
	else
		return false;
}

</script>
<?php
	if(!$gid)	back_close('경기 정보가 없습니다.', "/Admin_basketball/game/list.php");
	
	if($html == "print")	$print = "&nbsp;";
	else $print = "<img src='/images/print_img.gif' border='0' onClick=\"window.open('read.php?html=print&gid={$gid}');\">";
	
	//경기 기본정보 가져오기
	$trs = db_query(" SELECT *, sid as s_id FROM `savers_secret`.game WHERE gid={$gid} ");
	$tct = db_count($trs);
	if($tct) {	
		$tlist = db_array($trs);
		if($tlist['g_start']>0)
			$start = date("Y.m.d H:i", $tlist['g_start']);
		if($tlist['g_end']>0)
			$end	= date("Y.m.d H:i", $tlist['g_end']);
		
		//시즌 정보 가져오기
		$srs = db_query(" SELECT * FROM `savers_secret`.season WHERE sid={$tlist['s_id']} ");
		$sct = db_count($srs);
		$s_sel = "<option>선수선택</option>";
		if($sct)
			$slist = db_array($srs);
		
		//홈팀 선수 정보 가져오기
		$hprs = db_query(" SELECT * FROM `savers_secret`.player_teamhistory WHERE tid = {$tlist['g_home']} and sid = {$tlist['s_id']} order by length(pbackno), pbackno ");
		$hpct = db_count($hprs);
		$hp_sel = "<option value=''>선수선택</option>";
		if($hpct)	{
			for($i=0 ; $i<$hpct ; $i++)	{
				$hplist = db_array($hprs);
				$hp_num[$i] = $hplist['pid'];
				$hp_name[$i] = $hplist['pname'];
				$hp_back[$i] = $hplist['pbackno'];
				$hp_sel .= "<option value={$hp_num[$i]}>{$hp_name[$i]}</option>";
			}
		}
		
		//어웨이팀 선수 정보 가져오기
		$ayrs = db_query(" SELECT * FROM `savers_secret`.player_teamhistory WHERE tid = {$tlist['g_away']} and sid = {$tlist['s_id']} order by length(pbackno), pbackno");
		$ayct = db_count($ayrs);
		$ay_sel = "<option value=''>선수선택</option>";
		if($ayct)	{
			for($i=0 ; $i<$ayct ; $i++)	{
				$aylist = db_array($ayrs);
				$ay_num[$i] = $aylist['pid'];
				$ay_name[$i] = $aylist['pname'];
				$ay_back[$i] = $aylist['pbackno'];
				$ay_sel .= "<option value={$ay_num[$i]}>{$ay_name[$i]}</option>";
			}
		}
		
		//홈팀 정보
		$htrs = db_query( " SELECT * from `savers_secret`.team WHERE tid = {$tlist['g_home']} ");
		$htct = db_count( $htrs );
		if($htct)	{
			$htlist = db_array( $htrs );
		}
		
		//어웨이팀 정보
		$atrs = db_query( " SELECT * from `savers_secret`.team WHERE tid = {$tlist['g_away']} ");
		$atct = db_count( $atrs );
		if($atct)	{
			$atlist = db_array( $atrs );
		}
	}
	
	//홈팀 경기 기록 정보
	$re_rs1 = db_query(" select * from `savers_secret`.record where gid = {$gid} and tid = {$htlist['tid']} ");
	$re_cnt1 = db_count($re_rs1);
	if($re_cnt1)	{
		for($i=0 ; $i<$re_cnt1 ; $i++)	{
			$re_list_h = db_array($re_rs1);
			$home_1qs = $home_1qs + $re_list_h['1qs'];
			$home_2qs = $home_2qs + $re_list_h['2qs'];
			$home_3qs = $home_3qs + $re_list_h['3qs'];
			$home_4qs = $home_4qs + $re_list_h['4qs'];
			$home_1234 = $home_1qs + $home_2qs + $home_3qs + $home_4qs;
			$home_e1s = $home_e1s + $re_list_h['e1s'];
			$home_e2s = $home_e2s + $re_list_h['e2s'];
			$home_e3s = $home_e3s + $re_list_h['e3s'];
			$home_e123 = $home_e1s + $home_e2s + $home_e3s;
			
			$home_min1 = $home_min1 + $re_list_h['min'];
			$home_min2 = $home_min1 % 60;
			$home_min3 = ($home_min1 - $home_min2) / 60;
			$home_min4 = $home_min3.":".$home_min2;
			
			$home_3p_m = $home_3p_m + $re_list_h['3p_m'];
			$home_3p_a = $home_3p_a + $re_list_h['3p_a'];
			if($home_3p_m > 0 && $home_3p_a >0){
				$home_3p_per = round($home_3p_m / $home_3p_a * 100);
			}
			$home_2p_m = $home_2p_m + $re_list_h['2p_m'];
			$home_2p_a = $home_2p_a + $re_list_h['2p_a'];
			if($home_2p_m > 0 && $home_2p_a >0){
				$home_2p_per = round($home_2p_m / $home_2p_a * 100);
			}
			$home_ft_m = $home_ft_m + $re_list_h['ft_m'];
			$home_ft_a = $home_ft_a + $re_list_h['ft_a'];
			if($home_ft_m > 0 && $home_ft_a >0){
				$home_ft_per = round($home_ft_m / $home_ft_a * 100);
			}
			$home_re_off = $home_re_off + $re_list_h['re_off'];
			$home_re_def = $home_re_def + $re_list_h['re_def'];
			$home_ast = $home_ast + $re_list_h['ast'];
			$home_stl = $home_stl + $re_list_h['stl'];
			$home_gd = $home_gd + $re_list_h['gd'];
			$home_bs = $home_bs + $re_list_h['bs'];
			$home_w_ft = $home_w_ft + $re_list_h['w_ft'];
			$home_w_oft = $home_w_oft + $re_list_h['w_oft'];
			$home_tover = $home_tover + $re_list_h['tover'];
			$home_ldf = $home_ldf + $re_list_h['ldf'];
			$home_tf = $home_tf + $re_list_h['tf'];
			
			$home_rid[$i] = $re_list_h['rid'];
			$home_gid[$i] = $re_list_h['gid'];
			
			//선수기록			
			$home_ppid[$i] = $re_list_h['pid'];			
			for($j=0 ; $j<count($hp_num) ; $j++){
				if($home_ppid[$i] == $hp_num[$j])	{
					$home_pp_name[$i] = $hp_name[$j];
					$home_pp_back[$i] = $hp_back[$j];
				}
			}
			$home_start[$i] = $re_list_h['start'] ? "*":"";
			$home_p1qs[$i] = $re_list_h['1qs'];
			$home_p2qs[$i] = $re_list_h['2qs'];
			$home_p3qs[$i] = $re_list_h['3qs'];
			$home_p4qs[$i] = $re_list_h['4qs'];
			$home_pe1s[$i] = $re_list_h['e1s'];
			$home_pe2s[$i] = $re_list_h['e2s'];
			$home_pe3s[$i] = $re_list_h['e3s'];
			
			
			
			$home_pmin[$i] = $re_list_h['min'];
			$home_pmin1[$i] = $home_pmin[$i] % 60;	
			$home_pmin1[$i] = sprintf("%02d",$home_pmin1[$i]);
			$home_pmin2[$i] = ($home_pmin[$i] - $home_pmin1[$i]) / 60;
			$home_pmin2[$i]	= sprintf("%02d",$home_pmin2[$i]);
			$home_pmin[$i] = $home_pmin2[$i].":".$home_pmin1[$i];
			
			$home_p3p_m[$i] = $re_list_h['3p_m'];
			$home_p3p_a[$i] = $re_list_h['3p_a'];
			if($home_p3p_m[$i]>0 && $home_p3p_a[$i] >0)	{
				$home_p3p_per[$i] = round($home_p3p_m[$i] / $home_p3p_a[$i] * 100);
			}
			$home_p2p_m[$i] = $re_list_h['2p_m'];
			$home_p2p_a[$i] = $re_list_h['2p_a'];
			if($home_p2p_m[$i]>0 && $home_p2p_a[$i]>0)	{
				$home_p2p_per[$i] = round($home_p2p_m[$i] / $home_p2p_a[$i] * 100);
			}
			$home_pft_m[$i] = $re_list_h['ft_m'];
			$home_pft_a[$i] = $re_list_h['ft_a'];
			if($home_pft_m[$i]>0 && $home_pft_a[$i]>0)	{
				$home_pft_per[$i] = round($home_pft_m[$i] / $home_pft_a[$i] * 100);
			}
			$home_pre_off[$i] = $re_list_h['re_off'];
			$home_pre_def[$i] = $re_list_h['re_def'];
			$home_past[$i] = $re_list_h['ast'];
			$home_pstl[$i] = $re_list_h['stl'];
			$home_pgd[$i] = $re_list_h['gd'];
			$home_pbs[$i] = $re_list_h['bs'];
			$home_pw_ft[$i] = $re_list_h['w_ft'];
			$home_pre_def[$i] = $re_list_h['re_def'];
			$home_pw_oft[$i] = $re_list_h['w_oft'];
			$home_ptover[$i] = $re_list_h['tover'];
			$home_pldf[$i] = $re_list_h['ldf'];
			$home_ptf[$i] = $re_list_h['tf'];
			
			$home_pcontri1[$i] = ($re_list_h['1qs'] + $re_list_h['2qs'] + $re_list_h['3qs'] + $re_list_h['4qs'] + $re_list_h['e1s'] + $re_list_h['e2s'] + $re_list_h['e2s'] + $re_list_h['stl'] + $re_list_h['bs'] + $re_list_h['re_def'])
								* 1.0 + ( $re_list_h['re_off'] + $re_list_h['ast'] + $re_list_h['gd'])
								* 1.5 + $home_pmin2[$i] / 4;
			$home_pcontri2[$i] = ($home_ptover[$i] * 1.5 + ($home_p2p_a[$i] - $home_p2p_m[$i]) * 1.0 + ($home_p3p_a[$i] - $home_p3p_m[$i]) * 0.9
								+ ($home_pft_a[$i] - $home_pft_m[$i]) * 0.8);
			$home_pcontri[$i] = $home_pcontri1[$i] - $home_pcontri2[$i];
			$home_contri_total = $home_contri_total + $home_pcontri[$i];
		}
	}
	//어웨이팀 경기 기록 정보
	$re_rs2 = db_query(" select * from `savers_secret`.record where gid = {$gid} and tid = {$atlist['tid']} ");
	$re_cnt2 = db_count($re_rs2);
	if($re_cnt2)	{
		for($i=0 ; $i<$re_cnt2 ; $i++)	{
			$re_list_a = db_array($re_rs2);
			//팀기록
			$away_1qs = $away_1qs + $re_list_a['1qs'];
			$away_2qs = $away_2qs + $re_list_a['2qs'];
			$away_3qs = $away_3qs + $re_list_a['3qs'];
			$away_4qs = $away_4qs + $re_list_a['4qs'];
			$away_1234 = $away_1qs + $away_2qs + $away_3qs + $away_4qs;
			$away_e1s = $away_e1s + $re_list_a['e1s'];
			$away_e2s = $away_e2s + $re_list_a['e2s'];
			$away_e3s = $away_e3s + $re_list_a['e3s'];
			$away_e123 = $away_e1s + $away_e2s + $away_e3s;
			
			$away_min1 = $away_min1 + $re_list_a['min'];
			$away_min2 = $away_min1 % 60;
			$away_min3 = ($away_min1 - $away_min2) / 60;
			$away_min4 = $away_min3.":".$away_min2;
			
			$away_3p_m = $away_3p_m + $re_list_a['3p_m'];
			$away_3p_a = $away_3p_a + $re_list_a['3p_a'];
			if($away_3p_m>0 && $away_3p_a>0)	{
				$away_3p_per = round($away_3p_m / $away_3p_a * 100);
			}
			$away_2p_m = $away_2p_m + $re_list_a['2p_m'];
			$away_2p_a = $away_2p_a + $re_list_a['2p_a'];
			if($away_2p_m>0 && $away_2p_a>0)	{
				$away_2p_per = round($away_2p_m / $away_2p_a * 100);
			}
			$away_ft_m = $away_ft_m + $re_list_a['ft_m'];
			$away_ft_a = $away_ft_a + $re_list_a['ft_a'];
			if($away_ft_m>0 && $away_ft_a>0)	{
				$away_ft_per = round($away_ft_m / $away_ft_a * 100);
			}
			$away_re_off = $away_re_off + $re_list_a['re_off'];
			$away_re_def = $away_re_def + $re_list_a['re_def'];
			$away_ast = $away_ast + $re_list_a['ast'];
			$away_stl = $away_stl + $re_list_a['stl'];
			$away_gd = $away_gd + $re_list_a['gd'];
			$away_bs = $away_bs + $re_list_a['bs'];
			$away_w_ft = $away_w_ft + $re_list_a['w_ft'];
			$away_w_oft = $away_w_oft + $re_list_a['w_oft'];
			$away_tover = $away_tover + $re_list_a['tover'];
			$away_ldf = $away_ldf + $re_list_a['ldf'];
			$away_tf = $away_tf + $re_list_a['tf'];
			
			$away_rid[$i] = $re_list_a['rid'];
			$away_gid[$i] = $re_list_a['gid'];
			//선수기록
			$away_ppid[$i] = $re_list_a['pid'];
			for($j=0 ; $j<count($ay_num) ; $j++){
				if($away_ppid[$i] == $ay_num[$j])	{
					$away_pp_name[$i] = $ay_name[$j];
					$away_pp_back[$i] = $ay_back[$j];
				}
			}
			$away_start[$i] = $re_list_a['start'] ? "*":"";
			$away_p1qs[$i] = $re_list_a['1qs'];
			$away_p2qs[$i] = $re_list_a['2qs'];
			$away_p3qs[$i] = $re_list_a['3qs'];
			$away_p4qs[$i] = $re_list_a['4qs'];
			$away_pe1s[$i] = $re_list_a['e1s'];
			$away_pe2s[$i] = $re_list_a['e2s'];
			$away_pe3s[$i] = $re_list_a['e3s'];
			
			$away_pmin[$i] = $re_list_a['min'];
			$away_pmin1[$i] = $away_pmin[$i] % 60;	
			$away_pmin1[$i] = sprintf("%02d",$away_pmin1[$i]);
			$away_pmin2[$i] = ($away_pmin[$i] - $away_pmin1[$i]) / 60;
			$away_pmin2[$i] = sprintf("%02d",$away_pmin2[$i]);
			$away_pmin[$i] = $away_pmin2[$i].":".$away_pmin1[$i];
			
			$away_p3p_m[$i] = $re_list_a['3p_m'];
			$away_p3p_a[$i] = $re_list_a['3p_a'];
			if($away_p3p_m[$i]>0 && $away_p3p_a[$i]>0)	{
				$away_p3p_per[$i] = round($away_p3p_m[$i] / $away_p3p_a[$i] * 100);
			}
			$away_p2p_m[$i] = $re_list_a['2p_m'];
			$away_p2p_a[$i] = $re_list_a['2p_a'];
			if($away_p2p_m[$i]>0 && $away_p2p_a[$i]>0)	{
				$away_p2p_per[$i] = round($away_p2p_m[$i] / $away_p2p_a[$i] * 100);
			}
			$away_pft_m[$i] = $re_list_a['ft_m'];
			$away_pft_a[$i] = $re_list_a['ft_a'];
			if($away_pft_m[$i]>0 && $away_pft_a[$i]>0)	{
				$away_pft_per[$i] = round($away_pft_m[$i] / $away_pft_a[$i] * 100);
			}
			$away_p3p_m[$i] = $re_list_a['3p_m'];
			$away_pre_off[$i] = $re_list_a['re_off'];
			$away_pre_def[$i] = $re_list_a['re_def'];
			$away_past[$i] = $re_list_a['ast'];
			$away_pstl[$i] = $re_list_a['stl'];
			$away_pgd[$i] = $re_list_a['gd'];
			$away_pbs[$i] = $re_list_a['bs'];
			$away_pw_ft[$i] = $re_list_a['w_ft'];
			$away_pre_def[$i] = $re_list_a['re_def'];
			$away_pw_oft[$i] = $re_list_a['w_oft'];
			$away_ptover[$i] = $re_list_a['tover'];
			$away_pldf[$i] = $re_list_a['ldf'];
			$away_ptf[$i] = $re_list_a['tf'];
			
			$away_pcontri1[$i] = ($re_list_a['1qs'] + $re_list_a['2qs'] + $re_list_a['3qs'] + $re_list_a['4qs'] + $re_list_a['e1s'] + $re_list_a['e2s'] + $re_list_a['e2s'] + $re_list_a['stl'] + $re_list_a['bs'] + $re_list_a['re_def'])
								* 1.0 + ( $re_list_a['re_off'] + $re_list_a['ast'] + $re_list_a['gd'])
								* 1.5 + $away_pmin2[$i] / 4;
			$away_pcontri2[$i] = ($away_ptover[$i] * 1.5 + ($away_p2p_a[$i] - $away_p2p_m[$i]) * 1.0 + ($away_p3p_a[$i] - $away_p3p_m[$i]) * 0.9
								+ ($away_pft_a[$i] - $away_pft_m[$i]) * 0.8);
			$away_pcontri[$i] = $away_pcontri1[$i] - $away_pcontri2[$i];
			$away_contri_total = $away_contri_total + $away_pcontri[$i];
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
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22" /></td>
		<td background="/images/admin/tbox_bg.gif"><strong>한 경기 종합기록 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22" /></td>
		</tr>
	</table>
		<br />
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="0">
			<tr>
			<td width="29%" valign="middle"><table width="399" border="0" cellpadding="6" cellspacing="0">
				<tr align="center" height="25">
					<td width="399" height="15" align="left"><span class="style1">&nbsp;&nbsp;<strong>시즌</strong> : <?php echo $slist['s_name'] ; ?>
					</span></td>
				</tr>
				<tr align="center" height="25">
					<td height="15" align="left"><span class="style1">&nbsp;&nbsp;<strong>경기구분</strong> : <?php echo $tlist['g_division'] ; ?>
					</span></td>
				</tr>
				<tr align="center" height="25">
					<td height="15" align="left"><span class="style1">&nbsp;&nbsp;<strong>경기번호</strong> : <?php echo $tlist['gameno'] ; ?>
					</span></td>
				</tr>
				<tr align="center" height="25">
					<td height="15" align="left"><span class="style1">&nbsp;&nbsp;<strong>경기일자</strong> : <?php echo $start ; ?> ~ <?php echo $end ; ?>
					</span></td>
				</tr>
				<tr align="center" height="25">
					<td height="15" align="left"><span class="style1">&nbsp;&nbsp;<strong>경기장</strong> : <?php echo $tlist['g_ground'] ; ?>
					</span></td>
				</tr>
			</table></td>
			<td width="43%" valign="middle"><table width="100%" border="0" cellpadding="6" cellspacing="1" bgcolor="#666666">
				<tr align="center" bgcolor="#D2BF7E">
					<td height="40" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style1">팀 명 </span></strong></td>
					<td height="20" colspan="5"><strong><span class="style1">정 규 경 기</span></strong></td>
					<td colspan="4"><strong><span class="style1">연 장</span></strong></td>
					<td height="40" rowspan="2"><strong><span class="style1">합계</span></strong></td>
					<td rowspan="2"><strong><span class="style1">TR</span></strong></td>
					<td rowspan="2"><strong><span class="style1">TB</span></strong></td>
				</tr>
				<tr>
					<td height="20" align="center" bgcolor="#D2BF7E"><strong><span class="style1">1Q</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">2Q</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">3Q</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">4Q</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">계</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">E1</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">E2</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">E3</span></strong></td>
					<td align="center" bgcolor="#D2BF7E"><strong><span class="style1">계</span></strong></td>
				</tr>
				<tr align="center" bgcolor="#F8F8EA">
					<td height="20">홈 : <?php echo $htlist['t_name']." (".$htlist['tid'].")" ; ?></td>
					<td align="center"> <?php echo $home_1qs ; ?></td>
					<td align="center"> <?php echo $home_2qs ; ?></td>
					<td align="center"> <?php echo $home_3qs ; ?></td>
					<td align="center"> <?php echo $home_4qs ; ?></td>
					<td align="center"> <?php echo $home_1234 ; ?></td>
					<td align="center"> <?php echo $home_e1s ; ?></td>
					<td align="center"> <?php echo $home_e2s ; ?></td>
					<td align="center"> <?php echo $home_e3s ; ?></td>
					<td align="center"> <?php echo $home_e123 ; ?></td>
					<td align="center"> <?php echo $home_1234 + $home_e123 ; ?></td>
					<td align="center"> <?php echo $tlist['home_tr'] ; ?></td>
					<td align="center"> <?php echo $tlist['home_bf'] ; ?></td>
				</tr>
				<tr align="center" bgcolor="#F8F8EA">
					<td height="20">원정 : <?php echo $atlist['t_name']." (".$atlist['tid'].")" ; ?></td>
					<td align="center"> <?php echo $away_1qs ; ?></td>
					<td align="center"> <?php echo $away_2qs ; ?></td>
					<td align="center"> <?php echo $away_3qs ; ?></td>
					<td align="center"> <?php echo $away_4qs ; ?></td>
					<td align="center"> <?php echo $away_1234 ; ?></td>
					<td align="center"> <?php echo $away_e1s ; ?></td>
					<td align="center"> <?php echo $away_e2s ; ?></td>
					<td align="center"> <?php echo $away_e3s ; ?></td>
					<td align="center"> <?php echo $away_e123 ; ?></td>
					<td align="center"> <?php echo $away_1234 + $away_e123 ; ?></td>
					<td align="center"> <?php echo $tlist['away_tr'] ; ?></td>
					<td align="center"> <?php echo $tlist['away_bf'] ; ?></td>
				</tr>
			</table></td>
			</tr>
			<tr>
				<td valign="middle">&nbsp;</td>
				<td align="right" valign="middle"><input name="write" type="button" class="CCbox04" style="cursor: pointer" value="입력" onClick="javascript:location.href='write.php?mode=write&amp;gid=<?php echo $gid ; ?>'" /></td>
			</tr>
		</table>
		<br />
		<!--------------------- 홈팀팀 기록 시작 ------------------------------------->
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center" height="25">
				<td height="30" rowspan="2" bgcolor="#D2BF7E"><strong>No</strong></td>
				<td height="30" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style6">
				<?php echo $htlist['t_name']." (".$htlist['tid'].")" ; ?>
				</span></strong></td>
				<td colspan="6" bgcolor="#D2BF7E"><strong>Scoring</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Min</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>3P</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>2P</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>FT</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>REBOUNDS</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Ast</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Stl</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>GD</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>BS</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>PF</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>TO</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>ldf</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>TF</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>공헌도</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>M 수정<br />
				D 삭제 </strong></td>
			</tr>
			<tr align="center" height="25">
				<td bgcolor="#D2BF7E"><strong>1Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>2Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>3Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>4Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>EQ</strong></td>
				<td bgcolor="#D2BF7E"><strong>&nbsp;계</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>Off</strong></td>
				<td bgcolor="#D2BF7E"><strong>Def</strong></td>
				<td bgcolor="#D2BF7E"><strong>계</strong></td>
				<td bgcolor="#D2BF7E"><strong>w/FT</strong></td>
				<td bgcolor="#D2BF7E"><strong>w/oFT</strong></td>
				<td bgcolor="#D2BF7E"><strong>계</strong></td>
			</tr>
<?php
		for($i=0 ; $i<count($home_ppid) ; $i++){ 
?>
			<tr align="center" bgcolor="#F8F8EA" height="25" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30">
				<?php echo $home_start[$i] ; 
				 echo $home_pp_back[$i] ; ?></td>
				<td height="30" nowrap="nowrap">
				<?php echo $home_pp_name[$i] ; ?></td>
				<td><?php echo $home_p1qs[$i] ; ?></td>
				<td><?php echo $home_p2qs[$i] ; ?></td>
				<td><?php echo $home_p3qs[$i] ; ?></td>
				<td><?php echo $home_p4qs[$i] ; ?></td>
				<td><?php echo $home_pe1s[$i] + $home_pe2s[$i] + $home_pe3s[$i] ; ?></td>
				<td><?php echo $home_p1qs[$i] + $home_p2qs[$i] + $home_p3qs[$i] + $home_p4qs[$i] + $home_pe1s[$i] + $home_pe2s[$i] + $home_pe3s[$i] ; ?></td>
				<td><?php echo $home_pmin[$i] ; ?></td>
				<td><?php echo $home_p3p_m[$i] ; ?></td>
				<td><?php echo $home_p3p_a[$i] ; ?></td>
				<td><?php echo $home_p3p_per[$i] ; ?></td>
				<td><?php echo $home_p2p_m[$i] ; ?></td>
				<td><?php echo $home_p2p_a[$i] ; ?></td>
				<td><?php echo $home_p2p_per[$i] ; ?></td>
				<td><?php echo $home_pft_m[$i] ; ?></td>
				<td><?php echo $home_pft_a[$i] ; ?></td>
				<td><?php echo $home_pft_per[$i] ; ?></td>
				<td><?php echo $home_pre_off[$i] ; ?></td>
				<td><?php echo $home_pre_def[$i] ; ?></td>
				<td><?php echo $home_pre_off[$i] + $home_pre_def[$i] ; ?></td>
				<td><?php echo $home_past[$i] ; ?></td>
				<td><?php echo $home_pstl[$i] ; ?></td>
				<td><?php echo $home_pgd[$i] ; ?></td>
				<td><?php echo $home_pbs[$i] ; ?></td>
				<td><?php echo $home_pw_ft[$i] ; ?></td>
				<td><?php echo $home_pw_oft[$i] ; ?></td>
				<td><?php echo $home_pw_ft[$i] + $home_pw_oft[$i] ; ?></td>
				<td><?php echo $home_ptover[$i] ; ?></td>
				<td><?php echo $home_pldf[$i] ; ?></td>
				<td><?php echo $home_ptf[$i] ; ?></td>
				<td><?php echo $home_pcontri[$i] ; ?></td>
				<td><input name="modify" type="button" class="CCboxw" id="modify" style="cursor: pointer" value="M" onclick="javascript:location.href='modify.php?mode=modify&rid=<?php echo $home_rid[$i] ; ?>&gid=<?php echo $home_gid[$i] ; ?>&pid=<?php echo $home_ppid[$i] ; ?>&s_id=<?php echo $tlist['s_id'] ; ?>&season=<?=$season?>&tid=<?=$tid?>';" />
					<input name="delete" type="button" class="CCboxw" id="delete" style="cursor: pointer" value="D" onclick="javascript:if (confirm('정말 삭제 할까요?')) location.href='ok.php?mode=delete&rid=<?php echo $home_rid[$i] ; ?>&gid=<?php echo $home_gid[$i] ; ?>&pid=<?php echo $home_ppid[$i] ; ?>&s_id=<?php echo $tlist['s_id'] ; ?>&season=<?=$season?>&tid=<?=$tid?>';" /></td>
			</tr>
<?php
		} 
?>
			<tr align="center" bgcolor="#F8F8EA" height="25">
				<td height="30" colspan="2" bgcolor="#F0E9CF"><strong>TOTAL</strong></td>
				<td bgcolor="#F0E9CF"><?php echo $home_1qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_2qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_3qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_4qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_e123 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_1234 + $home_e123 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_min4 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_3p_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_3p_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_3p_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_2p_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_2p_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_2p_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_ft_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_ft_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_ft_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_re_off ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_re_def ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_re_off + $home_re_def ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_ast ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_stl ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_gd ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_bs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_w_ft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_w_oft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_w_ft + $home_w_oft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_tover ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_ldf ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_tf ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $home_contri_total ; ?></td>
				<td bgcolor="#F0E9CF">&nbsp;</td>
			</tr>
		</table>
		<!--------------------- 홈팀팀 기록 끝 -------------------------------------->
		<br />

		<!--------------------- 어웨이팀 기록 시작 ---------------------------------->
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666">
			<tr align="center" height="25">
				<td height="30" rowspan="2" bgcolor="#D2BF7E"><strong>No</strong></td>
				<td height="30" rowspan="2" bgcolor="#D2BF7E"><strong><span class="style6">
				<?php echo $atlist['t_name']." (".$atlist['tid'].")" ; ?>
				</span></strong></td>
				<td colspan="6" bgcolor="#D2BF7E"><strong>Scoring</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Min</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>3P</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>2P</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>FT</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>REBOUNDS</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Ast</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>Stl</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>GD</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>BS</strong></td>
				<td colspan="3" bgcolor="#D2BF7E"><strong>PF</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>TO</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>ldf</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>TF</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>공헌도</strong></td>
				<td rowspan="2" bgcolor="#D2BF7E"><strong>M 수정<br />
				D 삭제</strong></td>
			</tr>
			<tr align="center" height="25">
				<td bgcolor="#D2BF7E"><strong>1Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>2Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>3Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>4Q</strong></td>
				<td bgcolor="#D2BF7E"><strong>EQ</strong></td>
				<td bgcolor="#D2BF7E"><strong>계</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>M</strong></td>
				<td bgcolor="#D2BF7E"><strong>A</strong></td>
				<td bgcolor="#D2BF7E"><strong>%</strong></td>
				<td bgcolor="#D2BF7E"><strong>Off</strong></td>
				<td bgcolor="#D2BF7E"><strong>Def</strong></td>
				<td bgcolor="#D2BF7E"><strong>계</strong></td>
				<td bgcolor="#D2BF7E"><strong>w/FT</strong></td>
				<td bgcolor="#D2BF7E"><strong>w/oFT</strong></td>
				<td bgcolor="#D2BF7E"><strong>계</strong></td>
			</tr>
<?php
		for($i=0 ; $i<count($away_ppid) ; $i++){ 
?>
			<tr align="center" bgcolor="#F8F8EA" height="25" onMouseOver="this.style.backgroundColor='#C6E2F9'" onMouseOut="this.style.backgroundColor=''">
				<td height="30">
				<?php echo $away_start[$i] ; 
				 echo $away_pp_back[$i] ; ?></td>
				<td height="30" nowrap="nowrap">
				<?php echo $away_pp_name[$i] ; ?></td>
				<td><?php echo $away_p1qs[$i] ; ?></td>
				<td><?php echo $away_p2qs[$i] ; ?></td>
				<td><?php echo $away_p3qs[$i] ; ?></td>
				<td><?php echo $away_p4qs[$i] ; ?></td>
				<td><?php echo $away_pe1s[$i] + $away_pe2s[$i] + $away_pe3s[$i] ; ?></td>
				<td><?php echo $away_p1qs[$i] + $away_p2qs[$i] + $away_p3qs[$i] + $away_p4qs[$i] + $away_pe1s[$i] + $away_pe2s[$i] + $away_pe3s[$i] ; ?></td>
				<td><?php echo $away_pmin[$i] ; ?></td>
				<td><?php echo $away_p3p_m[$i] ; ?></td>
				<td><?php echo $away_p3p_a[$i] ; ?></td>
				<td><?php echo $away_p3p_per[$i] ; ?></td>
				<td><?php echo $away_p2p_m[$i] ; ?></td>
				<td><?php echo $away_p2p_a[$i] ; ?></td>
				<td><?php echo $away_p2p_per[$i] ; ?></td>
				<td><?php echo $away_pft_m[$i] ; ?></td>
				<td><?php echo $away_pft_a[$i] ; ?></td>
				<td><?php echo $away_pft_per[$i] ; ?></td>
				<td><?php echo $away_pre_off[$i] ; ?></td>
				<td><?php echo $away_pre_def[$i] ; ?></td>
				<td><?php echo $away_pre_off[$i] + $away_pre_def[$i] ; ?></td>
				<td><?php echo $away_past[$i] ; ?></td>
				<td><?php echo $away_pstl[$i] ; ?></td>
				<td><?php echo $away_pgd[$i] ; ?></td>
				<td><?php echo $away_pbs[$i] ; ?></td>
				<td><?php echo $away_pw_ft[$i] ; ?></td>
				<td><?php echo $away_pw_oft[$i] ; ?></td>
				<td><?php echo $away_pw_ft[$i] + $away_pw_oft[$i] ; ?></td>
				<td><?php echo $away_ptover[$i] ; ?></td>
				<td><?php echo $away_pldf[$i] ; ?></td>
				<td><?php echo $away_ptf[$i] ; ?></td>
				<td><?php echo $away_pcontri[$i] ; ?></td>
				<td><input name="modify2" type="button" class="CCboxw" id="modify" value="M" onclick="javascript:location.href='modify.php?mode=modify&rid=<?php echo $away_rid[$i] ; ?>&gid=<?php echo $away_gid[$i] ; ?>&pid=<?php echo $away_ppid[$i] ; ?>&s_id=<?php echo $tlist['s_id'] ; ?>&season=<?=$season?>&tid=<?=$tid?>';" />
					<input name="delete" type="button" class="CCboxw" id="delete" value="D" onclick="javascript:if (confirm('정말 삭제 할까요?')) location.href='ok.php?mode=delete&rid=<?php echo $away_rid[$i] ; ?>&gid=<?php echo $away_gid[$i] ; ?>&pid=<?php echo $away_ppid[$i] ; ?>&s_id=<?php echo $tlist['s_id'] ; ?>&season=<?=$season?>&tid=<?=$tid?>';" /></td>
			</tr>
<?php
		} 
?>
			<tr align="center" bgcolor="#F8F8EA" height="25">
				<td height="30" colspan="2" bgcolor="#F0E9CF"><strong>TOTAL</strong></td>
				<td bgcolor="#F0E9CF"><?php echo $away_1qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_2qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_3qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_4qs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_e123 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_1234 + $away_e123 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_min4 ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_3p_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_3p_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_3p_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_2p_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_2p_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_2p_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_ft_m ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_ft_a ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_ft_per ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_re_off ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_re_def ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_re_off + $away_re_def ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_ast ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_stl ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_gd ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_bs ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_w_ft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_w_oft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_w_ft + $away_w_oft ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_tover ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_ldf ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_tf ; ?></td>
				<td bgcolor="#F0E9CF"><?php echo $away_contri_total ; ?></td>
				<td bgcolor="#F0E9CF">&nbsp;</td>
			</tr>
		</table>
		<br />
		<table width="97%"	border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td align="center">&nbsp;
				<input name="Submit2" type="button" class="CCbox04" style="cursor: pointer" value=" 경기목록 " onclick="javascript:location.href='list.php?gid=<?=$gid;?>&season=<?=$season?>&tid=<?=$tid?>';" /></td>
			</tr>
		</table></td>
	</tr>
</table>
<!--------------------- 어웨이팀 기록 끝 ---------------------------------->
<br>
</br>
<br>
<?php echo $SITE['tail']; ?>
