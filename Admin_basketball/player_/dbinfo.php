<?php
// 데이터베이스 테이블 설정
$table = "player"; 

// dbinfo 배열 정의
$dbinfo = array(
	"table" => "player",
	"title" => "선수 기본 정보",
	"skin" => "sthis_player",
	"pern" => 40,
	"row_pern" => 8,	
	"cut_length" => 50,
	"priv_list" => "운영자,뉴스관리자", 
	"priv_write" => "운영자,뉴스관리자", 
	"priv_read" => "운영자,뉴스관리자", 
	"priv_delete" => "운영자,뉴스관리자", 
	"enable_upload" => "multi", 
	"html_headpattern" => "no",
	"html_headtpl" => "main",
	"orderby" => "p_seq, p_name",
	"enable_cate" => "Y",
	"upload_dir" => "{$_SERVER['DOCUMENT_ROOT']}/sthis/sthis_player/upload",
	"enable_getinfo" => "Y",
	"enable_level" => "Y",
	"default_docu_type" => "text"
	$dbinfo['enable_userid'] = "";
);
?>
