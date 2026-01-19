<?php
	$table				= $SITE['th'] . "board2_contents"; // new21_slist_event
	$table_logon		= $SITE['th'] . "logon"; // new21_slist_event
	$table_logon		= $SITE['th'] . "logon"; // new21_slist_event
	$table_userinfo		= $SITE['th'] . "userinfo"; // new21_slist_event
	$table_popup				= $SITE['th'] . "board2_popup"; // new21_slist_event
	$table_mail_message 		= $SITE['th'] . "board2_mailmessage"; // new21_slist_event
	
	$dbinfo['db']		= "popup"; // new21_slist_event
	$dbinfo['db_pop']		= "popup"; // new21_slist_event
	$dbinfo['title']		= "주문관리";
	$dbinfo['skin']		= "basic";
	$dbinfo['pern']		= 10;
	$dbinfo['bpern']		= 50;
	$dbinfo['cut_length']	= 50;
	$dbinfo['priv_list']	= 99; // 본 list.php 볼 권한 설정
	$dbinfo['priv_write']	= 99; // write.php 글 올릴 권한 설정
	$dbinfo['priv_read']	= 99; // 본 read.php 볼 권한 설정
	$dbinfo['priv_delete']= 99; // 무조건 삭제권한
	$dbinfo['enable_upload']="multi"; // 업로드지원
	$dbinfo['html_headpattern'] = 'no';
	$dbinfo['html_headtpl']	= "admin_basic";
	//$dbinfo['upload_dir'] = "/sboard4/upload";
	$dbinfo['enable_getinfo'] = "Y";



?>