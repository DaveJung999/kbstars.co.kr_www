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
	'priv'		=>'', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo'	=>1,
	'html_skin' =>'2022_d07'
);
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
$sql = "select * from new21_board2_contents_2016 where uid = 22 ";
$list = db_arrayone($sql);
?>

				<p id="contents_title">저작권관련안내</p> 
				<div id="sub_contents_main" class="clearfix" style="text-align:left;">
				<?=$list['content']; ?>
				</div>

<?php
//=======================================================
echo $SITE['tail'];
?>

