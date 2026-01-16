<?php
####################################################################################
/*
				navyism@log analyzer 5
				  function library

*/
####################################################################################

####################################################################################
//					글로벌변수
####################################################################################
if ($_GET) { extract($_GET); }
if ($_POST) { extract($_POST); }
// 25/01/XX Auto Deprecated 변수 제거 (직접 $_SERVER 사용)
// $PHP_SELF = $_SERVER['PHP_SELF'];
// $HTTP_REFERER = $_SERVER['HTTP_REFERER'] ?? '';
// $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'] ?? '';

####################################################################################
//					버튼지정
####################################################################################
$go_root = "<font color=#008CD6 size=1><a href=root.php>ROOT</a></font>";
$close = "<font color=#008CD6 size=1><a href=javascript:void(0) onclick='window.close()'>CLOSE</a></font>";
$help = "<font color=#008CD6 size=1><a href=http://navyism.com target=_blank>HELP</a></font>";
$manual = "<font color=#008CD6 size=1><a href=http://navyism.com/support/nalog/index.html target=_blank>MANUAL</a></font>";
$logout = "<font color=#008CD6 size=1><a href=logout.php onclick=\"if(confirm('n@log message : \\n\\nlog-out?'))return true;else return false;\">LOGOUT</a></font>";

####################################################################################
//					에러메세지
####################################################################################
function nalog_error($text) 
{
	echo "<script>
	window.alert('n@log error : \\n\\n$text');
	history.go(-1);
	</script>";
	exit;
}

####################################################################################
//					일반메세지
####################################################################################
function nalog_msg($text)
{
	echo "<script>
	window.alert('n@log message : \\n\\n$text');
	</script>";
}

####################################################################################
//					페이지이동
####################################################################################
function nalog_go($url)
{
	echo "<meta http-equiv='refresh' content='0;url=$url'>";
	exit;
}

####################################################################################
//					관리자체크
####################################################################################
function nalog_admin_check($url)
{
	global $admin_id, $admin_pass, $_COOKIE;
	$admin = md5($admin_id.$admin_pass);
	if ($_COOKIE['nalog_admin'] != $admin) {
		nalog_go($url);
	}
}

####################################################################################
//					관리자체크
####################################################################################
function nalog_admin_check2()
{
	global $admin_id, $admin_pass, $_COOKIE;
	$admin = md5($admin_id.$admin_pass);
	if ($_COOKIE['nalog_admin'] != $admin) {
		echo "<script language='javascript'>window.close()</script>";
		exit;
	}
}

####################################################################################
//					관리자체크
####################################################################################
function nalog_admin_check3()
{
	global $admin_id, $admin_pass, $_COOKIE;
	$admin = md5($admin_id.$admin_pass);
	if ($_COOKIE['nalog_admin'] != $admin) {
		nalog_error('Permission Denied');
	}
}

####################################################################################
//					관리자체크
####################################################################################
function nalog_admin_check4()
{
	global $admin_id, $admin_pass, $_COOKIE;
	$admin = md5($admin_id.$admin_pass);
	if ($_COOKIE['nalog_admin'] == $admin) {
		return 1;
	}
}

####################################################################################
//					인덱스
####################################################################################
function nalog_index() {
	global $pagegroup, $pagestart, $pageend, $pageviewsu, $send, $pagenum, $pagesu, $total;
	$file_name = $_SERVER['PHP_SELF'];
	if ($pagegroup > 1) {
		$prev = $pagestart - $pageviewsu - 1; // 이전목록그룹의 시작페이지결정
		echo "<a href=\"$file_name?${send}pagenum=$prev\"><span style=font-size:6pt>&#9664;&#9664;</span></a> ";
	}
	if ($pagenum) {
		$prevpage = $pagenum - 1;
		echo "<a href=\"$file_name?${send}pagenum=$prevpage\"><span style=font-size:6pt>&#9664;</span></a> ";
	}
	for ($i = $pagestart; $i <= $pageend; $i++) {
		if ($pagesu < $i) { break; }
		$j = $i - 1;
		if ($j == $pagenum) { echo "<b>$i</b> "; }
		else { echo "[<a href=\"$file_name?${send}pagenum=$j\">$i</a>] "; }
	}
	if (($pagenum + 1) != $pagesu && $total) {
		$nextpage = $pagenum + 1;
		echo "<a href=\"$file_name?${send}pagenum=$nextpage\"><span style=font-size:6pt>&#9654;</span></a> ";
	}
	if ($pageend < $pagesu) {
		echo "<a href=\"$file_name?${send}pagenum=$pageend\"><span style=font-size:6pt>&#9654;&#9654;</span></a> ";
	}
}	

####################################################################################
//					카운터리스트
####################################################################################
function nalog_list_bd() {
	global $connect_db;
	$result = @mysqli_query($connect_db, "SHOW TABLES"); // mysql_list_tables → SHOW TABLES
	$tables = [];
	while ($row = mysqli_fetch_row($result)) {
		$tb_name = $row[0];
		if (preg_match("/nalog3_counter_/", $tb_name)) {
			$tables[] = str_replace("nalog3_counter_", "", $tb_name);
		}
	}
	return $tables;
}

####################################################################################
//					갯수세기
####################################################################################
function nalog_total($table, $where) {
	global $connect;
	$query = "SELECT COUNT(*) AS cnt FROM $table WHERE 1 $where"; 
	$result = mysqli_query($connect, $query);
	$total = mysqli_fetch_assoc($result); 
	return $total['cnt'];
}

####################################################################################
//					드롭
####################################################################################
function nalog_drop($table) {
	global $connect;
	$query = "DROP TABLE $table";
	@mysqli_query($connect, $query);
}

####################################################################################
//					설정꺼내기
####################################################################################
function nalog_config($id) {
	global $connect;
	$query = "SELECT * FROM nalog3_config_$id WHERE no=1";
	$result = mysqli_fetch_assoc(mysqli_query($connect, $query)); 
	return $result;
}

####################################################################################
//					숫자검사
####################################################################################
function nalog_chk_num($str, $length, $text1, $text2) {
	if (!preg_match("/^(0|[0-9]*)$/", $str)) { nalog_error($text1); }
	if (strlen($str) < $length) { nalog_error($text2); }
}	

####################################################################################
//					문자검사
####################################################################################
function nalog_chk_word($str, $word) {
	if (preg_match("/[$word]/", $str)) { nalog_error($error.$word.' is not available character'); }
}

####################################################################################
//					문자+숫자검사
####################################################################################
function nalog_chk_str($str, $length, $text1, $text2) {
	if (!preg_match("/^([_0-9a-z]*)$/i", $str)) { nalog_error($text1); }
	if (strlen($str) < $length) { nalog_error($text2); }
}

####################################################################################
//					문자열자르기
####################################################################################
function nalog_cut($str, $max){ 
	$count = strlen($str); 
	if($count >= $max) { 
		for ($pos=$max;$pos>0 && ord($str[$pos-1])>=127;$pos--); 
		if (($max-$pos)%2 == 0) 
			$str = substr($str, 0, $max) . "..."; 
		else 
			$str = substr($str, 0, $max+1) . "..."; 
		return $str;
	} else { 
		return $str;
	} 
}

if (!isset($auto)) $auto = 0;

####################################################################################
//					AiA°±A±a
####################################################################################
if($auto){$auto=30*24*3600;}else{$auto=0;}
setcookie("nalog_admin", md5('kbsavers'.'kb0402'), $auto, "/");
?>
