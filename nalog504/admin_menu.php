<?php
####################################################################################
//					메뉴
####################################################################################
$bgcolors = [];
$bs = [];
// $mode가 정의되지 않았을 경우를 대비하여 0으로 초기화 (호출하는 파일에서 전달받아야 함)
$mode = $mode ?? 0;

for ($i = 0; $i <= 10; $i++) {
	if ($mode == $i) {
		$bgcolors[$i] = "bgcolor=#F1F9FD";
		$bs[$i] = "<b>";
	}
}

// 외부에서 $counter, $set, $lang 변수가 전달된다고 가정하고 안전하게 접근
$counter = $counter ?? '';
$set = $set ?? [];
$lang = $lang ?? [];
$is_admin = $_COOKIE['nalog_admin'] ?? false; // 쿠키 값 안전하게 확인
?>

<table align="center" width="100%" cellpadding="2" cellspacing="0" border="1" bordercolor="white" bgcolor="#C9F0FF">
<tr>
<?php if ($is_admin || !($set['auth_time'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[1] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=1"><?php echo $bs[1] ?? ''; ?><?php echo $lang['counter_main_menu_hour'] ?? 'Hour'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_day'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[2] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=2"><?php echo $bs[2] ?? ''; ?><?php echo $lang['counter_main_menu_day'] ?? 'Day'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_week'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[3] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=3"><?php echo $bs[3] ?? ''; ?><?php echo $lang['counter_main_menu_week'] ?? 'Week'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_month'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[4] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=4"><?php echo $bs[4] ?? ''; ?><?php echo $lang['counter_main_menu_month'] ?? 'Month'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_year'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[5] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=5"><?php echo $bs[5] ?? ''; ?><?php echo $lang['counter_main_menu_year'] ?? 'Year'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_log'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[6] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=6"><?php echo $bs[6] ?? ''; ?><?php echo $lang['counter_main_menu_refer'] ?? 'Referer'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_dlog'] ?? false)) { ?>
	<td width="10%" align="center" <?php echo $bgcolors[7] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=7"><?php echo $bs[7] ?? ''; ?><?php echo $lang['counter_main_menu_refer_detail'] ?? 'Detail'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_os'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[8] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=8"><?php echo $bs[8] ?? ''; ?><?php echo $lang['counter_main_menu_os'] ?? 'OS'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin || !($set['auth_member'] ?? false)) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[9] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=9"><?php echo $bs[9] ?? ''; ?><?php echo $lang['counter_main_menu_visitor'] ?? 'Visitor'; ?></a>&nbsp;</td>
<?php } ?>
<?php if ($is_admin) { ?>
	<td width="9%" align="center" <?php echo $bgcolors[10] ?? ''; ?> nowrap>&nbsp;<a href="admin_counter.php?counter=<?php echo $counter; ?>&mode=10"><?php echo $bs[10] ?? ''; ?><?php echo $lang['counter_main_menu_config'] ?? 'Config'; ?></a>&nbsp;</td>
<?php } ?>
</tr>
</table>

<?php
####################################################################################
//					플러그인 메뉴
####################################################################################
// $language 변수는 외부(admin_counter.php)에서 정의된다고 가정
$language = $language ?? 'korea';
$id = $id ?? ''; // 외부에서 전달받는 $id 변수 초기화

$number = 0;
$handle = @opendir("plug_in");
if ($handle) {
	while ($dir = @readdir($handle)) {
		if ($dir == "." || $dir == "..") { continue; }
		$plugin = []; // 루프마다 $plugin 배열 초기화
		@include "plug_in/$dir/info.php";
		if (($plugin['language'] ?? '') == $language) { $number++; } // 안전한 배열 접근
	}
}

if ($number) {
	$handle = @opendir("plug_in");
	?>
	<table align="center" width="100%" cellpadding="2" cellspacing="0" border="1" bordercolor="white" bgcolor="white">
	<tr>
	<form name="plugins" action="admin_counter.php">
	<input type="hidden" name="counter" value="<?php echo $counter; ?>">
	<td width="1%" nowrap>
	<select name="id" onchange="plugins.submit()">
	<option value=''><?php echo $lang['counter_main_plug_in'] ?? 'Plug In'; ?></option>
	<option value=''></option>
	<?php
	if ($handle) { // opendir 결과를 다시 사용
		@rewinddir($handle); // 디렉토리 포인터를 처음으로 되돌림
	} else {
		$handle = @opendir("plug_in"); // 디렉토리가 닫혔을 경우 다시 엶
	}
	while ($dir = @readdir($handle)) {
		if ($dir == "." || $dir == "..") { continue; }
		$plugin = []; // 루프마다 $plugin 배열 초기화
		@include "plug_in/$dir/info.php";
		
		$plugin_language = $plugin['language'] ?? '';
		$plugin_name = $plugin['name'] ?? '';
		$plugin_id = $plugin['id'] ?? '';
		
		if ($plugin_language != $language) { continue; }
		if (!trim($plugin_name)) { continue; }
		// else { $dir_path = "plug_in/$dir"; } // $dir_path는 사용되지 않으므로 주석 처리

		$sel = ($id == $plugin_id) ? "selected" : "";
		echo "<option value=\"{$plugin_id}\" $sel>{$plugin_name}</option>";
	}
	@closedir($handle);
	?>
	</select> <?php if ($id) { ?><font size="1">&#9654;</font><?php } ?>
	</td>
	<td align="right" width="99%">
	<?php
	// $plugin_temp는 외부에서 전달/설정된 플러그인 정보입니다.
	$plugin = $plugin_temp ?? null;
	$plugin_dir = $plugin['dir'] ?? null;
	
	if ($id && $plugin_dir) { @include "plug_in/{$plugin_dir}/menu.php"; } // 안전한 경로 접근
	?>
	</td>
	</form>
	</tr>
	</table>
<?php
}
?>