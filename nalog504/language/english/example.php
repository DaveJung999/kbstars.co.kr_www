<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
	<td colspan=2>
		<a href="http://english.navyism.com" target=_blank><img src='nalog_image/logo.gif' border=0></a>
	</td>
	</tr>
	<tr>
	<td>
		<span style='font-size:11pt'><b>n@log analyzer <?php echo $nalog_info[version]; ?>
		Usage example of counter: <?php echo $counter; ?></b></span>
	</td>
	<td align=right>
		written by <a href="http://kiddiken.net" target=_blank>kiddiken</a>
	</td>
	</tr>
	<tr>
	<td colspan=2 height=3 bgcolor=#2CBBFF></td>
	</tr>
</table>

<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
	<td colspan=2>
		A new counter named <b><?php echo $counter; ?></b> is created and active now.<br>
		I am going to briefly explain to you the usage examples based on the configuration of this counter.<br><br>
	</td>
	</tr>
	<tr>
	<td colspan=2>
		<font size=2><b>1. Using GD module to create counter images.</b> (Your site should support GD module)</font>
	</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
	<table border=0 width=100% cellpadding=5 cellspacing=0>
		<tr>
		<td width=1% nowrap valign=top><img src='<?php echo $test_gd; ?>'></td>
		<td width=99% valign=top>
			If there is a white circle above a black square on the left image, that means your web site supports GD module. If so, you may use n@log to create counter images easily with the following IMG tag.
		</td>
		</tr>
	</table>
	<br>
	<span style='font-family:Courier New;font-size:9pt'>
		&lt;img src="<b>n@log_path</b>/nalogd.php?counter=<b>counter_name</b>&url=&lt;?=$_SERVER[HTTP_REFERER]?&gt;" width=0 height=0>
	</span>
	<br><br>
	In the following text boxes, I will try to determine your n@log configuration and write down the IMG tags for you.<br>
	If you think that they seems to be correct, just copy and paste into your source code.<br><br>

<textarea class=input cols=80 rows=2 onclick=select() readonly style='font-family:Courier New;font-size:9pt'>
&lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/nalogd.php', $_SERVER['PHP_SELF']); ?>?counter=<?php echo $counter; ?>&url=<?php echo $_SERVER['HTTP_REFERER']; ?>" width=0 height=0></textarea>

	<br><br>
	If you use the above IMG tag, it will be counted when the page is loading. But the counter is hidden.<br>
	You can use the following tags to show your counter images:<br><br>

<textarea class=input cols=80 rows=6 onclick=select() readonly style='font-family:Courier New;font-size:9pt'>
TODAY &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_today.jpg', $_SERVER['PHP_SELF']); ?>">
YESTERDAY &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_yester.jpg', $_SERVER['PHP_SELF']); ?>">
TOTAL &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_total.jpg', $_SERVER['PHP_SELF']); ?>">
NOW &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_now.jpg', $_SERVER['PHP_SELF']); ?>">
ONLINE PEAK &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_peak.jpg', $_SERVER['PHP_SELF']); ?>">
DAY PEAK &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_day_peak.jpg', $_SERVER['PHP_SELF']); ?>"></textarea>

	<br><br>
	Note: The counter images created by GD module are in JPEG format. (with .jpg extensions)<br><br>
	</td></tr>
	<tr>
	<td colspan=2>
		<font size=2><b>2. Output the counter without GD module.</b> (Should be applicable in most cases)</font>
	</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
	In general, you should place a simple PHP source code on the <font color=#045C8A>top of the file</font> in order to pass n@log information when the page is loading.<br><br>
	However, <font color=#045C8A>if you place this code into a framed page, n@log will not be abled to gather the information of visitors' referral hosts and URLs.</font> Thus, in some cases, n@log will not function properly.<br><br>
	Therefore, <font color=#045C8A>do not place this code on a framed page.</font> If possible, place this code on the page with FRAMESET declarations, but not on the sub-pages.<br><br><br>
	For example, if your index page URL is <font color=#045C8A>http://navyism.com</font>/index.php and your n@log main program path is <font color=#045C8A>http://navyism.com/nalog5</font>/nalog.php , you are likely to place the following PHP source code on the topmost of your index.php file. (Please do not use absolute path for the include statement!)<br>
	<br>
	<span style='font-family:Courier New;font-size:9pt'>
		&lt;?<br>
		$path = "<font color=#045C8A><b>nalog5</b></font>";<br>
		$counter = "<font color=#045C8A><b><?php echo $counter; ?></b></font>";<br>
		include "$path/nalog.php";<br>
		?&gt;<br>
	</span>
	<br>
	If you place this PHP source code correctly, there will be no significant difference when the page is loading.<br><br>
	However, if there is any character (including any blank space) or include source placed before the n@log PHP source code, n@log will stop function and display an error message telling you that the code should be placed on top of the page.<br><br>
	Basically, there are two methods to display the five kinds of a counter - <b>text and graphics</b>. But also <b>skin pattern</b> is available for you to design an integrated output of your desired counter format.<br><br>
	You should make use of the PHP echo function ( &lt;?=$variable?&gt; ) to show them. Listed below are the variable names assigned by n@log for displaying counter results.<br><br>
	Text:<br><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$today_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of today's visitors (text) -> <font color='#666666'><?php echo $today_text; ?></font><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$yester_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of yesterday's visitors (text) -> <font color='#666666'><?php echo $yester_text; ?></font><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$total_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of total visitors (text) -> <font color='#666666'><?php echo $total_text; ?></font><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$now_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of visitors who are currently online (text) -> <font color='#666666'><?php echo $now_text; ?></font><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$peak_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of maximum visitors recorded in peak period (text) -> <font color='#666666'><?php echo $peak_text; ?></font><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$day_peak_text?&gt;&nbsp;&nbsp;:</span>
		The number of maximum visitors recorded in one day (text) -> <font color='#666666'><?php echo $day_peak_text; ?></font><br>
	<br>
	Graphics:<br><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$today_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of today's visitors (graphics) -> <?php echo $today_image; ?><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$yester_image?&gt;&nbsp;&nbsp;&nbsp;:</span>
		The number of yesterday's visitors (graphics) -> <?php echo $yester_image; ?><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$total_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of total visitors (graphics) -> <?php echo $total_image; ?><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$now_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of visitors who are currently online (graphics) -> <?php echo $now_image; ?><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$peak_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		The number of maximum visitors recorded in peak period (graphics) -> <?php echo $peak_image; ?><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$day_peak_image?&gt;&nbsp;:</span>
		The number of maximum visitors recorded in one day (graphics) -> <?php echo $day_peak_image; ?><br>
	<br>
	Skin pattern:<br><br>
	<span style='font-family:Courier New;font-size:9pt'>&lt;?=$nalog_result?&gt;&nbsp;&nbsp;&nbsp;:</span>
		Use the pre-defined skin pattern to display the desired counter format.<br>
	<br>
	<?php echo $nalog_result; ?><br>(please refer to skin.php in the skin folder to customize your counter format)<br><br><br>
	The purpose of the file nalog.php is to count and output the results.<br><br>
	However, if you do not need to analyze the web site traffic or keep track of referrer data, but only need to count the number of visitors currently online (NOW connections), you would better to include nalog_viewer.php instead of nalog.php . This will help eliminating the unnecessary usage of network resources.<br>
	<br>
	<span style='font-family:Courier New;font-size:9pt'>
		&lt;?<br>
		$path = "<font color=#045C8A><b>nalog5</b></font>";<br>
		$counter = "<font color=#045C8A><b><?php echo $counter; ?></b></font>";<br>
		include "$path/nalog_viewer.php";<br>
		?&gt;<br>
	</span>
	<br>
	As mentioned above, if you place this PHP source code instead of the former one, n@log will not analyze the web site traffic, but only monitor the visitors currently online.<br>
	<br>
	By the way, you can include nalog_viewer.php on each page of your web site in order to show the counter statistics on every page. The purpose of nalog_viewer.php is not to record any new data from the visitors, but to display current statistics of the counter.
	</td></tr>
	<tr><td colspan=2 height=10></td></tr>
	<tr><td colspan=2 height=1 bgcolor=#2CBBFF></td></tr>
</table>

<table width="95%" cellpadding=0 cellspacing=0 border=0 align=center>
	<tr>
	<td><font size=1><?php echo $lang[copy]; ?></td>
	<td align=right>
		<span style='font-size:6pt'>&#9654;</span>
		More information or support [ <a href="http://english.navyism.com" target=_blank>English</a> |
		<a href="http://navyism.com" target=_blank>Korean</a> |
		<a href="http://kiddiken.net" target=_blank>Traditional Chinese</a> ]
		<span style='font-size:6pt'>&#9654;</span>
		<a href="javascript:window.close()">Close</a>
	</td>
	</tr>
</table>
