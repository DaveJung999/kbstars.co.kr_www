<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
// 25/01/XX Auto 단축 태그 <?= → <?php echo 변경
//=======================================================
$HEADER = array(
	'priv'		=>'', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo'	=>1,
	'html_skin' =>'2022_d02'
);

if( $_GET['html_skin']) 
	$HEADER['html_skin'] = $_GET['html_skin'];

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
	if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"])==false ) {
		$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
		$_SESSION['seHTTP_REFERER'] = $seHTTP_REFERER;
	}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
	// 넘오온값 체크
	$table_player = "player";
	$table_cmletter	= "new21_board2_cmletter";
	
	// 해당 선수 정보
	$sql = "SELECT * from {$table_player} where tid=13 and uid='{$_GET['pid']}'";
	if(!$player=db_arrayone($sql))
		back('선수 정보가 없습니다.');
 
	
	$player['p_life']	= replace_string($player['p_life'], 'text');
	
	$_GET['mNum'] = "0203";
?>

				<p id="contents_title">선수</p> 
				<div id="sub_contents_main" class="clearfix">
					<table width="95%" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td height="50" align="right"><table border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td width="96"><a href="/kbstars/2022/d02/03.php?mNum=0203"><img src="/images/2011/image/other.jpg" width="96" height="25" border="0" align="absmiddle" /></a></td>
						</tr>
						</table></td>
					</tr>
					<tr>
						<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td><table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td width="450" align="center" valign="top"><img src="/sthis/sthis_player/download.php?uid=<?php echo $player['uid']; ?>&amp;upfile=upfile1&amp;mode=image&amp;notfound=any" width="420" height="690" /></td>
								<td align="right" valign="top"><table width="500" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td><img src="/images/2016/new/sub_player/gray_top.jpg" width="500" height="16" /></td>
								</tr>
								<tr>
									<td bgcolor="#F6F6F6"><table width="500" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><table width="330" border="0" cellspacing="0" cellpadding="0">
										<tr>
											<td height="25"><table width="330" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="11">&nbsp;</td>
												<td width="69" align="left"><strong>생년월일</strong></td>
												<td width="250"><?php echo $player['p_bdate']; ?></td>
											</tr>
											</table></td>
										</tr>
										<tr>
											<td height="25"><table width="330" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="11">&nbsp;</td>
												<td width="69" align="left"><strong>포지션</strong></td>
												<td width="250"><?php echo $player['p_position']; ?></td>
											</tr>
											</table></td>
										</tr>
										<tr>
											<td height="25"><table width="330" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="11">&nbsp;</td>
												<td width="69" align="left"><strong>신장</strong></td>
												<td width="250"><?php echo $player['p_height']; ?>
												cm</td>
											</tr>
											</table></td>
										</tr>
										<tr>
											<td height="25"><table width="330" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="11">&nbsp;</td>
												<td width="69" align="left"><strong>출신학교</strong></td>
												<td width="250"><?php echo $player['p_school']; ?></td>
											</tr>
											</table></td>
										</tr>
										<tr>
											<td height="25"><table width="330" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td width="11">&nbsp;</td>
												<td width="69" align="left"><strong>별명</strong></td>
												<td width="250"><?php echo $player['p_nickname']; ?></td>
											</tr>
											</table></td>
										</tr>
										</table></td>
										<td width="170" align="center" valign="middle"><img src="/images/team_logo/player_detail_page/logo.jpg" width="167" height="103" /></td>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td><img src="/images/2016/new/sub_player/gray_midlle.jpg" width="500" height="15" /></td>
								</tr>
								<tr>
									<td align="center" background="/images/2011/image/gray_midlle2.jpg"><table width="450" height="40" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td><?php echo $player['p_life']; ?></td>
									</tr>
									</table></td>
								</tr>
								<tr>
									<td><img src="/images/2016/new/sub_player/gray_end.jpg" width="500" height="26" /></td>
								</tr>
								</table></td>
							</tr>
							</table></td>
						</tr>
						</table></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					</table>
				</div>
		
<?php
//=======================================================
echo $SITE['tail'];
?>
