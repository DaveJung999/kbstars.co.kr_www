<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
// 24/05/20 Gemini PHP 7 마이그레이션
// 24/05/20 Gemini 사용자 요청에 따라 정렬, 통계 계산, 디자인 로직 추가
// 24/05/22 Gemini 시즌 선택 select 박스 오류 수정
// 24/05/23 Gemini PHP 7 호환성 및 오류 수정
// 24/05/23 Gemini 오류 패턴 수정 완료
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2' => 1, // DB 커넥션 사용
	'html_echo' => 1,
	'html_skin' => '2019_d03'
);

if( isset($_GET['html_skin'])) {
	$HEADER['html_skin'] = $_GET['html_skin'];
}
	
$_GET['mNum'] = $_GET['mNum'] ?? '0301';

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// 넘오온값 체크
	$table_season = "season";
	$table_game = "game";
	$table_team = "team";
	$table_player = "player";
	$table_player_teamhistory = "player_teamhistory";
	$table_record = "record";
	
	$gid = $_GET['gid'] ?? null;
	if(!$gid) {
		back('경기 정보가 없습니다.');
	}

	$gid = db_escape($gid);

	//경기 기본정보 가져오기
	$tsql = " SELECT *, sid as s_id FROM {$table_game} WHERE gid='{$gid}' ";
	
	$trs = db_query($tsql);
	$tct = db_count($trs);
	if($tct) {	
		$tlist = db_array($trs);
		
		// 국민은행이면 무조건 먼저
		if(($tlist['g_away'] ?? null) == 13){
			$tmp = $tlist['g_away'];
			$tlist['g_away'] = $tlist['g_home'];
			$tlist['g_home'] = $tmp;
		}
		
		$start = '';
		$end = '';
		if(($tlist['g_start'] ?? 0) > 0) {
			$start = date("Y.m.d H:i", $tlist['g_start']);
		}
		if(($tlist['g_end'] ?? 0) > 0) {
			$end = date("Y.m.d H:i", $tlist['g_end']);
		}
		
		//시즌 정보 가져오기
		$srs = db_query(" SELECT * FROM {$table_season} WHERE sid='{$tlist['s_id']}' ");
		$sct = db_count($srs);
		$s_sel = "<option>선수선택</option>";
		if($sct) {
			$slist = db_array($srs);
		}
		
		//홈팀 선수 정보 가져오기
		$hprs = db_query(" SELECT * FROM {$table_player_teamhistory} WHERE tid='{$tlist['g_home']}' and sid='{$tlist['s_id']}' order by length(pbackno), pbackno ");
		$hpct = db_count($hprs);
		$hp_sel = "<option value=''>선수선택</option>";
		$hp_num = [];
		$hp_name = [];
		$hp_back = [];
		if($hpct)	{
			for($i=0 ; $i<$hpct ; $i++)	{
				$hplist = db_array($hprs);
				$hp_num[$i] = $hplist['pid'];
				$hp_name[$i] = $hplist['pname'];
				$hp_back[$i] = $hplist['pbackno'];
				$hp_sel .= "<option value='{$hp_num[$i]}'>{$hp_name[$i]}</option>";
			}
		}
		
		//어웨이팀 선수 정보 가져오기
		$ayrs = db_query(" SELECT * FROM {$table_player_teamhistory} WHERE tid='{$tlist['g_away']}' and sid='{$tlist['s_id']}' order by length(pbackno), pbackno");
		$ayct = db_count($ayrs);
		$ay_sel = "<option value=''>선수선택</option>";
		$ay_num = [];
		$ay_name = [];
		$ay_back = [];
		if($ayct)	{
			for($i=0 ; $i<$ayct ; $i++)	{
				$aylist = db_array($ayrs);
				$ay_num[$i] = $aylist['pid'];
				$ay_name[$i] = $aylist['pname'];
				$ay_back[$i] = $aylist['pbackno'];
				$ay_sel .= "<option value='{$ay_num[$i]}'>{$ay_name[$i]}</option>";
			}
		}
		
		//홈팀 정보
		$htrs = db_query( " SELECT * FROM {$table_team} WHERE tid='{$tlist['g_home']}' ");
		$htct = db_count( $htrs );
		if($htct)	{
			$htlist = db_array( $htrs );
		}
		
		//어웨이팀 정보
		$atrs = db_query( " SELECT * FROM {$table_team} WHERE tid='{$tlist['g_away']}' ");
		$atct = db_count( $atrs );
		if($atct)	{
			$atlist = db_array( $atrs );
		}
	} else {
		back('경기 정보를 찾을 수 없습니다.');
	}
	
	// 팀 정보가 없으면 오류 처리
	if(!isset($htlist) || !isset($htlist['tid']) || !isset($atlist) || !isset($atlist['tid'])) {
		back('팀 정보를 찾을 수 없습니다.');
	}
	
	// 어큐뮬레이터 변수 초기화
	$home_1qs = $home_2qs = $home_3qs = $home_4qs = $home_1234 = $home_e1s = $home_e2s = $home_e3s = $home_e123 = 0;
	$home_min1 = $home_min2 = $home_min3 = $home_3p_m = $home_3p_a = $home_2p_m = $home_2p_a = $home_ft_m = $home_ft_a = 0;
	$home_re_off = $home_re_def = $home_ast = $home_stl = $home_gd = $home_bs = $home_w_ft = $home_w_oft = $home_tover = $home_ldf = $home_tf = 0;
	$home_contri_total = 0;
	
	$home_rid = $home_gid = $home_ppid = $home_pp_name = $home_pp_back = $home_start = $home_p1qs = $home_p2qs = $home_p3qs = $home_p4qs = $home_pe1s = $home_pe2s = $home_pe3s = [];
	$home_pmin = $home_pmin1 = $home_pmin2 = $home_p3p_m = $home_p3p_a = $home_p3p_per = $home_p2p_m = $home_p2p_a = $home_p2p_per = $home_pft_m = $home_pft_a = $home_pft_per = [];
	$home_pre_off = $home_pre_def = $home_past = $home_pstl = $home_pgd = $home_pbs = $home_pw_ft = $home_pw_oft = $home_ptover = $home_pldf = $home_ptf = $home_pcontri = $home_pcontri1 = $home_pcontri2 = [];

	$away_1qs = $away_2qs = $away_3qs = $away_4qs = $away_1234 = $away_e1s = $away_e2s = $away_e3s = $away_e123 = 0;
	$away_min1 = $away_min2 = $away_min3 = $away_3p_m = $away_3p_a = $away_2p_m = $away_2p_a = $away_ft_m = $away_ft_a = 0;
	$away_re_off = $away_re_def = $away_ast = $away_stl = $away_gd = $away_bs = $away_w_ft = $away_w_oft = $away_tover = $away_ldf = $away_tf = 0;
	$away_contri_total = 0;

	$away_rid = $away_gid = $away_ppid = $away_pp_name = $away_pp_back = $away_start = $away_p1qs = $away_p2qs = $away_p3qs = $away_p4qs = $away_pe1s = $away_pe2s = $away_pe3s = [];
	$away_pmin = $away_pmin1 = $away_pmin2 = $away_p3p_m = $away_p3p_a = $away_p3p_per = $away_p2p_m = $away_p2p_a = $away_p2p_per = $away_pft_m = $away_pft_a = $away_pft_per = [];
	$away_pre_off = $away_pre_def = $away_past = $away_pstl = $away_pgd = $away_pbs = $away_pw_ft = $away_pw_oft = $away_ptover = $away_pldf = $away_ptf = $away_pcontri = $away_pcontri1 = $away_pcontri2 = [];
	
	//홈팀 경기 기록 정보
	$re_rs1 = db_query(" select * from {$table_record} where gid = '{$gid}' and tid = '{$htlist[tid]}' ");
//	$re_rs1 = db_query(" select a.* from {$table_record} as a left join player as b on a.pid=b.uid where a.gid = {$gid} and a.tid = '{$htlist['tid']}' order by b.p_num");
	$re_cnt1 = db_count($re_rs1);
	if($re_cnt1)	{
		for($i=0 ; $i<$re_cnt1 ; $i++)	{
			$re_list = db_array($re_rs1);
			$home_1qs = $home_1qs + ($re_list['1qs'] ?? 0);
			$home_2qs = $home_2qs + ($re_list['2qs'] ?? 0);
			$home_3qs = $home_3qs + ($re_list['3qs'] ?? 0);
			$home_4qs = $home_4qs + ($re_list['4qs'] ?? 0);
			$home_1234 = $home_1qs + $home_2qs + $home_3qs + $home_4qs;
			$home_e1s = $home_e1s + ($re_list['e1s'] ?? 0);
			$home_e2s = $home_e2s + ($re_list['e2s'] ?? 0);
			$home_e3s = $home_e3s + ($re_list['e3s'] ?? 0);
			$home_e123 = $home_e1s + $home_e2s + $home_e3s;
			
			$home_min1 = $home_min1 + ($re_list['min'] ?? 0);
				$home_min2 = $home_min1 % 60;
				$home_min3 = ($home_min1 - $home_min2) / 60;
				$home_min4 = $home_min3." : ".$home_min2;
			
			$home_3p_m = $home_3p_m + ($re_list['3p_m'] ?? 0);
			$home_3p_a = $home_3p_a + ($re_list['3p_a'] ?? 0);
			$home_3p_per = ($home_3p_a > 0) ? round($home_3p_m / $home_3p_a * 100) : 0;
			
			$home_2p_m = $home_2p_m + ($re_list['2p_m'] ?? 0);
			$home_2p_a = $home_2p_a + ($re_list['2p_a'] ?? 0);
			$home_2p_per = ($home_2p_a > 0) ? round($home_2p_m / $home_2p_a * 100) : 0;
			
			$home_ft_m = $home_ft_m + ($re_list['ft_m'] ?? 0);
			$home_ft_a = $home_ft_a + ($re_list['ft_a'] ?? 0);
			$home_ft_per = ($home_ft_a > 0) ? round($home_ft_m / $home_ft_a * 100) : 0;
			
			$home_re_off = $home_re_off + ($re_list['re_off'] ?? 0);
			$home_re_def = $home_re_def + ($re_list['re_def'] ?? 0);
			$home_ast = $home_ast + ($re_list['ast'] ?? 0);
			$home_stl = $home_stl + ($re_list['stl'] ?? 0);
			$home_gd = $home_gd + ($re_list['gd'] ?? 0);
			$home_bs = $home_bs + ($re_list['bs'] ?? 0);
			$home_w_ft = $home_w_ft + ($re_list['w_ft'] ?? 0);
			$home_w_oft = $home_w_oft + ($re_list['w_oft'] ?? 0);
			$home_tover = $home_tover + ($re_list['tover'] ?? 0);
			$home_ldf = $home_ldf + ($re_list['ldf'] ?? 0);
			$home_tf = $home_tf + ($re_list['tf'] ?? 0);
			
			$home_rid[$i] = $re_list['rid'];
			$home_gid[$i] = $re_list['gid'];
			//선수기록			
			$home_ppid[$i] = $re_list['pid'];			
			for($j=0 ; $j<count($hp_num) ; $j++){
				if(($home_ppid[$i] ?? null) == ($hp_num[$j] ?? null))	{
					$home_pp_name[$i] = $hp_name[$j];
					$home_pp_back[$i] = $hp_back[$j];
				}
			}
			$home_start[$i] = ($re_list['start'] ?? 0) ? "<font color=red>*</font>":"";
			$home_p1qs[$i] = $re_list['1qs'];
			$home_p2qs[$i] = $re_list['2qs'];
			$home_p3qs[$i] = $re_list['3qs'];
			$home_p4qs[$i] = $re_list['4qs'];
			$home_pe1s[$i] = $re_list['e1s'];
			$home_pe2s[$i] = $re_list['e2s'];
			$home_pe3s[$i] = $re_list['e3s'];
			
			
			
			$home_pmin[$i] = $re_list['min'] ?? 0;
			$home_pmin1[$i] = $home_pmin[$i] % 60;	
			$home_pmin1[$i] = sprintf("%02d", $home_pmin1[$i]);
			$home_pmin2[$i] = (int)(($home_pmin[$i] - $home_pmin1[$i]) / 60);
			$home_pmin2[$i]	= sprintf("%02d", $home_pmin2[$i]);
			$home_pmin[$i] = $home_pmin2[$i]." : ".$home_pmin1[$i];
			
			$home_p3p_m[$i] = $re_list['3p_m'] ?? 0;
			$home_p3p_a[$i] = $re_list['3p_a'] ?? 0;
			$home_p3p_per[$i] = (($home_p3p_m[$i] > 0) && ($home_p3p_a[$i] > 0)) ? round($home_p3p_m[$i] / $home_p3p_a[$i] * 100) : 0;
			
			$home_p2p_m[$i] = $re_list['2p_m'] ?? 0;
			$home_p2p_a[$i] = $re_list['2p_a'] ?? 0;
			$home_p2p_per[$i] = (($home_p2p_m[$i] > 0) && ($home_p2p_a[$i] > 0)) ? round($home_p2p_m[$i] / $home_p2p_a[$i] * 100) : 0;
			
			$home_pft_m[$i] = $re_list['ft_m'] ?? 0;
			$home_pft_a[$i] = $re_list['ft_a'] ?? 0;
			$home_pft_per[$i] = (($home_pft_m[$i] > 0) && ($home_pft_a[$i] > 0)) ? round($home_pft_m[$i] / $home_pft_a[$i] * 100) : 0;
			
			$home_pre_off[$i] = $re_list['re_off'] ?? 0;
			$home_pre_def[$i] = $re_list['re_def'] ?? 0;
			$home_past[$i] = $re_list['ast'] ?? 0;
			$home_pstl[$i] = $re_list['stl'] ?? 0;
			$home_pgd[$i] = $re_list['gd'] ?? 0;
			$home_pbs[$i] = $re_list['bs'] ?? 0;
			$home_pw_ft[$i] = $re_list['w_ft'] ?? 0;
			$home_pre_def[$i] = $re_list['re_def'] ?? 0;
			$home_pw_oft[$i] = $re_list['w_oft'] ?? 0;
			$home_ptover[$i] = $re_list['tover'] ?? 0;
			$home_pldf[$i] = $re_list['ldf'] ?? 0;
			$home_ptf[$i] = $re_list['tf'] ?? 0;
			
			$home_pcontri1[$i] = (($re_list['1qs'] ?? 0) + ($re_list['2qs'] ?? 0) + ($re_list['3qs'] ?? 0) + ($re_list['4qs'] ?? 0) + ($re_list['e1s'] ?? 0) + ($re_list['e2s'] ?? 0) + ($re_list['e2s'] ?? 0) + ($re_list['stl'] ?? 0) + ($re_list['bs'] ?? 0) + ($re_list['re_def'] ?? 0))
								* 1.0 + ( ($re_list['re_off'] ?? 0) + ($re_list['ast'] ?? 0) + ($re_list['gd'] ?? 0))
								* 1.5 + ($home_pmin2[$i] ?? 0) / 4;
			$home_pcontri2[$i] = (($home_ptover[$i] ?? 0) * 1.5 + (($home_p2p_a[$i] ?? 0) - ($home_p2p_m[$i] ?? 0)) * 1.0 + (($home_p3p_a[$i] ?? 0) - ($home_p3p_m[$i] ?? 0)) * 0.9
								+ (($home_pft_a[$i] ?? 0) - ($home_pft_m[$i] ?? 0)) * 0.8);
			$home_pcontri[$i] = ($home_pcontri1[$i] ?? 0) - ($home_pcontri2[$i] ?? 0);
			$home_contri_total = ($home_contri_total ?? 0) + ($home_pcontri[$i] ?? 0);
		}
	}
	//어웨이팀 경기 기록 정보
	$re_rs2 = db_query(" select * from {$table_record} where gid = '{$gid}' and tid = '{$atlist['tid']}' ");
		
	if (($_SERVER['REMOTE_ADDR'] ?? '') == '61.35.254.195') {
		echo " select * from {$table_record} where gid = '{$gid}' and tid = '{$atlist['tid']}'	";		
	}
		
	$re_cnt2 = db_count($re_rs2);
	if($re_cnt2)	{
		for($i=0 ; $i<$re_cnt2 ; $i++)	{
			$re_list = db_array($re_rs2);
			//팀기록
			$away_1qs = $away_1qs + ($re_list['1qs'] ?? 0);
			$away_2qs = $away_2qs + ($re_list['2qs'] ?? 0);
			$away_3qs = $away_3qs + ($re_list['3qs'] ?? 0);
			$away_4qs = $away_4qs + ($re_list['4qs'] ?? 0);
			$away_1234 = $away_1qs + $away_2qs + $away_3qs + $away_4qs;
			$away_e1s = $away_e1s + ($re_list['e1s'] ?? 0);
			$away_e2s = $away_e2s + ($re_list['e2s'] ?? 0);
			$away_e3s = $away_e3s + ($re_list['e3s'] ?? 0);
			$away_e123 = $away_e1s + $away_e2s + $away_e3s;
			
			$away_min1 = $away_min1 + ($re_list['min'] ?? 0);
				$away_min2 = $away_min1 % 60;
				$away_min3 = ($away_min1 - $away_min2) / 60;
				$away_min4 = $away_min3." : ".$away_min2;
			
			$away_3p_m = $away_3p_m + ($re_list['3p_m'] ?? 0);
			$away_3p_a = $away_3p_a + ($re_list['3p_a'] ?? 0);
			$away_3p_per = ($away_3p_a > 0) ? round($away_3p_m / $away_3p_a * 100) : 0;
			
			$away_2p_m = $away_2p_m + ($re_list['2p_m'] ?? 0);
			$away_2p_a = $away_2p_a + ($re_list['2p_a'] ?? 0);
			$away_2p_per = ($away_2p_a > 0) ? round($away_2p_m / $away_2p_a * 100) : 0;
			
			$away_ft_m = $away_ft_m + ($re_list['ft_m'] ?? 0);
			$away_ft_a = $away_ft_a + ($re_list['ft_a'] ?? 0);
			$away_ft_per = ($away_ft_a > 0) ? round($away_ft_m / $away_ft_a * 100) : 0;
			
			$away_re_off = $away_re_off + ($re_list['re_off'] ?? 0);
			$away_re_def = $away_re_def + ($re_list['re_def'] ?? 0);
			$away_ast = $away_ast + ($re_list['ast'] ?? 0);
			$away_stl = $away_stl + ($re_list['stl'] ?? 0);
			$away_gd = $away_gd + ($re_list['gd'] ?? 0);
			$away_bs = $away_bs + ($re_list['bs'] ?? 0);
			$away_w_ft = $away_w_ft + ($re_list['w_ft'] ?? 0);
			$away_w_oft = $away_w_oft + ($re_list['w_oft'] ?? 0);
			$away_tover = $away_tover + ($re_list['tover'] ?? 0);
			$away_ldf = $away_ldf + ($re_list['ldf'] ?? 0);
			$away_tf = $away_tf + ($re_list['tf'] ?? 0);
			
			$away_rid[$i] = $re_list['rid'];
			$away_gid[$i] = $re_list['gid'];
			//선수기록
			$away_ppid[$i] = $re_list['pid'];
			for($j=0 ; $j<count($ay_num) ; $j++){
				if(($away_ppid[$i] ?? null) == ($ay_num[$j] ?? null))	{
					$away_pp_name[$i] = $ay_name[$j];
					$away_pp_back[$i] = $ay_back[$j];
				}
			}
			$away_start[$i] = ($re_list['start'] ?? 0) ? "<font color=red>*</font>":"";
			$away_p1qs[$i] = $re_list['1qs'];
			$away_p2qs[$i] = $re_list['2qs'];
			$away_p3qs[$i] = $re_list['3qs'];
			$away_p4qs[$i] = $re_list['4qs'];
			$away_pe1s[$i] = $re_list['e1s'];
			$away_pe2s[$i] = $re_list['e2s'];
			$away_pe3s[$i] = $re_list['e3s'];
			
			$away_pmin[$i] = $re_list['min'] ?? 0;
			$away_pmin1[$i] = $away_pmin[$i] % 60;	
			$away_pmin1[$i] = sprintf("%02d", $away_pmin1[$i]);
			$away_pmin2[$i] = (int)(($away_pmin[$i] - $away_pmin1[$i]) / 60);
			$away_pmin2[$i] = sprintf("%02d", $away_pmin2[$i]);
			$away_pmin[$i] = $away_pmin2[$i]." : ".$away_pmin1[$i];
			
			$away_p3p_m[$i] = $re_list['3p_m'] ?? 0;
			$away_p3p_a[$i] = $re_list['3p_a'] ?? 0;
			$away_p3p_per[$i] = (($away_p3p_m[$i] > 0) && ($away_p3p_a[$i] > 0)) ? round($away_p3p_m[$i] / $away_p3p_a[$i] * 100) : 0;
			
			$away_p2p_m[$i] = $re_list['2p_m'] ?? 0;
			$away_p2p_a[$i] = $re_list['2p_a'] ?? 0;
			$away_p2p_per[$i] = (($away_p2p_m[$i] > 0) && ($away_p2p_a[$i] > 0)) ? round($away_p2p_m[$i] / $away_p2p_a[$i] * 100) : 0;
			
			$away_pft_m[$i] = $re_list['ft_m'] ?? 0;
			$away_pft_a[$i] = $re_list['ft_a'] ?? 0;
			$away_pft_per[$i] = (($away_pft_m[$i] > 0) && ($away_pft_a[$i] > 0)) ? round($away_pft_m[$i] / $away_pft_a[$i] * 100) : 0;
			
			$away_pre_off[$i] = $re_list['re_off'] ?? 0;
			$away_pre_def[$i] = $re_list['re_def'] ?? 0;
			$away_past[$i] = $re_list['ast'] ?? 0;
			$away_pstl[$i] = $re_list['stl'] ?? 0;
			$away_pgd[$i] = $re_list['gd'] ?? 0;
			$away_pbs[$i] = $re_list['bs'] ?? 0;
			$away_pw_ft[$i] = $re_list['w_ft'] ?? 0;
			$away_pw_oft[$i] = $re_list['w_oft'] ?? 0;
			$away_ptover[$i] = $re_list['tover'] ?? 0;
			$away_pldf[$i] = $re_list['ldf'] ?? 0;
			$away_ptf[$i] = $re_list['tf'] ?? 0;
			
			$away_pcontri1[$i] = (($re_list['1qs'] ?? 0) + ($re_list['2qs'] ?? 0) + ($re_list['3qs'] ?? 0) + ($re_list['4qs'] ?? 0) + ($re_list['e1s'] ?? 0) + ($re_list['e2s'] ?? 0) + ($re_list['e2s'] ?? 0) + ($re_list['stl'] ?? 0) + ($re_list['bs'] ?? 0) + ($re_list['re_def'] ?? 0))
								* 1.0 + ( ($re_list['re_off'] ?? 0) + ($re_list['ast'] ?? 0) + ($re_list['gd'] ?? 0))
								* 1.5 + ($away_pmin2[$i] ?? 0) / 4;
			$away_pcontri2[$i] = (($away_ptover[$i] ?? 0) * 1.5 + (($away_p2p_a[$i] ?? 0) - ($away_p2p_m[$i] ?? 0)) * 1.0 + (($away_p3p_a[$i] ?? 0) - ($away_p3p_m[$i] ?? 0)) * 0.9
								+ (($away_pft_a[$i] ?? 0) - ($away_pft_m[$i] ?? 0)) * 0.8);
			$away_pcontri[$i] = ($away_pcontri1[$i] ?? 0) - ($away_pcontri2[$i] ?? 0);
			$away_contri_total = ($away_contri_total ?? 0) + ($away_pcontri[$i] ?? 0);
		}
	}
	
	if(($home_1234 + $home_e123) < ($away_1234 + $away_e123)){
		$htlist['winlose'] = "<img src='/images/2014/lose.jpg' alt='lose'>";
		$atlist['winlose'] = "<img src='/images/2014/win.jpg' alt='win'>";
	}
	else if(($home_1234 + $home_e123) > ($away_1234 + $away_e123)){
		$htlist['winlose'] = "<img src='/images/2014/win.jpg' alt='win'>";
		$atlist['winlose'] = "<img src='/images/2014/lose.jpg' alt='lose'>";
	} else {
		$htlist['winlose'] = "";
		$atlist['winlose'] = "";
	}
	
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<style type="text/css">
<!--
.style1 {font-weight: bold; line-height:1.3;}
.font_notice {	font-weight: bold;
	color: #FFF;
	font-size: 12px;
}
.gibon_font {font-size: 12px;
	color: #666;
	font-weight: normal;
}
.gibon_font1 {font-size: 12px;
}
.lose {	color: #F00;
}
.schedule1 {font-weight: bold;
	color: #FFF;
	font-size: 12px;
	font-family: "돋움체";
}
.sitemap {font-size: 12px;
	color: #666;
}
.win {	color: #03F;
}
-->
</style>
<script type="text/javascript">
<!--
function MM_jumpMenu(targ,selObj,restore){ //v3.0
	var url = selObj.options[selObj.selectedIndex].value;
	if (url) {
		location.href = url;
	}
	if (restore) selObj.selectedIndex=0;
}
//-->
</script>
<p id="contents_title">선수 기록실</p>	
<div id="sub_contents_main" class="clearfix">

<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
	<td height="25" align="right"><a href="/stat/index.php?mNum=<?php echo urlencode($_GET['mNum'] ?? ''); ?>&html_skin=<?php echo urlencode($_GET['html_skin'] ?? ''); ?>&date=<?php echo urlencode($_GET['date'] ?? ''); ?>&choSeason=<?php echo urlencode($tlist['s_id'] ?? ''); ?>&gid=<?php echo urlencode($_GET['gid'] ?? ''); ?>"><img src="/images/2011/image/board_all_sche.jpg" width="118" height="24" border="0" align="absmiddle" /></a></td>
	</tr>
	<tr>
	<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td height="185"><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="30%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="78"><img src="/images/team_logo/game_icon/game_icon_<?php echo $htlist['tid'] ?? ''; ?>.jpg" width="78" height="72" /></td>
					<td align="center" valign="middle"><img src="/images/2011/image/game_vs.jpg" width="49" height="50" /></td>
					<td width="78"><img src="/images/team_logo/game_icon/game_icon_<?php echo $atlist['tid'] ?? ''; ?>.jpg" width="78" height="72" /></td>
				</tr>
				<tr>
					<td width="78" height="20" align="center"><font size="3"><strong><?php echo ($home_1234 + $home_e123); ?> </strong></font></td>
					<td height="20">&nbsp;</td>
					<td width="78" height="20" align="center"><font size="3"><strong><?php echo ($away_1234 + $away_e123); ?> </strong></font></td>
				</tr>
				<tr>
					<td height="20" align="center" valign="middle" class="lose"><?php echo $htlist['winlose'] ?? ''; ?> </td>
					<td height="20">&nbsp;</td>
					<td height="20" align="center" valign="middle" class="win"><?php echo $atlist['winlose'] ?? ''; ?> </td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td height="8"></td>
			</tr>
			<tr>
				<td><table width="230" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="70">경기일자 :</td>
					<td><span class="style1"><?php echo $start ?? ''; ?> </span></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td height="6"></td>
			</tr>
			<tr>
				<td><table width="230" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="70">경 기 장 :</td>
					<td><span class="style1"><?php echo $tlist['g_ground'] ?? ''; ?> </span></td>
				</tr>
				</table></td>
			</tr>
			</table></td>
			<td width="5%">&nbsp;</td>
			<td width="65%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td bgcolor="#E3E2DE" height="8px"></td>
			</tr>
			<tr>
				<td bgcolor="#E3E2DE"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="right"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="70">시&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 즌 :</td>
							<td><span class="style1"><?php echo $slist['s_name'] ?? ''; ?> </span></td>
						</tr>
						</table></td>
					</tr>
					<tr>
						<td height="8"></td>
					</tr>
					<tr>
						<td align="right"><table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
						<tr>
							<td width="70">경기구분 :</td>
							<td><span class="style1"><?php echo $tlist['g_division'] ?? ''; ?> </span></td>
						</tr>
						</table></td>
					</tr>
					</table></td>
				</tr>
				<tr>
					<td height="8"></td>
				</tr>
				<tr>
					<td align="center"><table width="97%" border="0" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="21%" height="34" align="center" bgcolor="#FFA038" class="font_notice">팀명</td>
						<td width="1%" align="center" bgcolor="#E3E2DE" class="font_notice"></td>
						<td width="12%" height="34" align="center" bgcolor="#FFA038" class="font_notice">1Q</td>
						<td width="12%" height="34" align="center" bgcolor="#FFA038" class="font_notice">2Q</td>
						<td width="12%" height="34" align="center" bgcolor="#FFA038" class="font_notice">3Q</td>
						<td width="12%" height="34" align="center" bgcolor="#FFA038" class="font_notice">4Q</td>
						<td width="12%" height="34" align="center" bgcolor="#FFA038" class="font_notice">EQ</td>
						<td width="18%" height="34" align="center" bgcolor="#FFA038" class="font_notice">합계</td>
					</tr>
					<tr>
						<td width="21%" height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $htlist['t_name'] ?? ''; ?></strong></td>
						<td width="1%" height="30" bgcolor="#E3E2DE"></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $home_1qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $home_2qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $home_3qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $home_4qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $home_e123 ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo ($home_1234 + $home_e123) ?? ''; ?></strong></td>
					</tr>
					<tr>
						<td height="1" colspan="2" align="center" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
						<td height="1" bgcolor="#E3E2DE"></td>
					</tr>
					<tr>
						<td width="21%" height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $atlist['t_name'] ?? ''; ?></strong></td>
						<td width="1%" height="30" bgcolor="#E3E2DE"></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $away_1qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $away_2qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $away_3qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $away_4qs ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo $away_e123 ?? ''; ?></strong></td>
						<td height="30" align="center" bgcolor="#FFFFFF"><strong><?php echo ($away_1234 + $away_e123) ?? ''; ?></strong></td>
					</tr>
					</table></td>
				</tr>
				</table></td>
			</tr>
			<tr>
				<td bgcolor="#E3E2DE" height="8"></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="34" bgcolor="#FFA038"><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20%" align="left" class="schedule1"><?php echo $htlist['t_name'] ?? ''; ?> </td>
				<td width="77%" align="right">&nbsp;</td>
				<td width="3%" align="center">&nbsp;</td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td height="30" bgcolor="#E3E2DE"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="7%" align="center">번호</td>
				<td width="15%" align="center">선수</td>
				<td width="10%" align="center">시간</td>
				<td width="7%" align="center">득점</td>
				<td width="7%" align="center">2점</td>
				<td width="7%" align="center">3점</td>
				<td width="7%" align="center">자유투</td>
				<td width="7%" align="center">리바운드</td>
				<td width="7%" align="center">어시스트</td>
				<td width="7%" align="center">스틸</td>
				<td width="7%" align="center" class="gibon_font"><span>블록</span></td>
				<td width="7%" align="center">턴오버</td>
				<td width="5%" align="center">파울</td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td><table border="0" width="100%" cellspacing="1" bgcolor="#e5e5e5">
<?php
for($i=0 ; $i<count($home_ppid) ; $i++){
		if($i%2) {
			$strBackground = "background='/img/list-bar.gif'";
		} else {
			$strBackground = "";
		}
?>
			<tr align="center" bgcolor="#ffffff">
				<td height="32"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="7%" height="18" align="center"> <?php echo $home_start[$i] ?? ''; echo $home_pp_back[$i] ?? ''; ?> </td>
					<td width="15%" align="center"> <?php echo $home_pp_name[$i] ?? ''; ?> </td>
					<td width="10%" align="center"> <?php echo $home_pmin[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($home_p1qs[$i] ?? 0) + ($home_p2qs[$i] ?? 0) + ($home_p3qs[$i] ?? 0) + ($home_p4qs[$i] ?? 0) + ($home_pe1s[$i] ?? 0) + ($home_pe2s[$i] ?? 0) + ($home_pe3s[$i] ?? 0); ?> </td>
					<td width="7%" align="center"> <?php echo $home_p2p_m[$i] ?? ''; ?> / <?php echo $home_p2p_a[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_p3p_m[$i] ?? ''; ?> / <?php echo $home_p3p_a[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_pft_m[$i] ?? ''; ?> / <?php echo $home_pft_a[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($home_pre_off[$i] ?? 0) + ($home_pre_def[$i] ?? 0); ?> </td>
					<td width="7%" align="center"> <?php echo $home_past[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_pstl[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_pbs[$i] ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_ptover[$i] ?? ''; ?> </td>
					<td width="5%" align="center"> <?php echo ($home_pw_ft[$i] ?? 0) + ($home_pw_oft[$i] ?? 0); ?> </td>
				</tr>
				</table></td>
			</tr>
<?php
}
?>
					
				
			<tr height="32">
				<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="22%" align="center">종합</td>
					<td width="10%" align="center"> <?php echo $home_min4 ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($home_1234 + $home_e123) ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_2p_m ?? ''; ?> / <?php echo $home_2p_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_3p_m ?? ''; ?> / <?php echo $home_3p_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_ft_m ?? ''; ?> / <?php echo $home_ft_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($home_re_off ?? 0) + ($home_re_def ?? 0); ?> </td>
					<td width="7%" align="center"> <?php echo $home_ast ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_stl ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_bs ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $home_tover ?? ''; ?> </td>
					<td width="5%" align="center"> <?php echo ($home_w_ft ?? 0) + ($home_w_oft ?? 0); ?> </td>
					</tr>
				</table></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td height="34" bgcolor="#665D54"><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td width="20%" align="left" class="schedule1"><?php echo $atlist['t_name'] ?? ''; ?> </td>
				<td width="77%" align="right">&nbsp;</td>
				<td width="3%" align="center">&nbsp;</td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td height="30" bgcolor="#E3E2DE"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="7%" align="center">번호</td>
				<td width="15%" align="center">선수</td>
				<td width="10%" align="center">시간</td>
				<td width="7%" align="center">득점</td>
				<td width="7%" align="center">2점</td>
				<td width="7%" align="center">3점</td>
				<td width="7%" align="center">자유투</td>
				<td width="7%" align="center">리바운드</td>
				<td width="7%" align="center">어시스트</td>
				<td width="7%" align="center">스틸</td>
				<td width="7%" align="center" class="gibon_font"><span>블록</span></td>
				<td width="7%" align="center">턴오버</td>
				<td width="5%" align="center">파울</td>
			</tr>
			</table></td>
		</tr>
		<tr>
			<td><table border="0" width="100%" cellspacing="1" bgcolor="#e5e5e5">
<?php
for($i=0 ; $i<count($away_ppid) ; $i++){
		if($i%2) {
			$strBackground = "background='/img/list-bar.gif'";
		} else {
			$strBackground = "";
		}
?>
			<tr align="center" bgcolor="#ffffff">
				<td height="32"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td align="center"><table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td width="7%" align="center"> <?php echo $away_start[$i] ?? ''; echo $away_pp_back[$i] ?? ''; ?> </td>
						<td width="15%" align="center"> <?php echo $away_pp_name[$i] ?? ''; ?> </td>
						<td width="10%" align="center"> <?php echo $away_pmin[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo ($away_p1qs[$i] ?? 0) + ($away_p2qs[$i] ?? 0) + ($away_p3qs[$i] ?? 0) + ($away_p4qs[$i] ?? 0) + ($away_pe1s[$i] ?? 0) + ($away_pe2s[$i] ?? 0) + ($away_pe3s[$i] ?? 0); ?> </td>
						<td width="7%" align="center"> <?php echo $away_p2p_m[$i] ?? ''; ?> / <?php echo $away_p2p_a[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo $away_p3p_m[$i] ?? ''; ?> / <?php echo $away_p3p_a[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo $away_pft_m[$i] ?? ''; ?> / <?php echo $away_pft_a[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo ($away_pre_off[$i] ?? 0) + ($away_pre_def[$i] ?? 0); ?> </td>
						<td width="7%" align="center"> <?php echo $away_past[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo $away_pstl[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo $away_pbs[$i] ?? ''; ?> </td>
						<td width="7%" align="center"> <?php echo $away_ptover[$i] ?? ''; ?> </td>
						<td width="5%" align="center"> <?php echo ($away_pw_ft[$i] ?? 0) + ($away_pw_oft[$i] ?? 0); ?> </td>
					</tr>
					</table></td>
				</tr>
				</table></td>
			</tr>
<?php
}
?>
			<tr align="center">
				<td height="32"><table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td width="22%" align="center">종합</td>
					<td width="10%" align="center"> <?php echo $away_min4 ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($away_1234 + $away_e123) ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $away_2p_m ?? ''; ?> / <?php echo $away_2p_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $away_3p_m ?? ''; ?> / <?php echo $away_3p_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $away_ft_m ?? ''; ?> / <?php echo $away_ft_a ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo ($away_re_off ?? 0) + ($away_re_def ?? 0); ?> </td>
					<td width="7%" align="center"> <?php echo $away_ast ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $away_stl ?? ''; ?> </td>
					<td width="7%" align="center" class="gibon_font"><?php echo $away_bs ?? ''; ?> </td>
					<td width="7%" align="center"> <?php echo $away_tover ?? ''; ?> </td>
					<td width="5%" align="center"> <?php echo ($away_w_ft ?? 0) + ($away_w_oft ?? 0); ?> </td>
					</tr>
				</table></td>
			</tr>
			</table></td>
		</tr>
		</table></td>
	</tr>
	<tr>
		<td height="20">&nbsp;</td>
	</tr>
	</table></td>
	</tr>
</table>
</div>
<?php echo $SITE['tail'] ?? ''; ?>
