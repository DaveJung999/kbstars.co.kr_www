<?php
####################################################################################
//					준비
####################################################################################
if (!@include "nalog_connect.php") {
	echo "<script language='javascript'>alert('Please install n@log first :)')</script>
	<meta http-equiv='refresh' content='0;url=install.php'>";
	exit;
}
include "lib.php";
if (!@include "nalog_language.php") {
	include "header.inc";
}
@include "language/$language/language.php";

echo $lang['head'];
?>

<script language="javascript">
function on(){login.id.focus();}
function chk(){
	if(!login.id.value){
		alert('n@log error : \n\n<?php echo $lang['login_error_id']; ?>');
		login.id.focus();
		return false;
	}
	if(!login.pass.value){
		alert('n@log error : \n\n<?php echo $lang['login_error_pass']; ?>');
		login.pass.focus();
		return false;
	}
}
function auto_log(){
	if(login.auto.checked){
		result = confirm('n@log warning : \n\n<?php echo $lang['login_warning_auto']; ?>');
		if(!result){return false;}
	}
}
</script>

<body onload="on()">
<table width="100%" height="100%">
<form name="login" method="post" action="login_ing.php" onsubmit="return chk()" autocomplete="on">
<input type="hidden" name="history" value="<?= $_SERVER['HTTP_REFERER'] ?? '' ?>">
<input type="hidden" name="go" value="<?= $go ?>">
<input type="hidden" name="language" value="<?= $language ?>">
<tr><td valign="middle">

<table align="center" width="250" cellpadding="2" cellspacing="0" border="0" bgcolor="#F1F9FD">
<tr><td colspan="2" bgcolor="white"><font color="#008CD6" size="4"><b>&nbsp;<?= $lang['login_title'] ?></b></font></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td align="right"><font color="#008CD6"><?= $lang['login_id'] ?>&nbsp;&nbsp;</td><td><input type="text" class="input" name="id" size="15"></td></tr>
<tr><td align="right"><font color="#008CD6"><?= $lang['login_pass'] ?>&nbsp;&nbsp;</td><td><input type="password" class="input" name="pass" size="15"></td></tr>
<tr><td align="right"><font color="#008CD6"><?= $lang['login_auto'] ?>&nbsp;&nbsp;</td><td><input type="checkbox" name="auto" value="1" onclick="return auto_log()"></td></tr>
<tr><td colspan="2" height="5"></td></tr>
<tr><td></td><td><input type="submit" value="Login" class="button"> <input type="reset" value="Reset" class="button"></td></tr>
<tr><td colspan="2" height="8"></td></tr>
<tr><td colspan="2" height="3" bgcolor="#2CBBFF"></td></tr>
<tr><td colspan="2" bgcolor="white" align="right"><?= $lang['copy'] ?></td></tr>
</table>

</td></tr>
</form>
</table>
</body>
</html>

<?php
####################################################################################
//					끝
####################################################################################
mysqli_close($connect);
?>
