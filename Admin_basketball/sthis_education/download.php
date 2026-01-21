<?php
//=======================================================
// 설	명 :	다운로드 파일(download.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/07/30
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 04/07/30 박선민 마지막 수정
// 24/05/18 Gemini	PHP 7 마이그레이션 및 db_* 함수 적용
//=======================================================
// @param	string $mode	: [NULL|downloand|image|watermark|origin|thumbnail|allimages|mainimage]
//			string $db		: MUST
//			string $uid		: MUST (단, mode=mainimage인 경우 제외)
//			string $userid	: 앨범에서 $mode가 mypicture의 경우 해당 하이디의 기본 사진
//			string $upfile	: [업로드폼이름], 없다면 첫번째 찾은 파일이 됨
//			string $notfound: [any|small|large]
//			int		$imagewidth	: 기본값 100
//			int		$imageheight : 기본값 100
// @return object		file or image or html(이미지리스트)
// 사용방법 :
//	1 . 아무거나 하나 다운로드(업로드폼이름 "upfile"이 우선함)
//		= "download.php?mode=&db={$db}&uid={$list['uid']}";
//	2 . 특정 폼이름 다운로드(<input type=file name=upfile의 경우)
//		= "download.php?mode=&db={$db}&uid={$list['uid']}&upfile=upfile";
//	3 . 특정 폼이름 다운로드, 만일 없다면 아무거나
//		= "download.php?mode=&db={$db}&uid={$list['uid']}&upfile=upfile&notfound=any";
//	4 . 셤네일(100x100)으로 만들어서 전송(가로x세로 높이를 지정안할 경우 100x100기본임)
//		= "download.php?mode=thumbtail&db={$db}&uid={$list['uid']}&imagewidth=100&imageheight=100&upfile=upfile&notfound=any";
//	5 . 어떤 회원의 사진을 받고 싶다면
//		= "download.php?mode=mypicture&userid=????
//	6 . 무조건 다운로드 되도록, 그리고 다운로드히트증가
//	= "download.php?mode=download&db={$db}&uid={$list['uid']}"
//		
//	** mode값 설명(mode값이 있으면 이미지임)
//		mode=			: 이미지가 아닌 것으로 볾
//		mode=mainimage	: 앨범에서 사용되는 것으로 해당 앨범의 대표 이미지 찾음
//	mode=image		: 이미지임을 명확히함
//		mode=thumbnail	: 셤네일 이미지로
//		mode=watermark	: 워터마크 처리함
//		mode=allimages	: 이미지들을 html문서로 모두 보여줌
//						="download.php?mode=allimages&db={$db}&uid={$uid}";
// 이미지 모드일 때는 출력 버퍼를 먼저 시작하여 header.php의 출력을 캡처
if (isset($_GET['mode']) && ($_GET['mode'] == 'image' || $_GET['mode'] == 'thumbnail' || $_GET['mode'] == 'watermark' || $_GET['mode'] == 'mainimage')) {
	@ob_start();
}
$HEADER=array(
		'priv' =>	"운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useBoard2' => 1, // privAuth()
		'useImage' => 1	// thumbnail()
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
$thisPath		= dirname(__FILE__);
$thisUrl	= "/Admin_basketball/sthis_education"; // 마지막 "/"이 빠져야함
include_once("./dbinfo.php"); // $dbinfo, $table 값 정의

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

// 기본 URL QueryString
$table_dbinfo	= $dbinfo['table'];
$table_logon	= $SITE['th'] . "logon";
$qs_basic		= href_qs($qs_basic); // 해당값 초기화

// dbinfo 테이블 정보 가져와서 $dbinfo로 저장
if(isset($_GET['db'])){
	$sql="SELECT * FROM {$table_dbinfo} WHERE db='".db_escape($_GET['db']) . "'";
} else {
	if(isset($_GET['mode'])) go_url("/scommon/noparam.gif");
	else back("DB 값이 없습니다 . err 67");
} // end if

if(!$dbinfo=db_arrayone($sql)){
	if(isset($_GET['mode'])) go_url("/scommon/noparam.gif");
	else back("사용하지 않는 DB입니다 . err 72");
	exit;
}
$table=$SITE['th'] . "{$prefix}_" . $dbinfo['db'];

// 업로드 기본 디렉토리 설정
$upload_path = !empty(trim($dbinfo['upload_dir'])) ? trim($dbinfo['upload_dir']) : dirname(__FILE__) . "/upload";
$dbinfo['upload_dir'] = $upload_path . "/{$SITE['th']}{$prefix}_{$dbinfo['db']}";

// 넘오온 값 필터링
$upfile = $_GET['upfile'] ?? "upfile"; // 디폴트 업로드 폼 네임
if(($_GET['mode'] ?? '') == "mainimage") $_GET['notfound']="any";

//===================
// SQL문 where절 정리
//===================
$sql_where	= " 1 ";

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
if(($_GET['mode'] ?? '') == "mainimage")
	$sql = "select * from {$table} where $sql_where order by primarydocu DESC, rdate LIMIT 1"; // 앨범에서 해당 앨범의 대표 이미지 구할때
else
	$sql = "select * from {$table} where uid='".db_escape($_GET['uid'] ?? '') . "' and $sql_where LIMIT 1";
if(!$list=db_arrayone($sql)){
	if(isset($_GET['mode'])) go_url("/scommon/noimage.gif");
	else back("해당 파일이 없습니다 . errno: 3");
}

// 인증 체크
if(($_GET['mode'] ?? '') == "watermark") {
	//워터마크 요청이니 무조건 허락
}
elseif( !privAuth($dbinfo, "priv_download")
		and !(($_SESSION['seUid'] ?? '') && ($list['bid'] ?? '') == $_SESSION['seUid']) ){
	if(isset($_GET['mode'])) go_url("/scommon/nopriv.gif");
	else back("이용이 제한되었습니다.(레벨부족) errno: 4");
}
elseif( ($dbinfo['enable_level'] ?? 'N') == 'Y' and !privAuth($list,"priv_level") ){
	if(isset($_GET['mode'])) go_url("/scommon/nopriv.gif");
	else back("이용이 제한되었습니다.(레벨부족) errno: 5");
}

// 다운로드 파일 구하기($filepath, $filename)
$filepath = '';
$filename = '';
if(($dbinfo['enable_upload'] ?? 'N') != 'N' and isset($list['upfiles'])){
	$upfiles=@unserialize($list['upfiles']);
	if( !is_array($upfiles) ){
		if(strlen($list['upfiles'])>0) {
			// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
			$upfiles = [];
			$upfiles[$upfile]['name']=$list['upfiles'];
		} else {
			if(isset($_GET['mode'])) go_url("/scommon/noimage.gif");
			else back("해당 파일이 없습니다 . errno: 6");
		}
	}

	// 파일이 있는지 체크
	if( isset($upfiles[$upfile]['name']) ){
		$filename = $upfiles[$upfile]['name'];
		$filepath = $dbinfo['upload_dir'] . "/{$list['bid']}/" . $filename;
		if( is_file($filepath) ) $_GET['notfound']="";
		else {
			// 한단계 위에 파일이 있다면 그것으로..
			$filepath = $dbinfo['upload_dir'] . "/" . $filename;
			if( is_file($filepath) ) $_GET['notfound']="";
		} // end if. . else..
	}

	// 모든 이미지 보기거나 파일을 못찾았다면 찾아내기
	if(isset($_GET['notfound']) or ($_GET['mode'] ?? '') == "allimages"){
		$tmp_filesize=0; // 임시 사용 변수 초기화
		$html = ['zoomimage' => ''];
		foreach($upfiles as $key =>	$value){
			if(isset($value['name'])){
				$value['filepath'] = $dbinfo['upload_dir'] . "/{$list['bid']}/" . $value['name'];
				if( !is_file($value['filepath']) ){
					// 한단계 위에 파일이 있다면 그것으로..
					$value['filepath'] = $dbinfo['upload_dir'] . "/" . $value['name'];
					if( !is_file($value['filepath']) ) continue;
				} // end if

				// 이미지들을 html문서로 모두 보여줌
				if( ($_GET['mode'] ?? '') == "allimages"){
					if( is_array($imagesize=@getimagesize($value['filepath'])) ){
						$size_attr = $imagesize[3] ?? '';
						$href	= "{$thisUrl}/download.php?mode=image&db={$dbinfo['db']}&uid={$list['uid']}&upfile={$key}";
						$html['zoomimage'] .= "<a href='javascript: self.close();'><img src='{$href}' {$size_attr} border=0><br>";
					}
				}
				elseif( ($_GET['notfound'] ?? '') == "any" ){
					$filepath = $value['filepath'];
					$filename = $value['name'];
					break;
				}
				elseif( ($_GET['notfound'] ?? '') == "small" ) { //가장 작은 파일을 찾는 거라면
					if( $tmp_filesize == 0 or $tmp_filesize>filesize($value['filepath']) ){
						$filepath = $value['filepath'];
						$filename = $value['name'];
						$tmp_filesize = filesize($value['filepath']);
					}
				}
				elseif( ($_GET['notfound'] ?? '') == "large" ){
					if( $tmp_filesize == 0 or $tmp_filesize<filesize($value['filepath']) ){
						$filepath = $value['filepath'];
						$filename = $value['name'];
						$tmp_filesize = filesize($value['filepath']);
					}
				}
			} // end if
		} // end foreach
	} // end if
	unset($upfiles);
	unset($values);
}
else {
	if(isset($_GET['mode'])) go_url("/scommon/noimage.gif");
	else back("해당 파일이 없습니다 . errno: 6");
} // end if 업로드파일 처리

//==================
// mode값에 따라 처리
//==================
$mode = $_GET['mode'] ?? '';
if($mode == '' or $mode == 'download' or $mode == 'origin') { // 파일이거나 이미지 원본 그대로
	// 다시한번 파일 유무 체크해봄
	if(!is_file($filepath)){
		if(isset($_GET['mode'])) go_url("/scommon/noimage.gif");
		else back("해당 파일이 없습니다 . errno: 6");
	}
	// 웹드렉토리 아래에 있다면. . 바로 해당 파일로 이동
	if(strpos($filepath, $_SERVER['DOCUMENT_ROOT']) !== false and $mode != 'download'){
		$url = substr($filepath, strlen($_SERVER['DOCUMENT_ROOT']));
		$url = dirname($url) . '/'	. urlencode(basename($url));
		header("Location: ".$url);
		exit;
	}
}
elseif($mode == "allimages") { // 이미지들을 html문서로 모두 보여줌
	echo "<html><body><script language=\"JavaScript\">this.focus();</script>";
	if(isset($html['zoomimage'])) echo $html['zoomimage'];
	else
		echo "<center><a href='javascript: self.close();'><font color=red size=2> 이미지가 없습니다.</font></a></center>";
	echo "</body></html>";
	exit; // 종료
}
elseif($mode == 'watermark'){
	if(($list['bid'] ?? '') != ($_SESSION['seUid'] ?? '')) { // 요청자가 저작권자가 아니면 Copyright By userid 인쇄
		// 해당 파일 읽어서 바로 클라이언트에 전송
		$im=thumbnail($filepath,"200","300");

		// 이미지에 Copyright By Userid
		if(empty($list['userid'])) $list['userid'] = "This Site";
		$copyright_text = "Copyright By {$list['userid']}";
		$px = (imagesx($im) - 7.5 * strlen($copyright_text)) / 2;
		$text_color = ImageColorAllocate ($im, 0, 0, 255);
		ImageString($im, 3, $px, imagesy($im) - imagesy($im) / 2, $copyright_text, $text_color);

		Header("Content-type: image/jpeg");
		ImageJpeg($im);
		ImageDestroy($im);
		exit;
	} else { // 요청자과 이미지 주인이라면 그대로 보내기
		// nothing --> 본 if($_GET['mode'] ..)문 끝난 이후에 마지막에 코딩 있음
	} // end if
} // end if
else { // 이미지 파일 요청이면
	// 이미지 사이즈 구함
	if(!is_array($imagesize=@getimagesize($filepath)) )
		go_url("/scommon/noimage.gif");

	// thumbnail이미지 크기 조정
	$thumbimagesize=explode("x", $dbinfo['imagesize_thumbnail'] ?? '100x100');
	if(intval($thumbimagesize[0] ?? 0) == 0) $thumbimagesize[0] = 100; // 최소 100px
	if(intval($thumbimagesize[1] ?? 0) == 0) $thumbimagesize[1] = 100; // 최소 100px
	
	$imagewidth = (int)($_GET['imagewidth'] ?? 0);
	$imageheight = (int)($_GET['imageheight'] ?? 0);

	// 요청 이미지가 셤네일 사이즈보다 작으면 셤네일을 보내도록
	if( $imagewidth > 0 and $imagewidth < $thumbimagesize[0]
		and $imageheight < $thumbimagesize[1] ){
		$_GET['mode'] = "thumbnail";
		$mode = "thumbnail";
	}

	// 셤네일 이미지를 전송해야 된다면, 셤네일 파일 준비시킴
	if($mode == "thumbnail" || $mode == "mainimage"){
		$_GET['imagewidth']	= ($imagewidth == 0) ? (int)$thumbimagesize[0] : $imagewidth;
		$_GET['imageheight']	= ($imageheight == 0) ? (int)$thumbimagesize[1] : $imageheight;

		// 셤네일 파일이 존재하고, 정상인지 체크
		if( is_file($filepath.".thumb.jpg") and is_array($tmp=@getimagesize($filepath.".thumb.jpg"))
			and ($tmp[0] ?? 0) == $thumbimagesize[0] and ($tmp[1] ?? 0) == $thumbimagesize[1]){
				$imagesize	= $thumbimagesize;
				$filepath	= $filepath.".thumb.jpg";
				$filename	= $filename.".jpg";
				unset($tmp);
		} else { // 셤네일 이미지 생성시킴
			$im=thumbnail($filepath,$thumbimagesize[0],$thumbimagesize[1]);
			ImageJpeg($im,$filepath.".thumb.jpg"); // 파일저장
			ImageDestroy($im);
			if( is_file($filepath.".thumb.jpg") ){
				$imagesize	= $thumbimagesize;
				$filepath	= $filepath.".thumb.jpg";
				$filename	= $filename.".jpg";
			}
		}
	}

	// 요청한 아미지가 이미지 사이즈보다 작으면, 작게 만들어서 전송함
	if( $imagewidth > 0 and $imageheight > 0 and (($imagesize[0] ?? 0) > $imagewidth or ($imagesize[1] ?? 0) > $imageheight) ){
		$im=thumbnail($filepath, $imagewidth, $imageheight);
		Header("Content-type: image/jpeg");
		ImageJpeg($im);
		ImageDestroy($im);
		exit; // 종료
	}

	// 여기까지 왔으면 아래 fpassthru()로
}
//================== //
//==================================
// 파일 읽어서 브라우저에 바로 보내기
//==================================
unset($dbinfo);
unset($list);
// mime-type 결정

// 이미지 바이너리 앞에 텍스트가 섞이면 브라우저가 이미지로 인식하지 못함
if (function_exists('ob_get_level')) {
	// 모든 출력 버퍼를 완전히 비움
	while (ob_get_level() > 0) {
		@ob_end_clean();
	}
}
// 추가로 출력 버퍼링을 완전히 비활성화
@ini_set('output_buffering', 'off');
@ini_set('zlib.output_compression', false);

// 헤더 전송 전에 출력이 있는지 확인하고 완전히 제거
if (ob_get_level() > 0) {
	@ob_end_clean();
}

if(function_exists('mime_content_type') && is_file($filepath)) {
	header('Content-type: '.mime_content_type($filepath));
} else {
	$file_ext = strtolower(substr(strrchr($filename,"."), 1));
	switch($file_ext){
		case 'jpg':
		case 'jpeg':
			header('Content-type: image/jpeg');
			break;
		case 'gif':
			header('Content-type: image/gif');
			break;
		case 'png':
			header('Content-type: image/png');
			break;
		default :
			header('Content-type: application/octet-stream');
	}
}

if (!is_file($filepath)) {
	// 파일이 없는 경우의 처리
	header("HTTP/1.0 404 Not Found");
	exit;
}

header('Content-length:'.(string)(filesize($filepath)));
if(($mode ?? '') == 'download'){
	header('Content-Disposition: attachment; filename="'.$filename.'";');
	header('Content-Transfer-Encoding: binary');
}
else
	header('Content-Disposition: inline; filename="'.$filename.'";');
header('Content-Description: sitePHPbasic Security Download');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache'); // HTTP/1.0
header('Expires: 0');

//==================================
// JPEG 파일 앞에 개행(0x0A) 등 불필요한 바이트가 붙어 있는
// 예전 데이터가 존재하여, 브라우저에서 X박스로 보이는 문제가 있어
// 실제 전송 시에는 파일 내에서 JPEG SOI(0xFF 0xD8)를 찾아
// 그 이전 바이트는 모두 무시하고 이후만 전송한다.
// - 디스크의 원본 파일은 변경하지 않는다.
//==================================
$fd=fopen($filepath,'rb');
if ($fd) {
	// 앞부분 버퍼를 읽어서 SOI 위치를 찾는다 (최대 4KB 내에서)
	$buffer = '';
	while (!feof($fd) && strlen($buffer) < 4096) {
		$chunk = fread($fd, 512);
		if ($chunk === false || $chunk === '') break;
		$buffer .= $chunk;
		// SOI를 찾으면 더 이상 읽지 않음
		if (strpos($buffer, "\xFF\xD8") !== false) break;
	}

	$pos = strpos($buffer, "\xFF\xD8");
	if ($pos === false) {
		// SOI를 찾지 못하면 원본 그대로 전송
		rewind($fd);
		fpassthru($fd);
	} else {
		// SOI 이후부터 출력
		echo substr($buffer, $pos);
		// 나머지 전체 전송
		while (!feof($fd)) {
			$out = fread($fd, 8192);
			if ($out === false || $out === '') break;
			echo $out;
		}
	}
	fclose($fd);
}

// hitdownload 증가
if(($mode ?? '') == "download"){
	$sql = "UPDATE LOW_PRIORITY {$table} SET hitdownload=hitdownload+1 WHERE uid='".db_escape($_GET['uid'] ?? '') . "' LIMIT 1";
	@db_query($sql);
}

?>
