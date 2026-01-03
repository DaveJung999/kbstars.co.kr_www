
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
<style type="text/css">
<!--
.style1 {color: #333333}
.button { border:1x solid #dadada; background-Color:#f7f7f7; font:11px tahoma; color:#555555; }

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
		if($tlist['g_start']>0){
			$start = date("Y.m.d H:i", $tlist['g_start']);
			$s_date = date("Y/m/d", $tlist['g_start']);
		}
		if($tlist['g_end']>0)
			$end	= date("Y.m.d H:i", $tlist['g_end']);
		
		//시즌 정보 가져오기
		$srs = db_query(" SELECT * FROM `savers_secret`.season WHERE sid={$tlist['s_id']} ");
		$sct = db_count($srs);
		$s_sel = "<option>선수선택</option>";
		if($sct)
			$slist = db_array($srs);
		
		//홈팀 선수 정보 가져오기
		$hprs = db_query(" SELECT * FROM `savers_secret`.player WHERE tid={$tlist['g_home']} ");
		$hpct = db_count($hprs);
		$hp_sel = "<option value=''>선수선택</option>";
		if($hpct)	{
			for($i=0 ; $i<$hpct ; $i++)	{
				$hplist = db_array($hprs);
				$hp_num[$i] = $hplist['uid'];
				$hp_name[$i] = $hplist['p_name'];
				$hp_back[$i] = $hplist['p_num'];
				$hp_sel .= "<option value={$hp_num[$i]}>{$hp_name[$i]}</option>";
			}
		}
		
		//어웨이팀 선수 정보 가져오기
		$ayrs = db_query(" SELECT * FROM `savers_secret`.player WHERE tid={$tlist['g_away']} ");
		$ayct = db_count($ayrs);
		$ay_sel = "<option value=''>선수선택</option>";
		if($ayct)	{
			for($i=0 ; $i<$ayct ; $i++)	{
				$aylist = db_array($ayrs);
				$ay_num[$i] = $aylist['uid'];
				$ay_back[$i] = $aylist['p_num'];
				$ay_name[$i] = $aylist['p_name'];
				$ay_sel .= "<option value={$ay_num[$i]}>{$ay_name[$i]}</option>";
			}
		}
		
		//홈팀 정보
		$htrs = db_query( " SELECT * from `savers_secret`.team WHERE tid={$tlist['g_home']} ");
		$htct = db_count( $htrs );
		if($htct)	{
			$htlist = db_array( $htrs );
		}
		
		//어웨이팀 정보
		$atrs = db_query( " SELECT * from `savers_secret`.team WHERE tid={$tlist['g_away']} ");
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
			$re_list = db_array($re_rs1);
			$home_1qs = $home_1qs + $re_list['1qs'];
			$home_2qs = $home_2qs + $re_list['2qs'];
			$home_3qs = $home_3qs + $re_list['3qs'];
			$home_4qs = $home_4qs + $re_list['4qs'];
			$home_1234 = $home_1qs + $home_2qs + $home_3qs + $home_4qs;
			$home_e1s = $home_e1s + $re_list['e1s'];
			$home_e2s = $home_e2s + $re_list['e2s'];
			$home_e3s = $home_e3s + $re_list['e3s'];
			$home_e123 = $home_e1s + $home_e2s + $home_e3s;
			
			$home_min1 = $home_min1 + $re_list['min'];
			$home_min2 = $home_min1 % 60;
			$home_min3 = ($home_min1 - $home_min2) / 60;
			$home_min4 = $home_min3." : ".$home_min2;
			
			$home_3p_m = $home_3p_m + $re_list['3p_m'];
			$home_3p_a = $home_3p_a + $re_list['3p_a'];
			if($home_3p_m > 0 && $home_3p_a >0){
				$home_3p_per = round($home_3p_m / $home_3p_a * 100);
			}
			$home_2p_m = $home_2p_m + $re_list['2p_m'];
			$home_2p_a = $home_2p_a + $re_list['2p_a'];
			if($home_2p_m > 0 && $home_2p_a >0){
				$home_2p_per = round($home_2p_m / $home_2p_a * 100);
			}
			$home_ft_m = $home_ft_m + $re_list['ft_m'];
			$home_ft_a = $home_ft_a + $re_list['ft_a'];
			if($home_ft_m > 0 && $home_ft_a >0){
				$home_ft_per = round($home_ft_m / $home_ft_a * 100);
			}
			$home_re_off = $home_re_off + $re_list['re_off'];
			$home_re_def = $home_re_def + $re_list['re_def'];
			$home_ast = $home_ast + $re_list['ast'];
			$home_stl = $home_stl + $re_list['stl'];
			$home_gd = $home_gd + $re_list['gd'];
			$home_bs = $home_bs + $re_list['bs'];
			$home_w_ft = $home_w_ft + $re_list['w_ft'];
			$home_w_oft = $home_w_oft + $re_list['w_oft'];
			$home_tover = $home_tover + $re_list['tover'];
			$home_ldf = $home_ldf + $re_list['ldf'];
			$home_tf = $home_tf + $re_list['tf'];
			
			$home_rid[$i] = $re_list['rid'];
			$home_gid[$i] = $re_list['gid'];
			
			//선수기록			
			$home_ppid[$i] = $re_list['pid'];			
			for($j=0 ; $j<count($hp_num) ; $j++){
				if($home_ppid[$i] == $hp_num[$j])	{
					$home_pp_name[$i] = $hp_name[$j];
					$home_pp_back[$i] = $hp_back[$j];
				}
			}
			$home_start[$i] = $re_list['start'] ? "*":"";
			$home_p1qs[$i] = $re_list['1qs'];
			$home_p2qs[$i] = $re_list['2qs'];
			$home_p3qs[$i] = $re_list['3qs'];
			$home_p4qs[$i] = $re_list['4qs'];
			$home_pe1s[$i] = $re_list['e1s'];
			$home_pe2s[$i] = $re_list['e2s'];
			$home_pe3s[$i] = $re_list['e3s'];			
			
			$home_pmin[$i] = $re_list['min'];
			$home_pmin1[$i] = $home_pmin[$i] % 60;	
			$home_pmin1[$i] = sprintf("%02d",$home_pmin1[$i]);
			$home_pmin2[$i] = ($home_pmin[$i] - $home_pmin1[$i]) / 60;
			$home_pmin2[$i]	= sprintf("%02d",$home_pmin2[$i]);
			$home_pmin[$i] = $home_pmin2[$i]." : ".$home_pmin1[$i];
			
			$home_p3p_m[$i] = $re_list['3p_m'];
			$home_p3p_a[$i] = $re_list['3p_a'];
			if($home_p3p_m[$i]>0 && $home_p3p_a[$i] >0)	{
				$home_p3p_per[$i] = round($home_p3p_m[$i] / $home_p3p_a[$i] * 100);
			}
			$home_p2p_m[$i] = $re_list['2p_m'];
			$home_p2p_a[$i] = $re_list['2p_a'];
			if($home_p2p_m[$i]>0 && $home_p2p_a[$i]>0)	{
				$home_p2p_per[$i] = round($home_p2p_m[$i] / $home_p2p_a[$i] * 100);
			}
			$home_pft_m[$i] = $re_list['ft_m'];
			$home_pft_a[$i] = $re_list['ft_a'];
			if($home_pft_m[$i]>0 && $home_pft_a[$i]>0)	{
				$home_pft_per[$i] = round($home_pft_m[$i] / $home_pft_a[$i] * 100);
			}
			$home_pre_off[$i] = $re_list['re_off'];
			$home_pre_def[$i] = $re_list['re_def'];
			$home_past[$i] = $re_list['ast'];
			$home_pstl[$i] = $re_list['stl'];
			$home_pgd[$i] = $re_list['gd'];
			$home_pbs[$i] = $re_list['bs'];
			$home_pw_ft[$i] = $re_list['w_ft'];
			$home_pre_def[$i] = $re_list['re_def'];
			$home_pw_oft[$i] = $re_list['w_oft'];
			$home_ptover[$i] = $re_list['tover'];
			$home_pldf[$i] = $re_list['ldf'];
			$home_ptf[$i] = $re_list['tf'];
			
			$home_pcontri1[$i] = ($re_list['1qs'] + $re_list['2qs'] + $re_list['3qs'] + $re_list['4qs'] + $re_list['e1s'] + $re_list['e2s'] + $re_list['e2s'] + $re_list['stl'] + $re_list['bs'] + $re_list['re_def'])
								* 1.0 + ( $re_list['re_off'] + $re_list['ast'] + $re_list['gd'])
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
			$re_list = db_array($re_rs2);
			//팀기록
			$away_1qs = $away_1qs + $re_list['1qs'];
			$away_2qs = $away_2qs + $re_list['2qs'];
			$away_3qs = $away_3qs + $re_list['3qs'];
			$away_4qs = $away_4qs + $re_list['4qs'];
			$away_1234 = $away_1qs + $away_2qs + $away_3qs + $away_4qs;
			$away_e1s = $away_e1s + $re_list['e1s'];
			$away_e2s = $away_e2s + $re_list['e2s'];
			$away_e3s = $away_e3s + $re_list['e3s'];
			$away_e123 = $away_e1s + $away_e2s + $away_e3s;
			
			$away_min1 = $away_min1 + $re_list['min'];
			$away_min2 = $away_min1 % 60;
			$away_min3 = ($away_min1 - $away_min2) / 60;
			$away_min4 = $away_min3." : ".$away_min2;
			
			$away_3p_m = $away_3p_m + $re_list['3p_m'];
			$away_3p_a = $away_3p_a + $re_list['3p_a'];
			if($away_3p_m>0 && $away_3p_a>0)	{
				$away_3p_per = round($away_3p_m / $away_3p_a * 100);
			}
			$away_2p_m = $away_2p_m + $re_list['2p_m'];
			$away_2p_a = $away_2p_a + $re_list['2p_a'];
			if($away_2p_m>0 && $away_2p_a>0)	{
				$away_2p_per = round($away_2p_m / $away_2p_a * 100);
			}
			$away_ft_m = $away_ft_m + $re_list['ft_m'];
			$away_ft_a = $away_ft_a + $re_list['ft_a'];
			if($away_ft_m>0 && $away_ft_a>0)	{
				$away_ft_per = round($away_ft_m / $away_ft_a * 100);
			}
			$away_re_off = $away_re_off + $re_list['re_off'];
			$away_re_def = $away_re_def + $re_list['re_def'];
			$away_ast = $away_ast + $re_list['ast'];
			$away_stl = $away_stl + $re_list['stl'];
			$away_gd = $away_gd + $re_list['gd'];
			$away_bs = $away_bs + $re_list['bs'];
			$away_w_ft = $away_w_ft + $re_list['w_ft'];
			$away_w_oft = $away_w_oft + $re_list['w_oft'];
			$away_tover = $away_tover + $re_list['tover'];
			$away_ldf = $away_ldf + $re_list['ldf'];
			$away_tf = $away_tf + $re_list['tf'];
			
			$away_rid[$i] = $re_list['rid'];
			$away_gid[$i] = $re_list['gid'];
			//선수기록
			$away_ppid[$i] = $re_list['pid'];
			for($j=0 ; $j<count($ay_num) ; $j++){
				if($away_ppid[$i] == $ay_num[$j])	{
					$away_pp_name[$i] = $ay_name[$j];
					$away_pp_back[$i] = $ay_back[$i];
				}
			}
			
			$away_start[$i] = $re_list['start'] ? "*":"";
			$away_p1qs[$i] = $re_list['1qs'];
			$away_p2qs[$i] = $re_list['2qs'];
			$away_p3qs[$i] = $re_list['3qs'];
			$away_p4qs[$i] = $re_list['4qs'];
			$away_pe1s[$i] = $re_list['e1s'];
			$away_pe2s[$i] = $re_list['e2s'];
			$away_pe3s[$i] = $re_list['e3s'];
			
			$away_pmin[$i] = $re_list['min'];
			$away_pmin1[$i] = $away_pmin[$i] % 60;	
			$away_pmin1[$i] = sprintf("%02d",$away_pmin1[$i]);
			$away_pmin2[$i] = ($away_pmin[$i] - $away_pmin1[$i]) / 60;
			$away_pmin2[$i] = sprintf("%02d",$away_pmin2[$i]);
			$away_pmin[$i] = $away_pmin2[$i]." : ".$away_pmin1[$i];
			
			$away_p3p_m[$i] = $re_list['3p_m'];
			$away_p3p_a[$i] = $re_list['3p_a'];
			if($away_p3p_m[$i]>0 && $away_p3p_a[$i]>0)	{
				$away_p3p_per[$i] = round($away_p3p_m[$i] / $away_p3p_a[$i] * 100);
			}
			$away_p2p_m[$i] = $re_list['2p_m'];
			$away_p2p_a[$i] = $re_list['2p_a'];
			if($away_p2p_m[$i]>0 && $away_p2p_a[$i]>0)	{
				$away_p2p_per[$i] = round($away_p2p_m[$i] / $away_p2p_a[$i] * 100);
			}
			$away_pft_m[$i] = $re_list['ft_m'];
			$away_pft_a[$i] = $re_list['ft_a'];
			if($away_pft_m[$i]>0 && $away_pft_a[$i]>0)	{
				$away_pft_per[$i] = round($away_pft_m[$i] / $away_pft_a[$i] * 100);
			}
			$away_p3p_m[$i] = $re_list['3p_m'];
			$away_pre_off[$i] = $re_list['re_off'];
			$away_pre_def[$i] = $re_list['re_def'];
			$away_past[$i] = $re_list['ast'];
			$away_pstl[$i] = $re_list['stl'];
			$away_pgd[$i] = $re_list['gd'];
			$away_pbs[$i] = $re_list['bs'];
			$away_pw_ft[$i] = $re_list['w_ft'];
			$away_pre_def[$i] = $re_list['re_def'];
			$away_pw_oft[$i] = $re_list['w_oft'];
			$away_ptover[$i] = $re_list['tover'];
			$away_pldf[$i] = $re_list['ldf'];
			$away_ptf[$i] = $re_list['tf'];
			
			$away_pcontri1[$i] = ($re_list['1qs'] + $re_list['2qs'] + $re_list['3qs'] + $re_list['4qs'] + $re_list['e1s'] + $re_list['e2s'] + $re_list['e2s'] + $re_list['stl'] + $re_list['bs'] + $re_list['re_def'])
								* 1.0 + ( $re_list['re_off'] + $re_list['ast'] + $re_list['gd'])
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
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>경기 결과 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<table width="97%" border="0" align="center" cellpadding="6" cellspacing="1" bgcolor="#666666" class="">
			<tr align="center" bgcolor="#E0F0FE" height="24">
			<td height="35" colspan="2" bgcolor="#D2BF7E"><strong> [ <?php echo $s_date ; ?> ]&nbsp;<?php echo $htlist['t_name']." (".$htlist['tid'].")" ; ?>
				: <?php echo $atlist['t_name']." (".$atlist['tid'].")" ; ?>
				경기 결과</strong></td>
			</tr>
			<tr class="base">
			<td height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>시즌</strong></td>
			<td align="left" bgcolor="#F8F8EA" class="base">&nbsp;<span class="style1"><?php echo $slist['s_name'] ; ?>
			</span></td>
			</tr>
			<tr class="base">
			<td height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>경기구분</strong></td>
			<td align="left" bgcolor="#F8F8EA" class="base">&nbsp;<span class="style1"><?php echo $tlist['g_division'] ; ?>
			</span></td>
			</tr>
			<tr class="base">
			<td height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>경기번호</strong></td>
			<td align="left" bgcolor="#F8F8EA" class="base">&nbsp;<span class="style1"><?php echo $tlist['gameno'] ; ?>
			</span></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>경기일</strong></td>
			<td align="left" bgcolor="#F8F8EA" class="base">&nbsp;<span class="style1"><?php echo $start ; ?> &nbsp; ~ <?php echo $end ; ?>
			</span></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>경기장소</strong></td>
			<td width="78%" align="left" bgcolor="#F8F8EA" class="base">&nbsp;<span class="style1"><?php echo $tlist['g_ground'] ; ?>
			</span></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>홈 팀</strong></td>
			<td width="78%" align="left" bgcolor="#F8F8EA" class="base">&nbsp;<?php echo $htlist['t_name'] ; ?></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong>어웨이 팀 </strong></td>
			<td width="78%" align="left" bgcolor="#F8F8EA" class="base">&nbsp;<?php echo $atlist['t_name'] ; ?></td>
			</tr>
			<tr bgcolor="#F0E9CF" class="base">
			<td height="30" align="center" bgcolor="#F0E9CF" class="base">&nbsp;</td>
			<td align="left" bgcolor="#F0E9CF" class="base"><table width="100%"	border="0" cellpadding="0" cellspacing="1" bgcolor="#dddddd">
				<tr align="center">
					<td width="17%" height="20" bgcolor="#F0E9CF"><strong>1쿼터</strong></td>
					<td width="17%" height="20" bgcolor="#F0E9CF"><strong>2쿼터</strong></td>
					<td width="17%" height="20" bgcolor="#F0E9CF"><strong>3쿼터</strong></td>
					<td width="17%" height="20" bgcolor="#F0E9CF"><strong>4쿼터</strong></td>
					<td width="17%" height="20" bgcolor="#F0E9CF"><strong>연장전</strong></td>
					<td height="20" bgcolor="#F0E9CF"><strong>계</strong></td>
				</tr>
			</table></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong><?php echo $htlist['t_name'] ; ?>
			</strong></td>
			<td width="78%" align="left" bgcolor="#F8F8EA" class="base"><table width="100%"	border="0" cellpadding="0" cellspacing="1" bgcolor="dddddd">
				<tr align="center">
					<td width="17%" height="20" bgcolor="#F8F8EA">&nbsp;<?php echo $home_1qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $home_2qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $home_3qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $home_4qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $home_e123 ; ?></td>
					<td bgcolor="#F8F8EA">&nbsp;<?php echo $home_1234 + $home_e123 ; ?></td>
				</tr>
			</table></td>
			</tr>
			<tr class="base">
			<td width="22%" height="30" align="center" bgcolor="#D2BF7E" class="base"><strong><?php echo $atlist['t_name'] ; ?>
			</strong></td>
			<td width="78%" align="left" bgcolor="#F8F8EA" class="base"><table width="100%"	border="0" cellpadding="0" cellspacing="1" bgcolor="dddddd">
				<tr align="center">
					<td width="17%" height="20" bgcolor="#F8F8EA">&nbsp;<?php echo $away_1qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $away_2qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $away_3qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $away_4qs ; ?></td>
					<td width="17%" bgcolor="#F8F8EA">&nbsp;<?php echo $away_e123 ; ?></td>
					<td bgcolor="#F8F8EA">&nbsp;<?php echo $away_1234 + $away_e123 ; ?></td>
				</tr>
			</table></td>
			</tr>
		</table>
		<br />
		<table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
			<td align="center"><input name="back" type="button" class="CCbox04" onclick="javascript:history.back();" value=" 뒤 로 " />
				&nbsp;&nbsp;</td>
			</tr>
		</table></td>
	</tr>
</table>

<br>

<br>
<?php echo $SITE['tail']; ?>
