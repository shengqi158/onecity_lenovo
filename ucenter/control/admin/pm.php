<?php

/*
	[UCenter] (C)2001-2008 Comsenz Inc.
	This is NOT a freeware, use is subject to license terms

	$Id: badword.php 12180 2008-01-17 05:56:43Z heyond $
*/

!defined('IN_UC') && exit('Access Denied');

class control extends adminbase {

	function control() {
		$this->adminbase();
		if(!$this->user['isfounder'] && !$this->user['allowadminpm']) {
			$this->message('no_permission_for_this_module');
		}
		$this->load('pm');
		$this->check_priv();
	}

	function onls() {
		$folder = 'inbox';
		$filter = 'announcepm';
		$status = 0;
		if($this->submitcheck()) {
			$delnum = $_ENV['pm']->deletepm($this->user['uid'], $folder, $filter, $_POST['delete']);
			$status = 1;
			$this->writelog('pm_delete', "delete=".implode(',', $_POST['delete']));
		}
		$pmnum = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."pms WHERE msgtoid='0' AND folder='inbox'");
		$pmlist = $_ENV['pm']->get_pm_list($this->user['uid'], $pmnum, $folder, $filter, $_GET['page']);
		$multipage = $this->page($pmnum, 10, $_GET['page'], 'admin.php?m=pm&a=ls');
		$extra = 'extra='.rawurlencode($_GET['extra']);
		$a = getgpc('a');
		$this->view->assign('a', $a);
		$this->view->assign('status', $status);
		$this->view->assign('pmlist', $pmlist);
		$this->view->assign('extra', $extra);
		$this->view->assign('multipage', $multipage);

		$this->view->display('admin_pm');
	}

	function onview() {
		$pmid = @is_numeric($_GET['pmid']) ? $_GET['pmid'] : 0;
		$pms = $_ENV['pm']->get_pm_by_pmid($this->user['uid'], $pmid);

		if($pms[0]) {
			$pms = $pms[0];
			require_once UC_ROOT.'lib/uccode.class.php';
			$this->uccode = new uccode();
			$this->uccode->lang = &$this->lang;
			$pms['message'] = $this->uccode->complie($pms['message']);
			$pms['dateline'] = $this->date($pms['dateline']);
		}

		$extra = 'extra='.rawurlencode($_GET['extra']);
		$a = getgpc('a');
		$this->view->assign('a', $a);
		$this->view->assign('pms', $pms);
		$this->view->assign('extra', $extra);

		$this->view->display('admin_pm');
	}

	function onsend() {
		$status = 0;
		if($this->submitcheck()) {
			$lastpmid = $_ENV['pm']->sendpm($_POST['subject'], $_POST['message'], $this->user['isfounder'] ? '' : $this->user, 0);
			$status = 1;
			$this->writelog('pm_send', "subject=".htmlspecialchars($_POST['subject']));
		}
		$this->view->assign('status', $status);
		$this->view->display('admin_pm_send');
	}

	function onclear() {
		$delnum = 0;
		if($this->submitcheck()) {
			$cleardays = intval($_POST['cleardays']);
			$unread = !empty($_POST['unread']) ? 1 : 0;
			$sqladd = '';
			if($cleardays > 0) {
				$sqladd .= ' AND dateline < '.($this->time - $cleardays * 86400);
			}
			if($unread) {
				$sqladd .= " AND new='0'";
			}
			$this->db->query("DELETE FROM ".UC_DBTABLEPRE."pms WHERE 1 $sqladd", 'UNBUFFERED');
			$delnum = $this->db->affected_rows();
			$status = 1;
			$this->writelog('pm_clear', "cleardays=$cleardays&unread=$unread");
		}

		$pmnum = $this->db->result_first("SELECT COUNT(*) FROM ".UC_DBTABLEPRE."pms");
		$this->view->assign('pmnum', $pmnum);
		$this->view->assign('delnum', $delnum);
		$this->view->assign('status', $status);
		$this->view->display('admin_pm_clear');
	}

}

?>