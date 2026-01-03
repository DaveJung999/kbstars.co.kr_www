<?php
//=======================================================
// 설  명 : 일정관리(index.php)
// 책임자 : 박선민 , 검수: 03/10/04
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 03/10/04 박선민 마지막 수정
//=======================================================
/*
function minical() {
	$oldGET=$_GET;
	$_GET['db']	= "cs";
	include("./scalendar/mini.php");
	$_GET	= $oldGET;
}
minical();
*/
$HEADER = array(
	'priv'		=>'', // 인증유무 (비회원,회원,운영자,서버관리자)
	'usedb2'		=>1, // DB 커넥션 사용
		useApp	=>1,

);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$thisPath			= dirname(__FILE__);
	include_once("{$thisPath}/userfuntions.php");
	include_once("{$thisPath}/function_lunartosol.php");// 음력,양력 변환 함수
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
	$href['today']	= "{$thisUrl}/index.php?" 
					. href_qs("mode=day&date=".date("Y-m-d"),$qs_basic);
	$href['day']		= "{$thisUrl}/index.php?" 
					. href_qs("mode=day&date=".$_GET['date'],$qs_basic);
	$href['week']		= "{$thisUrl}/index.php?" 
					. href_qs("mode=week&date=".$_GET['date'],$qs_basic);
	$href['month']	= "{$thisUrl}/index.php?" 
					. href_qs("mode=month&date=".$_GET['date'],$qs_basic);


	////////////////////////////
	// 반복되지 않은 일정 구하기
	// $outCal[YYYY-MM-DD]
	$searchDateFrom = "{$intThisYear}-{$intThisMonth}-01";
	$searchDateTo	= "{$intThisYear}-{$intThisMonth}-{$intLastDay}";

	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes=0 ";
	$sql .= "AND (startdate>='{$searchDateFrom}' AND startdate<='{$searchDateTo}') ";
	$sql .= " AND (dtype = 'hour' OR dtype = 'day') ";
	$sql .= " ORDER BY startdate, starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			$list['title'] = cut_string($list['title'], 24);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			$href['view'] = "{$thisUrl}/index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);

		} // end if.. else

		$outCal[$list['startdate']] .= "<img src={$thisUrl}/images/micon.gif border=0><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"calview('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"nocalview();\">{$list['title']}</a></span></font><br> \n"	;
	} // end while
	////////////////////////////

	////////////////////////////
	// 반복 일정 구하기
	// $outCal['day']
	$sql = "SELECT * from {$table_calendar} WHERE {$sql_where_cal} AND retimes>0 ";
	$sql .= " AND (startdate<='{$searchDateTo}' AND enddate >='{$searchDateFrom}') ";
	$sql .= " AND (dtype = 'hour' or dtype = 'day') ";
	$sql .="  ORDER BY starthour";
	$result	= db_query($sql);
	while( $list=db_array($result) ) {
		// 반복되는 첫 $tmp_time 구함
		if(strcmp($list['startdate'],$searchDateFrom)<0) {
			$tmp_time = strtotime($searchDateFrom);
			switch($list['retype']) {
				case "day"://일일단위 반복설정
					// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
					$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;
					
					if($cday%$list['retimes']>0)
						$tmp_time += ($list['retimes']-$cday%$list['retimes']) * 86400;
					break;
				case "week"://주단위 반복설정
					// - 레코드 저장일과 출력셀의 날짜와의 날짜차이
					$cday	= userDateDiff("d",$list['startdate'],$searchDateFrom)-1;

					// 주단위기에 retimes에서 7을 곱함
					if($cday%($list['retimes']*7)>0)
						$tmp_time += ($list['retimes']*7-$cday%($list['retimes']*7)) * 86400;
					break;
				case "month"://월단위 반복설정
					// 월단위기에 startdate의 일(Day)임
					$tmp_time = strtotime("substr($searchDateFrom,0,8)".substr($list['startdate'],-2));
					break;
			} // end switch
		}
		else {
			// 기간안에 startdate가 있기에 그것이 첫날임
			$tmp_time = strtotime($list['startdate']);
		}

		if($list['dtype'] == "day" )
			$lhour= "[ 하루 종일 ]";
		else
			$lhour="[{$list['starthour']}:{$list['startmin']}~{$list['endhour']}:{$list['endmin']}]";

		// 권한체크
		// 권한체크
		if(!privAuth($list,"priv_level")) {
			$list['title']	= "비공개 일정";
			$list['content']	= "비공개 일정";

			// URL Link
			$href['view'] = "javascript: return false;";
		}
		else {
			$list['title'] = cut_string($list['title'], 24);
			$list['title'] = htmlspecialchars($list['title'],ENT_QUOTES);
			$list['content'] = cut_string($list['content'], 150);
			$list['content'] = htmlspecialchars($list['content'],ENT_QUOTES);
			$list['content'] = replace_string($list['content'], 'text');	// 문서 형식에 맞추어서 내용 변경

			// URL Link
			$href['view'] = "{$thisUrl}/index.php?".href_qs("mode=view&bmode={$_GET['mode']}&uid={$list['uid']}",$qs_basic);

		} // end if.. else


		// 일정 변수에 저장
		$tmp_enddate = (strcmp($searchDateTo,$list['enddate'])<0) ? $searchDateTo : $list['enddate'];
		$tmp_time_enddate = strtotime($tmp_enddate);
		while($tmp_time<=$tmp_time_enddate) {// 말일을 지나기 전까지
			$tmp = date("Y-m-d",$tmp_time);
			$outCal[$tmp] .= "<img src={$thisUrl}/images/micon.gif border=0><font face=굴림><span style='font-size:9pt'><a href='{$href['view']}' onMouseOver=\"calview('{$list['title']}', '{$lhour}','{$list['content']}');\" onMouseOut=\"nocalview();\">{$list['title']}</a></span></font><br> \n"	;

			switch($list['retype']) {
				case "day":
					$tmp_time	+= $list['retimes'] * 86400;
					break;
				case "week":
					$tmp_time	+= $list['retimes'] * 7*86400;
					break;
				case "month": 
					$tmp_time	+= $list['retimes'] * 30*86400;
					break;
			} // end switch
		} // end while
	} // end while
	////////////////////////////


	// 쓰기 권한이 있는지 확인
	if(privAuth($dbinfo, "priv_write"))	$enable_write = true;
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<div ID="overDiv" STYLE="position:absolute;top=50;substr=100; visibility:hide; z-index:2;"></div>
<script LANGUAGE="JavaScript" src="/scalendar/cal_div_calview.js" type="Text/JavaScript"></script>
<?php
if(is_array($outCal)) {
	foreach($outCal as $key => $value) {
		$key = substr($key,5);
		echo '[',$key,']',$value,'<br>';
	}
}
?>