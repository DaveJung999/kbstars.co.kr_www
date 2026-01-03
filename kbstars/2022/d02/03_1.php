<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo' => 1,
	'html_skin' => '2022_d02',
	'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'useSkin' =>	1, // 템플릿 사용
	'useBoard2' => 1, // privAuth()
	'useApp' => 1
);

if( $_GET['html_skin']) 
	$HEADER['html_skin'] = $_GET['html_skin'];

require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
	$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
	$_SESSION['seHTTP_REFERER'] = $seHTTP_REFERER;
}
	//=======================================================
	// Start.. . (DB 작업 및 display)
	//=======================================================
	function get_player(){
		global $SITE, $GAMEINFO, $PlayerCateBoard, $DEBUG; // global 변수 추
	
		$oldGET = $_GET;
		$_GET = array( p_position =>	"G" );
		include("{$_SERVER['DOCUMENT_ROOT']}/sthis/sthis_player/profile_list.php");
		$_GET = $oldGET;
	} 

?>
				<p id="contents_title">선수</p> 
				<div id="sub_contents_main" class="clearfix">
				
					<table width="990" border="0" cellspacing="0" cellpadding="0">
					<tbody>
						<tr>
						<td align="center"><table width="940" border="0" cellspacing="0" cellpadding="0">
							<tbody>
							<tr>
								<td width="235"><a href="/kbstars/2022/d02/03.php?mNum=0203"><img src="/images/2017/new/sub_player/tab_1_2.png" width="235" height="44" alt=""/></a></td>
								<td><a href="/kbstars/2022/d02/03_1.php?mNum=0203"><img src="/images/2017/new/sub_player/tab_2_1.png" width="235" height="44" alt=""/></a></td>
								<td><a href="/kbstars/2022/d02/03_2.php?mNum=0203"><img src="/images/2017/new/sub_player/tab_3_2.png" width="235" height="44" alt=""/></a></td>
								<td><a href="/kbstars/2022/d02/03_3.php?mNum=0203"><img src="/images/2017/new/sub_player/tab_4_2.png" width="235" height="44" alt=""/></a></td>
							</tr>
							</tbody>
						</table></td>
						</tr>
						<tr>
						<td>&nbsp;</td>
						</tr>
						<tr>
							<td>
<?php
 get_player(); 
?> </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						</table>
			</div><?php
//=======================================================
echo $SITE['tail']; 
?>
