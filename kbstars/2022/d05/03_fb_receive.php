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
	'html_skin' => '2022_d05'
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

?>
	<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js" ></script>
	<script type="text/javascript" src="/scommon/js/jquery-1.6.1.min.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/fb_active_feed.css" />
				<p id="contents_title">페이스북</p> 
				<div id="sub_contents_main" class="clearfix">
					
					<table width="760" border="0" cellspacing="0" cellpadding="0" align="center">
					<tr>
						<td height="50"><table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td align="left"><img src="/images/2013/sns/sub_facebook_txt.jpg" width="132" height="27" /></td>
							<td style="text-align:right"><a href="https://www.facebook.com/KBSTARSBASKETBALL" target="_blank"><img src="/images/2013/sns/btn_facebook.jpg" width="113" height="18" /></a></td>
						</tr>
						</table></td>
					</tr>
					<tr>
						<td height="2" bgcolor="#F3AE38"></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
						
					
						<div id="fb-root"><p align="center"><img src="/images/2013/sns/ajax-loader.gif" alt="" /></p></div>
						<script language="javascript" type="text/javascript">
					
						//Date 개체를 입력받아 yyyy-MM-dd hh:mm:ss 형식으로 반환
						function timeSt(dt){
							var d = new Date(dt);
							var yyyy = d.getFullYear();
							var MM = d.getMonth()+1;
							var dd = d.getDate();
							var hh = d.getHours();
							var mm = d.getMinutes();
							var ss = d.getSeconds();
							//return (yyyy + '-' + addzero(MM) + '-' + addzero(dd) + ' ' + addzero(hh) + ':' + addzero(mm) + ':' + addzero(ss));
							return (yyyy + '/' + addzero(MM) + '/' + addzero(dd));
						}
						
						//10보다 작으면 앞에 0을 붙임
						function addzero(n){
							return n < 10 ? "0" + n : n;
						}
						
						//날짜시간 보정
						function dateConverter(str)
						{	
							var convertTime=0;
							var int_y = 0;	//년
							var int_m = 0;	//월
							var int_d = 0;	//일
							var int_h = 0;	//시
							var int_i = 0;	//분
							var int_s = 0;	//초
							
							// 값 나누기
							var int_y = parseInt(str.substring(0,4), 10);
							var int_m = parseInt(str.substring(5,7), 10);
							var int_d = parseInt(str.substring(8,10), 10);
							var int_h = parseInt(str.substring(11,13), 10);
							var int_i = parseInt(str.substring(14,16), 10);
							var int_s = parseInt(str.substring(17,19), 10);
							
							// 보정시간
							var rev_hour = 8;
							
							var int_rev_h = int_h - rev_hour;
							
							//alert("Date : " + int_y + "," + int_m + "," + int_d + "," + int_h + "," + int_i + "," + int_s);
							//날짜시간 구하기
							//var startDate = new Date(int_y,int_m,int_d,int_h,int_i,int_s);
							var startDate = new Date(int_m+"/"+int_d+"/"+int_y+" "+int_h+":"+int_i+":"+int_s);
							
							//날짜시간 보정 + 9시간
							//startDate = new Date(Date.parse(startDate) + (rev_hour * 1000 * 60 * 60)); //9시간후
							
							//날짜 형태 변경
							convertTime = timeSt(startDate);
					
							return convertTime;
						}
					
						var feedCount = 0;
						var thClassName = 'fb_css_top';
						var tdClassName = 'fb_css_top_1';
						var editData = ''
					
						window.fbAsyncInit = function(){
							FB.init({
								appId	: '1417415871822265',
								status : true,
								cookie : true,
								xfbml	: true
							});
						};
					
						function feedFacebook(){
							
							feedCount += 20;
							access_token = 'CAAUJIdmJZBbkBAJoco9lMeN1ER7pTFyKkAUAIemjQIiEc0GjZBhC3hlETBfcZBGOqFu7vXcPLjAQUjZBueZCJJjSNNXqEf1Lw66mzRYEtO4hYgolNwbor7nAzYMBcFoX1ObYhiKbvgxzQjv9yVnFjSqe180OBxK7UZAnZB1LvXL70hVEwySppfSuB1ZCUAzNhqJgomDGU3DhCrfhe1vZAnrEZAwHZCAgZBSRCjMZD';
					//		access_token = '';
							
							var path = '/KBSTARSBASKETBALL/feed?access_token='+access_token;
							FB.api(path, { limit: feedCount}, function(response){
								for (i=0; i< response.data.length; i++){
					
									editData =	'<table width="95%" align="center">';
									editData += '<tbody border="1" cellpadding="0" cellspacing="0">';
									
									var i;
									for (i=0; i< response.data.length; i++){
									
										if(i == 0){
											thClassName = 'fb_css_top';
											tdClassName = 'fb_css_top_1';
										} else if(i == response.data.length-1){
											thClassName = 'fb_css_bottom';
											tdClassName = 'fb_css_bottom_1';
										} else {
											thClassName = 'fb_css_content';
											tdClassName = 'fb_css_content_1';
										}
					
										pictureAddUrl=""
										if(response.data[i].picture == null){
												pictureAddUrl='';
										}//if
										else{
												pictureAddUrl='<br><a href="'+ response.data[i].link +'" target="_blank"><img src='+response.data[i].picture+' alt="페이스북 바로가기" style="max-width: 600px; width: expression(this.width > 600 ? 600: true);	height: auto;" /></a>';
										}//else
										
										debugger;
										rp_message = "";
										//alert("created_time : " + response.data[i].created_time);
										//alert("updated_time : " + response.data[i].updated_time);
										
										if(typeof response.data[i].message == 'undefined')
											rp_message = response.data[i].story;
										else
											rp_message = response.data[i].message;
													
										editData += '<tr>';
										editData += '<th width="10%" class="' + thClassName + '"><img src=http://graph.facebook.com/' + response.data[i].from.id + '/picture></th>';
										editData += '<td width="90%" class="' + tdClassName + '">' + rp_message ;
										editData += '<br>' + pictureAddUrl + '<p class="txt1">';
										editData += '<a href="https://www.facebook.com/KBSTARSBASKETBALL" target="_blank">';
										editData += '<img src="/images/2013/sns/fb_icon.jpg" alt="facebook_icon" align="absmiddle"/>&nbsp;';
										editData += '<span class="name">'+ response.data[i].from.name + '</span></a> <span class="day">';
										editData +=	dateConverter(response.data[i].created_time) + '</span>';
										editData += '</p></td></tr>';
										//editData += '<tr><td colspan="2">&nbsp;</td></tr>';
										editData += '<tr><td colspan="2" height"1" bgcolor="#E8E8E8"></td></tr>';
										//editData += '<tr><td colspan="2">&nbsp;</td></tr>';
									}
					
									editData += "</tbody>";
									editData += "</table>";
									$('#fb-root').empty();
									$('#fb-root').append(editData);
								}
							});
						}
					
						feedFacebook();
						</script>
						
						<div class="btn" align="center"><a href="javascript:feedFacebook();"><img src="/images/2013/sns/fb_more.jpg" alt="" /></a></div>		
						
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					</table>
				</div><?php
//=======================================================
echo $SITE['tail']; 
?>
