<?php
if(!defined('__XE__')) exit();
/**
 * @file soo_parking.addon.php
 * @author MinSoo Kim <misol.kr@gmail.com>
 * @brief Display parking page.
 */
if(Context::getResponseMethod() == 'HTML') {
	if($called_position == 'before_display_content' && Context::get('module') != 'admin') {
		$oMemberModel = &getModel('member');
		$view_checker = 0;

		// 로그인 페이지일 경우 표시 안함
		if(Context::get('act') == 'dispMemberLoginForm' && !$oMemberModel->isLogged()) $view_checker = 1;

		// 시간 설정
		if($view_checker === 0)
		{
			if($addon_info->until)
			{
				if(time() > strtotime($addon_info->until))
				{
					$view_checker = 1;
				}
			}
		}

		// 회원 관련된 예외상황 들
		if($view_checker === 0)
		{
			if($oMemberModel->isLogged()) {
				$MemberID=$oMemberModel->getLoggedUserID();
				if($MemberID) {
					// member ID
					if($addon_info->but_group != '' || $addon_info->but_id != '') {
						$MemberSRL=$oMemberModel->getMemberSrlByUserID($MemberID);
						$MemberGroups=$oMemberModel->getMemberGroups($MemberSRL);
						if($addon_info->but_id) {
							$but_ids = explode(",",$addon_info->but_id);
							if(is_array($but_ids)) {
								if(in_array($MemberID, $but_ids) && $MemberID!='') $view_checker = 1;
							}
						}
						if($addon_info->but_group) {
							// member Group
							$but_groups = explode(",",$addon_info->but_group);
							if(is_array($MemberGroups)) {
								foreach($MemberGroups as $value) {
									if(in_array($value,$but_groups) && $value!='') $view_checker = 1;
								}
							}
						}
					}
				}
			}
		}

		//user-agent
		if($addon_info->except_useragent || $addon_info->do_useragent) {
			if(trim($addon_info->except_useragent) && isset($addon_info->except_useragent)) {
				$except_useragent = explode("\n",$addon_info->except_useragent);
				foreach($except_useragent as $value) {
					if(trim($value) && trim($value) != '') if(stristr($_SERVER['HTTP_USER_AGENT'],trim($value)) != FALSE) $view_checker = 1;
				}
			}
			if(trim($addon_info->do_useragent) && isset($addon_info->do_useragent)) {
				$do_useragent = explode("\n",$addon_info->do_useragent);
				foreach($do_useragent as $value) {
					if(trim($value) && trim($value) != '') if(stristr($_SERVER['HTTP_USER_AGENT'],trim($value)) != FALSE) $view_checker = 0;
				}
			}
		}


		// Print output
		if($view_checker === 0)
		{
			$addon_output = '';

			if($addon_info->view_message_ko && Context::getLangType() == 'ko')
			{
				$addon_output = $addon_info->view_message_ko;
			}
			elseif($addon_info->view_message_en)
			{
				$addon_output = $addon_info->view_message_en;
			}
			else
			{
				$addon_output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=yes, target-densitydpi=medium-dpi" />
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<title>Not ready to show</title>
	</head>
	<body>
		<div style="text-align:center;">
			<h1>This Homepage is not ready to show!</h1>
			<p>Coming Soon...</p>
		</div>
	</body>
</html>';
			}

			header("Content-Type: text/html; charset=UTF-8");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			header("Set-Cookie: ");
			if((defined('__OB_GZHANDLER_ENABLE__') && __OB_GZHANDLER_ENABLE__ == 1)
				&& strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== FALSE
				&& extension_loaded('zlib')
				&& !headers_sent())
			{
				ini_set('zlib.output_compression', true);
			}
			print($addon_output);
			Context::close();
			exit();
		}
	}
}
?>
