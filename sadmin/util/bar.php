<?php
//=======================================================
// 설  명 : MySQL SQL문을 통한 자동 Bar 그래프 그리기
// 책임자 : 박선민 (sponsor@new21.com), 검수: 04/03/25
// Project: sitePHPbasic
// ChangeLog
//   DATE   수정인			 수정 내용
// -------- ------ --------------------------------------
// 04/03/25 박선민 마지막 수정
//=======================================================
$HEADER=array(
		'class'	=> 'root', // 관리자만 로그인
		usedb2	=>1, // DB 커넥션 사용 (0:미사용, 1:사용)
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $_SERVER['HTTP_HOST']);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	$sql=stripslashes($sql);
	if(!preg_match("/^select /i",$sql))
		back("지원하지 않는 SQL문입니다.");
	elseif(strpos($sql,";") === true)
		back("지원하지 않는 SQl문입니다.");

//=======================================================
// Start... (DB 작업 및 display)
//=======================================================
   // PhpBarGraph Version 2.0
   // Bar Graph Generator Example for PHP
   // Written By TJ Hunter (tjhunter@ruistech.com)
   // Released Under the GNU Public License.
   // http://www.ruistech.com/phpBarGraph

   // header("Content-type: image/png");
   header("Content-type: image/gif");

   require("class_phpBarGraph2.php");

   // Setup how high and how wide the ouput image is
   $imageHeight = $height ? $height : 300;
   $imageWidth = $width ? $width : 600;

   // Create a new Image
   $image = ImageCreate($imageWidth, $imageHeight);

   // Fill it with your favorite background color..
   $backgroundColor = ImageColorAllocate($image, 50, 50, 50);
   ImageFill($image, 0, 0, $backgroundColor);

   // Interlace the image..
   Imageinterlace($image, 1);


   // Create a new BarGraph..
   $myBarGraph = new PhpBarGraph;
   $myBarGraph->SetX(10);			  // Set the starting x position
   $myBarGraph->SetY(10);			  // Set the starting y position
   $myBarGraph->SetWidth($imageWidth-20);	// Set how wide the bargraph will be
   $myBarGraph->SetHeight($imageHeight-20);  // Set how tall the bargraph will be
   $myBarGraph->SetNumOfValueTicks(10); // Set this to zero if you don't want to show any. These are the vertical bars to help see the values.
   
   
   // You can try uncommenting these lines below for different looks.
   
   // $myBarGraph->SetShowLabels(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the labels of each bar.
   // $myBarGraph->SetShowValues(false);  // The default is true. Setting this to false will cause phpBarGraph to not print the values of each bar.
   // $myBarGraph->SetBarBorder(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the border of each bar.
   // $myBarGraph->SetShowFade(false);	// The default is true. Setting this to false will cause phpBarGraph to not print each bar as a gradient.
   // $myBarGraph->SetShowOuterBox(false);   // The default is true. Setting this to false will cause phpBarGraph to not print the outside box.
   // $myBarGraph->SetBarSpacing(20);	 // The default is 10. This changes the space inbetween each bar.

   // Set the colors of the bargraph..
   $myBarGraph->SetStartBarColor("0000ff");  // This is the color on the top of every bar.
   $myBarGraph->SetEndBarColor("A624A6");	// This is the color on the bottom of every bar. This is not used when SetShowFade() is set to false.
   $myBarGraph->SetLineColor("ffffff");	  // This is the color all the lines and text are printed out with.

// db 할당
// PHP 7+에서는 mysql_* 함수가 제거되었으므로 db_* 함수 사용
$rs=db_query($sql);
if(!$rs) back("지원하지 않는 SQL문입니다.");
// 필드 개수 확인을 위해 첫 번째 행 가져오기
$first_row = db_array($rs);
if(!$first_row || count($first_row) < 2) // 필드가 무조건 2개여야 한다.
	back("지원하지 않는 SQL문입니다.");
// 결과를 배열로 변환
$rows = array();
$rows[] = $first_row; // 첫 번째 행은 이미 가져옴
while($row = db_array($rs)) {
	$rows[] = $row;
}
for($i=0;$i<count($rows);$i++) {
	//$myBarGraph->AddValue("A",200);  // AddValue(string label, int value)
	$title = $rows[$i][0];
	$data = $rows[$i][1];
	$myBarGraph->AddValue($title,$data);  // AddValue(string label, int value)
}

// Print the BarGraph to the image..
$myBarGraph->DrawBarGraph($image);

// Output the Image to the browser in GIF (or PNG) format
// ImagePNG($image);
ImageGif($image);
// Destroy the image.
Imagedestroy($image);
?>