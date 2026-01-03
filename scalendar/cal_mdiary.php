<?php
$HEADER=array(
		header	=>1, // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		html	=>"schedule" // html header 파일(/stpl/basic/index_$HEADER['html'].php 파일을 읽음)
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sin/header.php");
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
						<font face=굴림><span style='font-size:9pt'>
						&nbsp;<?=$intThisMonth
?>월중 일정
						</span></font>
					</td>
				</tr>
				<tr height=40>
					<td>
<?php
					$intMday=$intThisYear."-".$intThisMonth."-01";
					$sqlList = "Select cc_no, cc_title,cc_sdate, cc_shour, cc_smin, cc_ehour, cc_emin,cc_desc From  club_cal  Where ";
					$sqlList = $sqlList." (cc_memid = '".$session_memid."'  or cc_open = '1')  ";
					//'$sqlList = $sqlList." and  str_date_diff(""d"",cc_sdate,'".$intMday."') = 0 "
					$sqlList = $sqlList." and  cc_sdate = '".($intMday)."' ";
					$sqlList = $sqlList." and  cc_dtype = '3'  ";
					$sqlList = $sqlList."  Order by  cc_shour asc \n";

					$result=db_query($sqlList)	;	
					
					// PHP 7+에서는 mysql_* 함수가 제거되었으므로 db_* 함수 사용
					if ($result == false)
						$rcount = 0;
					else
						$rcount = db_count($result);

					If  ($rcount !=0 )
					{
						Do {
							$rsList = db_array($result);
							// db_array()는 연관 배열을 반환하므로 필드명으로 접근
							$cc_no = $rsList['cc_no'];
							$cc_title = $rsList['cc_title'];
							$cc_sdate = $rsList['cc_sdate'];
							$cc_shour = $rsList['cc_shour'];
							$cc_smin = $rsList['cc_smin'];
							$cc_ehour = $rsList['cc_ehour'];
							$cc_emin = $rsList['cc_emin'];
							$cc_desc = $rsList['cc_desc'];

							$cc_title = str_replace("<","&lt;", $cc_title);
							$cc_title = str_replace(">","&gt;", $cc_title);

							$cc_desc = substr($cc_desc, 0,150);
							$cc_desc = str_replace("<" , "&lt;", $cc_desc);
							$cc_desc = str_replace(">" , "&gt;", $cc_desc);
							$cc_desc = str_replace(chr(13).chr(10), "<br>", $cc_desc);


							$lhour=$intThisMonth."월중 일정";

							echo "<img src=images/micon.gif border=0>";
							echo  "<font face=굴림><span style='font-size:9pt'><a href=diary.php?d=".$d."&m=view&cid=".$cc_no." onMouseOver=\"view('".$cc_title."', '".$lhour."','".$cc_desc."');\"  onMouseOut=\"noview();\" >".$cc_title."</a></span></font><br> \n"	;

							$rcount = $rcount - 1;
							
						}while ($rcount > 0 );

					}  
					Else
					{
						echo "<font face=굴림><span style='font-size:9pt'>등록된 월중일정이 없습니다</span></font> \n";
					}
					
					

					
?>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>


<?=$SITE['tail']
?>