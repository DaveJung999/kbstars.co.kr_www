<?php
// PHP 7+에서는 $_SERVER, $_GET, $_FILES가 항상 존재하므로 호환성 코드 불필요
// PHP 4 호환성을 위한 코드이므로 PHP 7+에서는 실행되지 않음
/*
if ( !isset( $_SERVER ) ) {
	$_SERVER = $HTTP_SERVER_VARS ;
}
if ( !isset( $_GET ) ) {
	$_GET = $HTTP_GET_VARS ;
}
if ( !isset( $_FILES ) ) {
	$_FILES = $HTTP_POST_FILES ;
}
*/

if ( !defined( 'DIRECTORY_SEPARATOR' ) ) {
	define( 'DIRECTORY_SEPARATOR',
		strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? '\\' : '/'
	) ;
}
