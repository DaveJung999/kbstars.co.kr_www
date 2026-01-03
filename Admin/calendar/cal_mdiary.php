<?php
$HEADER=array(
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo'	 => 1, // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'html_skin'	 => "schedule" // html header 파일(/stpl/basic/index_$HEADER['html'].php 파일을 읽음)
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
?>
<?php
	@session_start();
	include("../global/dbconn.inc");
?>

<table border="0" width="130" cellspacing="0" cellpadding="0" bordercolor="#000000" bordercolorlight="#000000">
	<tr>
		<td>
			<table border="1" width="590" cellspacing="0" cellpadding="0" bordercolor="#ffffff" bordercolorlight="#000000">
				<tr height=25>
					<td bgcolor=FFC125>
						<span style='font-size:9pt'>&nbsp;<?=$intThisMonth?>월중 일정</span>
					</td>
				</tr>
				<tr height=40>
					<td>
<?php
					$intMday=$intThisYear."-".$intThisMonth."-01";
					$sqlList = "Select cc_no, cc_title,cc_sdate, cc_shour, cc_smin, cc_ehour, cc_emin,cc_desc From	club_cal	Where ";
					$sqlList = $sqlList." (cc_memid = '".$session_memid."'	or cc_open = '1')	";
					//'$sqlList = $sqlList." and	str_date_diff(""d"",cc_sdate,'".$intMday."') = 0 "
					$sqlList = $sqlList." and	cc_sdate = '" . ($intMday) . "' ";
					$sqlList = $sqlList." and	cc_dtype = '3'	";
					$sqlList = $sqlList."	Order by	cc_shour asc \n";

					$result=db_query($sqlList)	;	
					
					if ($result == false)
						$rcount = 0;
					else
						$rcount = db_count($result);

					If	($rcount !=0 )
					{
						Do {
							$rsList = db_array($result);
							$cc_no = $rsList[0];
							$cc_title = $rsList[1];
							$cc_sdate = $rsList[2];
							$cc_shour = $rsList[3];
							$cc_smin = $rsList[4];
							$cc_ehour = $rsList[5];
							$cc_emin = $rsList[6];
							$cc_desc = $rsList[7];

							$cc_title = str_replace("<","&lt;", $cc_title);
							$cc_title = str_replace(">","&gt;", $cc_title);

							$cc_desc = substr($cc_desc, 0,150);
							$cc_desc = str_replace("<" , "&lt;", $cc_desc);
							$cc_desc = str_replace(">" , "&gt;", $cc_desc);
							$cc_desc = str_replace(chr(13).chr(10), "<br>", $cc_desc);


							$lhour=$intThisMonth."월중 일정";

							echo "<img src=images/micon.gif border=0>";
							echo	"<span style='font-size:9pt'><a href=diary.php?d=".$d."&m=view&cid=".$cc_no." onMouseOver=\"view('".$cc_title."', '".$lhour."','".$cc_desc."');\"	onMouseOut=\"noview();\" >".$cc_title."</a></span><br> \n"	;

							$rcount = $rcount - 1;
							
						}while ($rcount > 0 );

					}	
					Else
					{
						echo "<span style='font-size:9pt'>등록된 월중일정이 없습니다</span> \n";
					}
					
?>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<?=$SITE['tail']?>