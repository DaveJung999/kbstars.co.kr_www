<?php
//=======================================================
// 설  명 : 일정관리(index.php)
// 책임자 : 박선민 , 검수: 03/10/06
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/09/16 박선민 마지막 수정
// 03/10/06 박선민 버그 수정
//=======================================================
$HEADER = array(
	'priv'		=>'', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2'		=>1, // DB 커넥션 사용
		useBoard=>1,
		useApp	=>1,
	'html_echo'	=>1,
	'html_skin'	=>'team'

);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$thisPath			= dirname(__FILE__);
	include_once("{$thisPath}/userfuntions.php");
	include_once("{$thisPath}/function_lunartosol.php");
	$thisUrl			= "/scalendar"; // 마지막 "/"이 빠져야함

	// 기본 URL QueryString
	$qs_basic = "db={$_GET['db']}";

	$table_calendarinfo	= $SITE['th'] . "calendarinfo";

	if($_GET['db']) {
		$sql = "SELECT * from {$table_calendarinfo} WHERE db='{$_GET['db']}'";
		if( !$dbinfo=db_arrayone($sql) )
			back("사용하지 않은 DB입니다.","infoadd.php?mode=user");

		$table_calendar	= "{$SITE['th']}calendar_" . $dbinfo['table_name']; // 게시판 테이블

		$sql_where_cal = " infouid='{$dbinfo['uid']}' ";
	}
	else back("DB 값이 없습니다");

	// 넘어온 mode값 체크
	if(!$_GET['mode']) $_GET['mode'] = "month";

	// 넘오온 date값 체크
	if(!$_GET['date']) 
		$_GET['date'] = date("Y-m-d");
	elseif( !preg_match("/[0-9]{4}-[01]?[0-9]-[0123]?[0-9]/",$_GET['date']) ) {
		back("잘못된 날짜입니다");
	}
	$_GET['date'] = date("Y-m-d",strtotime($_GET['date']));

	// 각종 날짜변수 - 현재 날짜
	$NowThisYear	= date("Y");
	$NowThisMonth	= date("m");
	$NowThisDay		= date("d");

	// 각종 날짜 변수 - 넘오온 날짜
	$intThisTimestamp	= strtotime($_GET['date']);
	$intThisYear	= date("Y",$intThisTimestamp);
	$intThisMonth	= date("m",$intThisTimestamp);
	$intThisDay		= date("d",$intThisTimestamp);
	$intThisWeekday	= date("w",$intThisTimestamp); 
	switch ($intThisWeekday) {
		Case 0: $varThisWeekday="일"; break;
		Case 1: $varThisWeekday="월"; break;
		Case 2: $varThisWeekday="화"; break;
		Case 3: $varThisWeekday="수"; break;
		Case 4: $varThisWeekday="목"; break;
		Case 5: $varThisWeekday="금"; break;
		Case 6: $varThisWeekday="토"; break;
	}

	// 각종 날짜변수 - 이전달,다음달
	if($intThisMonth==1) { // 1월달이라면 
		$intPrevYear	= $intThisYear-1;	//이전달 년도 = 이번년도 - 1
		$intPrevMonth	= 12;				//이전달 = 12월
		$intNextYear	= $intThisYear ;	//다음달 년도 = 이번달 년도
		$intNextMonth	= 2;				//다음달 = 2월
	}
	elseif($intThisMonth==12) { //12월달이라면
		$intPrevYear	= $intThisYear;		//이전달 년도 = 이번달 년도
		$intPrevMonth	= 11;				//이전달 = 11월
		$intNextYear	= $intThisYear + 1;	//다음달 년도 = 이번달 년도 + 1
		$intNextMonth	= 01;				// 다음달 = 1월
	}
	else { //1월과 12월을 제외한 경우에는
		$intPrevYear	= $intThisYear;		//이전달 년도 = 이번달 년도
		$intPrevMonth	= $intThisMonth - 1;//이전달 = 이번달  - 1
		$intNextYear	= $intThisYear;		//다음달 년도 = 이번달 년도
		$intNextMonth	= $intThisMonth+1;	//다음달 = 이번달 + 1
	}

	// 각종 날짜변수 - 월말일
	$intLastDay		= userLastDay($intThisMonth,$intThisYear);	//이번달
	$intPrevLastDay = userLastDay($intPrevMonth,$intPrevYear);	//지난달
	$intNextLastDay = userLastDay($intNextMonth,$intNextYear);	//다음달

	// 각종 날짜변수 - 월 1일의 요일(숫자로)
	$intFirstWeekday = date('w', strtotime($intThisYear."-".$intThisMonth."-1"));

	// 각종 날짜 변수 - ex)2003년 9월 1일, 월요일 (음력 8월 5일) 
	$thisFullDate	= date("Y년 n월 j일",$intThisTimestamp) . " {$varThisWeekday}요일";
	$sol2lun = sol2lun(date("Ymd",$intThisTimestamp));
	$sol2lun = explode("-", $sol2lun);
	$thisFullDate	.= "  (음력 {$sol2lun[1]}월 {$sol2lun[2]}일)";


	// URL Link
	$href['today']	= "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=day&date=".date("Y-m-d"),$qs_basic);
	$href['day']		= "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=day&date=".$_GET['date'],$qs_basic);
	$href['week']		= "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=week&date=".$_GET['date'],$qs_basic);
	$href['month']	= "{$_SERVER['PHP_SELF']}?" 
					. href_qs("mode=month&date=".$_GET['date'],$qs_basic);
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<style type = "text/css">
<!--
	A:link {font: 굴림,Arial; font-size: 9pt; text-decoration: none; color: "#363688";}
	A:visited {font: 굴림,Arial; font-size: 9pt; text-decoration: none; color: "#363688";}
	A:hover{font: 굴림,Arial; font-size: 9pt; text-decoration: none; color: "black";}
-->
</style>
<table width="690" border="0" align="center" cellpadding="0" cellspacing="0">
	<tr>
		<td><img src="/img/intro-10.gif" width="690" height="69" border="0" /></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
</table>
<table border="0" cellpadding="0" cellspacing="0"  width="590" bgcolor="white" align=center>
	<tr>
		<td>
			<!--table border="0" cellpadding="2" cellspacing="1"  width="590">
				<tr>
					<td height=30 valign=left><p align=left><span style='font-size:10pt'><b>
<?php
	echo $thisFullDate;
?>
						</b></span></p>

						<p align=right>
						<a href='<?=$href['today']
?>'><font color=red>▒ </font> 오늘일정(<?=date("m월 d일")
?>) </a>   : 
						<a href='<?=$href['day']
?>'><font color=red>▒ </font> 일별일정 </a>   : 
						<a href='<?=$href['week']
?>'><font color=red>▒ </font>  주별일정 </a> : 
						<a href='<?=$href['month']
?>'><font color=red>▒ </font>  월별일정  </a>
						</p>
					</td>
				</tr>
			</table-->
<?php
			if  ( $_GET['mode']=="input" || $_GET['mode']=="edit") {
				include("./inc_input.php");
			}
			elseif($_GET['mode']=="view") {
				include("./inc_view.php");
			}
			elseif($_GET['mode']=="day" ) {
				include("./inc_day.php");		
			}
			elseif($_GET['mode']=="week" ) {
				include("./inc_week.php");		
			}
			elseif($_GET['mode']=="month" ) {
				include("./inc_month.php");		
			}
		
?>
		</td>
	</tr>
</table>
<?=$SITE['tail'];
?>