<?php

// 해더 해킹 차단 동작
$SECURITY = array(
	'server_ip'			=> "117.52.31.195", 	// 기입할 경우 해더 해킹 동작
	'domain'			=> "new.kbstars.co.kr", // 기입할 경우 해더 해킹 동작
	'header_version'	=> "1"					// 기입할 경우 해더 해킹 동작
);
//header_security();

// 수정 포인트: $HEADER 배열 내 인덱스 존재 여부를 체크하도록 개선
if(
	(isset($HEADER['usedb']) && $HEADER['usedb']) || 
	(isset($HEADER['usedb2']) && $HEADER['usedb2']) || 
	(isset($HEADER['priv']) && $HEADER['priv']) || 
	(isset($HEADER['log']) && $HEADER['log'])
) { // DB를 사용한다면...

	$SECURITY['db_server']	= "localhost";
	$SECURITY['db_user']	= "root";
	$SECURITY['db_pass']	= "dnflsp1004!";
	$SECURITY['db_name']	= "kbstars";
}

// 사이트 기본 환경 변수
$SITE = array(
	'th'		=> "new21_",  // MySQL Table prefix
	'name'		=> "KB세이버스",
	'version'	=> "2.0.0",
	'webmaster'	=> "sendonly@kbstars.co.kr",
	'hp'		=> "0196959505",
	'company'	=> "KB국민은행",
	'debug'		=> 0,
	'database'	=> $SECURITY['db_name'],
	'database2'	=> "savers_secret",
	'season'	=> '10',
	'tid'		=> '13'
);

$GAMEINFO = array(
	'season'	=> '10',
	'tid'		=> '13'
);

// 선수 카테고리를 위해서.....
$PlayerCateBoard = array(
	"cmletter" , 
	"cmmemo" , 
	"roomgallery" 
);

if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == '59.3.40.149')
	$DEBUG = true;

?>