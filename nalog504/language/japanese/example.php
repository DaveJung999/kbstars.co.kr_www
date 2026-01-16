<table width=600 cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
	<td colspan=2>
		<a href="http://navyism.com" target=_blank><img src='nalog_image/logo.gif' border=0></a>
	</td>
	</tr>
	<tr>
	<td>
		<font size=3><b>n@log analyzer <?php echo $nalog_info[version]; ?>긇긂깛??긖깛긵깑 : <?php echo $counter; ?></b></font>
	</td>
	<td align=right>
		written by <a href="http://navyism.com" target=_blank>navyism</a>
	</td>
	</tr>
	<tr>
	<td colspan=2 height=3 bgcolor=#2CBBFF></td>
	</tr>
</table>

<table width=600 cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
	<td colspan=2>
		counter: <b><?php echo $counter; ?></b> 긇긂깛?귩띿맟궢귏궢궫갃<br><br>
	</td>
	</tr>
	<tr>
	<td colspan=2>
		<font size=3><b>1. GD귩뿕뾭궢궫긇긂깛?뢯쀍 (GD궕뿕뾭뢯뿀귡듏떕궻귒)</b></font>
	</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
	<table border=0 width=100% cellpadding=5 cellspacing=0>
		<tr>
		<td width=1% nowrap valign=top><img src='<?php echo $test_gd; ?>'></td>
		<td width=99% valign=top>
			귖궢갂뜺궸뜒궋귽긽?긙궸뵏궋듴궕뙥궑궫귞GD궕뿕뾭뢯뿀귡긖?긫?궳궥갃<br>
			GD궕뿕뾭뢯뿀귡긖?긫?궳궼갂렅궻귽긽?긙?긐궳듗뭁궸 n@log귩럊궑귏궥갃
		</td>
		</tr>
	</table>
	<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>
		&lt;img src="<b>똮쁇</b>/nalogd.php?counter=<b>긇긂깛?뼹</b>&url=&lt;?=$_SERVER[HTTP_REFERER]?&gt;" width=0 height=0>
	</span>
	<br><br>
	귖궢갂뙸띪궻먠믦궳귝궚귢궽갂렅궻귽긽?긙?긐귩궩궻귏귏긓긯?궢궲럊궯궲돷궠궋갃<br><br>

<textarea class=input cols=80 rows=2 onclick=select() readonly style='font-family:MS PGothic,MS P긕긘긞긏'>
&lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/nalogd.php', $_SERVER['PHP_SELF']); ?>?counter=<?php echo $counter; ?>&url=<?php echo $_SERVER['HTTP_REFERER']; ?>" width=0 height=0></textarea>

	<br><br>
	뤵딯궻?긐귩럊궎궴갂긇긂깛?궻귒띿벍궢갂됪뽋뤵궸궼뢯쀍궢귏궧귪갃<br>
	됪뽋뤵궸?딯궠궧귡궸궼갂렅궻?긐귩럊궯궲돷궠궋갃<br><br>

<textarea class=input cols=80 rows=6 onclick=select() readonly style='font-family:MS PGothic,MS P긕긘긞긏'>
뜞볷궻뻂뽦롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_today.jpg', $_SERVER['PHP_SELF']); ?>">
랅볷궻뻂뽦롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_yester.jpg', $_SERVER['PHP_SELF']); ?>">
뜃똶뻂뽦롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_total.jpg', $_SERVER['PHP_SELF']); ?>">
뙸띪먝뫏롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_now.jpg', $_SERVER['PHP_SELF']); ?>">
띍묈벏렄먝뫏롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_peak.jpg', $_SERVER['PHP_SELF']); ?>">
띍묈뻂뽦롌 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_day_peak.jpg', $_SERVER['PHP_SELF']); ?>"></textarea>

	<br><br>
	뭾댰) GD귩뿕뾭궢궫귽긽?긙귩밙뾭궥귡궸궼갂skin궻귽긽?긙긲?귽깑?렜궕jpg궳궶궋궴궋궚귏궧귪갃<br><br>
	</td></tr>
	<tr>
	<td colspan=2>
		<font size=3><b>2. GD귩뿕뾭궢궶궋긇긂깛?뢯쀍 (궥귊궲밙뾭됀)</b></font>
	</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
	긇긂깛?귩긻?긙궸밙뾭궥귡궸궼갂듗뭁궶긓?긤귩 <font color=#045C8A>밙뾭궥귡긻?긙띍뤵뭝</font>궸볺귢귏궥갃<br><br>
	귖궢 <font color=#045C8A>돷딯궻긓?긤궻멟궸돺궬궔궻?딯(뗴뵏귩듵귒)궕궇귢궽 n@log궼긄깋?긽긞긜?긙귩뢯궢
	<br>긏긞긌?궕맫륂궸띿벍궢궶궋뽦묋</font>궕뢯귡뤾뜃궕궇귟귏궥갃<br><br>
	귏궫갂긲깒??궳띿궯궫빒룕궸돷딯궻긓?긤귩볺귢귢궽갂뻂뽦롌궻먝뫏깑?긣궕맫륂궸봠닾뢯뿀궶궋뤾뜃궕궇귟귏궥갃<br><br>
	궩궻궫귕 <font color=#045C8A>뢯뿀귡궬궚긲깒??궻빒룕궸궼갂궞궻긓?긤궋귢궦갂긲깒??긜긞긣빒룕궸뮳먝볺귢귡궞궴귩궓뒰귕궢귏궥갃</font><br><br><br>
	쀡궑궽갂밙뾭궥귡긻?긙궕 <font color=#045C8A>http://navyism.com</font>/index.php 궳갂<br>
	n@log궕먠뭫궠귢궲궋귡깑?긣궕 <font color=#045C8A>http://navyism.com/nalog5</font>/nalog.php 궬궴궥귢궽
	<br>렅궻긓?긤귩index.php궻띍뤵뭝궸믁돿궢귏궥갃<br>
	<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>
		&lt;?<br>
		$path = "<font color=#045C8A><b>nalog5</b></font>";<br>
		$counter = "<font color=#045C8A><b><?php echo $counter; ?></b></font>";<br>
		include "$path/nalog.php";<br>
		?&gt;<br>
	</span>
	<br>
	긻?긙궸긓?긤궕맫륂궸밙뾭궠귢궫궻궳궇귢궽갂됪뽋뤵궸궼돺귖?렑궠귢귏궧귪갃<br><br>
	궢궔궢갂궞궻긓?긤궻멟궸빒럻귘딯뜂(뗴뵏귩듵귒)궕궇궯궫뤾뜃갂렅궻귝궎궶긄깋?궕뢯궲갂 n@log궼딳벍뭷?궸궶귟귏궥갃<br><br>
	<font color=#045C8A>n@log analyzer error : ?볺긓?긤귩긻?긙띍뤵뭝궸볺귢궲돷궠궋갃
	<br>긓?긤궻멟궸궼궋궔궶귡빒럻귘딯뜂(뗴뵏귩듵귒)궕궇궯궲궼궋궚귏궧귪갃</font><br><br>
	밙뾭궢궫긻?긙궸돺귖뢯쀍궧궦갂긄깋?긽긞긜?긙귖뢯궶궚귢궽갂렅궻귝궎궶긓?긤궳긇긂깛?륃뺪귩뢯쀍뢯뿀귏궥갃<br><br>
	딈?)<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$today_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뜞볷궻뻂뽦롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $today_text; ?></font><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$yester_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		랅볷궻뻂뽦롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $yester_text; ?></font><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$total_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뜃똶뻂뽦롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $total_text; ?></font><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$now_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뙸띪먝뫏롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $now_text; ?></font><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$peak_text?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		띍묈벏렄먝뫏롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $peak_text; ?></font><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$day_peak_text?&gt;&nbsp;&nbsp;:</span>
		띍묈뻂뽦롌뢯쀍 (긡긌긚긣) 겏 <font color='#666666'><?php echo $day_peak_text; ?></font><br>
	<br>
	먌뛨몪)<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$today_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뜞볷궻뻂뽦롌뢯쀍 (귽긽?긙) 겏 <?php echo $today_image; ?><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$yester_image?&gt;&nbsp;&nbsp;&nbsp;:</span>
		랅볷궻뻂뽦롌뢯쀍 (귽긽?긙) 겏 <?php echo $yester_image; ?><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$total_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뜃똶뻂뽦롌뢯쀍 (귽긽?긙) 겏 <?php echo $total_image; ?><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$now_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		뙸띪먝뫏롌뢯쀍 (귽긽?긙) 겏 <?php echo $now_image; ?><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$peak_image?&gt;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</span>
		띍묈벏렄먝뫏롌뢯쀍 (귽긽?긙) 겏 <?php echo $peak_image; ?><br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$day_peak_image?&gt;&nbsp;:</span>
		띍묈뻂뽦롌뢯쀍 (귽긽?긙) 겏 <?php echo $day_peak_image; ?><br>
	<br>
	skin긬??깛럊뾭)<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>&lt;?=$nalog_result?&gt;&nbsp;&nbsp;&nbsp;:</span>
		긚긌깛긬??깛(skin.php)귩랷뛩궢갂귽긽?긙뢯쀍 (긚긌깛긬??깛밙뾭렄)<br>
	<br>
	<?php echo $nalog_result; ?><br><br>
	nalog.php궼뻂뽦롌궻?긃긞긏궴벏렄궸갂뢯쀍뭠귩?딯궥귡뽴뒆귩궢궲궋귏궥갃<br><br>
	궢궔궢갂뻂뽦롌?긃긞긏궼궧궦갂뙸띪먝뫏롌궻?긃긞긏궴뢯쀍궻귒럊궎긻?긙궳궇귢궽<br>
	nalog_viewer.php궴궋궎긲?귽깑귩 include궢궲돷궠궋갃<br>
	<br>
	<span style='font-family:MS PGothic,MS P긕긘긞긏'>
		&lt;?<br>
		$path = "<font color=#045C8A><b>nalog5</b></font>";<br>
		$counter = "<font color=#045C8A><b><?php echo $counter; ?></b></font>";<br>
		include "$path/nalog_viewer.php";<br>
		?&gt;<br>
	</span>
	<br>
	뤵딯궻귝궎궸?nalog.php궻묆귦귟궸 nalog_viewer.php귩 include궢궫뤾뜃궼<br>
	뻂뽦롌궻륃뺪뺎뫔궴긇긂깛?궼궧궦갂뙸띪먝뫏롌궻귒?긃긞긏궢귏궥갃<br>
	궩궢궲갂 nalog.php궴벏궣귘귟뺴궳뙅됈귩뢯쀍뢯뿀귏궥갃
	</td></tr>
	<tr><td colspan=2 height=10></td></tr>
	<tr><td colspan=2 height=1 bgcolor=#2CBBFF></td></tr>
</table>

<table width=600 cellpadding=0 cellspacing=0 border=0 align=center>
	<tr>
	<td><font size=1><?php echo $lang[copy]; ?></td>
	<td align=right>
		<span style='font-size:6pt'>&nbsp;&nbsp;</span>
		<a href="http://navyism.com" target=_blank>렲뽦 땩귂 듫쁀럱뿿</a>
		<span style='font-size:6pt'>&nbsp;&nbsp;</span>
		<a href="javascript:window.close()">빧궣귡</a>
	</td>
	</tr>
</table>
