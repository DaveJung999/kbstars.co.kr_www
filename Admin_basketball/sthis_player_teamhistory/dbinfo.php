<?php
//=======================================================
// 설	명 : 게시판 정보 설정 (dbinfo.php)
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 24/05/18 Gemini	PHP 7 마이그레이션 및 논리 오류 수정
//=======================================================
	$dbinfo['table']		= "player_teamhistory"; // new21_slist_event
	$table		= $dbinfo['table'];
	$table_player = "player";
	$table_season = "season";
	
	$dbinfo['title']		= "선수 기본 정보";
	$dbinfo['skin']		= "board_monitoring";
	$dbinfo['pern']		= 160;
	$dbinfo['row_pern']		= 1;	
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= "운영자,뉴스관리자"; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	= "운영자,뉴스관리자"; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= "운영자,뉴스관리자"; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']	= "운영자,뉴스관리자"; // 무조건 삭제권한
	$dbinfo['enable_upload']="Y"; // 업로드지원
	$dbinfo['html_headpattern'] = "no";
	$dbinfo['html_headtpl'] = "no";
	$dbinfo['orderby'] = "sname desc, num";
	$dbinfo['enable_cate'] = "N";
	$dbinfo['enable_type'] = "Y";
	$dbinfo['enable_getinfo'] = "Y";
	$dbinfo['enable_level'] = "Y";
	$dbinfo['default_docu_type'] = "text";
?>
