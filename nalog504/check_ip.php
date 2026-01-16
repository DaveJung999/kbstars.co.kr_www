<?php
if(!@include"nalog_connect.php"){echo"<script language='javascript'>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
if(!@include"language/$language/language.php"){echo"<script>window.close()</script>";}
echo $lang['head'];
?>
<body>
<br><br>
<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
<tr><td colspan=2><a href=http://navyism.com target=_blank><img src=nalog_image/logo.gif border=0></a></td></tr>
<tr><td colspan=2><font size=5>&nbsp;<b><?php echo $lang['check_ip_title']; ?><?php echo $ip; ?></b></font></td></tr>
<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
</table>
<br>
<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
<tr><td colspan=2>
<?php
$info=@join("",@file("http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip"));
if(!$info){echo"<meta http-equiv='refresh' content='2;url=http://www.apnic.net/apnic-bin/whois.pl?searchtext=$ip'>";
echo"<br><center>$lang[check_ip_false_msg]</center><br>";
}
$info=strip_tags($info);
$info=preg_replace("/^.+% \[/i","% [",$info);
$info=preg_replace("/search for.+$/i","",$info);
$info=nl2br(trim($info));
echo $info;
?>
<br><br>
powered by &copy;1999-2003 APNIC Pty. Ltd. <a href=http://www.apnic.net target=_blank><b>www.apnic.net</b></a><br><br>
</td></tr>
<tr><td colspan=2 height=1 bgcolor=#2CBBFF></td></tr>
</table>
<table width="95%" cellpadding=0 cellspacing=0 border=0 align=center>
<tr><td><?php echo $lang['copy']; ?></td>
<td align=right><?php echo $lang['check_ip_right_arrow']; ?> <a href=http://navyism.com target=_blank><?php echo $lang['check_ip_support']; ?></a> <?php echo $lang['check_ip_right_arrow']; ?> <a href=javascript:window.close()><?php echo $lang['check_ip_close']; ?></a></td></tr>
</table>
<br><br>
</body>
</html>
<?php @mysqli_close($connect);?>