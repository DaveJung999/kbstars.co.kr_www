<?php
	$table				= "basket_player"; // new21_slist_event

	$dbinfo['title']		= "선수 기본 정보";
	$dbinfo['skin']		= "sthis_player";
	$dbinfo['pern']		= 40;
	$dbinfo['row_pern']		= 8;	 
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= 70; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	='운영자'; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= 70; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']='운영자'; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원 
	$dbinfo['html_headpattern'] = "no";
	$dbinfo['html_headtpl'] = "main";
	$dbinfo['orderby'] = "p_seq, p_name";
	$dbinfo['enable_cate'] = "Y";
	$dbinfo['upload_dir'] = "{$_SERVER['DOCUMENT_ROOT']}/images/upload";
	$dbinfo['enable_getinfo'] = "Y";
	$dbinfo['enable_level'] = "Y";
	$dbinfo['default_docu_type'] = "text";

?>
