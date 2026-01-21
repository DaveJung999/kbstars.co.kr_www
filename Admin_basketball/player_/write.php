<?php
$HEADER=array(
		'priv' => "운영자,뉴스관리자", // 인증유무 (0:모두에게 허용, 숫자가 logon테이블 Level)
		'usedb2' => 1, // DB 커넥션 사용 (0:미사용, 1:사용)
		'html_echo' => '', // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
		'log' => '' // log_site 테이블에 지정한 키워드로 로그 남김
	);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
if( !isset($_SESSION['seUserid']) && !isset($_SESSION['seHTTP_REFERER']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"]) == false ){
	$_SESSION['seHTTP_REFERER']=$_SERVER['HTTP_REFERER'];
}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================

$p_name = '';
$p_position = '';
$p_num = '';
$list = [];
$sel_po_G = '';
$sel_po_F = '';
$sel_po_C = '';
$tselect = '';
$dbinfo = []; // Assuming $dbinfo is an array, it needs to be defined if it's used
$upfiles = [];

if($mode == "modify" && $pid)	{
	$sql = " SELECT * FROM player WHERE uid = " . (int)$pid;
	$rs = db_query($sql);
	$cnt = db_count($rs);

	if($cnt){
		$list = db_array($rs);
		$p_name = $list['p_name'];
		$p_position = $list['p_position'];
		$p_num = $list['p_num'];
		$tid = $list['tid'];

		//선수 포지션 저장된 항목 셀렉트
		if($p_position == "G")
			$sel_po_G = " selected";
		else if($p_position == "F")
			$sel_po_F = " selected";
		else
			$sel_po_C = " selected";
		// 업로드 처리
//			if($dbinfo['enable_upload'] != 'N' and $list['upfiles']) {	//davej...................2005.12.21
		// dbinfo는 정의되지 않았으므로 임시로 'Y'로 가정하거나 필요에 따라 수정
		if(!isset($dbinfo['enable_upload']) || $dbinfo['enable_upload'] != 'N'){
			if(isset($list['upfiles'])){
				$upfiles = @unserialize($list['upfiles']);
				if(!is_array($upfiles))	{
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile']['name'] = $list['upfiles'];
					$upfiles['upfile']['size'] = (int)($list['upfiles_totalsize'] ?? 0);
				}
			}
			$list['upfiles'] = $upfiles;
		}


	} else {
		back("수정할 선수가 없습니다.");
	}
}else if($mode == "modify" && !$pid){
	back("수정할 선수가 없습니다.");
}

//팀명, 팀아이디 가져오기
$tsql = " SELECT * FROM team ORDER BY tid ASC ";
$trs = db_query($tsql);
$tcnt = db_count($trs);

if($tcnt){
	$t_name = [];
	for($i = 0 ; $i < $tcnt ; $i++)	{
		$tlist = db_array($trs);
		$teamid = $tlist['tid'];
		$t_name[$i] = $tlist['t_name']." (".$tlist['tid'].")";
		//저장된 팀 항목 셀렉트
		if($tid && $tid == $teamid){
			$tsel = "selected";
			$tselect .= "<option value=\"".htmlspecialchars($teamid, ENT_QUOTES, 'UTF-8') . "\" {$tsel}>".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
		} else {
			$tselect .= "<option value=\"".htmlspecialchars($teamid, ENT_QUOTES, 'UTF-8') . "\">".htmlspecialchars($t_name[$i], ENT_QUOTES, 'UTF-8') . "</option>";
		}
	}
}

?>

<script LANGUAGE="JavaScript" src="/scommon/js/chkform.js" type="Text/JavaScript"></script>

<link href="/css/basic_text.css" rel="stylesheet" type="text/css">
<link href="/css/link01.css" rel="stylesheet" type="text/css">

<script>
	function pop_Player_teamhistory(uid, pname){
		window.open('/Admin_basketball/sthis_player_teamhistory/list.php?pid='+uid+'&pname='+encodeURIComponent(pname), 'player_teamhistory', 'status=no,menubar=no, scrollbars=yes,width=600,height=500');
	}
</script>

<style type="text/css">
<!--
body {
	margin-left: 5px;
	margin-top: 15px;
	margin-right: 5px;
	margin-bottom: 5px;
	background-color:F8F8EA;
}
-->
</style>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td><table width="97%" border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		<td width="22"><img src="/images/admin/tbox_l.gif" width="22" height="22"></td>
		<td background="/images/admin/tbox_bg.gif"><strong>선수정보 </strong></td>
		<td align="right" width="5"><img src="/images/admin/tbox_r.gif" width="5" height="22"></td>
		</tr>
	</table>
		<br>
		<form action="ok.php" method="post" name="write" id="write" onsubmit="return chkForm(this)" ENCTYPE='multipart/form-data'>
			<input name="uid" type="hidden" value="<?php echo htmlspecialchars($list['uid'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="pid" type="hidden" value="<?php echo htmlspecialchars($pid, ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="mode" type="hidden" value="<?php echo htmlspecialchars($mode, ENT_QUOTES, 'UTF-8') ; ?>" />
			<input name="team" type="hidden" value="<?php echo htmlspecialchars($_GET['team'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
			<table width="97%"	border="0" align="center" cellpadding="2" cellspacing="1" bordercolorlight="#cccccc" bgcolor="#666666">
			<tr>
				<td width="20%" height="30" align="center" bgcolor="#D2BF7E"><strong> 이 름 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<input name="p_name" type="text" size="10" value="<?php echo htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8') ; ?>" required="required" hname="이름을 입력 해 주세요." /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>포 지 션</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="p_position">
					<option value="G" <?php echo $sel_po_G ; ?>>가드</option>
					<option value="F" <?php echo $sel_po_F ; ?>>포워드</option>
					<option value="C" <?php echo $sel_po_C ; ?>>센터</option>
				</select></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>백 넘 버</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<input name="p_num" type="text" size="3" maxlength="3" value="<?php echo htmlspecialchars($p_num, ENT_QUOTES, 'UTF-8') ; ?>" required="required" option="regNum" hname="백넘버를 숫자로 입력 해 주세요." /></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>소 속 팀</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="tid">
					<?php echo $tselect ; ?>
					</select> </td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>선수구분</strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<select name="p_gubun">
					<option <?php if(isset($list['p_gubun']) && $list['p_gubun'] == '현역') echo 'selected' ; ?>>현역</option>
					<option <?php if(isset($list['p_gubun']) && $list['p_gubun'] == '은퇴') echo 'selected' ; ?>>은퇴</option>
					<option <?php if(isset($list['p_gubun']) && $list['p_gubun'] == '기타') echo 'selected' ; ?>>기타</option>
				</select></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#D2BF7E"><strong>홈페이지 나열순서 </strong></td>
				<td bgcolor="#F8F8EA">&nbsp;&nbsp;
					<input name="p_seq" type="text" size="5" maxlength="3" value="<?php echo htmlspecialchars($list['p_seq'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" option="regNum" hname="숫자를 입력 해 주세요."/></td>
			</tr>
			<tr>
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>생년월일</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_bdate" size="20"	value="<?php echo htmlspecialchars($list['p_bdate'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>졸업년도</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_ddate" size="20"	value="<?php echo htmlspecialchars($list['p_ddate'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>출신학교</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_school" size="45"	value="<?php echo htmlspecialchars($list['p_school'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:95%"/> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>프로입단</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_pro" size="10"	value="<?php echo htmlspecialchars($list['p_pro'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" />
				년 </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>신장</strong></td>
				<td bgcolor="#F8F8EA"><input type="text" name="p_height" size="10"	value="<?php echo htmlspecialchars($list['p_height'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>몸무게</strong></td>
				<td bgcolor="#F8F8EA"><input type="text" name="p_weight" size="10"	value="<?php echo htmlspecialchars($list['p_weight'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /></td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>혈핵형</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_oab" size="10"	value="<?php echo htmlspecialchars($list['p_oab'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>신발크기</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_sin" size="10"	value="<?php echo htmlspecialchars($list['p_sin'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>이메일</strong></td>
				<td bgcolor="#F8F8EA"><input type="text" name="p_email" size="45"	value="<?php echo htmlspecialchars($list['p_email'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>홈페이지(까페)</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_homepage" size="45"	value="<?php echo htmlspecialchars($list['p_homepage'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:95%"/> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>가족관계</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_family" size="45"	value="<?php echo htmlspecialchars($list['p_family'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>별명</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_nickname" size="20"	value="<?php echo htmlspecialchars($list['p_nickname'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>나의 이상형</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_lee" size="45"	value="<?php echo htmlspecialchars($list['p_lee'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:95%"/> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>평소 취미 활동</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_hobby" size="45"	value="<?php echo htmlspecialchars($list['p_hobby'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:95%"/> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>스트레스 해소법</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_stress" size="45"	value="<?php echo htmlspecialchars($list['p_stress'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>"	style="width:95%"/> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>가수 및 음악장르</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_music" size="45"	value="<?php echo htmlspecialchars($list['p_music'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>좋아하는 음식</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_food" size="45"	value="<?php echo htmlspecialchars($list['p_food'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>좋아하는 스포츠 </strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_spo" size="45"	value="<?php echo htmlspecialchars($list['p_spo'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>농구시작은 언제</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_start" size="45"	value="<?php echo htmlspecialchars($list['p_start'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>마음에 드는 선수</strong></td>
				<td bgcolor="#F8F8EA"><input type="text" name="p_mplayer" size="45"	value="<?php echo htmlspecialchars($list['p_mplayer'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>소속팀 변경</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input name="teamhistory" type="button" class="CCbox03" id="teamhistory" value="소속팀 변경사항 보기"	onclick="pop_Player_teamhistory('<?php echo htmlspecialchars($list['uid'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>', '<?php echo htmlspecialchars($list['p_name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>')"/>
				(등록시에는 <span class="style5">먼저 등록하신 후</span> 수정 클릭 후 변경사항 입력 해 주세요.)</td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>Player Life</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><textarea name="p_life" cols="50" rows="7" style="width:98%"><?php echo htmlspecialchars($list['p_life'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></td>
			</tr>
			<tr class="base">
				<td height="30" align="center" bgcolor="#E5E5E5" ><strong>은퇴후 계획</strong></td>
				<td width="80%" bgcolor="#F8F8EA"><input type="text" name="p_end" size="45"	value="<?php echo htmlspecialchars($list['p_end'] ?? '', ENT_QUOTES, 'UTF-8') ; ?>" /> </td>
			</tr>
			<tr class="base">
				<td height="30" colspan="2" align="center" bgcolor="#D2BF7E" ><strong>선수 사진 이미지</strong> </td>
			</tr>
			<tr bgcolor="999999" class="base">
				<td height="25" align="center" bgcolor="#EDE1F7" ><strong>시크릿용 선수사진	- 명함판 사진</strong></td>
				<td bgcolor="#F8F8EA"><table width="500"	border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>파일명 : <?php echo htmlspecialchars($list['upfiles']['upfile']['name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
				</tr>
				</table></td>
			</tr>
			<tr bgcolor="999999" class="base">
				<td height="25" align="center" bgcolor="#EDE1F7" ><strong>메일 슬라이드용 png</strong></td>
				<td bgcolor="#F8F8EA"><table width="500"	border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>파일명 : <?php echo htmlspecialchars($list['upfiles']['upfile0']['name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
				</tr>
				</table></td>
			</tr>
			<tr bgcolor="999999" class="base">
				<td height="25" align="center" bgcolor="#EDE1F7" ><strong>선수소개 1 - 서 있는 사진</strong></td>
				<td bgcolor="#F8F8EA"><table width="500"	border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>파일명 : <?php echo htmlspecialchars($list['upfiles']['upfile1']['name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
				</tr>
				</table></td>
			</tr>
			<tr bgcolor="999999" class="base">
				<td height="25" align="center" bgcolor="#EDE1F7" ><strong>선수종합기록 1 - 명함판 사진</strong></td>
				<td bgcolor="#F8F8EA"><table width="500"	border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>파일명 : <?php echo htmlspecialchars($list['upfiles']['upfile3']['name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
				</tr>
				</table></td>
			</tr>
			<tr bgcolor="999999" class="base">
				<td height="25" align="center" bgcolor="#EDE1F7" ><strong>선수종합기록 2 - 옆으로 넓은 사진</strong></td>
				<td bgcolor="#F8F8EA"><table width="500"	border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td>파일명 : <?php echo htmlspecialchars($list['upfiles']['upfile4']['name'] ?? '', ENT_QUOTES, 'UTF-8') ; ?></td>
				</tr>
				</table></td>
			</tr>

			<tr bgcolor="999999" class="base">
				<td height="50" colspan="2" align="center" bgcolor="#F8F8EA" >선수사진은 <a href="http://savers-secret.kbstars.co.kr/sthis/sthis_player/plist.php" target="_blank"><strong>http://savers-secret.kbstars.co.kr</strong></a> 에서 등록 해 주세요.</td>
			</tr>
			</table>
			<br />
			<table width="90%"	border="0" align="center" cellpadding="0" cellspacing="0">
			<tr>
				<td align="center"><input name="Submit" type="submit" class="CCbox04" value=" 입 력 " />
				&nbsp;
				<input name="Submit2" type="button" class="CCbox04" value=" 뒤 로 " onclick="javascript:history.back();" /></td>
			</tr>
			</table>
			<br />
		</form></td>
	</tr>
</table>

<br />
<?php echo $SITE['tail']; ?>