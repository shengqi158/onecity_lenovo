<?php

/**
 * SINEMALL 资料分类管理程序    $Author: testyang $
 * $Id: ideacat.php 14481 2008-04-18 11:23:01Z testyang $
*/
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/cls_image.php');


/*初始化数据交换对象 */
$exc = new exchange($ecs->table("idea_cat"), $db, 'cat_id', 'cat_name');
$image = new cls_image($_CFG['bgcolor']);

/* act操作项的初始化 */
$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}

/*------------------------------------------------------ */
//-- 分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $ideacat = idea_cat_list(0, 0, false);
    foreach ($ideacat as $key => $cat)
    {
        $ideacat[$key]['type_name'] = $_LANG['type_name'][$cat['cat_type']];
    }
    $smarty->assign('ur_here',     $_LANG['06_ideacat_list']);
    $smarty->assign('action_link', array('text' => $_LANG['ideacat_add'], 'href' => 'ideacat.php?act=add'));
    $smarty->assign('full_page',   1);
    $smarty->assign('ideacat',        $ideacat);

    assign_query_info();
    $smarty->display('ideacat_list.htm');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $ideacat = idea_cat_list(0, 0, false);
    foreach ($ideacat as $key => $cat)
    {
        $ideacat[$key]['type_name'] = $_LANG['type_name'][$cat['cat_type']];
    }
    $smarty->assign('ideacat',        $ideacat);

    make_json_result($smarty->fetch('ideacat_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('idea_cat');

    $smarty->assign('cat_select',  idea_cat_list(0));
    $smarty->assign('ur_here',     $_LANG['ideacat_add']);
    $smarty->assign('action_link', array('text' => $_LANG['06_ideacat_list'], 'href' => 'ideacat.php?act=list'));
    $smarty->assign('form_action', 'insert');
    $smarty->assign('user_rank_list', get_rank_list());

    assign_query_info();
    $smarty->display('ideacat_info.htm');

}
elseif ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('idea_cat');

    /*检查分类名是否重复*/
    $is_only = $exc->is_only('cat_name', $_POST['cat_name']);

    if (!$is_only)
    {
        sys_msg(sprintf($_LANG['catname_exist'], stripslashes($_POST['cat_name'])), 1);
    }

    $cat_type = 1;
    if ($_POST['parent_id'] > 0)
    {
        $sql = "SELECT cat_type FROM " . $ecs->table('idea_cat') . " WHERE cat_id = '$_POST[parent_id]'";
        $p_cat_type = $db->getOne($sql);
        if ($p_cat_type == 2 || $p_cat_type == 3 || $p_cat_type == 5)
        {
            sys_msg($_LANG['not_allow_add'], 0);
        }
        else if ($p_cat_type == 4)
        {
            $cat_type = 5;
        }
    }

	/*处理图片*/    
    $img_name = basename($image->upload_image($_FILES['cat_logo'],'idealogo'));

    $sql = "INSERT INTO ".$ecs->table('idea_cat')."(cat_name, cat_type, cat_desc,keywords, templete , parent_id, sort_order, show_in_nav,cat_logo,cat_idea_id,cat_user_rank)
           VALUES ('$_POST[cat_name]', '$cat_type',  '$_POST[cat_desc]','$_POST[keywords]', '$_POST[templete]', '$_POST[parent_id]', '$_POST[sort_order]', '$_POST[show_in_nav]','$img_name','$_POST[cat_idea_id]','$_POST[cat_user_rank]')";
    $db->query($sql);

    if($_POST['show_in_nav'] == 1)
    {
        $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
        $vieworder += 2;
        //显示在自定义导航栏中
        $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) VALUES('" . $_POST['cat_name'] . "', 'a', '" . $db->insert_id() . "','1','$vieworder','0', '" . build_uri('idea_cat', array('acid'=> $db->insert_id())) . "','middle')";
        $db->query($sql);
    }

    admin_log($_POST['cat_name'],'add','ideacat');

    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'ideacat.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'ideacat.php?act=list';
    clear_cache_files();
    sys_msg($_POST['cat_name'].$_LANG['catadd_succed'],0, $link);
}

/*------------------------------------------------------ */
//-- 编辑资料分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('idea_cat');

    $sql = "SELECT cat_id, cat_name, cat_type, cat_desc, show_in_nav, keywords, templete,sort_order, parent_id,cat_idea_id,cat_logo,cat_user_rank FROM ".
           $ecs->table('idea_cat'). " WHERE cat_id='$_REQUEST[id]'";
    $cat = $db->GetRow($sql);

    if ($cat['cat_type'] == 2 || $cat['cat_type'] == 3 || $cat['cat_type'] ==4)
    {
        $smarty->assign('disabled', 1);
    }
    $options    =   idea_cat_list(0, $cat['parent_id'], false);
    $select     =   '';
    $selected   =   $cat['parent_id'];
    $smarty->assign('user_rank_list', get_rank_list());

    foreach ($options as $var)
    {
        if ($var['cat_id'] == $_REQUEST['id'])
        {
            continue;
        }
        $select .= '<option value="' . $var['cat_id'] . '" ';
        $select .= ' cat_type="' . $var['cat_type'] . '" ';
        $select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
        $select .= '>';
        if ($var['level'] > 0)
        {
            $select .= str_repeat('&nbsp;', $var['level'] * 4);
        }
        $select .= htmlspecialchars($var['cat_name']) . '</option>';
    }
    unset($options);
    $smarty->assign('cat',         $cat);
    $smarty->assign('cat_select',  $select);
    $smarty->assign('ur_here',     $_LANG['ideacat_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['06_ideacat_list'], 'href' => 'ideacat.php?act=list'));
    $smarty->assign('form_action', 'update');
    assign_query_info();
    $smarty->display('ideacat_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    /* 权限判断 */
    admin_priv('idea_cat');

    /*检查重名*/
    if ($_POST['cat_name'] != $_POST['old_catname'])
    {
        $is_only = $exc->is_only('cat_name', $_POST['cat_name'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf($_LANG['catname_exist'], stripslashes($_POST['cat_name'])), 1);
        }
    }

    if(!isset($_POST['parent_id']))
    {
        $_POST['parent_id'] = 0;
    }

    $row = $db->getRow("SELECT cat_type, parent_id FROM " . $ecs->table('idea_cat') . " WHERE cat_id='$_POST[id]'");
    $cat_type = $row['cat_type'];
    if ($cat_type == 3 || $cat_type ==4)
    {
        $_POST['parent_id'] = $row['parent_id'];
    }

    /* 检查设定的分类的父分类是否合法 */
    $child_cat = idea_cat_list($_POST['id'], 0, false);
    if (!empty($child_cat))
    {
        foreach ($child_cat as $child_data)
        {
            $catid_array[] = $child_data['cat_id'];
        }
    }
    if (in_array($_POST['parent_id'], $catid_array))
    {
        sys_msg(sprintf($_LANG['parent_id_err'], stripslashes($_POST['cat_name'])), 1);
    }

    if ($cat_type == 1 || $cat_type == 5)
    {
        if ($_POST['parent_id'] > 0)
        {
            $sql = "SELECT cat_type FROM " . $ecs->table('idea_cat') . " WHERE cat_id = '$_POST[parent_id]'";
            $p_cat_type = $db->getOne($sql);
            if ($p_cat_type == 4)
            {
                $cat_type = 5;
            }
            else
            {
                $cat_type = 1;
            }
        }
        else
        {
            $cat_type = 1;
        }
    }

    $dat = $db->getOne("SELECT cat_name, show_in_nav FROM ". $ecs->table('idea_cat') . " WHERE cat_id = '" . $_POST['id'] . "'");

	/*处理图片*/
	$img_name = basename($image->upload_image($_FILES['cat_logo'],'idealogo'));

	
    
	$param = " cat_name = '$_POST[cat_name]', cat_desc ='$_POST[cat_desc]', keywords='$_POST[keywords]', templete='$_POST[templete]',  parent_id = '$_POST[parent_id]', cat_type='$cat_type', sort_order='$_POST[sort_order]', show_in_nav = '$_POST[show_in_nav]', cat_idea_id = '$_POST[cat_idea_id]',cat_user_rank = '$_POST[cat_user_rank]' ";
    
	if (!empty($img_name))
    {
        //有图片上传
        $param .= " ,cat_logo = '$img_name' ";
    }


    if ($exc->edit("$param", $_POST['id']))
    {
        if($_POST['cat_name'] != $dat['cat_name'])
        {
            //如果分类名称发生了改变
            $sql = "UPDATE " . $ecs->table('nav') . " SET name = '" . $_POST['cat_name'] . "' WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'";
            $db->query($sql);
        }
        if($_POST['show_in_nav'] != $dat['show_in_nav'])
        {
            if($_POST['show_in_nav'] == 1)
            {
                //显示
                $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'");
                if(empty($nid))
                {
                    $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
                    $vieworder += 2;
                    $uri = build_uri('idea_cat', array('acid'=> $_POST['id']));
                    //不存在
                    $sql = "INSERT INTO " . $ecs->table('nav') .
                        " (name,ctype,cid,ifshow,vieworder,opennew,url,type) ".
                        "VALUES('" . $_POST['cat_name'] . "', 'a', '" . $_POST['id'] . "','1','$vieworder','0', '" . $uri . "','middle')";
                }
                else
                {
                    $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'";
                }
                $db->query($sql);
            }
            else
            {
                //去除
                $db->query("UPDATE " . $ecs->table('nav') . " SET ifshow = 0 WHERE ctype = 'a' AND cid = '" . $_POST['id'] . "' AND type = 'middle'");
            }
        }
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'ideacat.php?act=list';
        $note = sprintf($_LANG['catedit_succed'], $_POST['cat_name']);
        admin_log($_POST['cat_name'], 'edit', 'ideacat');
        clear_cache_files();
        sys_msg($note, 0, $link);

    }
    else
    {
        die($db->error());
    }
}



/*------------------------------------------------------ */
//-- 编辑资料分类的排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
    check_authz_json('idea_cat');

    $id    = intval($_POST['id']);
    $order = json_str_iconv(trim($_POST['val']));

    /* 检查输入的值是否合法 */
    if (!preg_match("/^[0-9]+$/", $order))
    {
        make_json_error(sprintf($_LANG['enter_int'], $order));
    }
    else
    {
        if ($exc->edit("sort_order = '$order'", $id))
        {
            clear_cache_files();
            make_json_result(stripslashes($order));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 删除资料分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('idea_cat');

    $id = intval($_GET['id']);

    $sql = "SELECT cat_type FROM " . $ecs->table('idea_cat') . " WHERE cat_id = '$id'";
    $cat_type = $db->getOne($sql);
    if ($cat_type == 2 || $cat_type == 3 || $cat_type ==4)
    {
        /* 系统保留分类，不能删除 */
        make_json_error($_LANG['not_allow_remove']);
    }

    $sql = "SELECT COUNT(*) FROM " . $ecs->table('idea_cat') . " WHERE parent_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        /* 还有子分类，不能删除 */
        make_json_error($_LANG['is_fullcat']);
    }

    /* 非空的分类不允许删除 */
    $sql = "SELECT COUNT(*) FROM ".$ecs->table('idea')." WHERE cat_id = '$id'";
    if ($db->getOne($sql) > 0)
    {
        make_json_error(sprintf($_LANG['not_emptycat']));
    }
    else
    {
        $exc->drop($id);
        $db->query("DELETE FROM " . $ecs->table('nav') . "WHERE  ctype = 'a' AND cid = '$id' AND type = 'middle'");
        clear_cache_files();
        admin_log($cat_name, 'remove', 'category');
    }

    $url = 'ideacat.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 切换是否显示在导航栏
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'toggle_show_in_nav')
{
    check_authz_json('cat_manage');

    $id = intval($_POST['id']);
    $val = intval($_POST['val']);

    if (cat_update($id, array('show_in_nav' => $val)) != false)
    {
        if($val == 1)
        {
            //显示
            $nid = $db->getOne("SELECT id FROM ". $ecs->table('nav') . " WHERE ctype='a' AND cid='$id' AND type = 'middle'");
            if(empty($nid))
            {
                //不存在
                $vieworder = $db->getOne("SELECT max(vieworder) FROM ". $ecs->table('nav') . " WHERE type = 'middle'");
                $vieworder += 2;
                $catname = $db->getOne("SELECT cat_name FROM ". $ecs->table('idea_cat') . " WHERE cat_id = '$id'");
                $uri = build_uri('idea_cat', array('acid'=> $id));

                $sql = "INSERT INTO " . $ecs->table('nav') . " (name,ctype,cid,ifshow,vieworder,opennew,url,type) ".
                    "VALUES('" . $catname . "', 'a', '$id','1','$vieworder','0', '" . $uri . "','middle')";
            }
            else
            {
                $sql = "UPDATE " . $ecs->table('nav') . " SET ifshow = 1 WHERE ctype='a' AND cid='$id' AND type = 'middle'";
            }
            $db->query($sql);
        }
        else
        {
            //去除
            $db->query("UPDATE " . $ecs->table('nav') . " SET ifshow = 0 WHERE ctype='a' AND cid='$id' AND type = 'middle'");
        }
        clear_cache_files();
        make_json_result($val);
    }
    else
    {
        make_json_error($db->error());
    }
}

/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('idea_cat'), $args, 'update', "cat_id='$cat_id'");
}
?>
