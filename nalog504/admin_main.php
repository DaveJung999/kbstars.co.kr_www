<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>

<?php
// PHP 7 호환성을 위해 변수 초기화 (슈퍼글로벌 변수 사용)
$mode = $_REQUEST['mode'] ?? '';
$all = $_REQUEST['all'] ?? '';
$yy = $_REQUEST['yy'] ?? '';
$mm = $_REQUEST['mm'] ?? '';
$dd = $_REQUEST['dd'] ?? '';
$counter = $_REQUEST['counter'] ?? '';
$sort = $_REQUEST['sort'] ?? '';
$word = $_REQUEST['word'] ?? '';
$today_only = $_REQUEST['today_only'] ?? '';
$page = $_REQUEST['page'] ?? '';
$pagenum = $_REQUEST['pagenum'] ?? 0;
$nw = $_REQUEST['nw'] ?? '';
$sa = $_REQUEST['sa'] ?? 'or'; // 기본값 or로 설정
$day_start = $_REQUEST['day_start'] ?? '';
$day_end = $_REQUEST['day_end'] ?? '';
$members = $_REQUEST['members'] ?? '';
$set = $set ?? []; // 설정 배열이 다른 곳에서 로드된다고 가정

####################################################################################
//					메인
####################################################################################
if(!$mode){
echo"
<tr><td>
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td align=center><br><br><br><br><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=http://navyism.com target=_blank onfocus=this.blur()><img src=nalog_image/logo.gif border=0></a><br><br><br><br><br><br>
</td></tr>
</table>
</td></tr>
";
}


####################################################################################
//					시간통계
####################################################################################
if($mode==1){

	$time=time()+$time_zone;

	if(!$all && (!$yy || !$mm || !$dd))
	{
		$yy=date('Y',time()+$time_zone);
		$mm=date('m',time()+$time_zone);
		// ereg_replace를 preg_replace로 변경
		$mm=preg_replace("/^0/", "", $mm);
		$dd=date('d',time()+$time_zone);
		$hh=date('H',time()+$time_zone);
		$week=date('w',time()+$time_zone);
		$its_today=" [$lang[counter_main_1_today]]";
	}

	if(!$all){
		$today_info=date($lang['counter_main_1_date_format'],strtotime("$yy-$mm-$dd",time()+$time_zone))."$its_today";
	}
	else{
		$today_info=$lang['counter_main_1_sum'];
	}

	$sub="and yy='$yy' and mm='$mm' and dd='$dd'";

	if($all){
		$sub="";
	}

	$now=date('H',time()+$time_zone);

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_data where counter='$counter' $sub";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));

	$today_hit=$today_hit_temp["sum(hit)"];
	if(!$today_hit)$today_hit="0";

echo"
<tr><td>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td>
$lang[counter_main_1_date]<b>$today_info</b>$lang[counter_main_1_total]<b>".number_format($today_hit)."</b>$lang[counter_main_1_total_visitor]
</td></tr>
</table>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
";




	for($i=0;$i<=23;$i++){
		// mysql_query를 mysqli_query로 변경
		$query="select sum(h$i) from nalog3_data where counter='$counter' $sub";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));

		$temp=$today_temp["h$i"]=$today_hit_temp["sum(h$i)"];
		if($max<$temp){$max=$temp;}
	}


	for($i=0;$i<=11;$i++){
		$j=$i+12;


		$i_hit=$today_temp["h$i"];
		if(!$i_hit){$i_hit=0;}
		$j_hit=$today_temp["h$j"];
		if(!$j_hit){$j_hit=0;}

		if($today_hit){

			$i_per=round($i_hit/$today_hit*100,2);
			$j_per=round($j_hit/$today_hit*100,2);

			$i_graph=round($i_hit/$max*160);
			$j_graph=round($j_hit/$max*160);

			if($max==$i_hit){$ibn=2;}else{$ibn="";}
			if($max==$j_hit){$jbn=2;}else{$jbn="";}

			$i_graph="<img src=nalog_image/block$ibn.gif width='$i_graph' height=10>";
			$j_graph="<img src=nalog_image/block$jbn.gif width='$j_graph' height=10>";

		}

		if($now==$i){$bgcolor1="bgcolor=#C9F0FF";}else{$bgcolor1="";}
		if($now==$j){$bgcolor2="bgcolor=#C9F0FF";}else{$bgcolor2="";}

		$i_start=@date("U",mktime($i,0,0,$mm,$dd,$yy));
		$j_start=@date("U",mktime($j,0,0,$mm,$dd,$yy));
		$i_end=$i_start+3600;
		$j_end=$j_start+3600;

		if($i_hit && !$all){
			if($i<10)$i="0".$i;
			$i_msg=str_replace("{yy}",$yy,$lang['counter_main_1_view_visitor']);
			$i_msg=str_replace("{mm}",$mm,$i_msg);
			$i_msg=str_replace("{dd}",$dd,$i_msg);
			$i_msg=str_replace("{hh}",$i,$i_msg);
			$i_msg="<div title='$i_msg'>";
			$i_link="<a href=admin_counter.php?counter=$counter&mode=9&day_start=$i_start&day_end=$i_end>";}
		else{$i_link="";$i_msg="";}
		if($j_hit && !$all){
			$j_msg=str_replace("{yy}",$yy,$lang['counter_main_1_view_visitor']);
			$j_msg=str_replace("{mm}",$mm,$j_msg);
			$j_msg=str_replace("{dd}",$dd,$j_msg);
			$j_msg=str_replace("{hh}",$j,$j_msg);
			$j_msg="<div title='$j_msg'>";
			$j_link="<a href=admin_counter.php?counter=$counter&mode=9&day_start=$j_start&day_end=$j_end>";}
		else{$j_link="";$j_msg="";}

echo "<tr height=20>
<td width=1% nowrap $bgcolor1>$i_msg$i_link".date($lang['counter_main_1_hour_format'],strtotime("$i:00",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor1>".number_format($i_hit)."$lang[counter_main_1_visitor]</td>
<td width=48%>$i_graph <font size=1>$i_per%</font></td>
<td width=1% nowrap $bgcolor2>$j_msg$j_link".date($lang['counter_main_1_hour_format'],strtotime("$j:00",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor2>".number_format($j_hit)."$lang[counter_main_1_visitor]</td>
<td width=48%>$j_graph <font size=1>$j_per%</font></td>
</tr>";
	}

echo"
</table><br>
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=#C9F0FF>
<form method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value='$mode'>
<tr><td>&nbsp;
<select name=yy><option value=''>--------</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct yy from nalog3_data where counter='$counter' order by yy";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($yy==$data['yy']){$select="selected";}else{$select="";}
echo "<option value='$data[yy]' $select>$data[yy]</option>";
	}
echo"
</select> $lang[counter_main_year]

<select name=mm><option value=''>----</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct mm from nalog3_data where counter='$counter' order by mm";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($mm==$data['mm']){$select="selected";}else{$select="";}
echo "<option value='$data[mm]' $select>$data[mm]</option>";
	}
echo"
</select> $lang[counter_main_month]

<select name=dd><option value=''>----</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct dd from nalog3_data where counter='$counter' order by dd";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($dd==$data['dd']){$select="selected";}else{$select="";}
echo "<option value='$data[dd]' $select>$data[dd]</option>";
	}
echo"
</select> $lang[counter_main_day] &nbsp; <input type=submit class=button value='$lang[counter_main_button_view]'> <input type=button onclick=view_all() class=button value='$lang[counter_main_button_view_all]'>
<input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)>

</td></tr>

<script language=javascript>
function view_all(){
	location.href=\"<?php echo $_SERVER['PHP_SELF']; ?>?counter=<?php echo $counter; ?>&mode=<?php echo $mode; ?>&all=1\";
}
</script>
</form>
</table>
</td></tr>
";
}


####################################################################################
//					날자통계
####################################################################################
if($mode==2){

	$time=time()+$time_zone;

	if(!$all && (!$yy || !$mm))
	{
		$yy=date('Y',time()+$time_zone);
		$mm=date('m',time()+$time_zone);
		// ereg_replace를 preg_replace로 변경
		$mm=preg_replace("/^0/", "", $mm);
		$dd=date('d',time()+$time_zone);
		$week=date('w',time()+$time_zone);
		$its_today=" [$lang[counter_main_2_this_month]]";
	}
	else{$show_this_day=1;}

	if(!$all){
		$today_info=date($lang['counter_main_2_date_format'],strtotime("$yy-$mm-1",time()+$time_zone))."$its_today";
	}
	else{
		$today_info=$lang['counter_main_2_sum'];
	}

	$sub="and yy='$yy' and mm='$mm'";
	if($all){
		$sub="";
	}

	$now=date('d',time()+$time_zone);
	$now_month_end=date("U",mktime(1,1,1,$mm,1,$yy));
	$to=date('t',$now_month_end);

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_data where counter='$counter' $sub";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
	$today_hit=$today_hit_temp["sum(hit)"];

echo"
<tr><td>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td>
$lang[counter_main_2_month]<b>$today_info</b>$lang[counter_main_2_total]<b>".number_format($today_hit)."</b>$lang[counter_main_2_total_visitor]
</td></tr>
</table>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
";


	if(!$all){
		// mysql_query를 mysqli_query로 변경
		$query="select max(hit) from nalog3_data where counter='$counter' $sub";
		$max_result=mysqli_fetch_array(mysqli_query($connect, $query));
		$max=$max_result["max(hit)"];
	}
	else{
		$temp=0;
		for($i=1;$i<=31;$i++){
			// mysql_query를 mysqli_query로 변경
			$query="select sum(hit) from nalog3_data where counter='$counter' and dd='$i'";
			$sum_result=mysqli_fetch_array(mysqli_query($connect, $query));
			$sum=$sum_result["sum(hit)"];
			if($sum>$temp){$temp=$sum;}
		}
		$max=$temp;
	}


	for($i=1;$i<=16;$i++){
		$j=$i+16;

		//if(strlen($i)==1){$i="0".$i;}
		//if(strlen($j)==1){$j="0".$j;}


		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' $sub and dd='$i'";
		$i_hit_result=@mysqli_fetch_array(@mysqli_query($connect, $query));
		$i_hit=$i_hit_result["sum(hit)"];
		if(!$i_hit){$i_hit=0;}

		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' $sub and dd='$j'";
		$j_hit_result=@mysqli_fetch_array(@mysqli_query($connect, $query));
		$j_hit=$j_hit_result["sum(hit)"];
		if(!$j_hit){$j_hit=0;}

		if($today_hit){

			$i_per=round($i_hit/$today_hit*100,2);
			$j_per=round($j_hit/$today_hit*100,2);

			$i_graph=round($i_hit/$max*150);
			$j_graph=round($j_hit/$max*150);


			if($max==$i_hit){$ibn=2;}else{$ibn="";}
			if($max==$j_hit){$jbn=2;}else{$jbn="";}

			$i_graph="<img src=nalog_image/block$ibn.gif width='$i_graph' height=10>";
			$j_graph="<img src=nalog_image/block$jbn.gif width='$j_graph' height=10>";

		}

		if($now==$i && !$show_this_day){$bgcolor1="bgcolor=#C9F0FF";}else{$bgcolor1="";}
		if($now==$j && !$show_this_day){$bgcolor2="bgcolor=#C9F0FF";}else{$bgcolor2="";}



		if($j>$to){
			if($i_hit && !$all){
				$i_msg=str_replace("{yy}",$yy,$lang['counter_main_2_view_visitor']);
				$i_msg=str_replace("{mm}",$mm,$i_msg);
				$i_msg=str_replace("{dd}",$i,$i_msg);
				$i_msg="<div title='$i_msg'>";
				$link="<a href=admin_counter.php?counter=$counter&mode=1&yy=$yy&mm=$mm&dd=$i>";}
			else{$i_msg="";$link="";}
echo "<tr height=20>
<td align=right width=1% nowrap $bgcolor1>$i_msg$link".date($lang['counter_main_2_day_format'],strtotime("2003-1-$i",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor1>".number_format($i_hit)."$lang[counter_main_2_visitor]</td>
<td width=48%>$i_graph <font size=1>$i_per%</font></td>
<td width=1% nowrap $bgcolor2></td>
<td width=1% nowrap $bgcolor2></td>
<td width=48% $bgcolor2></td>
</tr>";
		}

		else{
			if($i_hit && !$all){
				$i_msg=str_replace("{yy}",$yy,$lang['counter_main_2_view_visitor']);
				$i_msg=str_replace("{mm}",$mm,$i_msg);
				$i_msg=str_replace("{dd}",$i,$i_msg);
				$i_msg="<div title='$i_msg'>";
				$i_link="<a href=admin_counter.php?counter=$counter&mode=1&yy=$yy&mm=$mm&dd=$i>";}
			else{$i_msg="";$i_link="";}
			if($j_hit && !$all){
				$j_msg=str_replace("{yy}",$yy,$lang['counter_main_2_view_visitor']);
				$j_msg=str_replace("{mm}",$mm,$j_msg);
				$j_msg=str_replace("{dd}",$j,$j_msg);
				$j_msg="<div title='$j_msg'>";
				$j_link="<a href=admin_counter.php?counter=$counter&mode=1&yy=$yy&mm=$mm&dd=$j>";}
			else{$j_msg="";$j_link="";}
echo "<tr>
<td align=right width=1% nowrap $bgcolor1>$i_msg$i_link".date($lang['counter_main_2_day_format'],strtotime("2003-1-$i",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor1>".number_format($i_hit)."$lang[counter_main_2_visitor]</td>
<td width=48%>$i_graph <font size=1>$i_per%</font></td>
<td align=right width=1% nowrap $bgcolor2>$j_msg$j_link".date($lang['counter_main_2_day_format'],strtotime("2003-1-$j",time()+$time_zone))."</td>
<td align=right width=1% nowrap $bgcolor2>".number_format($j_hit)."$lang[counter_main_2_visitor]</td>
<td width=48%>$j_graph <font size=1>$j_per%</font></td>
</tr>";
		}
	}

echo"
</table><br>
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=#C9F0FF>
<form method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value='$mode'>
<tr><td>&nbsp;
<select name=yy><option value=''>--------</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct yy from nalog3_data where counter='$counter' order by yy";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($yy==$data['yy']){$select="selected";}else{$select="";}
echo "<option value='$data[yy]' $select>$data[yy]</option>";
	}
echo"
</select> $lang[counter_main_year]

<select name=mm><option value=''>----</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct mm from nalog3_data where counter='$counter' order by mm";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($mm==$data['mm']){$select="selected";}else{$select="";}
echo "<option value='$data[mm]' $select>$data[mm]</option>";
	}
echo"
</select> $lang[counter_main_month] &nbsp; <input type=submit class=button value='$lang[counter_main_button_view]'> <input type=button onclick=view_all() class=button value='$lang[counter_main_button_view_all]'>

<input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)>
</td></tr>

<script language=javascript>
function view_all(){
	location.href=\"<?php echo $_SERVER['PHP_SELF']; ?>?counter=<?php echo $counter; ?>&mode=<?php echo $mode; ?>&all=1\";
}
</script>
</form>
</table>
</td></tr>
";
}


####################################################################################
//					요일통계
####################################################################################
if($mode==3){

	$week=date('w',time()+$time_zone);
	$sub="";

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_data where counter='$counter'";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
	$today_hit=$today_hit_temp["sum(hit)"];


	$total_avg=0;
	for($i=0;$i<=6;$i++){
		// mysql_query를 mysqli_query로 변경
		$query="select count(*) from nalog3_data where counter='$counter' and week='$i'";
		$total_day_result=mysqli_fetch_array(mysqli_query($connect, $query));
		$total_day=$total_day_result["count(*)"];

		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' and week='$i'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
		if($total_day){$temp=round($today_hit_temp["sum(hit)"]/$total_day,0);}
		else{$temp=0;}
		if($max<$temp){$max=$temp;}

		$total_avg=$total_avg+$temp;
	}

echo"
<tr><td>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td>
<b>$lang[counter_main_3_sum]</b>$lang[counter_main_3_total]<b>".number_format($today_hit)."</b>$lang[counter_main_3_total_visitor]$lang[counter_main_3_average]<b>".number_format($total_avg)."</b>$lang[counter_main_3_average_visitor]
</td></tr>
</table>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
";

	for($i=0;$i<=6;$i++){
		// mysql_query를 mysqli_query로 변경
		$query="select count(*) from nalog3_data where counter='$counter' and week='$i'";
		$total_day_result=mysqli_fetch_array(mysqli_query($connect, $query));
		$total_day=$total_day_result["count(*)"];

		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' and week='$i'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
		$i_hit=$today_hit_temp["sum(hit)"];
		if($total_day){$i_hit_avg=round($today_hit_temp["sum(hit)"]/$total_day,0);}
		else{$i_hit_avg=0;}
		if(!$i_hit){$i_hit=0;}

		if($today_hit){

			$i_per=round($i_hit/$today_hit*100,2);

			$i_graph=round($i_hit_avg/$max*400);
			if($max==$i_hit_avg){$ibn=2;}else{$ibn="";}

			$i_graph="<img src=nalog_image/block_big$ibn.gif width='$i_graph' height=30>";

		}

		if($week==$i){$bgcolor1="bgcolor=#C9F0FF";}else{$bgcolor1="";}



		if($i==0){$name=$lang['counter_main_3_day_name0'];}
		if($i==1){$name=$lang['counter_main_3_day_name1'];}
		if($i==2){$name=$lang['counter_main_3_day_name2'];}
		if($i==3){$name=$lang['counter_main_3_day_name3'];}
		if($i==4){$name=$lang['counter_main_3_day_name4'];}
		if($i==5){$name=$lang['counter_main_3_day_name5'];}
		if($i==6){$name=$lang['counter_main_3_day_name6'];}

echo "<tr>
<td width=1% nowrap $bgcolor1>{$name}</td>
<td align=right width=1% nowrap $bgcolor1>".number_format($i_hit_avg)."$lang[counter_main_3_visitor]</td>
<td width=98%>$i_graph <font size=1>$i_per%</font></td>
</tr>";
	}

echo"
<tr><td colspan=3 align=center><input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</table>
</td></tr>
";
}


####################################################################################
//					월별통계
####################################################################################
if($mode==4){

	$time=time()+$time_zone;

	if(!$all && (!$yy))
	{
		$yy=date('Y',time()+$time_zone);
		$mm=date('m',time()+$time_zone);
		$its_today=" [$lang[counter_main_4_this_year]]";
	}

	if(!$all){
		$today_info="$yy$its_today";
	}
	else{
		$today_info=$lang['counter_main_4_sum'];
	}


	$sub="and yy='$yy'";

	if($all){
		$sub="";
	}

	$now=date('m',time()+$time_zone);

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_data where counter='$counter' $sub";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));

	$today_hit=$today_hit_temp["sum(hit)"];


echo"
<tr><td>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td>
$lang[counter_main_4_year]<b>$today_info</b>$lang[counter_main_4_total]<b>".number_format($today_hit)."</b>$lang[counter_main_4_total_visitor]
</td></tr>
</table>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
";

	for($i=1;$i<=12;$i++){
		//if(strlen($i)==1){$i="0".$i;}
		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' $sub and mm='$i'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
		$temp=$today_hit_temp["sum(hit)"];
		if($max<$temp){$max=$temp;}
	}



	for($i=1;$i<=12;$i++){
		//if(strlen($i)==1){$i="0".$i;}

		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' $sub and mm='$i'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
		$i_hit=$today_hit_temp["sum(hit)"];
		if(!$i_hit){$i_hit=0;}


		if($today_hit){

			$i_per=round($i_hit/$today_hit*100,2);

			$i_graph=round($i_hit/$max*400);
			if($max==$i_hit){$ibn=2;}else{$ibn="";}

			$i_graph="<img src=nalog_image/block$ibn.gif width='$i_graph' height=10>";
		}

		if($now==$i){$bgcolor1="bgcolor=#C9F0FF";}else{$bgcolor1="";}
		if($i_hit && !$all){
			$i_msg=str_replace("{yy}",$yy,$lang['counter_main_4_view_visitor']);
			$i_msg=str_replace("{mm}",$i,$i_msg);
			$i_msg="<div title='$i_msg'>";
			$link="<a href=admin_counter.php?counter=$counter&mode=2&yy=$yy&mm=$i>";}
		else{
			$i_msg="";$link="";}

echo "<tr height=20>
<td align=right width=1% nowrap $bgcolor1>$i_msg$link".date($lang['counter_main_4_month_format'],strtotime("2003-$i-1",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor1>".number_format($i_hit)."$lang[counter_main_4_visitor]</td>
<td width=98%>$i_graph <font size=1>$i_per%</font></td>
</tr>";

	}

echo"
</table><br>
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=#C9F0FF>
<form method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value='$mode'>
<tr><td>&nbsp;
<select name=yy><option value=''>--------</option>";
	// mysql_query를 mysqli_query로 변경
	$query="select distinct yy from nalog3_data where counter='$counter' order by yy";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		if($yy==$data['yy']){$select="selected";}else{$select="";}
echo "<option value='$data[yy]' $select>$data[yy]</option>";
	}
echo"
</select> $lang[counter_main_year] &nbsp; <input type=submit class=button value='$lang[counter_main_button_view]'> <input type=button onclick=view_all() class=button value='$lang[counter_main_button_view_all]'>

<input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)>
</td></tr>

<script language=javascript>
function view_all(){
	location.href=\"<?php echo $_SERVER['PHP_SELF']; ?>?counter=<?php echo $counter; ?>&mode=<?php echo $mode; ?>&all=1\";
}
</script>
</form>
</table>
</td></tr>
";
}


####################################################################################
//					년도통계
####################################################################################
if($mode==5){

	$yy=date('Y',time()+$time_zone);

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_data where counter='$counter'";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
	$total=$today_hit=$today_hit_temp["sum(hit)"];

echo"
<tr><td>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr><td>
<b>$lang[counter_main_5_sum]</b>$lang[counter_main_5_total]<b>".number_format($today_hit)."</b>$lang[counter_main_5_total_visitor]
</td></tr>
</table>

<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
";

	// mysql_query를 mysqli_query로 변경
	$queryx="select distinct yy from nalog3_data where counter='$counter'";
	$resultx=@mysqli_query($connect, $queryx);
	while($datax=@mysqli_fetch_array($resultx))
	{
		// mysql_query를 mysqli_query로 변경
		$query="select sum(hit) from nalog3_data where counter='$counter' and yy='$datax[yy]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
		$temp=$today_hit_temp["sum(hit)"];
		if($max<$temp){$max=$temp;}
	}


	// mysql_query를 mysqli_query로 변경
	$queryx="select distinct yy from nalog3_data where counter='$counter' order by yy";
	$resultx=@mysqli_query($connect, $queryx);
	while($datax=@mysqli_fetch_array($resultx))
	{
		// mysql_query를 mysqli_query로 변경
		$queryq="select sum(hit) from nalog3_data where counter='$counter' and yy='$datax[yy]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $queryq));
		$yt=$today_hit_temp["sum(hit)"];

		$i_per=round($yt/$total*100,2);
		$i_graph=round($yt/$max*400);

		if($max==$yt){$ibn=2;}else{$ibn="";}

		$i_graph="<img src=nalog_image/block_big$ibn.gif width='$i_graph' height=30>";


		if($yy==$datax['yy']){$bgcolor1="bgcolor=#C9F0FF";}else{$bgcolor1="";}
		if($yt){
			$i_msg=str_replace("{yy}",$datax['yy'],$lang['counter_main_5_view_visitor']);
			$i_msg="<div title='$i_msg'>";
			$link="<a href=admin_counter.php?counter=$counter&mode=4&yy=$datax[yy]>";}
		else{$i_msg="";$link="";}

echo "<tr>
<td width=1% nowrap $bgcolor1>$i_msg$link".date($lang['counter_main_5_year_format'],strtotime("$datax[yy]-1-1",time()+$time_zone))."</a></td>
<td align=right width=1% nowrap $bgcolor1>".number_format($yt)."$lang[counter_main_5_visitor]</td>
<td width=98%>$i_graph <font size=1>$i_per%</font></td>
</tr>";


	}

echo"
<tr><td colspan=3 align=center><input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</table>
</td></tr>
";
}


####################################################################################
//					로그통계
####################################################################################
if($mode==6){
	// eregi_replace를 preg_replace로 변경 (i 플래그는 제거)
	$word=preg_replace("/\"/","",stripslashes($word));
	$word=preg_replace("/\s\s/"," ",stripslashes($word));

	if(!$sort){$sort="time_desc";}
	if($sort=="hit"){$sort1="selected";$sortq="hit";}
	if($sort=="hit_desc"){$sort2="selected";$sortq="hit desc";}
	if($sort=="time"){$sort3="selected";$sortq="time";}
	if($sort=="time_desc"){$sort4="selected";$sortq="time desc";}
	if($sort=="log"){$sort5="selected";$sortq="log";}
	if($sort=="log_desc"){$sort6="selected";$sortq="log desc";}

echo"
<tr><td>
<body onload=\"log.word.focus()\">
<form name=log method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value=$mode>


<script language=javascript>
function gogo(){
	log.submit();
}
function select_all(){
	if(chk_log.checker.value==0){
		var i =0;
		while (i < document.chk_log.elements.length)
		{
			if (document.chk_log.elements[i].type=='checkbox')
			{
				document.chk_log.elements[i].checked=true;
			}
			i++;
		}
		chk_log.checker.value=1;
		chk_log.selector.value='$lang[counter_main_button_cancel_all]';
	}
	else{
		var i =0;
		while (i < document.chk_log.elements.length)
		{
			if (document.chk_log.elements[i].type=='checkbox')
			{
				document.chk_log.elements[i].checked=false;
			}
			i++;
		}
		chk_log.checker.value=0;
		chk_log.selector.value='$lang[counter_main_button_check_all]';
	}
}
</script>

";

	//if($word){$wheredd=" and log like '%$word%' ";}

	if($word){
		$where=engine($sa,"log",0,0,1,0,0,$word,0,0);
		// $word_list 변수가 외부에서 정의된다고 가정.
		$word_list_array=explode(" ",$word_list);
	}else{$where=1;}
	if($nw){
		$where2=engine("and","log",0,0,1,0,0,$nw,1,0);
		$where=" (($where) and ($where2)) ";
	}
	if($where){$where=" and $where ";}
	if($sa=="and"){$sa1="checked";}
	else{$sa2="checked";}

//////////////////////////////////오늘만보기
	if($today_only){
		$today_only_check="checked";
		// date(m, ...) 대신 date('m', ...) 사용 (PHP 7.1 deprecated)
		$today_start=mktime(0,0,0,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$today_end=mktime(23,59,59,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$where.=" and time between $today_start and $today_end ";
	}

//////////////////////////////////페이지당 카운터수
	if($page){nalog_chk_num($page,0,"{$error}$lang[counter_main_6_error_pagenum]",0);}
	else{$page=10;}

//////////////////////////////////목록수
	$pageviewsu=10;

//////////////////////////////////인덱스설정
	$total=nalog_total("nalog3_log_".$counter,"$where");
	// mysql_query를 mysqli_query로 변경
	$total_hit_result=mysqli_fetch_array(mysqli_query($connect, "select sum(hit) from nalog3_log_$counter where 1 $where"));
	$total_hit=$total_hit_result["sum(hit)"];
	if(!$total){nalog_error($lang['counter_main_6_total_zero']);}

	$pagesu=ceil($total/$page);
	$start=($page*$pagenum);
	$no=$total-$start;
	$pagegroup=ceil(($pagenum+1)/$pageviewsu);
	$pagestart=($pageviewsu*($pagegroup-1))+1;
	$pageend=$pagestart+$pageviewsu-1;
	$nowpage=$pagenum+1;

echo"
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>

<tr>
<td nowrap><label for=today_only>$lang[counter_main_6_today_only]</label><input type=checkbox name=today_only value=1 $today_only_check id=today_only>
$lang[counter_main_6_sort_by] <select name=sort onchange=gogo()>
<option value=hit $sort1>$lang[counter_main_6_sort_1]</option>
<option value=hit_desc $sort2>$lang[counter_main_6_sort_2]</option>
<option value=time $sort3>$lang[counter_main_6_sort_3]</option>
<option value=time_desc $sort4>$lang[counter_main_6_sort_4]</option>
<option value=log $sort5>$lang[counter_main_6_sort_5]</option>
<option value=log_desc $sort6>$lang[counter_main_6_sort_6]</option>
</select></td>
<td align=right nowrap>$lang[counter_main_6_search_negative] <input type=text name=nw size=8 class=input value='$nw'>
<input type=radio name=sa value=and $sa1>$lang[counter_main_6_search_and]
<input type=radio name=sa value=or $sa2>$lang[counter_main_6_search_or]
<input type=text name=word size=14 class=input value=\"$word\"> <input type=submit value=\"$lang[counter_main_button_search]\" class=button>
</td>
</tr>
<tr>
<td>$lang[counter_main_6_total]<b>".number_format($total)."</b>$lang[counter_main_6_total_url]<b>".number_format($total_hit)."</b>$lang[counter_main_6_total_visitor]
<td align=right valign=top><font size=1>powered by <A href=http://nasearch.navyism.com target=_blank><b>n@search</b></a> engine</font></td>
</tr>
</form>
</table>

<table align=center width=100% cellpadding=1 cellspacing=0 bgcolor=white>
<form name=chk_log action=del_ing.php onsubmit=\"return confirm('n@log warning :\\n\\n$lang[counter_main_6_total_delete]')\">
<input type=hidden name=mode value=del_log_3>
<input type=hidden name=counter value='$counter'>
<input type=hidden name=checker value=0>
<tr bgcolor=#C9F0FF>
<td colspan=3 align=center width=99%>$lang[counter_main_6_table_url]</td>
<td align=center width=1% nowrap>$lang[counter_main_6_table_hit]</td>
</tr>
";

	$today=date('Ymd',time()+$time_zone);
	$color=0;
	// mysql_query를 mysqli_query로 변경
	$query="select * from nalog3_log_$counter where 1 $where order by bookmark desc,$sortq,no desc limit $start,$page";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		$thisday=date('Ymd',$data['time']);
		if($today==$thisday){$thisdaydata="<img src=nalog_image/mark_new.gif border=0 align=absmiddle>";}else{$thisdaydata="<img src=nalog_image/mark_old.gif border=0 align=absmiddle>";}

		$check_box="<input type=checkbox name=chk[$color] value=$data[no]>";

		$per=round($data['hit']/$total_hit*100,2);
		$data['time']=date($lang['counter_main_6_date_format'],$data['time']);

		// nalog_cut 함수는 외부에서 정의된다고 가정.
		$log_cut=nalog_cut($data['log'],70);

		if($word){
			for($i=0;$i<sizeof($word_list_array);$i++){
				$log_cut=str_replace("$word_list_array[$i]","###[-_-]###$word_list_array[$i]###[_-_]###",$log_cut);
			}
			$log_cut=str_replace("###[-_-]###","<font color=red>",$log_cut);
			$log_cut=str_replace("###[_-_]###","</font>",$log_cut);
		}

		if($color%2){$bgcolor="bgcolor=#F1F9FD";}else{$bgcolor="bgcolor=white";}


		if(!$data['bookmark']){$cellcolor="#CEF3FF";$fontcolor="";$remember="<div title='$lang[counter_main_6_url_remember]'><a href='remember.php?counter=$counter&no=$data[no]&kind=1'>$lang[counter_main_6_url_remember_button]</a></div>";}
		else{$cellcolor="#FFDBEF";$fontcolor="<font color=#F7418C>";$remember="<div title='$lang[counter_main_6_url_forget]'><a href='remember.php?counter=$counter&no=$data[no]&mode=del&kind=1'>$lang[counter_main_6_url_forget_button]</a></div>";}


		// eregi를 preg_match로 변경 (i 플래그 제거)
		if(trim($data['log'])=="http://" || !trim($data['log']) || preg_match("/HTTP_SERVER_VARS\[HTTP_REFERER\]/",$data['log'])){$log="$check_box <b>$no</b> ${thisdaydata}  ${fontcolor}$lang[counter_main_6_direct_connect]";
			$detail="";
		}
		else{
			$log="<div title='$data[log]'>$check_box ${fontcolor}<b>$no</b> <a href='$data[log]' target=_blank>${thisdaydata} $fontcolor$log_cut</a></div>";
			$temp_log=str_replace("http://","",$data['log']);
			$detail="[<a href='admin_counter.php?counter=$counter&mode=7&word=$temp_log'>${fontcolor}$lang[counter_main_6_view_detail_url]</a>]";
		}



echo "
<tr $bgcolor>
<td colspan=3 width=99%>$fontcolor$log</td>
<td align=center width=1% nowrap><b>$fontcolor".number_format($data['hit'])."</b></td>
</tr>
<tr $bgcolor>
<td width=1% nowrap onMouseOver=\"this.style.backgroundColor='$cellcolor';return true;\" onMouseOut=\"this.style.backgroundColor='';return true;\">$remember</td>
<td width=97% nowrap>&nbsp;$fontcolor$data[time]</td>
<td align=right width=1% nowrap>$fontcolor$detail [<a href=del_ing.php?counter=$counter&mode=del_log_1&no=$data[no] onclick=\"return confirm('n@log warning :\\n\\n$lang[counter_main_6_delete_question]')\">${fontcolor}$lang[counter_main_6_delete_button]</a>]</td>
<td align=center width=1% nowrap>$fontcolor$per%</td>
</tr>
<tr><td colspan=4 height=1 bgcolor=$cellcolor></td></tr>
";


		$color++;$no--;
	}

	$send="&counter=$counter&mode=$mode&&sort=$sort&word=$word&today_only=$today_only&sa=$sa&nw=$nw&";

echo"<tr><td colspan=4 align=center>";
	// nalog_index 함수는 외부에서 정의된다고 가정.
	nalog_index();
echo"</td></tr>
<tr><td colspan=4 align=center><input type=button class=button value='$lang[counter_main_button_check_all]' onclick=select_all() name=selector> <input type=submit class=button value='$lang[counter_main_button_delete]'> <input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</form>
</table>
</td></tr>
";
}


####################################################################################
//					상세로그
####################################################################################
if($mode==7){
	// eregi_replace를 preg_replace로 변경 (i 플래그 제거)
	$word=preg_replace("/\"/","",stripslashes($word));
	$word=preg_replace("/\s\s/"," ",stripslashes($word));

	if(!$sort){$sort="time_desc";}
	if($sort=="hit"){$sort1="selected";$sortq="hit";}
	if($sort=="hit_desc"){$sort2="selected";$sortq="hit desc";}
	if($sort=="time"){$sort3="selected";$sortq="time";}
	if($sort=="time_desc"){$sort4="selected";$sortq="time desc";}
	if($sort=="log"){$sort5="selected";$sortq="log";}
	if($sort=="log_desc"){$sort6="selected";$sortq="log desc";}

echo"
<tr><td>
<body onload=\"log.word.focus()\">
<form name=log method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value=$mode>


<script language=javascript>
function gogo(){
	log.submit();
}
function select_all(){
	if(chk_log.checker.value==0){
		var i =0;
		while (i < document.chk_log.elements.length)
		{
			if (document.chk_log.elements[i].type=='checkbox')
			{
				document.chk_log.elements[i].checked=true;
			}
			i++;
		}
		chk_log.checker.value=1;
		chk_log.selector.value='$lang[counter_main_button_cancel_all]';
	}
	else{
		var i =0;
		while (i < document.chk_log.elements.length)
		{
			if (document.chk_log.elements[i].type=='checkbox')
			{
				document.chk_log.elements[i].checked=false;
			}
			i++;
		}
		chk_log.checker.value=0;
		chk_log.selector.value='$lang[counter_main_button_check_all]';
	}
}
</script>

";

	if($word){
		$where=engine($sa,"log",0,0,1,0,0,$word,0,0);
		// $word_list 변수가 외부에서 정의된다고 가정.
		$word_list_array=explode(" ",$word_list);
	}else{$where=1;}
	if($nw){
		$where2=engine("and","log",0,0,1,0,0,$nw,1,0);
		$where=" (($where) and ($where2)) ";
	}
	if($where){$where=" and $where ";}
	if($sa=="and"){$sa1="checked";}
	else{$sa2="checked";}

//////////////////////////////////오늘만보기
	if($today_only){
		$today_only_check="checked";
		// date(m, ...) 대신 date('m', ...) 사용 (PHP 7.1 deprecated)
		$today_start=mktime(0,0,0,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$today_end=mktime(23,59,59,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$where.=" and time between $today_start and $today_end ";
	}

//////////////////////////////////페이지당 카운터수
	if($page){nalog_chk_num($page,0,"{$error}$lang[counter_main_7_error_pagenum]",0);}
	else{$page=10;}

//////////////////////////////////목록수
	$pageviewsu=10;

//////////////////////////////////인덱스설정
	$total=nalog_total("nalog3_dlog_".$counter,"$where");
	// mysql_query를 mysqli_query로 변경
	$total_hit_result=mysqli_fetch_array(mysqli_query($connect, "select sum(hit) from nalog3_dlog_$counter where 1 $where"));
	$total_hit=$total_hit_result["sum(hit)"];

	if(!$total){nalog_error($lang['counter_main_6_total_zero']);}
	$pagesu=ceil($total/$page);
	$start=($page*$pagenum);
	$no=$total-$start;
	$pagegroup=ceil(($pagenum+1)/$pageviewsu);
	$pagestart=($pageviewsu*($pagegroup-1))+1;
	$pageend=$pagestart+$pageviewsu-1;
	$nowpage=$pagenum+1;

echo"
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr>
<td nowrap><label for=today_only>$lang[counter_main_6_today_only]</label><input type=checkbox name=today_only value=1 $today_only_check id=today_only>
$lang[counter_main_6_sort_by] <select name=sort onchange=gogo()>
<option value=hit $sort1>$lang[counter_main_6_sort_1]</option>
<option value=hit_desc $sort2>$lang[counter_main_6_sort_2]</option>
<option value=time $sort3>$lang[counter_main_6_sort_3]</option>
<option value=time_desc $sort4>$lang[counter_main_6_sort_4]</option>
<option value=log $sort5>$lang[counter_main_6_sort_5]</option>
<option value=log_desc $sort6>$lang[counter_main_6_sort_6]</option>
</select></td>
<td align=right nowrap>$lang[counter_main_6_search_negative] <input type=text name=nw size=8 class=input value='$nw'>
<input type=radio name=sa value=and $sa1>$lang[counter_main_6_search_and]
<input type=radio name=sa value=or $sa2>$lang[counter_main_6_search_or]
<input type=text name=word size=14 class=input value='$word'> <input type=submit value=\"$lang[counter_main_button_search]\" class=button>
</td>
</tr>
<tr>
<td>$lang[counter_main_6_total]<b>".number_format($total)."</b>$lang[counter_main_6_total_url]<b>".number_format($total_hit)."</b>$lang[counter_main_6_total_visitor]
<td align=right><font size=1>powered by <A href=http://nasearch.navyism.com target=_blank><b>n@search</b></a> engine</font></td>
</tr>
</form>
</table>

<table align=center width=100% cellpadding=1 cellspacing=0 bgcolor=white>
<form name=chk_log action=del_ing.php onsubmit=\"return confirm('n@log warning :\\n\\n$lang[counter_main_6_total_delete]')\">
<input type=hidden name=mode value=del_log_4>
<input type=hidden name=counter value='$counter'>
<input type=hidden name=checker value=0>
<tr bgcolor=#C9F0FF>
<td colspan=3 align=center width=99%>$lang[counter_main_6_table_url]</td>
<td align=center width=1% nowrap>$lang[counter_main_6_table_hit]</td>
</tr>

";

	$today=date('Ymd',time()+$time_zone);
	$color=0;
	// mysql_query를 mysqli_query로 변경
	$query="select * from nalog3_dlog_$counter where 1 $where order by bookmark desc,$sortq,no desc limit $start,$page";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		$thisday=date('Ymd',$data['time']);
		if($today==$thisday){$thisdaydata="<img src=nalog_image/mark_new.gif border=0 align=absmiddle>";}else{$thisdaydata="<img src=nalog_image/mark_old.gif border=0 align=absmiddle>";}

		$check_box="<input type=checkbox name=chk[$color] value=$data[no]>";

		$per=round($data['hit']/$total_hit*100,2);
		$data['time']=date($lang['counter_main_6_date_format'],$data['time']);

		// nalog_cut 함수는 외부에서 정의된다고 가정.
		$log_cut=nalog_cut($data['log'],70);

		if($word){
			for($i=0;$i<sizeof($word_list_array);$i++){
				$log_cut=str_replace("$word_list_array[$i]","###[-_-]###$word_list_array[$i]###[_-_]###",$log_cut);
			}
			$log_cut=str_replace("###[-_-]###","<font color=red>",$log_cut);
			$log_cut=str_replace("###[_-_]###","</font>",$log_cut);
		}

		if($color%2){$bgcolor="bgcolor=#F1F9FD";}else{$bgcolor="bgcolor=white";}

		if(!$data['bookmark']){$cellcolor="#CEF3FF";$fontcolor="";$remember="<div title='$lang[counter_main_6_url_remember]'><a href='remember.php?counter=$counter&no=$data[no]'>$lang[counter_main_6_url_remember_button]</a></div>";}
		else{$cellcolor="#FFDBEF";$fontcolor="<font color=#F7418C>";$remember="<div title='$lang[counter_main_6_url_forget]'><a href='remember.php?counter=$counter&no=$data[no]&mode=del'>$lang[counter_main_6_url_forget_button]</a></div>";}

		// eregi를 preg_match로 변경 (i 플래그 제거)
		if($data['log']=="" || $data['log']=="http://" || preg_match("/HTTP_SERVER_VARS\[HTTP_REFERER\]/",$data['log'])){$log="$check_box ${fontcolor}<b>$no</b> $thisdaydata $lang[counter_main_6_direct_connect]";}
		else{$log="<div title='$data[log]'>$check_box ${fontcolor}<b>$no</b> <a href='$data[log]' target=_blank>$thisdaydata $fontcolor$log_cut</a></div>";}

echo "
<tr $bgcolor>
<td colspan=3 width=99%>$log</td>
<td align=center width=1% nowrap>${fontcolor}<b>".number_format($data['hit'])."</b></td>
</tr>
<tr $bgcolor>
<td width=1% nowrap onMouseOver=\"this.style.backgroundColor='$cellcolor';return true;\" onMouseOut=\"this.style.backgroundColor='';return true;\">$remember</td>
<td width=97%>&nbsp;${fontcolor}$data[time]</td>
<td align=center width=1% nowrap>${fontcolor}[<a href=del_ing.php?counter=$counter&mode=del_log_2&no=$data[no] onclick=\"return confirm('n@log warning :\\n\\n$lang[counter_main_6_delete_question]')\">${fontcolor}$lang[counter_main_6_delete_button]</a>]</td>
<td align=center width=1% nowrap>${fontcolor}$per%</td>
</tr>
<tr><td colspan=4 height=1 bgcolor=$cellcolor></td></tr>
";


		$color++;$no--;
	}

	$send="&counter=$counter&mode=$mode&&sort=$sort&word=$word&today_only=$today_only&sa=$sa&nw=$nw&";

echo"<tr><td colspan=4 align=center>";
	// nalog_index 함수는 외부에서 정의된다고 가정.
	nalog_index();
echo"</td></tr>
<tr><td colspan=4 align=center><input type=button class=button value='$lang[counter_main_button_check_all]' onclick=select_all() name=selector> <input type=submit class=button value='$lang[counter_main_button_delete]'> <input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</form>
</table>
</td></tr>
";
}



####################################################################################
//					os통계
####################################################################################
if($mode==8){

	// mysql_query를 mysqli_query로 변경
	$query="select sum(hit) from nalog3_os where counter='$counter' and os='1'";
	$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $query));
	$total=$today_hit_temp["sum(hit)"];

	if(!$total){nalog_error($lang['counter_main_7_total_zero']);}

	// mysql_query를 mysqli_query로 변경
	$query="select distinct name from nalog3_os where counter='$counter' and os='1'";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		// mysql_query를 mysqli_query로 변경
		$queryx="select sum(hit) from nalog3_os where counter='$counter' and os='1' and name='$data[name]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $queryx));
		$temp=$today_hit_temp["sum(hit)"];
		if($max<$temp){$max=$temp;}
		$os_count++;
	}

echo"
<tr><td>
<table align=center width=100% cellpadding=2 cellspacing=1 bordercolor=white bgcolor=white>
<tr><td colspan=4>$lang[counter_main_7_total]<b>".number_format($os_count)."</b>$lang[counter_main_7_total_os]<b>".number_format($total)."</b>$lang[counter_main_7_total_visitor]</td></tr>
<tr bgcolor=#C9F0FF>
<td colspan=4 align=center>$lang[counter_main_7_title_os]</td>
</tr>
";

	$no=1;
	// mysql_query를 mysqli_query로 변경
	$query="select distinct name from nalog3_os where counter='$counter' and os='1' order by hit desc";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		// mysql_query를 mysqli_query로 변경
		$queryx="select * from nalog3_os where counter='$counter' and os='1' and name='$data[name]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $queryx));
		$os_total=$today_hit_temp['hit'];

		$i_per=round($os_total/$total*100,2);
		$i_graph=round($os_total/$max*350);


		if($max==$os_total){$ibn=2;}else{$ibn="";}
		$i_graph="<img src=nalog_image/block$ibn.gif width='$i_graph' height=10>";

		if(!$data['name'] || $data['name']=="Unknown"){$data['name']="<font color=#95D2F1>Unknown</font>";}

echo "<tr>
<td width=1% nowrap><b>$no</b></td>
<td width=1% nowrap>{$data[name]}</td>
<td align=right width=1% nowrap>".number_format($os_total)."$lang[counter_main_7_visitor]</td>
<td width=97%>$i_graph <font size=1>$i_per%</font></td>
</tr>";
		$no++;
	}

	$max=0;
	// mysql_query를 mysqli_query로 변경
	$query="select distinct name from nalog3_os where counter='$counter' and os='0'";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		// mysql_query를 mysqli_query로 변경
		$queryx="select sum(hit) from nalog3_os where counter='$counter' and os='0' and name='$data[name]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $queryx));
		$temp=$today_hit_temp["sum(hit)"];
		if($max<$temp){$max=$temp;}
		$bs_count++;
	}

echo"
<tr><td colspan=4><br>$lang[counter_main_7_total]<b>".number_format($bs_count)."</b>$lang[counter_main_7_total_browser]<b>".number_format($total)."</b>$lang[counter_main_7_total_visitor]</td></tr>
<tr bgcolor=#C9F0FF>
<td colspan=4 align=center>$lang[counter_main_7_title_browser]</td>
</tr>
";

	$no=1;
	// mysql_query를 mysqli_query로 변경
	$query="select distinct name from nalog3_os where counter='$counter' and os='0' order by hit desc";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		// mysql_query를 mysqli_query로 변경
		$queryx="select * from nalog3_os where counter='$counter' and os='0' and name='$data[name]'";
		$today_hit_temp=mysqli_fetch_array(mysqli_query($connect, $queryx));
		$os_total=$today_hit_temp['hit'];

		$i_per=round($os_total/$total*100,2);
		$i_graph=round($os_total/$max*350);

		if($max==$os_total){$ibn=2;}else{$ibn="";}
		$i_graph="<img src=nalog_image/block$ibn.gif width='$i_graph' height=10>";

		if(!$data['name'] || $data['name']=="Unknown"){$data['name']="<font color=#95D2F1>Unknown</font>";}

echo "<tr>
<td width=1% nowrap><b>$no</b></td>
<td width=1% nowrap>{$data[name]}</td>
<td align=right width=1% nowrap>".number_format($os_total)."$lang[counter_main_7_visitor]</td>
<td width=97%>$i_graph <font size=1>$i_per%</font></td>
</tr>";

		$no++;
	}

echo"
<tr><td colspan=4 align=center><input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</table>
</td></tr>
";
}


####################################################################################
//					방문자통계
####################################################################################
if($mode==9){

	if(!$sort){$sort="time_desc";}
	if($sort=="time"){$sort3="selected";$sortq="time";}
	if($sort=="time_desc"){$sort4="selected";$sortq="time desc";}
	if($sort=="log"){$sort5="selected";$sortq="id";$member=1;}
	if($sort=="log_desc"){$sort6="selected";$sortq="id desc";$member=1;}
	if($members){$member_check="checked";}

echo"
<tr><td>
<body onload=\"log.word.focus();\">
<form name=log method=get action="<?php echo $_SERVER['PHP_SELF']; ?>">
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value=$mode>
<input type=hidden name=day_start value=$day_start>
<input type=hidden name=day_end value=$day_end>

<script language=javascript>
function gogo(){
	log.submit();
}
</script>

";

	if($word){$where=" and (id like '%$word%' or ip like '%$word%') ";}
	if($member || $members){$where.=" and id<>''";}
	if($day_start && $day_end){$where.=" and time between $day_start and $day_end";}


//////////////////////////////////오늘방문자
	if($today_only){
		$today_only_check="checked";
		// date(m, ...) 대신 date('m', ...) 사용 (PHP 7.1 deprecated)
		$today_start=mktime(0,0,0,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$today_end=mktime(23,59,59,date('m',time()+$time_zone),date('d',time()+$time_zone),date('Y',time()+$time_zone));
		$where.=" and time between $today_start and $today_end ";
	}

//////////////////////////////////페이지당 방문자수
	if($page){nalog_chk_num($page,0,"{$error}$lang[counter_main_8_error_pagenum]",0);}
	else{$page=10;}

//////////////////////////////////목록수
	$pageviewsu=10;

//////////////////////////////////인덱스설정
	$total=nalog_total("nalog3_counter_".$counter,"$where");
	if(!$total){nalog_error($lang['counter_main_8_total_zero']);}
	$pagesu=ceil($total/$page);
	$start=($page*$pagenum);
	$no=$total-$start;
	$pagegroup=ceil(($pagenum+1)/$pageviewsu);
	$pagestart=($pageviewsu*($pagegroup-1))+1;
	$pageend=$pagestart+$pageviewsu-1;
	$nowpage=$pagenum+1;

echo"
<table align=center width=100% cellpadding=2 cellspacing=1 border=0 bordercolor=white bgcolor=white>
<tr>
<td nowrap>$lang[counter_main_8_total]<b>".number_format($total)."</b>$lang[counter_main_8_total_visitor]
<td align=right nowrap>
<label for=today_only>$lang[counter_main_8_today_only]</label><input type=checkbox name=today_only value=1 $today_only_check id=today_only>
<label for=members>$lang[counter_main_8_member_only]</label><input type=checkbox name=members value=1 $member_check id=members> &nbsp;$lang[counter_main_8_sort_by] <select name=sort onchange=gogo()>


<option value=time $sort3>$lang[counter_main_8_sort_1]</option>
<option value=time_desc $sort4>$lang[counter_main_8_sort_2]</option>
<option value=log $sort5>$lang[counter_main_8_sort_3]</option>
<option value=log_desc $sort6>$lang[counter_main_8_sort_4]</option>
</select> <input type=text name=word size=14 class=input value='$word'> <input type=submit value=$lang[counter_main_button_search] class=button></td>
</tr>
</form>
</table>

<table align=center width=100% cellpadding=2 cellspacing=0 bgcolor=white>
<tr bgcolor=#C9F0FF>
<td colspan=3 align=center width=99%>$lang[counter_main_8_title_1]</td>
<td align=center width=1% nowrap>$lang[counter_main_8_title_2]</td>
</tr>
";

	$today=date('Ymd',time()+$time_zone);
	$color=0;
	// mysql_query를 mysqli_query로 변경
	$query="select * from nalog3_counter_$counter where 1 $where order by $sortq,no desc limit $start,$page";
	$result=@mysqli_query($connect, $query);
	while($data=@mysqli_fetch_array($result))
	{
		$thisday=date('Ymd',$data['time']);
		if($today==$thisday){$thisdaydata="<img src=nalog_image/mark_new.gif border=0 align=absmiddle>";}else{$thisdaydata="<img src=nalog_image/mark_old.gif border=0 align=absmiddle>";}

		$data['time']=date($lang['counter_main_8_date_format'],$data['time']);
		if($color%2){$bgcolor="bgcolor=#F1F9FD";}else{$bgcolor="bgcolor=white";}

		$data['idx']=$data['id'];

		if($word){
			$data['id']=str_replace("$word","<font color=red>$word</font>",$data['id']);
			$data['ip_link']=str_replace("$word","<font color=red>$word</font>",$data['ip']);
		}
		else{
			$data['ip_link']=$data['ip'];
		}

		if(!$data['id']){$data['id']="<font color=#95D2F1>$lang[counter_main_8_not_login]</font>";}
		else{$data['id']="<a href=admin_counter.php?counter=$counter&mode=9&word=$data[idx]><b>$data[id]</b></a>";}


		if(!$data['browser'] || $data['browser']=="Unknown"){$data['browser']="<font color=#95D2F1>$lang[counter_main_8_unknown_browser]</font>";}
		if(!$data['os'] || $data['os']=="Unknown"){$data['os']="<font color=#95D2F1>$lang[counter_main_8_unknown_os]</font>";}

		// eregi를 preg_match로 변경 (i 플래그 제거)
		if(!$data['referer'] || $data['referer']=="http://"){
			$referer="$lang[counter_main_8_direct_connect]";
		} else {
			// nalog_cut 함수는 외부에서 정의된다고 가정.
			$referer="<a href='$data[referer]' target=_blank>".nalog_cut($data['referer'],50)."</a>";
		}


echo "
<tr $bgcolor>
<td width=1% nowrap><b>$no</b></td>
<td width=1% nowrap><div title='$lang[counter_main_8_search]'> $thisdaydata $data[id]</div></td>
<td width=97%><div title='$data[referer]'>$lang[counter_main_8_right_arrow]$referer</div></td>
<td align=right width=1% nowrap><a href='check_ip.php?ip=$data[ip]' target=_blank>$data[ip_link]</a></td>
</tr>
<tr $bgcolor>
<td colspan=3 width=99% >$data[os] / $data[browser]</td>
<td align=right width=1% nowrap>$data[time]</td>
</tr>
<tr><td colspan=4 height=1 bgcolor=#CEF3FF></td></tr>
";

		$no--;
		$color++;
	}

	$send="&counter=$counter&mode=$mode&&sort=$sort&word=$word&members=$members&day_start=$day_start&day_end=$day_end&today_only=$today_only&";

echo"<tr><td colspan=4 align=center>";
	// nalog_index 함수는 외부에서 정의된다고 가정.
	nalog_index();
echo"</td></tr>
<tr><td colspan=4 align=center><input type=button class=button value='$lang[counter_main_button_print]' onclick=window.print()> <input type=button class=button value='$lang[counter_main_button_back]' onclick=history.go(-1)></td></tr>
</table>
</td></tr>
";
}


####################################################################################
//					환경설정
####################################################################################
if($mode==10){

	if(($set['cookie'] ?? null)==0){$cookie0="checked";}
	if(($set['cookie'] ?? null)==1){$cookie1="checked";}
	if(($set['cookie'] ?? null)==2){$cookie2="checked";}
	if(($set['cookie'] ?? null)==3){$cookie3="checked";}
	if($set['auth_time'] ?? null){$auth_time="checked";}
	if($set['auth_day'] ?? null){$auth_day="checked";}
	if($set['auth_week'] ?? null){$auth_week="checked";}
	if($set['auth_month'] ?? null){$auth_month="checked";}
	if($set['auth_year'] ?? null){$auth_year="checked";}
	if($set['auth_log'] ?? null){$auth_log="checked";}
	if($set['auth_dlog'] ?? null){$auth_dlog="checked";}
	if($set['auth_os'] ?? null){$auth_os="checked";}
	if($set['auth_member'] ?? null){$auth_member="checked";}
	if($set['auth_ip'] ?? null){$auth_ip="checked";}
	if($set['now_check'] ?? null){$now_check="checked";}
	if($set['counter_check'] ?? null){$counter_check="checked";}
	if($set['log_check'] ?? null){$log_check="checked";}
	if($set['skin_check'] ?? null){$skin_check="checked";}
	if($set['check_admin'] ?? null){$check_admin="checked";}

echo"
<form method=post action=admin_main_ing.php>
<input type=hidden name=counter value='$counter'>
<input type=hidden name=mode value='$mode'>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_total]&nbsp;</td>
<td width=99%>&nbsp;<input type=text name=total size=20 class=input value='{$set['total']}' onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\"></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_skin]&nbsp;</td>
<td width=99%>&nbsp;<select name=skin>";
	// eregi를 preg_match로 변경 (i 플래그 제거)
	$handle=opendir("skin");
	while ($skinname = readdir($handle))
	{
		if(!preg_match("/\./",$skinname))
		{
			if($skinname==($set['skin'] ?? null)) $select="selected"; else $select="";
			echo"<option value=$skinname $select>$skinname</option>";
		}
	}
	closedir($handle);
echo"</select></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_skin_pattern]&nbsp;</td>
<td width=99%>&nbsp;<input type=checkbox name=skin_check value=1 $skin_check id=skin_check> <label for=skin_check>$lang[counter_config_skin_pattern_use]</label></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF valign=top>&nbsp;$lang[counter_config_reconnect]&nbsp;</td>
<td width=99%>&nbsp;<input type=radio name=cookie value='0' $cookie0 id=cookie0> <label for=cookie0>$lang[counter_config_reconnect_always]</label><br>
&nbsp;<input type=radio name=cookie value='1' $cookie1 id=cookie1> <label for=cookie1>$lang[counter_config_reconnect_new_open]</label><br>
&nbsp;<input type=radio name=cookie value='2' $cookie2 id=cookie2> <label for=cookie2>$lang[counter_config_reconnect_by_time1]<input type=text name=cookie_time size=5 class=input value='{$set['cookie_time']}' onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\">$lang[counter_config_reconnect_by_time2]</label><br>
&nbsp;<input type=radio name=cookie value='3' $cookie3 id=cookie3> <label for=cookie3>$lang[counter_config_reconnect_once]</label>
</td>
</tr>

<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_time_zone1]&nbsp;</td>
<td width=99%>&nbsp;
<select name=time_zone1>
<option value=1>+</option>
<option value=0 "; if(!($set['time_zone1'] ?? null)){echo"selected";} echo">-</option>
</select>
<input type=text name=time_zone2 value=\"{$set['time_zone2']}\" size=2 maxlength=2 onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\"> $lang[counter_config_time_zone2]</td>
</tr>

<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_admin_check]&nbsp;</td>
<td width=99%>&nbsp;<input type=checkbox name=check_admin value=1 $check_admin id=check_admin> <label for=check_admin>$lang[counter_config_admin_check_not]</label></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_now_check]&nbsp;</td>
<td width=99%>&nbsp;<input type=checkbox name=now_check value=1 $now_check id=now_check> <label for=now_check>$lang[counter_config_now_check_use]</label></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_now_time]&nbsp;</td>
<td width=99%>&nbsp;$lang[counter_config_now_time_use1]<input type=text name=connecting size=5 class=input value='{$set['connecting']}' onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\">$lang[counter_config_now_time_use2]</td>
</tr>

<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_admin_data]&nbsp;</td>
<td width=99%>&nbsp;<input type=button class=button onclick=del_data() value='$lang[counter_config_admin_data_delete1]'>$lang[counter_config_admin_data_delete2]</td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_admin_os]&nbsp;</td>
<td width=99%>&nbsp;<input type=button class=button onclick=del_os() value='$lang[counter_config_admin_os_delete1]'>$lang[counter_config_admin_os_delete2]</td>
</tr>

<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_visitor_check]&nbsp;</td>
<td width=99%>&nbsp;<input type=checkbox name=counter_check value=1 $counter_check id=counter_check> <label for=counter_check>$lang[counter_config_visitor_check_use]</label></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_visitor_limit]&nbsp;</td>
<td width=99%>&nbsp;<input type=button class=button onclick=del_counter() value='$lang[counter_config_visitor_delete1]'>$lang[counter_config_visitor_delete2]<br><br>&nbsp;$lang[counter_config_visitor_limit_set1]<input type=text name=counter_limit size=11 class=input value='{$set['counter_limit']}' onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\">$lang[counter_config_visitor_limit_set2]</td>
</tr>

<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_log_check]&nbsp;</td>
<td width=99%>&nbsp;<input type=checkbox name=log_check value=1 $log_check id=log_check> <label for=log_check>$lang[counter_config_log_check_use]</label></td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_log_limit]&nbsp;</td>
<td width=99%>&nbsp;<input type=button class=button onclick=del_log() value='$lang[counter_config_log_delete1]'>$lang[counter_config_log_delete2]<br><br>&nbsp;$lang[counter_config_log_limit_set1]<input type=text name=log_limit size=11 class=input value='{$set['log_limit']}' onKeyPress=\"if((event.keyCode>57||event.keyCode<48)) event.returnValue=false;\">$lang[counter_config_log_limit_set2]</td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF>&nbsp;$lang[counter_config_member_cookie]&nbsp;</td>
<td width=99%>&nbsp;<input type=text name=member_id size=14 class=input value='{$set['member_id']}'>> $lang[counter_config_member_cookie_is]</td>
</tr>
<tr>
<td width=1% nowrap bgcolor=#C9F0FF valign=top>&nbsp;$lang[counter_config_permission]&nbsp;</td>
<td width=99%>
&nbsp;<input type=checkbox name=auth_time value='1' id=auth_time $auth_time> <label for=auth_time>$lang[counter_config_permission1]</label><br>
&nbsp;<input type=checkbox name=auth_day value='1' id=auth_day $auth_day> <label for=auth_day>$lang[counter_config_permission2]</label><br>
&nbsp;<input type=checkbox name=auth_week value='1' id=auth_week $auth_week> <label for=auth_week>$lang[counter_config_permission3]</label><br>
&nbsp;<input type=checkbox name=auth_month value='1' id=auth_month $auth_month> <label for=auth_month>$lang[counter_config_permission4]</label><br>
&nbsp;<input type=checkbox name=auth_year value='1' id=auth_year $auth_year> <label for=auth_year>$lang[counter_config_permission5]</label><br>
&nbsp;<input type=checkbox name=auth_log value='1' id=auth_log $auth_log> <label for=auth_log>$lang[counter_config_permission6]</label><br>
&nbsp;<input type=checkbox name=auth_dlog value='1' id=auth_dlog $auth_dlog> <label for=auth_dlog>$lang[counter_config_permission7]</label><br>
&nbsp;<input type=checkbox name=auth_os value='1' id=auth_os $auth_os> <label for=auth_os>$lang[counter_config_permission8]</label><br>
&nbsp;<input type=checkbox name=auth_member value='1' id=auth_member $auth_member> <label for=auth_member>$lang[counter_config_permission9]</label><br>
</td>
</tr>
<tr>
<td colspan=2 height=10></td>
</tr>
<tr>
<td width=1% nowrap></td>
<td width=99%>&nbsp;<input type=submit class=button value='$lang[counter_config_button_save]'> <input type=reset class=button value='$lang[counter_config_button_reset]'></td>
</tr>
</form>

<script language=javascript>
function del_counter(){
	if(confirm('n@log warning :\\n\\n$lang[counter_config_warning_visitor]')){
		location.href='del_ing.php?counter=$counter&mode=del_counter';
	}
}
function del_log(){
	if(confirm('n@log warning :\\n\\n$lang[counter_config_warning_log]')){
		location.href='del_ing.php?counter=$counter&mode=del_log';
	}
}
function del_data(){
	if(confirm('n@log warning :\\n\\n$lang[counter_config_warning_data]')){
		location.href='del_ing.php?counter=$counter&mode=del_data';
	}
}
function del_os(){
	if(confirm('n@log warning :\\n\\n$lang[counter_config_warning_os]')){
		location.href='del_ing.php?counter=$counter&mode=del_os';
	}
}
</script>
";
}
?>

</table>