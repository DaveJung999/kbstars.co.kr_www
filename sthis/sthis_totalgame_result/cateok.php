<?php
//=======================================================
// 설	명 : 게시판 카테고리 처리(cateok.php)
// 책임자 : 박선민 (sponsor@new21.com), 검수: 03/12/09
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 02/07/10 박선민 마지막 수정
// 03/12/09 박선민 버그 및 개선
//=======================================================
$HEADER=array(
		'priv' => '', // 인증유무 (0:모두에게 허용, 숫자가 높을 수록 레벨업)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'useCheck' => 1,
		'useBoard2' => 1,
		version => 1,
		'html_echo' => ''	// html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sin/header.php");
page_security("", $_SERVER['HTTP_HOST']);

//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// boardinfo 테이블 정보 가져와서 $dbinfo로 저장
	if($db){
		$rs_dbinfo=db_query("SELECT * FROM {$SITE['th']}boardinfo WHERE db='{$db}'");
		$dbinfo=db_count() ? db_array($rs_dbinfo) : back("사용하지 않은 DB입니다.");
		// 테이블이름 가져오기
		$table=$SITE['th'] . "board_" . $dbinfo['table_name'];

		// 인증 체크
		if(!boardAuth($dbinfo, "priv_catemanage")) back("이용이 제한되었습니다.(레벨부족)");
	}
	else back("DB 값이 없습니다");

	// 카테고리 테이블 구함
	if($dbinfo['enable_cate'] == 'Y' or $dbinfo['enable_cate'] == 'last'){
		// 카테고리 테이블 이름과 where절 구함 
		if($dbinfo['enable_type'] == 'Y'){
			$table_cate=$table;
			$sql_where_cate=" type='cate' "; // {$sql_where_cate} 사용 시작
			$sql_set_cate=", type='cate' "; // $sql_set_cate 사용 시작
			$sql_where = " type='docu' "; // $sql_where 사용 시작
		} else {
			$table_cate=$table	. "_cate";
			$sql_where_cate=" 1 ";
			$sql_set_cate="";
		}
	}
	else back("카테고리를 지원하지 않습니다.");

//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
// mode값에 따른 함수 호출
switch($mode){
	case 'catewrite' :
		cateWriteOK($table_cate,$sql_where_cate, $sql_set_cate, $dbinfo['cate_depth']);
		go_url("./cate.php?db={$db}&cateuid={$cateuid}");
		break;
	case 'catemodify' :
		cateModifyOK($table_cate,$sql_where_cate);
		go_url("cate.php?db={$db}");	
		break;
	case 'catedelete' :
		cateDeleteOK($table_cate, $sql_where_cate, $sql_set_cate,$table,$sql_where);
		go_url("cate.php?db={$db}");
		break;		
	default :
		back("잘못된 웹페이지에 접근하였습니다.");
}

//=======================================================
// User functions.. . (사용자 함수 정의)
//=======================================================
// 카테고리 추가 부분($sql_set_cate 가져오는 것 필히 확인)
function cateWriteOK($table_cate, $sql_where_cate, $sql_set_cate,$cate_depth=0){
	$qs	= array(
				cateuid =>  "post,trim",
				title =>  "post,trim,notnull"
			);
	$qs=check_value($qs);

	if(trim($sql_where_cate) == "") $sql_where_cate = " 1 ";

	if($qs['cateuid']){ // 서브카테고리 추가인경우
		$rs = db_query("SELECT * from {$table_cate} WHERE {$sql_where_cate} and uid='{$qs['cateuid']}'");
		$list = db_count() ? db_array($rs) : back("해당 부모 카테고리가 없습니다.");
		$qs['num']=$list['num'];
		$qs['re'] =getCateRe($table_cate,$sql_where_cate,$list['num'],$list['re']);
		if($cate_depth and $cate_depth < strlen($qs['re'])) back("더 하부의 서브카테고리를 만드실 수 없습니다");

		$sql="INSERT 
				INTO 
					$table_cate
				SET
					num='{$qs['num']}',
					re='{$qs['re']}',
					title='{$qs['title']}'
					$sql_set_cate
			";
	} else { // 탑카테고리 추가인경우
		$max = db_result(db_query("SELECT MAX(num) as num FROM {$table_cate}"), 0, "num") + 1;
		$sql="INSERT 
				INTO
					$table_cate
				SET
					num=$max,
					title='{$qs['title']}'
					$sql_set_cate
			";
	} // end if . . else ..

	db_query($sql);
	return db_insert_id();
}

// 카테고리 수정 부분
function cateModifyOK($table_cate, $sql_where_cate){
	$qs	= array(
				cateuid =>  "post,trim,notnull",
				title =>  "post,trim,notnull",
			);
	$qs=check_value($qs);

	if(trim($sql_where_cate) == "") $sql_where_cate = " 1 ";

	$sql="UPDATE
				$table_cate
			SET
				title='{$qs['title']}'
			WHERE
				$sql_where_cate
			AND
				uid={$qs['cateuid']}
		";
	db_query($sql);
	return true;
}

// 카테고리 삭제부분
function cateDeleteOK($table_cate, $sql_where_cate, $sql_set_cate,$table_data,$sql_where_data){
	$qs	= array(
				cateuid =>  "get,trim,notnull",
			);
	$qs=check_value($qs);

	if(trim($sql_where_cate) == "") $sql_where_cate = " 1 ";
	if(trim($sql_where_data) == "") $sql_where_data = " 1 ";

	$rs_cateinfo = db_query("SELECT * from {$table_cate} WHERE {$sql_where_cate} and uid='{$qs['cateuid']}'");
	$cateinfo= db_count() ? db_array($rs_cateinfo) : back("이미 삭제되었거나 삭제할 데이터가 없습니다.");

	// 하위 카테고리 uid,toreno 구함
	$subcate_uid[]=$cateinfo['uid'];
	$sql="SELECT * from {$table_cate} WHERE {$sql_where_cate} and num={$cateinfo['num']} and re like '{$cateinfo['re']}%'";
	$rs2 = db_query($sql);
	for($i=0;$i<db_count();$i++){
		$subcate_uid[] = db_result($rs2,$i,"uid");
	}
	
	// SQL문 where부분 만들기
	$sql_cates_where = " ( cateuid in (" . implode(",",$subcate_uid) . ") )	";

	// 해당 카테고리의 DB 데이터가 있다면 삭제못함
	$sql="select count(*) as count from {$table_cate} where {$sql_where_data} and {$sql_cates_where}";
	if(db_resultone($sql,0,"count")){
		back("해당 카테고리와 관련된 DB 데이터가 있습니다.\\n해당 데이터를 먼저 삭제하시기 바랍니다.");
	}

	// 해당 카테고리 삭제
	$sql="DELETE FROM {$table_cate} WHERE {$sql_where_cate} and num={$cateinfo['num']} and re like '{$cateinfo['re']}%'";
	db_query($sql);
	
	// 카테고리값 시프트
	if(strlen($cateinfo['re']))
		$sql="UPDATE 
					{$table_cate}
				SET	
						re=concat( substring(re,1,length('{$cateinfo['re']}')-1),
						char(ord(substring(re,length('{$cateinfo['re']}'),1))-1 ),
						substring(re,length('{$cateinfo['re']}')+1) )
				WHERE
						num='{$cateinfo['num']}' 
				AND 
						{$sql_where_cate}
				AND
						re like '" . substr($cateinfo['re'],0,-1) . "%'
				AND
						strcmp(re,'{$cateinfo['re']}')>= 0
			";
	else 
		$sql="UPDATE
					{$table_cate}
				SET
					num=num-1
				WHERE
					num > {$cateinfo['num']}
				AND
					{$sql_where_cate}
			";
	db_query($sql);
	
	return true;
}

function getCateRe($table_cate, $sql_where_cate, $num, $re){
	if(trim($sql_where_cate) == "") $sql_where_cate=" 1 ";

	$sql="SELECT re, right(re,1) FROM {$table_cate} WHERE {$sql_where_cate} and num='{$num}' AND length(re)=length('{$re}')+1 AND locate('{$re}', re)=1 ORDER BY re DESC LIMIT 1";

	$row = db_arrayone(db_query($sql));
	if($row){
		$ord_head = substr($row[0],0,-1);
		$ord_foot = chr(ord($row[1]) + 1);
		$re = $ord_head	. $ord_foot;
	} else {
		$re .= "1";
	}
	return $re;
} 

?>
