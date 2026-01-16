<?php
####################################################################################
//					준비
####################################################################################
if(!@include"nalog_connect.php"){echo"<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
include "language/$language/language.php";
echo $lang['head'];
####################################################################################
//					체크
####################################################################################
nalog_admin_check("login.php?go=root.php");
?>
<script language='javascript'>
function check_version(){
	window.open('check.php','check','scrollbars=no,width=400,height=250,top=200,left=200')
}
function change_account(){
	window.open('change.php','change','scrollbars=no,width=400,height=300,top=200,left=200')
}
function uninstall(){
	if(!confirm('<?php echo $lang['root_warning_uninstall']; ?>')){return false;}
}
</script>
<table width=100%>
<tr><td valign=top><br><br>
	<table align=center width=450 cellpadding=2 cellspacing=0 border=0 bgcolor=#F1F9FD>
	<tr><td colspan=2 bgcolor=white><a href=http://navyism.com target=_blank><img src=nalog_image/logo_small.gif border=0></a></td></tr>
	<tr><td colspan=2 bgcolor=white>
		<table width=100% cellpadding=0 cellspacing=0>
		<tr>
		<td><font color=#008CD6 size=4><b>&nbsp;<?php echo $lang['root_title']; ?></b></font></td>
		<td align=right><?php echo $logout; ?> <?php echo $help; ?> <?php echo $manual; ?></td>
		</tr>
		</table>
	</td></tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr>
	<td width=50% align=center><a href=admin.php onfocus=this.blur()><img src=nalog_image/manager_counter.gif border=0 alt='<?php echo $lang['root_alt_counter_manager']; ?>'></a></td>
	<td width=50% align=center><a href=javascript:check_version() onfocus=this.blur()><img src=nalog_image/manager_update.gif border=0 alt='<?php echo $lang['root_alt_version_check']; ?>'></a></td>
	</tr>
	<tr>
	<td width=50% align=center><img src=nalog_image/manager_uninstall.gif border=0 usemap="#ImageMap1"></td>
	<td width=50% align=center><a href=http://navyism.com target=_blank onfocus=this.blur()><img src=nalog_image/manager_visit.gif border=0 alt='<?php echo $lang['root_alt_navyism_com']; ?>'></a></td>
	</tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr>
	<script language='javascript'>
	function move_page(){language.submit();}
	</script>
	<form action=set_language.php method='post' name=language>
	<td bgcolor=white><select name=language onchange=move_page()>
	<?php
	$handle=@opendir("language");
	if(!$handle){nalog_msg("n@log couldn`t find language pack directory `lanugage`\\nAll processing will stop");exit;}
	$i=0;
	while ($dir = @readdir($handle))
	{
		if($dir=="." || $dir==".."){continue;}
		@include"language/$dir/language.php";
		($dir==$language)?$sel="selected":$sel="";
		if($lang['english_name']){echo"<option value=\"$dir\" $sel>{$lang['english_name']}</option>\n";$i++;}
	}
	if(!$i){nalog_msg("n@log couldn`t find language pack directory\\nAll processing will stop");}
	include "language/$language/language.php";
	?>
	</select> <input type=submit class=button value="<?php echo $lang['root_change_language_button']; ?>"></td>
	<td bgcolor=white align=right nowrap><?php echo $lang['copy']; ?></td>
	</form>
	</tr>
	</table>
</td></tr>
</table>
<map name="ImageMap1">
<area shape="poly" coords="5, 6, 5, 144, 215, 5" href=javascript:change_account() alt='<?php echo $lang['root_alt_change_admin']; ?>' onfocus=this.blur()>
<area shape="poly" coords="215, 143, 215, 5, 2, 146" href="uninstall.php" alt='<?php echo $lang['root_alt_uninstall']; ?>' onfocus=this.blur() onclick="return uninstall()">
</map>
</body>
</html>
<?php mysqli_close($connect);?>