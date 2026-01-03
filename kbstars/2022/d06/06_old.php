<?php
//=======================================================
// 설	명 : 템플릿 샘플
// 책임자 : 박선민 (sponsor@new21.com), 검수: 05/11/20
// Project: sitePHPbasic
// ChangeLog
//	DATE	수정인				수정 내용
// -------- ------ --------------------------------------
// 05/11/20 박선민 마지막 수정
//=======================================================
$HEADER = array(
	'priv' => '', // 인증유무 (비회원,회원,운영자,서버관리자)
	'html_echo' => 1,
	'html_skin' => '2022_d06'
);
require($_SERVER['DOCUMENT_ROOT'].'/sinc/header.php');
//=======================================================
// Ready.. . (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
	$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
	$_SESSION['seHTTP_REFERER'] = $seHTTP_REFERER;
}
//=======================================================
// Start.. . (DB 작업 및 display)
//=======================================================
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
$(function (){
	// shopImg 시작하는 모든 id를 숨김
	$("[id^=shopImg]").hide();
	$("[id^=shopImg]").eq(0).toggle();
	
	// 클래스가 일치하는 버튼을 가지지만, float_l 클래스가 아닌 요소
	$(".buttonImg:not(.float_l)").click(function (e){
		e.stopPropagation();
		
		if ($(".buttonImg float_l") ){
		// index가 클릭한 것과 일치하는 요소를 찾음
		$("[id^=shopImg]").hide();
		$("[id^=shopImg]").eq($(this).index()).toggle();
		}
	});
});

function switchImg(img){
	getVals();
	img.src = img.src.match(/_on/) ? img.src.replace(/_on/, "_off") : img.src.replace(/_off/, "_on");
}

// 폼 요소와 사용하는 연관 배열
// 폼 요소의 이름(id)과 입력값 구하기
function getVals(){
	// 모든 입력 요소들을 변수에 할당
	var elems = $("[id^=buttonImg]");
	// HTMLFormElement.elements == 폼 요소에 포함된 모든 폼 컨트롤를 리턴?
	// 배열에 새 값을 추가 === 객체에 새로운 속성을 추가
	// 객체로 만듬! Array가 아닌 Object 임에 주의!
	
	var elemArray = new Object();

	// for...in문으로 배열을 탐색!
	for (var i= 0; i < elems.length; i++ ){
		// 입력된 값이 "text"라면...
		if (elems[i].nodeName == "IMG"){
			// elems[i].value === 각각의 입력 요소에 입력된
			// 값을 elemArray[elems[i].id] 에 할당!
			elemArray[i] = elems[i].src;
			if (elems[i].src.match(/_on/) )
				elems[i].src = elems[i].src.replace(/_on/, "_off") ;
		}
	}
	

	// 실행~
	//checkVals(elemArray);
	return false; //...
}

// 값 확인 함수
function checkVals(elemArray){
	var str = "";
	for (var key in elemArray){
		// key값 === input.id // elemArray[key]값 === input에 입력된 값~
		str += key + ": " + elemArray[key] + " " + "<br/>";
	}
	// 결과 뿌림!
	document.getElementById("result").innerHTML = str;
}
</script>
				<p id="contents_title">KB스타즈 샵</p> 
				<div id="sub_contents_main" class="clearfix">
				<table width="940" border="0" cellspacing="0" cellpadding="0" align="center">
					<tbody>
					<tr>
						<td><img src="/images/2019/event/store_top_2019.jpg" width="940" height="479" alt=""/></td>
					</tr>
					<tr>
						<td height="20">&nbsp;</td>
					</tr>
					<tr>
						<td><table width="940" border="0" cellspacing="0" cellpadding="0">
						<tbody>
							<tr>
							<td><table width="940" border="0" cellspacing="0" cellpadding="0">
								<tbody>
								<tr>
									<td><div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_1_on.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_2_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_3_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_4_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;margin-bottom:1px;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_5_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_6_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_7_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_8_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_9_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;margin-bottom:1px;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_10_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_11_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_12_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_13_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_14_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;margin-bottom:1px;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_15_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_16_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_17_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_18_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_19_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;margin-bottom:1px;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_20_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									<div class="buttonImg" style="width:188px;float:left;"><img id="buttonImg float_l" src="/images/2019/event/store_menu_21_off.jpg" style="cursor:pointer;" onclick="switchImg(this)"/></div>
									</td>
								</tr>
								</tbody>
								</table></td>
							</tr>
						</tbody>
						</table></td>
					</tr>
					<tr>
						<td height="20"><div id="result"></div></td>
					</tr>
					<tr>
						<td><div id="shopImg_1"><img src="/images/2019/event/store_1.jpg" width="940" height="2482" /></div>
						<div id="shopImg_2"><img src="/images/2019/event/store_2.jpg" width="940" height="2482" /></div>
						<div id="shopImg_3"><img src="/images/2019/event/store_3.jpg" width="940" height="2482" /></div>
						<div id="shopImg_4"><img src="/images/2019/event/store_4.jpg" width="940" height="2482" /></div>
						<div id="shopImg_5"><img src="/images/2019/event/store_5.jpg" width="940" height="2482" /></div>
						<div id="shopImg_6"><img src="/images/2019/event/store_6.jpg" width="940" height="2482" /></div>
						<div id="shopImg_7"><img src="/images/2019/event/store_7.jpg" width="940" height="2482" /></div>
						<div id="shopImg_8"><img src="/images/2019/event/store_8.jpg" width="940" height="2482" /></div>
						<div id="shopImg_9"><img src="/images/2019/event/store_9.jpg" width="940" height="2482" /></div>
						<div id="shopImg_10"><img src="/images/2019/event/store_10.jpg" width="940" height="2482" /></div>
						<div id="shopImg_11"><img src="/images/2019/event/store_11.jpg" width="940" height="2482" /></div>
						<div id="shopImg_12"><img src="/images/2019/event/store_12.jpg" width="940" height="2482" /></div>
						<div id="shopImg_13"><img src="/images/2019/event/store_13.jpg" width="940" height="2482" /></div>
						<div id="shopImg_14"><img src="/images/2019/event/store_14.jpg" width="940" height="2482" /></div>
						<div id="shopImg_15"><img src="/images/2019/event/store_15.jpg" width="940" height="2482" /></div>
						<div id="shopImg_16"><img src="/images/2019/event/store_16.jpg" width="940" height="2482" /></div>
						<div id="shopImg_17"><img src="/images/2019/event/store_17.jpg" width="940" height="2482" /></div>
						<div id="shopImg_18"><img src="/images/2019/event/store_18.jpg" width="940" height="2482" /></div>
						<div id="shopImg_19"><img src="/images/2019/event/store_19.jpg" width="940" height="2482" /></div>
						<div id="shopImg_20"><img src="/images/2019/event/store_20.jpg" width="940" height="2482" /></div>
						<div id="shopImg_21"><img src="/images/2019/event/store_21.jpg" width="940" height="2482" /></div>
						</td>
					</tr>
					</tbody>
				</table>
				</div><?php
//=======================================================
echo $SITE['tail']; 
?>
