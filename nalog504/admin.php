<?php
####################################################################################
//					준비
####################################################################################
if(!@include"nalog_connect.php"){echo"<script lanugage=javascript>alert('Please install n@log first :)')</script>
<meta http-equiv='refresh' content='0;url=install.php'>";exit;}
include "lib.php";
if(!@include "nalog_language.php"){nalog_go("install.php");}
if(!@include"language/$language/language.php"){nalog_go("install.php");}
echo $lang['head']; // 배열 접근 수정


####################################################################################
//					체크
####################################################################################
nalog_admin_check("login.php?go=admin.php");

####################################################################################
//					꺼내기
####################################################################################
$tables=nalog_list_bd();
$total=count($tables);

####################################################################################
//					테이블 옵티마이징
####################################################################################
mysqli_query($connect, "OPTIMIZE TABLE nalog3_os") or die(mysqli_error($connect));
mysqli_query($connect, "OPTIMIZE TABLE nalog3_data") or die(mysqli_error($connect));

####################################################################################
//					카운터수
####################################################################################
if($page){nalog_chk_num($page,0,$lang['counter_manager_view_error'],0);} // 배열 접근 수정
else{$page=10;}


####################################################################################
//					목록수
####################################################################################
$pageviewsu=10; 


####################################################################################
//					인덱스
####################################################################################
$pagesu=ceil($total/$page); 
$start=($page*$pagenum); 
$no=$total-$start;
$pagegroup=ceil(($pagenum+1)/$pageviewsu); 
$pagestart=($pageviewsu*($pagegroup-1))+1; 
$pageend=$pagestart+$pageviewsu-1;
$nowpage=$pagenum+1; 
?>

<script language=javascript>

function chk_drop(){
if(!confirm('n@log warning : \n\n<?php echo $lang['counter_manager_warning_drop']; ?>')){return false;} // 배열 접근 수정
}
function chk_del(){
if(!confirm('n@log warning : \n\n<?php echo $lang['counter_manager_warning_clean']; ?>')){return false;} // 배열 접근 수정
}
function chk_new(){
if(!chk.new_board.value){alert('n@log error : \n\n<?php echo $lang['counter_manager_error_create']; ?>');chk.new_board.focus();return false;} // 배열 접근 수정
}

</script>

<table width=100% height=100%>
<tr><td valign=top><br><br>
	<table align=center width=95% cellpadding=2 cellspacing=0 border=0 bgcolor=#F1F9FD>
	<tr><td colspan=2 bgcolor=white><a href=http://navyism.com target=_blank><img src=nalog_image/logo_small.gif border=0></a></td></tr>
	<tr><td colspan=2 bgcolor=white>
		<table width=100% cellpadding=0 cellspacing=0>
		<tr>
		<td><font color=#008CD6 size=4><b>&nbsp;<a href=root.php><?php echo $lang['root_title']; ?></a> > <?php echo $lang['counter_manager_title']; ?></b></font></td> <td align=right><?php echo $logout; ?> <?php echo $help; ?> <?php echo $manual; ?></td>
		</tr>
		</table>
	</td></tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr><td colspan=2>
		<table align=center width=98% cellpadding=0 cellspacing=0 border=0>
		<form method=get action=admin.php>
		<tr>
		<td><?php echo $lang['counter_manager_paging1']; ?><b><?php echo $total; ?></b><?php echo $lang['counter_manager_paging2']; ?><b><?php echo $nowpage; ?></b><?php echo $lang['counter_manager_paging3']; ?><b><?php echo $pagesu; ?></b><?php echo $lang['counter_manager_paging4']; ?></td> <td align=right><?php echo $lang['counter_manager_view']; ?> <input type=text size=3 class=input maxlength=3 name=page value=<?php echo $page; ?> onKeyPress="if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;"> <input type=submit class=button value="<?php echo $lang['counter_manager_view_button']; ?>"></td> </tr>
		</form>
		</table>
		<table align=center width=98% cellpadding=2 cellspacing=0 border=1 bordercolor=white>
		<form method=post action=admin_ing.php name=chk onsubmit="return chk_new()">
		<input type=hidden name=mode value=make>
		<tr bgcolor=#C9F0FF>
		<td width=1% nowrap align=center><?php echo $lang['counter_manager_table_no']; ?></td> <td width=91% align=center><?php echo $lang['counter_manager_table_name']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_config']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_example']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_drop']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_clean']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_total']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_today']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_today_peak']; ?></td> <td width=1% nowrap align=center><?php echo $lang['counter_manager_table_peak']; ?></td> </tr>
		<?php
		
		####################################################################################
		//					넘겨질변수
		####################################################################################
		$send="&page=$page&";
	

		####################################################################################
		//					출력값
		####################################################################################
		for($i=$start;$i<$start+$page;$i++)
		{
		if(!$tables[$i]){break;}
		$board_name=$tables[$i];


		####################################################################################
		//					출력
		####################################################################################
		$yy=date('Y'); // date() 인자 수정
		$mm=date('m'); // date() 인자 수정
		$dd=date('d'); // date() 인자 수정

		$query="select * from nalog3_data where counter='$board_name' and yy=$yy and mm=$mm and dd=$dd"; 
		$result_today=mysqli_query($connect, $query);
		$counter_today_temp=mysqli_fetch_array($result_today); 
		$counter_today=$counter_today_temp['hit']; // 배열 접근 수정

		$set=nalog_config("$board_name");
		$counter_max=$set['peak']; // 배열 접근 수정
		$counter_total=$set['total']; // 배열 접근 수정

		$query="select max(hit) from nalog3_data where counter='$board_name'"; 
		$result_today_peak=mysqli_query($connect, $query);
		$counter_today_peak=mysqli_fetch_array($result_today_peak); 
		$counter_today_peak=$counter_today_peak[0];
				

		echo"
		<tr bgcolor=white>
		<td width=1% nowrap align=center>$no</td>
		<td width=91%><a href=admin_counter.php?counter=$board_name target=_blank>$board_name</a></td>
		<td width=1% nowrap align=center><a href=admin_counter.php?counter=$board_name&mode=10 target=_blank>$lang[counter_manager_table_config]</a></td>
		<td width=1% nowrap align=center><a href=example.php?counter=$board_name target=_blank>$lang[counter_manager_tablecell_view]</a></td>
		<td width=1% nowrap align=center><a href=admin_ing.php?new_board=$board_name&mode=drop onclick=\"return chk_drop()\">$lang[counter_manager_tablecell_drop]</a></td>
		<td width=1% nowrap align=center><a href=admin_ing.php?new_board=$board_name&mode=del onclick=\"return chk_del()\">$lang[counter_manager_tablecell_clean]</a></td>
		<td width=1% nowrap align=right>".number_format($counter_total)."</td>
		<td width=1% nowrap align=right>".number_format($counter_today)."</td>
		<td width=1% nowrap align=right>".number_format($counter_today_peak)."</td>
		<td width=1% nowrap align=right>".number_format($counter_max)."</td>
		</tr>
		";

		$no--;

		####################################################################################
		//					테이블 옵티마이징
		####################################################################################
		mysqli_query($connect, "OPTIMIZE TABLE nalog3_counter_$board_name") or die(mysqli_error($connect));
		mysqli_query($connect, "OPTIMIZE TABLE nalog3_config_$board_name") or die(mysqli_error($connect));
		mysqli_query($connect, "OPTIMIZE TABLE nalog3_log_$board_name") or die(mysqli_error($connect));
		mysqli_query($connect, "OPTIMIZE TABLE nalog3_dlog_$board_name") or die(mysqli_error($connect));
		mysqli_query($connect, "OPTIMIZE TABLE nalog3_now_$board_name") or die(mysqli_error($connect));
		}

		####################################################################################
		//					총갯수
		####################################################################################
		echo"<input type=hidden name=count value=$i>";
		?>
		</table>
	</td></tr>
	<tr><td colspan=2 align=center><?php nalog_index()?> </td></tr>
	<tr><td colspan=2 height=5></td></tr>
	<tr><td colspan=2>
		<table align=center width=98% cellpadding=2 cellspacing=0 border=0 bordercolor=white bgcolor=#C9F0FF>
		<tr>	
		<td>
		<input type=hidden name=pagenum value=<?=$pagenum?>>
		<input type=input class=input name=new_board size=20 value='<?=$word?>' onclick=select()> <input type=submit value="<?=$lang['counter_manager_create_button']?>" class=button>  </td></tr>
		</table>
	</td></tr></form>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td bgcolor=white><a href=admin.php>List</a></td><td bgcolor=white align=right><?=$lang['copy']?></td></tr> </table>
</td></tr>
</table>
</body>
</html>
<?php @mysqli_close($connect);?>