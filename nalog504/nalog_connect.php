<?php
	$connect_host = "localhost";
	$connect_id   = "root";
	$connect_pass = "dnflsp1004!";
	$connect_db   = "kbstars";
	$admin_id     = "kbsavers";
	$admin_pass   = "kb0402";

	// 1. DB 연결 (mysqli 사용)
	$connect = mysqli_connect($connect_host, $connect_id, $connect_pass, $connect_db);

	// 2. 연결 확인 및 오류 처리
	if (!$connect) {
		die("연결 실패: " . mysqli_connect_error());
	}
	// 3. 한글 깨짐 방지 설정
	mysqli_set_charset($connect, "utf8mb4");

?>