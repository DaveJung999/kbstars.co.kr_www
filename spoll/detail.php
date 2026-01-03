<?php
//=======================================================
// 설	명 : 설문조사 삽입 예제
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/08/25
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			수정 내용
// -------- ------ --------------------------------------
// 03/08/25 박선민 김평수 소스에서 포팅
//=======================================================
$HEADER=array(
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useApp' => 1
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$table_pollinfo = "{$SITE['th']}pollinfo";
	$table_userinfo = "{$SITE['th']}userinfo";

	if( !$list_pollinfo = db_arrayone("SELECT * from {$table_pollinfo} WHERE db ='{$_REQUEST['db']}'") )
		back("해당 설문이 없습니다 . 감사합니다.");
	
	$table_poll = "{$SITE['th']}poll_" . $list_pollinfo['db'];

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================

## 총 투표수
$result2 = db_query("SELECT * from {$table_poll}");
$total_poll = db_count(); 
?>
<html>
<head>
<title>설문조사 리스트</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>

<body bgcolor="#FFFFFF" text="#000000">
<table width="950" border="0">
	<tr> 
	<td height="135"> 
	<table width="968" border="0" cellpadding="3" cellspacing="0">
		<tr> 
		<td colspan="2"><font size="2"><b>설문 주제</b> : 
<?php echo $list_pollinfo['title'] ; ?>
			</font></td>
		</tr>
		<tr> 
		<td width="401" valign="top"> 
			<table width="401" cellspacing="1" bgcolor="#000000" cellpadding="3">
			<tr bgcolor="#CCCCCC"> 
				<td colspan="3"><font size="2"><b>[일반설문조사]</b></font></td>
			</tr>
<?php
###############################################################################################
	# 조건에 관계없이 순수한 설문통계
	###############################################################################################
	$result2 = db_query("SELECT * from {$table_poll}");
	$total_poll = db_count();
	for($i=1; $i<=$list_pollinfo['q_num']; $i++){
		$result0 = db_query("SELECT count(*) FROM {$table_poll} WHERE val = {$i}");
		$list0['val'] = db_result($result0,0,"count(*)");
		$v_num = "q".$i; 
?>
			<tr bgcolor="#FFFFFF"> 
				<td width="163"><font size="2"> 
<?php echo $i ; ?>
 . 
<?php echo $list_pollinfo["q{$i}"]; ?>
				</font></td>
				<td width="119"><img src="images/line.gif" width="<?php echo ($total_poll == 0 ? "" : round(($list0['val']/$total_poll)*100)); ?>%" height="18"></td>
				<td width="97"><font size="2"> 
<?php echo $list0['val']; ?>
				( 
<?php echo ($total_poll == 0 ? "" : round(($list0['val']/$total_poll)*100)); 
?>
				%)</font></td>
			</tr>
<?php
} 
?>
			<tr bgcolor="#f6f6f6"> 
				<td width="163"> 
				<div align="center"><font size="2">합계</font></div></td>
				<td width="119"> 
				<div align="center"><font size="2">&nbsp;</font></div></td>
				<td width="97"> 
				<div align="center"><font size="2"> 
<?php echo $total_poll; ?>
					</font></div></td>
			</tr>
			</table>
		</td>
		<td width="550" valign="top"> 
			<table width="550" cellpadding="3" cellspacing="1" bgcolor="#000000">
			<tr bgcolor="#CCCCCC"> 
				<td width="166" height="19"><font size="2"><b>[성별 조사]</b></font></td>
				<td width="78" height="19"> 
				<div align="center"><font size="2">전체</font></div></td>
				<td width="89" height="19"> 
				<div align="center"><font size="2">남성</font></div></td>
				<td width="75" height="19"> 
				<div align="center"><font size="2">여성</font></div></td>
				<td width="108" height="19"> 
				<div align="center"><font size="2">방문객</font></div></td>
			</tr>
<?php
###################################################################
	# 성별에 따른 설문 통계	
	###################################################################
	
	for($i=1; $i<=$list_pollinfo['q_num']; $i++){
		$list0['val'] = db_result($result0,$i-1,"count(val)");
		$v_num = "q".$i;

		## 남성과 여성의 각 설문에 따른 통계를 구한다 . 어렵당..ㅠㅠ (sex=0 , 1 , 2	기타, 남성, 여성)
		for($j=0; $j<3; $j++){
			$result2 = db_query("SELECT count(*) FROM {$table_poll} WHERE val={$i} and sex = {$j}");
			$sex = db_result($result2,0,"count(*)");
			$sex_arr[$i] = $sex_arr[$i]."_".$sex;

			$result3 = db_query("SELECT count(*) FROM {$table_poll} WHERE sex={$j}");
			$sex_total[$j] = db_result($result3,0,"count(*)");

		}
		$result4 = db_query("SELECT count(*) FROM {$table_poll} WHERE val={$i}");
		$sex_total_sum = db_result($result4,0,"count(*)");
		
		## _00_00_00	이런식으로 기타, 남성 , 여성의 설문합이 구해진다.
		$sex_value = explode("_",$sex_arr[$i]);
?>
			<tr bgcolor="#FFFFFF"> 
				<td width="166"><font size="2"> 
<?php echo $i ; ?>
 . 
<?php echo $list_pollinfo["q{$i}"]; ?>
				</font></td>
				<td width="78"> 
				<div align="center"><font size="2"> 
<?php echo $sex_total_sum; ?>
					</font></div></td>
				<td width="89"> 
				<div align="center"><font size="2"> 
<?php echo $sex_value[2] ; ?>
					( 
<?php echo ($sex_total[1] == 0 ? "" : round(($sex_value[2]/$sex_total[1])*100)); ?>
					%) </font></div></td>
				<td width="75"> 
				<div align="center"><font size="2"> 
<?php echo $sex_value[3] ; ?>
					( 
<?php echo ($sex_total[2] == 0 ? "" : round(($sex_value[3]/$sex_total[2])*100)); ?>
					%) </font></div></td>
				<td width="108"> 
				<div align="center"><font size="2"> 
<?php echo $sex_value[1] ; ?>
					( 
<?php echo ($sex_total[0] == 0 ? "" : round(($sex_value[1]/$sex_total[0])*100)); ?>
					%) </font></div></td>
			</tr>
<?php
## 남성의 총합과 여성의 총합을 구한다.
		$total_man += $sex_value[2];
		$total_woman += $sex_value[3];
		$total_etc += $sex_value[1];
	} 

?>
			<tr bgcolor="#f6f6f6"> 
				<td width="166"> 
				<div align="center"><font size="2">합계</font></div></td>
				<td width="78"> 
				<div align="center"><font size="2"> 
<?php echo $total_poll; ?>
					</font></div></td>
				<td width="89"> 
				<div align="center"><font size="2"> 
<?php echo $total_man ; ?>
					</font></div></td>
				<td width="75"> 
				<div align="center"><font size="2"> 
<?php echo $total_woman ; ?>
					</font></div></td>
				<td width="108"> 
				<div align="center"><font size="2"> 
<?php echo $total_etc ; ?>
					</font></div></td>
			</tr>
			</table>
		</td>
		</tr>
		<tr> 
		<td colspan="2">&nbsp; </td>
		</tr>
		<tr> 
		<td colspan="2"> 
			<table width="750" bgcolor="#000000" cellspacing="1" cellpadding="3">
			<tr bgcolor="#CCCCCC"> 
				<td width="116"><font size="2"><b>[연령별 조사]</b></font></td>
				<td width="38"> 
				<div align="center"><font size="2">전체</font></div></td>
				<td width="77"> 
				<div align="center"><font size="2">10대미만</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2">10대</font></div></td>
				<td width="81"> 
				<div align="center"><font size="2">20대</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2">30대</font></div></td>
				<td width="77"> 
				<div align="center"><font size="2">40대</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2">50대 이상</font></div></td>
				<td width="75"> 
				<div align="center"><font size="2">방문객</font></div></td>
			</tr>
<?php
###################################################################################################
	# 연령층으로 보는 설문
	# 설문에 응한 회원의 나이가 DB에 저장되어 있고 이것을 가지고 뽑는다 . 에구.힘든그...
	###################################################################################################
	for($i=1; $i<=$list_pollinfo['q_num']; $i++){
	
		$v_num = "q".$i;
		$result2 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=1 and age<10)"); //10대이하
		$age[0] = db_count();
		$result3 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=10 and age<20)"); //10대
		$age[1] = db_count();
		$result4 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=20 and age<30)"); //20대
		$age[2] = db_count();
		$result5 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=30 and age<40)"); //30대
		$age[3] = db_count();
		$result6 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=40 and age<50)"); //40대 
		$age[4] = db_count();
		$result7 = db_query("SELECT * from {$table_poll} WHERE val={$i} and (age>=50) ");	//50대 이상
		$age[5] = db_count();
		$result8 = db_query("SELECT * from {$table_poll} WHERE val={$i} and age=0 ");	// 기타
		$age[6] = db_count();

		$result2 = db_query("SELECT * from {$table_poll} WHERE (age>=1 and age<10)"); //10대이하 총합
		$total_age[0] = db_count();
		$result3 = db_query("SELECT * from {$table_poll} WHERE (age>=10 and age<20)"); //10대 총합
		$total_age[1] = db_count();
		$result4 = db_query("SELECT * from {$table_poll} WHERE (age>=20 and age<30)"); //20대 총합
		$total_age[2] = db_count();
		$result5 = db_query("SELECT * from {$table_poll} WHERE (age>=30 and age<40)"); //30대 총합
		$total_age[3] = db_count();
		$result6 = db_query("SELECT * from {$table_poll} WHERE (age>=40 and age<50)"); //40대 총합
		$total_age[4] = db_count();
		$result7 = db_query("SELECT * from {$table_poll} WHERE (age>=50) ");	//50대이상 총합
		$total_age[5] = db_count();
		$result8 = db_query("SELECT * from {$table_poll} WHERE age=0 ");	//기타 총합
		$total_age[6] = db_count();
		
		
		$total_value = $age[0] + $age[1] + $age[2] + $age[3] + $age[4] + $age[5] + $age[6];
?>
			<tr bgcolor="#FFFFFF"> 
				<td width="116"><font size="2"> 
<?php echo $i ; ?>
 . 
<?php echo $list_pollinfo["q{$i}"]; ?>
				</font></td>
				<td width="38"> 
				<div align="center"><font size="2"> 
<?php echo $total_value ; ?>
					</font></div></td>
				<td width="77"> 
				<div align="center"><font size="2"> 
<?php echo $age[0] ; ?>
					( 
<?php echo ($total_age[0] == 0 ? "" : round(($age[0]/$total_age[0])*100)); ?>
					%)</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2"> 
<?php echo $age[1] ; ?>
					( 
<?php echo ($total_age[1] == 0 ? "" : round(($age[1]/$total_age[1])*100)); ?>
					%)</font></div></td>
				<td width="81"> 
				<div align="center"><font size="2"> 
<?php echo $age[2] ; ?>
					( 
<?php echo ($total_age[2] == 0 ? "" : round(($age[2]/$total_age[2])*100)); ?>
					%)</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2"> 
<?php echo $age[3] ; ?>
					( 
<?php echo ($total_age[3] == 0 ? "" : round(($age[3]/$total_age[3])*100)); ?>
					%)</font></div></td>
				<td width="77"> 
				<div align="center"><font size="2"> 
<?php echo $age[4] ; ?>
					( 
<?php echo ($total_age[4] == 0 ? "" : round(($age[4]/$total_age[4])*100)); ?>
					%)</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2"> 
<?php echo $age[5] ; ?>
					( 
<?php echo ($total_age[5] == 0 ? "" : round(($age[5]/$total_age[5])*100)); ?>
					%)</font></div></td>
				<td width="75"> 
				<div align="center"><font size="2"> 
<?php echo $age[6] ; ?>
					( 
<?php echo ($total_age[6] == 0 ? "" : round(($age[6]/$total_age[6])*100)); ?>
					%)</font></div></td>
			</tr>
<?php
// PHP 7+에서는 mysql_free_result()가 제거되었으므로 db_free() 사용
		db_free($result2);
		db_free($result3);
		db_free($result4);
		db_free($result5);
		db_free($result6);
		db_free($result7);
		db_free($result8);
	} 

?>
			<tr bgcolor="#f6f6f6"> 
				<td width="116"> 
				<div align="center"><font size="2">합계</font></div></td>
				<td width="38"> 
				<div align="center"><font size="2"> 
<?php echo $total_poll; ?>
					</font></div></td>
				<td width="77"> 
				<div align="center"> <font size="2"> 
<?php echo $total_age[0] ; ?>
					</font></div></td>
				<td width="76"> 
				<div align="center"> <font size="2"> 
<?php echo $total_age[1] ; ?>
					</font></div></td>
				<td width="81"> 
				<div align="center"> <font size="2"> 
<?php echo $total_age[2] ; ?>
					</font></div></td>
				<td width="76"> 
				<div align="center"> <font size="2"> 
<?php echo $total_age[3] ; ?>
					</font></div></td>
				<td width="77"> 
				<div align="center"><font size="2"> 
<?php echo $total_age[4] ; ?>
					</font></div></td>
				<td width="76"> 
				<div align="center"><font size="2"> 
<?php echo $total_age[5] ; ?>
					</font></div></td>
				<td width="75"> 
				<div align="center"><font size="2"> 
<?php echo $total_age[6] ; ?>
					</font></div></td>
			</tr>
			</table>
		</td>
		</tr>
		<tr> 
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr> 
		<td colspan="2"> 
			<table width="550" cellspacing="1" cellpadding="3" bgcolor="#000000">
			<tr bgcolor="#CCCCCC"> 
				<td width="150"> 
				<div align="left"><font size="2"><b>[회원별 조사]</b></font></div></td>
				<td> 
				<div align="center"><font size="2">전체</font></div></td>
				<td> 
				<div align="center"><font size="2">정회원</font></div></td>
				<td> 
				<div align="center"><font size="2">준회원</font></div></td>
				<td> 
				<div align="center"><font size="2">일반회원</font></div></td>
				<td> 
				<div align="center"><font size="2">방문객</font></div></td>
			</tr>
<?php
###################################################################################################
	# 설문한 내용을 테이블에 저장한다.
	# 설문테이블에 들어가는 member는 1은 정회원 2는 준회원 3은 일반회원(비서비스) 4는 방문객
	###################################################################################################
		
	for($i=1; $i<=$list_pollinfo['q_num']; $i++){
		
	
		$v_num = "q".$i;
		$result2 = db_query("SELECT * from {$table_poll} WHERE val={$i} and member=1"); // 정회원
		$member_value[0] = (db_count());
		$result3 = db_query("SELECT * from {$table_poll} WHERE val={$i} and member=2"); // 준회원
		$member_value[1] = db_count();
		$result4 = db_query("SELECT * from {$table_poll} WHERE val={$i} and member=3"); // 일반회원(비서비스 회원)
		$member_value[2] = db_count();
		$result5 = db_query("SELECT * from {$table_poll} WHERE val={$i} and member=4"); // 방문객
		$member_value[3] = db_count();

		$result2 = db_query("SELECT * from {$table_poll} WHERE member=1"); // 정회원 총합
		$total_member[0] = db_count();
		$result3 = db_query("SELECT * from {$table_poll} WHERE member=2"); // 준회원 총합
		$total_member[1] = db_count();
		$result4 = db_query("SELECT * from {$table_poll} WHERE member=3"); // 일반회원 총합
		$total_member[2] = db_count();
		$result5 = db_query("SELECT * from {$table_poll} WHERE member=4"); // 방문객 총합
		$total_member[3] = db_count();
		

		// PHP 7+에서는 mysql_free_result()가 제거되었으므로 db_free() 사용
		db_free($result2);
		db_free($result3);
		db_free($result4);
		db_free($result5);

		$total_value = $member_value[0] + $member_value[1] + $member_value[2] + $member_value[3];
?>
			<tr bgcolor="#FFFFFF"> 
				<td width="150"> 
				<div align="left"><font size="2"> 
<?php echo $i; ?>
 . 
<?php echo $list_pollinfo[$v_num]; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_value ; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $member_value[0] ; ?>
					( 
<?php echo ($total_member[0] == 0 ? "" : round(($member_value[0]/$total_member[0])*100)); ?>
					%)</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $member_value[1] ; ?>
					( 
<?php echo ($total_member[1] == 0 ? "" : round(($member_value[1]/$total_member[1])*100)); ?>
					%)</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $member_value[2] ; ?>
					( 
<?php echo ($total_member[2] == 0 ? "" : round(($member_value[2]/$total_member[2])*100)); ?>
					%)</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $member_value[3] ; ?>
					( 
<?php echo ($total_member[3] == 0 ? "" : round(($member_value[3]/$total_member[3])*100)); ?>
					%)</font></div></td>
			</tr>
<?php
} 
?>
			<tr bgcolor="#f6f6f6"> 
				<td width="150"> 
				<div align="center"><font size="2">합계</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_poll; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_member[0] ; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_member[1] ; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_member[2] ; ?>
					</font></div></td>
				<td> 
				<div align="center"><font size="2"> 
<?php echo $total_member[3] ; ?>
					</font></div></td>
			</tr>
			</table>
		</td>
		</tr>
	</table>
	</td>
	</tr>
</table>
</body>
</html>