<?php
//=======================================================
// 설	명 : URL에서 링크 url 추출 프로그램(extractemail/geturls.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 02/11/14
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인			 수정 내용
// -------- ------ --------------------------------------
// 02/03/20 김평수 자체 개발 완료
// 02/11/14 박선민 마지막 수정
//=======================================================
$HEADER=array(
		auth	=>0, // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		usedb	=>1, // DB 커넥션 사용 (0:미사용, 1:사용)
		useBoard=>1, // href_qs()
	);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//page_security("", $HTTP_HOST);

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	set_time_limit(0);

	// 관리자페이지 환경파일 읽어드림
	$rs=db_query("select * from {$SITE['th']}admin_config where skin='{$SITE['th']}' or skin='basic' order by uid DESC");
	$pageinfo=db_count() ? db_array($rs) : back("관리자페이지 환경파일을 읽을 수가 없습니다");


	$table = $SITE['th'] . "extractemail";
	// 테이블이 없을 경우 생성
	$sql="
			CREATE TABLE $table (
				uid mediumint(8) unsigned NOT NULL auto_increment,
				userid varchar(20) NOT NULL default '',
				email varchar(40) NOT NULL default '',
				category varchar(20) NOT NULL default '',
				rdate int(10) unsigned NOT NULL default '0',
				PRIMARY KEY	(uid),
				KEY cartegory (category)
			)
		";
	// PHP 7+에서는 mysql_* 함수가 제거되었으므로 db_* 함수 사용
	@db_query($sql);

	// 넘오온값필터링
	$url		=trim($url);
	$page		=trim($page);
	$start_num	=trim($start_num);
	$end_num	=trim($end_num);

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
?>
<html>
<?=$pageinfo['html_header']
?>
<body>
<form name="form1" method="post" action="">
<input type="hidden" name="mode" value="geturls">
<table border=0 cellspacing='<?=$pageinfo['table_cellspacing']
?>' cellpadding='<?=$pageinfo['table_cellpadding']
?>' bgcolor='<?=$pageinfo['table_linecolor']
?>'>
	<tr bgcolor='<?=$pageinfo['table_thcolor']
?>'>
		<td width="466"> <font size="2"> 
		URL : <input type="text" name="url" size="40" value="<?=htmlspecialchars(stripslashes($url),ENT_QUOTES)
?>"></font></td>
	</tr>
	<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
		<td height="28" width="466"> <font size="2"> 
		<input type="text" name="page" size="6" value="<?=$page
?>">(게시판 서브쿼리 예를 들어 page...) 
		<input type="text" name="start_num" size="2" value="<?=$start_num
?>">
		~ 
		<input type="text" name="end_num" size="2" value="<?=$end_num
?>">
		뽑고 싶은 페이지 수</font></td>
	</tr>
	<tr bgcolor='<?=$pageinfo['table_thcolor']
?>'>
		<td width="466"> <font size="2"> 
		<input type="submit" name="Submit" value=" 이메일 추출하기 ">
		</font></td>
	</tr>
	</table>
</form>



<table border=0 cellspacing='<?=$pageinfo['table_cellspacing']
?>' cellpadding='<?=$pageinfo['table_cellpadding']
?>' bgcolor='<?=$pageinfo['table_linecolor']
?>'>
	<tr bgcolor='<?=$pageinfo['table_thcolor']
?>'>
	<td colspan="4"><font size="2"><b>[DB에 쌓인 DATA]</b></font></td>
	</tr>
	<tr bgcolor='<?=$pageinfo['table_thcolor']
?>'>
	<td> 
		<div align="center"><font size="2">추출한관리자</font></div>
	</td>
	<td> 
		<div align="center"><font size="2">카테고리</font></div>
	</td>
	<td> 
		<div align="center"><font size="2">수량</font></div>
	</td>
	<td> 
		<div align="center"><font size="2">추출날짜</font></div>
	</td>
	</tr>
	<?php
################################## 
# Email 추출한 내역 DB 에서 뽑아옴
##################################
$result = db_query("SELECT userid,category,count(category) as num,rdate FROM {$table} group by category, userid ");
$total = db_count();
for($i=0; $i<$total; $i++){
	$list = db_array($result);
	$total_email += $list['num'];
	
?>
		<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
		<td height="23"> 
			<div align="center"><font size="2"> 
<?php 
echo $list['userid'] 
?>
			</font></div>
		</td>
		<td height="23"> 
			<div align="center"><font size="2"> 
<?php 
echo $list['category'] 
?>
			</font></div>
		</td>
		<td height="23"> 
			<div align="center"><font size="2"> 
<?php 
echo $list['num'] 
?>
			</font></div>
		</td>
		<td height="23"> 
			<div align="center"><font size="2"> 
<?php 
echo date('Y.m.d', $list['rdate']); 
?>
			</font></div>
		</td>
		</tr>
<?php
} // end for	
?>
	<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
	<td colspan="4"> 
		<div align="right"><font size="2">이메일 총수량 : <b><?php 
echo $total_email; 
?></b> 개</font></div>
	</td>
	</tr>

</table>

<?php
// $mode에 따른 처리
if($mode == "geturls"){
	$all_urls	= user_geturls($url);
}
?>
<br>
	<form name="form2" method="post" action="">
	<input type="hidden" name="mode" value="emailtodb">
	<input type="hidden" name="all_email" value="<?php 
echo $all_email; 
?>">

		
<table border=0 cellspacing='<?=$pageinfo['table_cellspacing']
?>' cellpadding='<?=$pageinfo['table_cellpadding']
?>' bgcolor='<?=$pageinfo['table_linecolor']
?>'>
	<tr bgcolor='<?=$pageinfo['table_thcolor']
?>'>
		<td width="183" height="20"><font size="2"><b>[이메일 추출]</b></font></td>
		<td width="210" height="20"><font size="2"></font></td>
	</tr>
	
	<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
		<td colspan="2" height="20"><font size="2">이메일을 추출한 사이트 : <b> 
<?php 
echo $first_url 
?>
		</b> <br>
		추출한 E-mail 수량 <font color="#FF0000"> 
<?php 
echo sizeof($all_email_arr) -1 ; 
?>
		<font color="#000000">개</font></font></font></td>
	</tr>
	<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
		<td width="183"> 
		<table border=0 cellspacing='<?=$pageinfo['table_cellspacing']
?>' cellpadding='<?=$pageinfo['table_cellpadding']
?>' bgcolor='<?=$pageinfo['table_linecolor']
?>'>

<?php
	// 추출한 메일을 출력해주는 부분
	for($i=1; $i <= sizeof($all_urls); $i++ ){	
		if(trim($all_urls[$i])) {
			
?>
				<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'> 
				<td><font size="2"> 
					<?php 
echo $all_urls[$i] 
?>
					</font></td>
				</tr>
<?php
		}
	}	
	
?>
		</table>
		</td>
		<td valign="bottom" width="210"> <font size="2"> </font> 
		<table border=0 cellspacing='<?=$pageinfo['table_cellspacing']
?>' cellpadding='<?=$pageinfo['table_cellpadding']
?>' bgcolor='<?=$pageinfo['table_linecolor']
?>'>

		 <tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
			<td valign="top"><font size="2" color="#3F7058">본 이메일 추출은 악의성<br>
				없이 내부적으로 사용할 <br>
				것을 굳게 다짐합니다.<br>
				ㅋㅋㅋ<br>
				<br>
				DB 에 이메일이 입력될땐<br>
				중복된 이메일은 DB에서<br>
				제외됩니다.</font></td>
			</tr>
		 <tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
			<td valign="bottom"><font size="2"> 
				<select name="category">
				<option>신문사</option>
				<option>게시판</option>
				<option>뉴21커뮤니티</option>
				<option>야후</option>
				</select>
				</font></td>
			</tr>
		<tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
			<td valign="bottom" height="41"><font size="2"> 
				<input type="text" name="direct_cartegory">
				<input type="checkbox" name="direct_check" value="1">
				<br>
				(직접 분류를 원할땐 체크)</font></td>
			</tr>
		 <tr bgcolor='<?=$pageinfo['table_tdcolor']
?>'>
			<td valign="bottom"><font size="2"> 
				<input type="submit" name="Submit2" value=" DB에 입력하기 ">
				</font></td>
			</tr>
		</table>
		<font size="2"><br>
		<br>
		</font></td>
	</tr>
	</table>
	</form>
	<br>
</body>
</html>
<?php
//=======================================================
// User functions... (사용자 함수 정의)
//=======================================================
// 테이블이 존재하지 않을 경우 admin_tableinfo 테이블정보대로 table생성
function mysql_table_create($table,$createtable) {
	global $SITE;
	$rs=db_query("select sql FROM {$SITE['th']}admin_tableinfo where table_name='$table'");
	if(db_count()) {
		$sql="CREATE TABLE $createtable (" . db_result($rs,0,"sql") . ")";
		// PHP 7+에서는 mysql_* 함수가 제거되었으므로 db_* 함수 사용
		if(@db_query($sql))
			return 1;
		else // 아마 해당 데이터베이스가 존재할 경우겠지.. 생성하다가 실패했으니..
			return -1; // -1로 리턴함..
	}
	else {
		return 0;
	}
} // end func


function user_geturls($url) {
	$url		= trim($url);
	$html_url	= file($url);
	echo "<pre>";
	foreach($html_url as $value){	// 메일 추출
		$tmp=explode("http",$value);
		foreach($tmp as $tmp_key => $tmp_value) {
			if($tmp_key) $tmp_value="http".$tmp_value;
			if(preg_match("/(http|https):\/\/[a-z0-9_\-]+\.[][a-zA-Z0-9:;&#@=_~%\?\/\.\,\+\-]+/i", $tmp_value,$search)) {
				if(!preg_match("/\.yahoo\.co/i",$search[0]))	$all_urls[] = $search[0];
			}
		} // end foreach
	} // end foreach
	return array_unique($all_urls);
} // func user_geturls()
?>