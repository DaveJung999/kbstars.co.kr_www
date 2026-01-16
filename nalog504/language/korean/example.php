<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
		<td colspan=2>
			<a href="http://navyism.com" target=_blank><img src='nalog_image/logo.gif' border=0></a>
		</td>
	</tr>
	<tr>
		<td>
			<font size=3><b>n@log analyzer <?php echo $nalog_info['version']; ?> 카운터 예제 : <?php echo $counter; ?></b></font>
		</td>
		<td align=right>
			written by <a href="http://navyism.com" target=_blank>navyism</a>
		</td>
	</tr>
	<tr>
		<td colspan=2 height=3 bgcolor=#2CBBFF></td>
	</tr>
</table>

<table width="95%" cellpadding=3 cellspacing=0 border=0 align=center>
	<tr>
		<td colspan=2>
			counter: <b><?php echo $counter; ?></b> 카운터를 생성 하셨습니다.<br><br>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<font size=3><b>1. GD를 이용한 카운터 출력 (GD를 지원할 경우에만 적용가능)</b></font>
		</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
		<table border=0 width=100% cellpadding=5 cellspacing=0>
			<tr>
				<td width=1% nowrap valign=top><img src='<?php echo $test_gd; ?>'></td>
				<td width=99% valign=top>
					만약 좌측의 검정 이미지에 흰색 원이 보이신다면 GD가 지원되는 서버 입니다.<br>
					GD가 지원되는 서버에서는 다음의 이미지 태그로 간단하게 n@log를 사용 하실 수 있습니다.
				</td>
			</tr>
		</table>
		<br>
		<span style='font-family:굴림체,GulimChe'>
			&lt;img src="<b>경로</b>/nalogd.php?counter=<b>카운터이름</b>&url=<?php echo $_SERVER['HTTP_REFERER']; ?>" width=0 height=0>
		</span>
		<br><br>
		만약 현재의 설정대로라면 다음의 이미지 태그를 그대로 복사하여 사용하세요.<br><br>

	<textarea class=input cols=80 rows=2 onclick=select() readonly style='font-family:굴림체,GulimChe'>
	&lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/nalogd.php', $_SERVER['PHP_SELF']); ?>?counter=<?php echo $counter; ?>&url=<?php echo $_SERVER['HTTP_REFERER']; ?>" width=0 height=0></textarea>

		<br><br>
		위의 태그를 이용하시면 카운팅만 이루어지며 화면출력은 이루어지지 않습니다.<br>
		화면출력은 다음의 태그를 이용하시면 됩니다.<br><br>

	<textarea class=input cols=80 rows=6 onclick=select() readonly style='font-family:굴림체,GulimChe'>
오늘방문자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_today.jpg', $_SERVER['PHP_SELF']); ?>">
어제방문자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_yester.jpg', $_SERVER['PHP_SELF']); ?>">
전체방문자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_total.jpg', $_SERVER['PHP_SELF']); ?>">
현재접속자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_now.jpg', $_SERVER['PHP_SELF']); ?>">
최대동시접속자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_peak.jpg', $_SERVER['PHP_SELF']); ?>">
최대방문자 &lt;img src="http://<?php echo $_SERVER['HTTP_HOST'] . preg_replace('/example\.php$/i', '/' . $counter . '_day_peak.jpg', $_SERVER['PHP_SELF']); ?>"></textarea>

		<br><br>
		주의) GD를 이용한 적용을 하시려면 사용 스킨의 이미지 파일 형식이 jpg형태 이어야만 합니다.<br><br>
	</td></tr>
	<tr>
		<td colspan=2>
			<font size=3><b>2. GD를 이용하지 않는 카운터 출력 (모든경우에 적용가능)</b></font>
		</td>
	</tr>
	<tr><td colspan=2 height=3 bgcolor=#2CBBFF></td></tr>
	<tr><td colspan=2>
		카운터를 페이지에 적용하기 위해서는 간단한 코드를 <font color=#045C8A>적용할 페이지의 최상단</font>에 넣어 주어야 합니다.<br><br>
		예: <span style='font-family:굴림체,GulimChe'>
		&lt;?php
		$path = "nalog5";
		$counter = "<?php echo $counter; ?>";
		include "$path/nalog.php";
		?&gt;
		</span>
		<br><br>
		적용된 페이지에 아무것도 출력되지 않고, 에러가 없으면 아래와 같이 출력할 수 있습니다.<br><br>
		기본 텍스트, 이미지, 스킨 패턴 등 <?php echo $nalog_result; ?><br>
	</td></tr>
	<tr><td colspan=2 height=10></td></tr>
	<tr><td colspan=2 height=1 bgcolor=#2CBBFF></td></tr>
</table>

<table width="95%" cellpadding=0 cellspacing=0 border=0 align=center>
	<tr>
		<td><font size=1><?php echo $lang['copy']; ?></td>
		<td align=right>
			<span style='font-size:6pt'>▶</span>
			<a href="http://navyism.com" target=_blank>질문 및 관련자료</a>
			<span style='font-size:6pt'>▶</span>
			<a href="javascript:window.close()">창닫기</a>
		</td>
	</tr>
</table>