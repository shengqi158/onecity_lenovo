<?php

/**
 * SINEMALL 鍟嗗搧椤
 * ============================================================================
 * 鐗堟潈鎵€鏈 (C) 2005-2008 搴风洓鍒涙兂锛堝寳浜?級绉戞妧鏈夐檺鍏?徃锛屽苟淇濈暀鎵€鏈夋潈鍒┿€
 * 缃戠珯鍦板潃: http://www.ecshop.com锛沨ttp://www.comsenz.com
 * ----------------------------------------------------------------------------
 * 杩欎笉鏄?竴涓?嚜鐢辫蒋浠讹紒鎮ㄥ彧鑳藉湪涓嶇敤浜庡晢涓氱洰鐨勭殑鍓嶆彁涓嬪?绋嬪簭浠ｇ爜杩涜?淇?敼鍜
 * 浣跨敤锛涗笉鍏佽?瀵圭▼搴忎唬鐮佷互浠讳綍褰㈠紡浠讳綍鐩?殑鐨勫啀鍙戝竷銆
 * ============================================================================
 * $Author: testyang $
 * $Id: buy.php 14543 2008-05-04 02:18:37Z testyang $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$smarty->assign('footer', get_footer());
$smarty->display('buy.wml');

?>