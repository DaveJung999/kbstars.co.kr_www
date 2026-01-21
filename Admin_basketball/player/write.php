<?php
$HEADER=array(
	'priv' => '운영자', // 인증유무 (비회원,회원,운영자,서버관리자) 
	'usedb2'	=>1, // DB 커넥션 사용 (0:미사용, 1:사용)
	'header'	=>1, // html header, tail 삽입(tail은 파일 마지막에 echo $SITE['tail'])
	'useUtil'	=>1,
	'html_echo'	=>1,
	'html_skin' =>"contribution", // html header 파일(/stpl/basic/index_$HEADER['html'].php 파일을 읽음)
	'log'		=>'' // log_site 테이블에 지정한 키워드로 로그 남김
);
require("{$_SERVER['DOCUMENT_ROOT']}/sinc/header.php");
//page_security("", $HTTP_HOST);

//===================================================
// REQUEST 값 대입......2025-09-10
$params = ['db', 'table', 'cateuid', 'pern', 'cut_length', 'row_pern', 'sql_where', 'sc_column', 'sc_string', 'page', 'mode', 'sup_bid', 'modify_uid', 'uid', 'goto', 'game', 'pid', 'gid', 'sid', 's_id', 'season', 'session_id', 'tid', 'rid', 'num', 'name', 'pback', 'search_text', 'upfile', 'notfound', 'limitno', 'limitrows'];
foreach ($params as $param) {
	$$param = $_REQUEST[$param] ?? $$param ?? null;
}
//===================================================
//=======================================================
// Ready... (변수 초기화 및 넘어온값 필터링)
//=======================================================
	// $seHTTP_REFERER는 어디서 링크하여 왔는지 저장하고, 로그인하면서 로그에 남기고 삭제된다.
	if( !$_SESSION['seUserid'] && !$_SESSION['seHTTP_REFERER'] && $_SERVER['HTTP_REFERER'] && strpos($_SERVER['HTTP_REFERER'],$_SERVER["HTTP_HOST"])==false ) {
		$seHTTP_REFERER=$_SERVER['HTTP_REFERER'];
		session_register(seHTTP_REFERER);
	}
//=======================================================
// Start... (DB 작업 및 display)
//=======================================================

?>
<script LANGUAGE="JavaScript" src="/scommon/js/chkform.js" type="Text/JavaScript"></script> 

<?php
	$mode = $_GET['mode'];
	$pid = $_GET['pid'];
	
	if($mode == "modify" && $pid)	{
		$sql = " SELECT * FROM player WHERE uid = $pid ";
		$rs = db_query($sql);
		$cnt = db_count($rs);
		
		if($cnt) {
			$list = db_array($rs);
			$p_name = $list['p_name'];
			$p_position = $list['p_position'];
			$p_num = $list['p_num'];
			$tid = $list['tid'];
			
			//선수 포지션 저장된 항목 셀렉트
			if($p_position == "G")	
				$sel_po_G = "selected";
			else if($p_position == "F")
				$sel_po_F = "selected";
			else
				$sel_po_C = "selected";
				

			// 업로드 처리
//			if($dbinfo['enable_upload']!='N' and $list['upfiles']) {  //davej...................2005.12.21
			if($dbinfo['enable_upload']!='N') {
				$upfiles=unserialize($list['upfiles']);
				if(!is_array($upfiles))  { 
					// 시리얼화된 변수가 아닌 파일 명으로 되어 있을 경우
					$upfiles['upfile'][name]=$list['upfiles'];
					$upfiles['upfile'][size]=(int)$list['upfiles_totalsize'];
				}
				$list['upfiles'] = $upfiles;
			}
				
				
		}else {
			back("수정할 선수가 없습니다.");
		}
	}else if($mode == "modify" && !$pid){
		back("수정할 선수가 없습니다.");
	}
	
	//팀명, 팀아이디 가져오기
	$tsql = " SELECT * FROM team ORDER BY tid ASC ";
	$trs = db_query($tsql);
	$tcnt = db_count($trs);
	
	if($tcnt) {
		for($i = 0 ; $i < $tcnt ; $i++)	{
			$tlist = db_array($trs);
			$teamid = $tlist['tid'];
			$t_name[$i] = $tlist['t_name'];
			//저장된 팀 항목 셀렉트
			// [수정] 조건부 로직을 PHP 변수 할당으로 단순화
			$tsel = ($tid && $tid == $teamid) ? " selected" : "";
			
			// [수정] HTML 표준 및 PHP 7+ 표준에 맞게 수정
			// - value 속성에 따옴표(`\"`) 추가
			// - 모든 변수에 중괄호(`{...}`) 적용
			$tselect .= "<option value=\"{$teamid}\"{$tsel}>{$t_name[$i]}</option>";
		}
	}
?>
<link href="/css/basic_text.css" rel="stylesheet" type="text/css">
<link href="/css/link01.css" rel="stylesheet" type="text/css">

<script>
	function pop_Player_teamhistory(uid, pname){
		window.open('/sthis/sthis_player_teamhistory/list.php?pid='+uid+'&pname='+pname, 'player_teamhistory', 'status=no,menubar=no,width=600,height=500');
	}
</script>

<style type="text/css">
<!--
.style4 {color: #CC3300;
	font-weight: bold;
}
.style5 {color: #FF0000}
.style7 {color: #000000}
-->
</style>
<br />
<table width="95%" border="0" align="center" cellpadding="1" cellspacing="1" bgcolor="#999999">
  <tr>
	<td height="40" align="center" bgcolor="#FFFFFF"><span class="style4">선수정보</span></td>
  </tr>
</table>
<br />
<form name="write" method="post" onSubmit="return chkForm(this)" action="ok.php">
			
<input name="pid" type="hidden" value="<?= $pid ?>">
<input name="mode" type="hidden" value="<?= $mode ?>">	
<input name="team" type="hidden" value="<?= $team ?>">
<table width="95%"  border="0" align="center" cellpadding="2" cellspacing="1" bordercolorlight="#cccccc" bgcolor="999999">
  <tr>
	<td width="20%" height="22" align="center" bgcolor="#D2BF7E"><span class="style7"> 이 름 </span></td>
	<td bgcolor="#FFFFFF">&nbsp;&nbsp;
		<input name="p_name" type="text" size="10" value="<?= $p_name ?>" required hname="이름을 입력 해 주세요." /></td>
  </tr>
  <tr>
	<td height="22" align="center" bgcolor="#D2BF7E"><span class="style7">포 지 션</span></td>
	<td bgcolor="#FFFFFF">&nbsp;&nbsp;
		<select name="p_position">
		  <option value="G" <?= $sel_po_G ?>>가드</option>
		  <option value="F" <?= $sel_po_F ?>>포워드</option>
		  <option value="C" <?= $sel_po_C ?>>센터</option>
	  </select></td>
  </tr>
  <tr>
	<td height="22" align="center" bgcolor="#D2BF7E"><span class="style7">백 넘 버</span></td>
	<td bgcolor="#FFFFFF">&nbsp;&nbsp;
		<input name="p_num" type="text" size="3" maxlength="3" value="<?= $p_num ?>" required hname="백넘버를 입력 해 주세요." /></td>
  </tr>
  <tr>
	<td height="22" align="center" bgcolor="#D2BF7E"><span class="style7">소 속 팀</span></td>
	<td bgcolor="#FFFFFF">&nbsp;&nbsp;
		<select name="tid">
		  <?= $tselect ?>
		</select>	</td>
  </tr>
  <tr>
	<td height="22" align="center" bgcolor="#D2BF7E"><span class="style7">선수구분</span></td>
	<td bgcolor="#FFFFFF">&nbsp;&nbsp;
		<select name="p_gubun">
		  <option <?php if($list['p_gubun']=='현역') echo 'selected' ?>>현역</option>
		  <option <?php if($list['p_gubun']=='은퇴') echo 'selected' ?>>은퇴</option>
		  <option <?php if($list['p_gubun']=='기타') echo 'selected' ?>>기타</option>
	  </select></td>
  </tr>
	<tr>
	  <td height="22" align="center" bgcolor="#D2BF7E"><span class="style7">홈페이지 나열순서 </span></td>
	  <td bgcolor="#FFFFFF">&nbsp;&nbsp;
		  <input name="p_seq" type="text" size="5" maxlength="3" value="<?= $list['p_seq']?>" /></td>
	</tr>
  <tr>
		<td height="22" align="center" bgcolor="#EDE1F7" >생년월일</td>
	  <td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_bdate" size="20"  value="<?=$list['p_bdate']?>" /></td>
	</tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >졸업년도</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_ddate" size="20"  value="<?=$list['p_ddate']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >출신학교</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_school" size="45"  value="<?=$list['p_school']?>"  style="width:95%"/>		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >프로입단</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_pro" size="10"  value="<?=$list['p_pro']?>" />
		년 </td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >신장</td>
		<td bgcolor="#FFFFFF"><input type="text" name="p_height" size="10"  value="<?=$list['p_height']?>" /></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >몸무게</td>
		<td bgcolor="#FFFFFF"><input type="text" name="p_weight" size="10"  value="<?=$list['p_weight']?>" /></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >혈핵형</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_oab" size="10"  value="<?=$list['p_oab']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >신발크기</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_sin" size="10"  value="<?=$list['p_sin']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >이메일</td>
		<td bgcolor="#FFFFFF"><input type="text" name="p_email" size="45"  value="<?=$list['p_email']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >홈페이지(까페)</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_homepage" size="45"  value="<?=$list['p_homepage']?>"  style="width:95%"/>		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >가족관계</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_family" size="45"  value="<?=$list['p_family']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >별명</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_nickname" size="20"  value="<?=$list['p_nickname']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >나의 이상형</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_lee" size="45"  value="<?=$list['p_lee']?>"  style="width:95%"/>		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >평소 취미 활동</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_hobby" size="45"  value="<?=$list['p_hobby']?>"  style="width:95%"/>		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >스트레스 해소법</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_stress" size="45"  value="<?=$list['p_stress']?>"  style="width:95%"/>		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >가수 및 음악장르</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_music" size="45"  value="<?=$list['p_music']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >좋아하는 음식</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_food" size="45"  value="<?=$list['p_food']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >좋아하는 스포츠 </td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_spo" size="45"  value="<?=$list['p_spo']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >농구시작은 언제</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_start" size="45"  value="<?=$list['p_start']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >마음에 드는 선수</td>
		<td bgcolor="#FFFFFF"><input type="text" name="p_mplayer" size="45"  value="<?=$list['p_mplayer']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >소속팀 변경</td>
		<td width="80%" bgcolor="#FFFFFF"><input name="teamhistory" type="button" class="CCbox03" id="teamhistory" value="	  소속팀 변경사항 보기	"  onclick="pop_Player_teamhistory('<?=$list['uid']?>', '<?=$list['p_name']?>')"/>
		(등록시에는 <span class="style5">먼저 등록하신 후</span> 수정 클릭 후 변경사항 입력 해 주세요.)</td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >Player Life</td>
		<td width="80%" bgcolor="#FFFFFF"><textarea name="p_life" cols="50" rows="7" style="width:98%"><?=$list['p_life']?>
</textarea></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >은퇴후 계획</td>
		<td width="80%" bgcolor="#FFFFFF"><input type="text" name="p_end" size="45"  value="<?=$list['p_end']?>" />		</td>
	  </tr>
	  <tr class="base">
		<td height="30" colspan="2" align="center" bgcolor="efefef" ><strong>선수 사진 이미지</strong> </td>
	</tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수사진 </td>
		<td bgcolor="#FFFFFF"><input name="upfile" type="file" id="upfile" size="30" />
		  &nbsp;&nbsp; (90 X 110) =&gt; Savers Secret 용 
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="40%">파일명 :
				<?=$list['upfiles'][upfile][name] ?></td>
			  <td width="60%"><input name="del_upfile" type="checkbox" id="del_upfile" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수소개 1 </td>
		<td bgcolor="#FFFFFF"><input type="file" name="upfile1" size="30" />
		  &nbsp;&nbsp; (138 X 244) =&gt; 사각 회색 명함사진
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="60%">파일명 :
				<?=$list['upfiles'][upfile1][name] ?></td>
			  <td width="40%"><input name="del_upfile1" type="checkbox" id="del_upfile1" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수소개 2 </td>
		<td bgcolor="#FFFFFF"><input type="file" name="upfile2" size="30" />
		  &nbsp;&nbsp; (169 X 249) =&gt;  분홍색 안쪽 사진
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="60%">파일명 :
				<?=$list['upfiles'][upfile2][name] ?></td>
			  <td width="40%"><input name="del_upfile2" type="checkbox" id="del_upfile2" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수소개 3 </td>
		<td bgcolor="#FFFFFF"><input type="file" name="upfile3" size="30" />
		  &nbsp;&nbsp; (154 X 636) =&gt; 아래로 길죽한 사진 
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="60%">파일명 :
				<?=$list['upfiles'][upfile3][name] ?></td>
			  <td width="640%"><input name="del_upfile3" type="checkbox" id="del_upfile3" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수종합기록 1 </td>
		<td bgcolor="#FFFFFF"><input type="file" name="upfile4" size="30" />
		  &nbsp;&nbsp; (131 X 178) =&gt; 손잡이 거울같은 사진 
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="60%">파일명 :
				<?=$list['upfiles'][upfile4][name] ?></td>
			  <td width="40%"><input name="del_upfile4" type="checkbox" id="del_upfile4" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
	  <tr class="base">
		<td height="22" align="center" bgcolor="#EDE1F7" >선수종합기록 2 </td>
		<td bgcolor="#FFFFFF"><input type="file" name="upfile5" size="30" />
		  &nbsp;&nbsp; (654 X 214) =&gt; 검정색 붓터치 + 농구공 
		  <table width="500"  border="0" cellspacing="0" cellpadding="0">
			<tr>
			  <td width="60%">파일명 :
				<?=$list['upfiles'][upfile5][name] ?></td>
			  <td width="40%"><input name="del_upfile5" type="checkbox" id="del_upfile5" value="checkbox" />
				삭제 </td>
			</tr>
		  </table></td>
	  </tr>
  </table>
<br>
	<table width="90%"  border="0" align="center" cellpadding="0" cellspacing="0">
		<tr>
		  <td align="center"><input name="Submit" type="submit" class="CCbox04" value="	입 력	">
			&nbsp;
		  <input name="Submit2" type="button" class="CCbox04" value="	뒤 로	" onclick="javascript:history.back();" /></td>
		</tr>
	</table>
	<br>	
</form>

<?php
echo $SITE['tail'] ?? '';
?>
