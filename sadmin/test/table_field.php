	$rs_inc=db_query("select * from `{$table['inc'][$inc]}` where `균주정보ID`='{$uid}'");
	$list_inc=db_array($rs_inc);
	table_field($rs_inc);
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
function table_field(&$result,$tableinfo=="") {
   // PHP 7+에서는 mysql_* 함수가 제거되었으므로 db_* 함수 사용
   // 결과를 배열로 변환하여 처리
   $rows_array = array();
   $first_row = db_array($result);
   if($first_row) {
	   $rows_array[] = $first_row;
	   while($row = db_array($result)) {
		   $rows_array[] = $row;
	   }
   }
   $fields = $first_row ? count($first_row) : 0;
   $rows   = count($rows_array);
   // 테이블명은 별도로 전달받아야 함 (mysql_field_table 대체 불가)
   $table = isset($tableinfo['table']) ? $tableinfo['table'] : 'unknown';
   echo "Your '".$table."' table has ".$fields." fields and ".$rows." record(s)\n";
   echo "The table has the following fields:\n";
   if($first_row) {
	   $field_names = array_keys($first_row);
	   for ($i=0; $i < $fields; $i++) {
		   if($tableinfo=="") { // 그냥 출력
			   $name  = $field_names[$i];
			   // 타입, 길이, 플래그는 별도 쿼리로 확인 필요
			   echo "<br>name:".$name."\n";
		   }
		   else { // 폼을 위해 정말 사용!!
			$tableinfo[
		   }
	   }
   }
} // function
