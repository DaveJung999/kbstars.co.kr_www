<?php


####################################################################################
//					헤더
####################################################################################
header('P3P: CP="NOI CURa ADMa DEVa TAIa OUR DELa BUS IND PHY ONL UNI COM NAV INT DEM PRE"');

// $lang, $plugin, $id 변수를 사용 전에 항상 초기화하여 Notice: Undefined variable 오류를 방지합니다.
$lang = [];
$plugin = [];
$id = ''; 

####################################################################################
//					준비
####################################################################################
// set_language 처리
if (isset($_REQUEST['set_language'])) {
	setcookie("nalog_my_language", $_REQUEST['set_language'], 0, "/");
	$language = $_REQUEST['set_language'];
} elseif (isset($_COOKIE['nalog_my_language'])) {
	$language = $_COOKIE['nalog_my_language'];
} else {
	$language = '';
}


if (!@include "nalog_connect.php") {
	echo "<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";
	exit;
}
include "lib.php";

if (!$language) {
	include "nalog_language.php";
}
// 언어 파일 include 시 @ 제거 또는 오류 처리 강화
if (!@include "language/$language/language.php") {
	nalog_go("install.php");
}

// $lang['head']에 안전하게 접근
echo $lang['head'] ?? '';

####################################################################################
//					체크
####################################################################################
$is_admin = nalog_admin_check4();
$counter_val = isset($_REQUEST['counter']) ? $_REQUEST['counter'] : 0;
$set = nalog_config($counter_val);

$time_zone = 0;
if (!empty($set['time_zone2'])) {
	$time_zone = $set['time_zone2'] * 3600;
	if (empty($set['time_zone1'])) {
		$time_zone *= -1;
	}
}

####################################################################################
//					갯수제한
####################################################################################
if (!empty($set['counter_limit'])) {
	$limit = nalog_total("nalog3_counter_" . $counter_val, "");
	$limit -= $set['counter_limit'];
	if ($limit > 0) {
		$query = "SELECT no FROM nalog3_counter_" . $counter_val . " ORDER BY no LIMIT 1";
		$result_min = mysqli_query($connect, $query);
		$min = mysqli_fetch_array($result_min);
		$min = $min['no'];

		$query = "SELECT no FROM nalog3_counter_" . $counter_val . " ORDER BY no LIMIT $limit,1";
		$result_max = mysqli_query($connect, $query);
		$max = mysqli_fetch_array($result_max);
		$max = $max['no'] - 1;

		$query = "DELETE FROM nalog3_counter_" . $counter_val . " WHERE no BETWEEN $min AND $max";
		@mysqli_query($connect, $query);
	}
}

if (!empty($set['log_limit'])) {
	// 로그 테이블 삭제
	$log_tables = ["nalog3_log_", "nalog3_dlog_"];
	foreach ($log_tables as $table_prefix) {
		$limit2 = nalog_total($table_prefix . $counter_val, "");
		$limit2 -= $set['log_limit'];
		if ($limit2 > 0) {
			$query = "SELECT time FROM {$table_prefix}{$counter_val} WHERE bookmark='0' ORDER BY time LIMIT 1";
			$result_min = mysqli_query($connect, $query);
			$min = mysqli_fetch_array($result_min);
			$min = $min['time'];

			$query = "SELECT time FROM {$table_prefix}{$counter_val} WHERE bookmark='0' ORDER BY time LIMIT $limit2,1";
			$result_max = mysqli_query($connect, $query);
			$max_temp = mysqli_fetch_array($result_max);
			$max = ($max_temp['time'] ?? 0) - 1;

			$query = "DELETE FROM {$table_prefix}{$counter_val} WHERE time BETWEEN $min AND $max";
			@mysqli_query($connect, $query);
		}
	}
}

####################################################################################
//					권한검사
####################################################################################
$mode = isset($_REQUEST['mode']) ? (int)$_REQUEST['mode'] : 0; // 정수형으로 명확히 변환
$admin = false;

$auth_keys = [
	1 => 'auth_time',
	2 => 'auth_day',
	3 => 'auth_week',
	4 => 'auth_month',
	5 => 'auth_year',
	6 => 'auth_log',
	7 => 'auth_dlog',
	8 => 'auth_os',
	9 => 'auth_member',
	10 => null
];

if ($mode === 10 || (!empty($auth_keys[$mode]) && !empty($set[$auth_keys[$mode]]))) {
	$admin = true;
}

if ($admin) {
	nalog_admin_check("login.php?go=admin_counter.php?counter=" . $counter_val);
}
if (!$set) {
	nalog_error($lang['counter_main_not_exist'] ?? 'Counter not exist.');
}

// $lang 배열 키 접근 시 오류 방지를 위해 맵 정의 시 ?? 연산자 사용 (PHP 7.0+)
$title_map = [
	0 => $lang['counter_main_title'] ?? 'Main',
	1 => $lang['counter_main_title_hour'] ?? 'Hour',
	2 => $lang['counter_main_title_day'] ?? 'Day',
	3 => $lang['counter_main_title_week'] ?? 'Week',
	4 => $lang['counter_main_title_month'] ?? 'Month',
	5 => $lang['counter_main_title_year'] ?? 'Year',
	6 => $lang['counter_main_title_refer'] ?? 'Referer Log',
	7 => $lang['counter_main_title_refer_detail'] ?? 'Detail Log',
	8 => $lang['counter_main_title_os'] ?? 'OS/Browser',
	9 => $lang['counter_main_title_visitor'] ?? 'Visitor',
	10 => $lang['counter_main_title_config'] ?? 'Config'
];

$title = "<a href='admin_counter.php?counter=$counter_val'>{$title_map[0]}</a>";
if ($mode > 0) {
	$title .= " > " . ($mode !== 10 ? $title_map[$mode] : $title_map[10] . " : $counter_val");
}

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$handle = @opendir("plug_in");
$found = false;

if ($handle && $id) {
	while ($dir = @readdir($handle)) {
		if ($dir == "." || $dir == "..") continue;
		
		// admin_menu.php 오류 방지를 위해 포함 전에 $plugin을 초기화하고, info.php가 $plugin을 덮어쓰도록 유도
		$plugin = []; 
		include "plug_in/$dir/info.php"; 
		
		// Undefined index 방지
		$plugin_name = $plugin['name'] ?? '';
		$plugin_id = $plugin['id'] ?? '';
		$plugin_language = $plugin['language'] ?? '';

		if (!trim($plugin_name)) continue;
		
		if ($id == $plugin_id && $language == $plugin_language) {
			$title = "<a href='admin_counter.php?counter=$counter_val'>{$title_map[0]}</a> > {$plugin_name}";
			$plugin['dir'] = $dir;
			$found = true;
			break;
		}
	}
}

if (!$found) unset($id);
$plugin_temp = $plugin;
?>

<table width="100%" height="100%">
<tr><td valign="top"><br><br>
<iframe width="1" height="1" marginwidth="0" marginheight="0" hspace="0" vspace="0" frameborder="0" scrolling="no" src="http://navyism.com/support/nalog/catch.php"></iframe>
<table align="center" width="95%" cellpadding="2" cellspacing="0" border="0" bgcolor="#F1F9FD">
<tr><td colspan="2" bgcolor="white"><a href="http://navyism.com" target="_blank"><img src="nalog_image/logo_small.gif" border="0"></a></td></tr>
<tr><td colspan="2" bgcolor="white">
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td><font color="#008CD6" size="4"><b>&nbsp;<?php echo $title; ?></b></font></td>
<td align="right">
<?php 
// nalog_admin 쿠키 체크는 isset을 통해 안전하게 처리
$is_logged_in = isset($_COOKIE['nalog_admin']); 
?>
<?php if (!$is_logged_in): ?>
<font color="#008CD6" size="1"><a href="login.php?go=admin_counter.php?counter=<?php echo $counter_val; ?>">LOGIN</a></font>
<?php else: ?>
<font color="#008CD6" size="1"><a href="logout.php?go=admin_counter.php?counter=<?php echo $counter_val; ?>">LOGOUT</a></font>
<?php endif; ?>
</td>
</tr>
</table>
</td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2"><?php include "admin_menu.php"; ?></td></tr>
<tr><td colspan="2">
<?php
$plugin = $plugin_temp;
if (!$id) {
	@include "admin_main.php";
} elseif (!empty($plugin['dir'])) { // $plugin['dir']가 정의되었는지 확인
	@include "plug_in/" . $plugin['dir'] . "/main.php";
}
?>
</td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<form method="post" name="language">
<tr bgcolor="white">
<td nowrap>
<font size="1"><?php echo date($lang['counter_main_date_format1'] ?? 'Y-m-d H:i:s', time() + $time_zone); ?> : <?php echo number_format($set['total'] ?? 0); ?> Visitors : Counter <b><?php echo $counter_val; ?></b></font>
<br>
<select name="set_language" onchange="language.submit()">
<?php
$handle = @opendir("language");
// 파일이 없을 때 메시지를 출력하는 대신, 오류를 방지하고 계속 진행
if ($handle) { 
	$i = 0;
	while ($dir = @readdir($handle)) {

		if ($dir == "." || $dir == "..") continue;
		// 임시 $lang을 초기화하고 언어 파일을 로드하여 충돌 방지
		$temp_lang = [];
		@include "language/$dir/language.php";
		if (isset($temp_lang['english_name'])) {
			$sel = ($dir == $language) ? "selected" : "";
			echo "<option value=\"$dir\" $sel>{$temp_lang['english_name']}</option>\n";
			$i++;
		}
	}
}

// 다시 원래의 $lang 배열을 로드하거나, $lang이 비어 있으면 기본값으로 초기화
if (!include "language/$language/language.php") {
	// 로드 실패 시 $lang에 기본값 제공
	$lang = $title_map; 
}

?>
</select>
<input type="submit" class="button" value="<?php echo $lang['root_change_language_button'] ?? 'Change Language'; ?>">
</td>
<td align="right" valign="top" nowrap><?php echo $lang['copy'] ?? 'Copy'; ?></td>
</tr>
</form>
</table>
</td></tr>
</table>
</body>
</html>
<?php mysqli_close($connect); ?>