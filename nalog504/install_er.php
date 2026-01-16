<?php
####################################################################################
//					준비
####################################################################################
include "lib.php";
if(!@include "language/$language/language.php"){ nalog_go("install.php"); }
echo $lang['head'];
?>

<script language="javascript">
function on(){ document.install.db_id.focus(); }
function chk(){
	if(!document.install.host.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_db_host']; ?>'); document.install.host.focus(); return false; }
	if(!document.install.db_id.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_db_id']; ?>'); document.install.db_id.focus(); return false; }
	if(!document.install.db_pass.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_db_pass']; ?>'); document.install.db_pass.focus(); return false; }
	if(!document.install.db_name.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_db_name']; ?>'); document.install.db_name.focus(); return false; }
	if(!document.install.admin_id.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_admin_id']; ?>'); document.install.admin_id.focus(); return false; }
	if(!document.install.admin_pass.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_admin_pass']; ?>'); document.install.admin_pass.focus(); return false; }
	if(!document.install.admin_pass2.value){ alert('n@log error : \n\n<?php echo $lang['install_mysql_error_admin_repass']; ?>'); document.install.admin_pass2.focus(); return false; }
	if(document.install.admin_pass.value != document.install.admin_pass2.value){
		alert('n@log error : \n\n<?php echo $lang['install_mysql_error_admin_match']; ?>'); 
		document.install.admin_pass2.value = ""; 
		document.install.admin_pass.select(); 
		return false;
	}
}
</script>

<body onload="on()">
<br><br>
<table align="center" width="500" cellpadding="2" cellspacing="0" border="0" bgcolor="#F1F9FD">
<form name="install" method="post" action="install_ing.php" onsubmit="return chk()">
<input type="hidden" name="language" value="<?php echo $language; ?>">
<tr><td colspan="2" bgcolor="white">
	<a href="http://navyism.com" target="_blank"><img src="nalog_image/logo_small.gif" border="0"></a>
</td></tr>
<tr><td colspan="2" bgcolor="white">
	<table width="100%" cellpadding="0" cellspacing="0">
	<tr>
	<td><font color="#008CD6" size="4"><b>&nbsp;<?php echo $lang['install_mysql_title']; ?></b></font></td>
	<td align="right"><?php echo $help; ?></td>
	</tr>
	</table>
</td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2">
	<table width="100%" cellpadding="5" cellspacing="0">
	<tr><td><?php echo $lang['install_mysql_text']; ?></td></tr>
	</table>
</td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2" bgcolor="#E3F1FF" align="center"><b><?php echo $lang['install_mysql_account_mysql']; ?></b></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_db_host']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="text" class="input" name="host" size="20" value="localhost"></td>
</tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_db_id']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="text" class="input" name="db_id" size="15"></td>
</tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_db_pass']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="password" class="input" name="db_pass" size="15"></td>
</tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_db_name']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="text" class="input" name="db_name" size="15"></td>
</tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td colspan="2" bgcolor="#E3F1FF" align="center"><b><?php echo $lang['install_mysql_account_nalog']; ?></b></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_admin_id']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="text" class="input" name="admin_id" size="15"></td>
</tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_admin_pass']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="password" class="input" name="admin_pass" size="15"></td>
</tr>
<tr>
	<td align="right"><font color="#008CD6"><?php echo $lang['install_mysql_input_admin_repass']; ?>&nbsp;&nbsp;</font></td>
	<td><input type="password" class="input" name="admin_pass2" size="15"></td>
</tr>
<tr><td colspan="2" height="5"></td></tr>
<tr>
	<td></td>
	<td><input type="submit" value="INSTALL" class="button"> <input type="reset" value="RESET" class="button"></td>
</tr>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" bgcolor="white" align="right"><?php echo $lang['copy']; ?></td></tr>
</table>
</body>
</html>
