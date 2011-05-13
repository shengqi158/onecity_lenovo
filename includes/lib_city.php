<?php
/*区域航班*/
function get_city_children($arr)
{
	$all_array = array();
	foreach($arr AS $val){
		//$tt = array_merge(array($val), array_keys(cat_list($val, 0, false)));
		$tt =  array_keys(cat_list($val, 0, false));
		$all_array  = array_merge($all_array,$tt);
		/*
		echo "<br>".count($tt)."<br>";
		print_r($tt);
		echo "<br><br>==============<br><br>";
		*/
	}
	/*
	echo "<br><br>==============<br><br>";
	echo "<br>".count($all_array)."<br>";
	print_r($all_array);
	*/
	return 'a.cat_id ' . db_create_in(array_unique($all_array));		
	
}

function get_city_children_a($arr)
{
	$all_array = array();
	foreach($arr AS $val){
		$tt =  array_keys(cat_list($val, 0, false));
		$all_array  = array_merge($all_array,$tt);
		
	}
	return 'a.city_id ' . db_create_in(array_unique($all_array));		
	
}


function get_user_region()
{
	/* 检查有没有缺货 */
    $sql = "SELECT msn FROM ".$GLOBALS['ecs']->table('users')." WHERE user_id = '$_SESSION[user_id]' AND user_rank > 0 ";
    $res = $GLOBALS['db']->getOne($sql);
	return explode(",",$res);
}

function get_user_permission($user_region)
{
	$user_permission = array();
	foreach($user_region AS $key => $val){
		if(!empty($val)){
			$cat_name = $GLOBALS['db']->getOne("SELECT cat_name FROM ".$GLOBALS['ecs']->table('category')." WHERE cat_id = $val ");
		    array_push($user_permission,$cat_name);	
		}	
	}
	return $user_permission;
}


/**/
function get_city_list($children,$limit = 0){

	$filter['start_price'] = empty($_REQUEST['start_price']) ? 0 : $_REQUEST['start_price'];
    $filter['end_price'] = empty($_REQUEST['end_price']) ? 1000000 : $_REQUEST['end_price'];

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? 0 : $_REQUEST['audit_status'];
	
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = ' WHERE '. $children ." AND a.sys_level = 5 ";
	
    if ($filter['county_name'])
    {
        $where .= " AND a.cat_name LIKE '%" . mysql_like_quote($filter['county_name']) . "%'";
    }
	if ($filter['city_name'])
    {
        $where .= " AND a1.cat_name LIKE '%" . mysql_like_quote($filter['city_name']) . "%'";
    }
    if ($filter['region_name'])
    {
        $where .= " AND a3.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }
    if ($filter['audit_status'])
    {
        $where .= " AND a.audit_status = $filter[audit_status] ";
    }



	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);


	/**/
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    else
    {
        $filter['page_size'] = 50;
    }
	
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id "
               . $where;
    }
    elseif ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
                $where;
    }
    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
				$where;
		
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id ,a.is_upload, a.audit_status, a.is_audit_confirm, ". //
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region ".
			",$request_title AS city_request " . 
			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_request').   " AS re ON re.city_id = a.cat_id ".
			
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c  ON c.city_id = a.cat_id '.
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad'). 	' AS ad ON ad.city_id = a.cat_id '.
			"$where ORDER BY city_request DESC, a.is_upload DESC, a.audit_status DESC ".
			$limit_sql;
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		$ad_list = get_ad_list_by_cityid($val['cat_id']);//用做弹窗 
		$ad_summary = get_ad_summary($ad_list);
		$res[$key]['status_summary'] = get_ad_status_summary($ad_list);
		
		$res[$key]['photo_summary'] = $ad_summary['photo_summary']/4; //照片总量
		$res[$key]['ad_count'] = count($ad_list) ;		//上传条数
		$res[$key]['time_summary'] = $ad_summary['time_summary']; 	//最新上传时间
		$res[$key]['audit_status_summary'] = $ad_summary['audit_status_summary']; //已经审核数量
		$res[$key]['audit_confirm_summary'] = $ad_summary['audit_confirm_summary']; //审核通过数量


	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}

function get_ad_status_summary($ad_list){
	$audit_level_array = array("1"=>"0","2"=>"0","3"=>"0","4"=>"0","5"=>"0");
	foreach($audit_level_array AS $k => $v){
		foreach($ad_list AS $key => $val){
			if($k == $val['audit_status'] && $val['is_upload']  && $val['photo_num'] && $val['is_audit_confirm']){
					$audit_level_array[$k+1] += 1;
			}else{
				if($val['audit_status'] == 1 && $_SESSION['user_rank'] == 2 && $val['is_audit_confirm'] == 0 && $val['photo_num']){
					$audit_level_array[$k+1] += 1;
				}				
			}
		}
	}
	return $audit_level_array;
	
}
function get_ad_summary($ad_list)
{
	$res = array();
	$res['time_summary'] = 	$res['audit_status_summary'] = 	$res['audit_confirm_summary'] = 0;
	foreach($ad_list AS $key=>$val)
	{
		$res['photo_summary'] += $val['photo_num'];
		$res['time_summary'] =  $res['time_summary'] < $val['time_original'] ? $val['time_original'] : $res['time_summary'] ;
		if($val['audit_status'] > 1){
			$res['audit_status_summary'] += 1;
			if($val['is_audit_confirm'] && $val['audit_status'] == 5){
				$res['audit_confirm_summary'] += 1;
			}
		}
	}
	
	$res['time_summary'] = local_date('Y-m-d', $res['time_summary']);
	
	return $res;
}
//一个城市的左右广告列表
function get_ad_list_by_cityid($city_id)
{
	$sql = "SELECT a.*,c.user_time,c.col_7,COUNT(g.img_id) AS photo_num ".
			//", c.city_id, c.user_time FROM " . 
			" FROM ".$GLOBALS['ecs']->table('city_ad') . " AS a ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c ON c.ad_id = a.ad_id '.
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_gallery'). ' AS g ON g.ad_id = a.ad_id '.
			" WHERE a.city_id = $city_id GROUP BY c.record_id ORDER BY a.ad_id ASC ";
	//echo $sql."<br>";	
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		$res[$key]['time_original'] = $val['user_time'];
		$res[$key]['user_time'] = local_date('Y-m-d', $val['user_time']);
	}
	return $res;
}
/* 生成空数据 */
function make_city_content($count = CONTENT_COLS)
{
	$city_content = array();
	
	for($i=1;$i<=$count;$i++){
		$key = "col_".$i;
		$city_content[$key]= "";
	}
	return $city_content;
}

/* 将 excel数据 填充进入 空数组 */
function full_city_content($xls_array,$city_content)
{	
	if(count($xls_array)){
		foreach($xls_array AS $key => $val){
			//为7列数字列 清除 _)
			/*
			if($key == 19 || $key == 22 || $key == 30 || $key == 31 || $key == 32 || $key == 34 || $key == 36 || $key == 38)
			{
				$city_content["col_".$key] = substr($val,0,strlen(trim($val))-2);
			}
			*/
			
			if(strripos($val,"_)")){
				$city_content["col_".$key] = substr($val,0,strlen(trim($val))-2);
			}
			else{
				$city_content["col_".$key] = trim($val);
			}
		}	
	}
	$city_content['city_id'] = get_cat_id_by_name($xls_array[3]);
	$city_content['user_id'] = $_SESSION['user_id'];
	$city_content['user_time'] = gmtime();
	return $city_content;
}

function get_cat_id_by_name($cat_name)
{
	$sql = "SELECT cat_id  FROM " .$GLOBALS['ecs']->table('category') .
			" WHERE cat_name LIKE '". $cat_name ."' ";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_city_id($ad_id)
{
	$sql = "SELECT city_id  FROM " .$GLOBALS['ecs']->table('city_ad') .
			" WHERE ad_id = $ad_id ";
	//echo $sql;	
	
	$res = $GLOBALS['db']->getOne($sql);
	return $res;
}

function get_city_info($ad_id = 0)
{
	if($ad_id){
		$city_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('city') . " WHERE ad_id = $ad_id");
		return $city_info;
	}
}

function get_ad_info($ad_id = 0)
{
	if($ad_id){
		$ad_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('city_ad') . " WHERE ad_id = $ad_id");
		return $ad_info;
	}
}


function get_price_info($ad_id = 0)
{
	if($ad_id){
		$price_info = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('project_request') . " WHERE ad_id = $ad_id");
		return $price_info;
	}
}



function get_ad_photo_info($ad_id = 0){
	if($ad_id){
		$photo_info = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('city_gallery') . " WHERE ad_id = $ad_id");
		return $photo_info;
	}
}

/* 获得审核路径 */
function get_audit_path($ad_id = 0,$audit_level_array,$type="audit")
{
	switch($type)
    {
        case 'audit':
			$type_sql = " AND price_audit = 0 AND feedback_audit = 0 ";
			break;
        case 'price':
			$type_sql = " AND price_audit = 1 AND feedback_audit = 0 ";
			break;
        case 'feedback':
			$type_sql = " AND price_audit = 0 AND feedback_audit = 1 ";
			break;
    }
	
	$audit_path = array(); //"2","3","4","5"级别
	
	$sql = "SELECT c.*, u.user_name ,u.real_name  FROM " . $GLOBALS['ecs']->table('city_ad_audit') . " AS c ". // , r.rank_name
 			" LEFT JOIN " .$GLOBALS['ecs']->table('users') . " AS u ON u.user_id = c.user_id ". 
 			//" LEFT JOIN " .$GLOBALS['ecs']->table('user_rank') . " AS r ON r.rank_id = c.user_rank ". 
			" WHERE c.ad_id = $ad_id ".$type_sql."ORDER BY time DESC ";

	$res = $GLOBALS['db']->getAll($sql);
	
	foreach($audit_level_array AS $v){
		$unit = array();
		foreach($res AS $val){
			if($val['user_rank'] == $v){
				array_push($unit,$val);
			}
		}
		$audit_path[$v] = $unit;
	}
	
	//print_r($audit_path);
	return $audit_path;
}


function is_exist_city_ad($city_id,$col_7)
{
	$sql = "SELECT a.audit_status ,c.record_id FROM " . $GLOBALS['ecs']->table('city') . " AS c ". // , r.rank_name
 			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS a ON a.ad_id = c.ad_id ". 
 			//" LEFT JOIN " .$GLOBALS['ecs']->table('user_rank') . " AS r ON r.rank_id = c.user_rank ". 
			" WHERE c.city_id = $city_id AND  c.col_7 LIKE '$col_7' ";
	$res = $GLOBALS['db']->getRow($sql);
	return $res;
}
//城市级别不对
function get_sys_level($city_id)
{
	$sql = "SELECT sys_level  FROM " . $GLOBALS['ecs']->table('category') .
			" WHERE cat_id = $city_id ";
	$res = $GLOBALS['db']->getOne($sql);
	//echo $sql."<br>";
	return $res;
}

function act_city_request($ad_id,$level,$is_cancel = 0)
{
	$city_id = get_city_id($ad_id);
	$now_level_col = "lv_".$level;
	
	$prev_level = $level - 1;
	$prev_level_col = "lv_".$prev_level;
	
	$next_level = $level + 1;
	$next_level_col = "lv_".$next_level;
	
	if($is_cancel){
		$sql = "SELECT $now_level_col,$prev_level_col FROM ".$GLOBALS['ecs']->table('city_request')." WHERE city_id = '$city_id' ";
	}else{
		$sql = "SELECT $now_level_col,$next_level_col FROM ".$GLOBALS['ecs']->table('city_request')." WHERE city_id = '$city_id' ";
	}
    $res = $GLOBALS['db']->getRow($sql);

	//print_r($res);
	$now_num  = $res[$now_level_col] - 1; //当前等级减1
	$next_num = $res[$next_level_col] + 1; //下一等级加1
	$prev_num = $res[$prev_level_col] + 1; //下一等级加1
	
	
	//更新city_request
	if($is_cancel){ //不通过操作		
		$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . 
				 " SET $now_level_col = '$now_num',$prev_level_col = '$prev_num'  WHERE city_id = '$city_id' ";
	}else{
		$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_request') . 
				 " SET $now_level_col = '$now_num',$next_level_col = '$next_num'  WHERE city_id = '$city_id' ";
	}
	//echo $sql_2;
	$GLOBALS['db']->query($sql_2);
	
	//echo $now_level_col."-".$now_num."-".$next_level_col."-".$next_num."-"."<br>".$sql."<br>".$sql_2;
	
}

/**/
function getFull_ad_list($children,$limit = 0,$r_title){
	
	$where = ' WHERE '. $children ;
		
	$sql = "SELECT a.*, ad.*, c.resource ".
			" FROM ".$GLOBALS['ecs']->table('city') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad') . " AS ad ON ad.ad_id = a.ad_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS c ON c.cat_id = a.city_id ". 
			
			"$where ORDER BY a.ad_id DESC ";
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		if($val['is_upload'] && $val['audit_status'])
		{
			$res[$key]['lv_2'] = get_audit_note($val['ad_id'],2);
			$res[$key]['lv_3'] = get_audit_note($val['ad_id'],3);
			$res[$key]['lv_4'] = get_audit_note($val['ad_id'],4);
			$res[$key]['lv_5'] = get_audit_note($val['ad_id'],5);
			$res[$key]['resource_type'] = $r_title[$val['resource']];
			//echo $res[$key]['ad_id']."-".$val['resource']."-".$res[$key]['resource_type']."<br>";
		}
	}
    return $res;
}

function get_audit_note($ad_id,$user_rank){
	$sql = "SELECT audit_note FROM ".$GLOBALS['ecs']->table('city_ad_audit')." WHERE ad_id = '$ad_id' AND user_rank = $user_rank ORDER BY record_id DESC";
	$res = $GLOBALS['db']->getAll($sql);
	$tt = array_shift($res); //返回第一个
	return $tt['audit_note'];
}



function excel_write_with_sub_array($_name,$title = array(),$data = array(),$sub_folder='city'){
	include_once(ROOT_PATH . 'includes/excelwriter.inc.php');
	
	$name = ROOT_PATH.'xls/'.$sub_folder.'/'.$_name.".xls";

	$excel=new ExcelWriter($name);

	if($excel==false)	
		echo $excel->error;

	$excel->GetHeader(); //头部 用来定义编码
	
	//写 标题title
	$excel->writeRow();
	foreach($title AS $v){
		if(is_array($v)){
			foreach($v AS $item){
				$excel->writeCol($item);
			}	
		}else{
			$excel->writeCol($v);
			//echo $v;
		}
	}
	
	/*按照 title 组织内容*/
	foreach($data AS $key => $val){
		$excel->writeRow();
		foreach($title AS $k => $v){
			if(is_array($v)){
				foreach($v AS $m => $n){
					$excel->writeCol($val[$k][$m]);
				}
			}else{
				$excel->writeCol($val[$k]);
			}
		}
	}
	
	/*
	$myArr=array("名字","姓氏","地址","年龄");
	$excel->writeLine($myArr);

	$myArr=array("Sriram","Pandit","23 mayur vihar",24);
	$excel->writeLine($myArr);

	$excel->writeRow();
	$excel->writeCol("Manoj");
	$excel->writeCol("Tiwari");
	$excel->writeCol("80 Preet Vihar");
	$excel->writeCol(24);

	$excel->writeRow();
	$excel->writeCol("Harish");
	$excel->writeCol("Chauhan");
	$excel->writeCol("115 Shyam Park Main");
	$excel->writeCol(22);

	$myArr=array("Tapan","Chauhan","1st Floor Vasundhra",25);
	$excel->writeLine($myArr);
	*/
	$excel->GetFooter(); //头部 用来定义编码

	$excel->close();
	return true;
}
/**********************************/
//
//
/**********************************/


/**/
function get_project_list($user_rank){
	$sql = "SELECT p.*  ".
			" FROM ".$GLOBALS['ecs']->table('project') . " AS p ".			
			"WHERE 1 ORDER BY p.project_id DESC ";
	//echo $sql;	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val){
		if($user_rank > 1){
			$res[$key]['request_count'] = 0; 		//上报城市总数
			$res[$key]['price_audit_count'] = 0; 	//价格审核通过数
			$res[$key]['feedback_audit_count'] = 0;	//换画反馈通过数			
		}
		$res[$key]['end_time'] 	= date( 'Y-m-d',(strtotime($val['start_time']) + $val['duration_time'] * 86400 ));
		$res[$key]['pic_count']	= $GLOBALS['db']->getOne("SELECT COUNT(*) FROM ".$GLOBALS['ecs']->table('project_picture')." WHERE project_id  =  $val[project_id]");
	}
	return $res;
}

function get_project_info($project_id){
	if($project_id){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project') .	" WHERE project_id =  $project_id ";
		$res =  $GLOBALS['db']->getRow($sql);
		return $res;
	}else{
		return false;
	}
}

/**/
function get_project_city($children,$limit = 0){

    $filter['county_name'] = empty($_REQUEST['county_name']) ? '' : trim($_REQUEST['county_name']);
    $filter['city_name'] = empty($_REQUEST['city_name']) ? '' : trim($_REQUEST['city_name']);
    $filter['region_name'] = empty($_REQUEST['region_name']) ? '' : trim($_REQUEST['region_name']);
    $filter['audit_status'] = empty($_REQUEST['audit_status']) ? 0 : $_REQUEST['audit_status'];
	
	$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'inv_id' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

	$where = ' WHERE '. $children ." AND a.sys_level = 5 AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 "; 
	$where.= $_SESSION['user_rank'] > 1 ? " AND pr.req_id > 0 " : "" ;
	// 最终通过的权限要求 ID
    if ($filter['county_name'])
    {
        $where .= " AND a.cat_name LIKE '%" . mysql_like_quote($filter['county_name']) . "%'";
    }
	if ($filter['city_name'])
    {
        $where .= " AND a1.cat_name LIKE '%" . mysql_like_quote($filter['city_name']) . "%'";
    }
    if ($filter['region_name'])
    {
        $where .= " AND a3.cat_name LIKE '%" . mysql_like_quote($filter['region_name']) . "%'";
    }
    if ($filter['audit_status'])
    {
        $where .= " AND a.audit_status = $filter[audit_status] ";
    }



	/* 分页大小 */
    $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);


	/**/
    if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
    {
        $filter['page_size'] = intval($_REQUEST['page_size']);
    }
    else
    {
        $filter['page_size'] = 50;
    }
	
	
	/* 记录总数 */
    if ($filter['city_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id "
               . $where;
    }
    elseif ($filter['region_name'])
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') . " AS a ".
				" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ".
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
			 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ".
                $where;
    }
    else
    {
        $count_sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('category') ." AS a " . 
					" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
					" LEFT JOIN " .$GLOBALS['ecs']->table('project_request').   " AS pr ON pr.city_id = a.cat_id  AND pr.ad_id  = ad.ad_id ".
					" $where " ;
		
    }

    $filter['record_count']   = $GLOBALS['db']->getOne($count_sql);
    $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
	
	$request_title = "re.lv_".$_SESSION['user_rank'];
	$limit_sql = $limit > 0 ? " LIMIT 0,$limit ": " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	$order_sql = $_SESSION['user_rank'] > 1 ? " ORDER BY t1 ASC " : " ORDER BY a.cat_id DESC";
	
	$sql = "SELECT a.cat_name AS county, a.market_level, a.cat_id ,a.is_upload, a.audit_status, a.is_audit_confirm, ". //
			"a1.cat_name AS city, a2.cat_name AS province, a3.cat_name AS region , ad.ad_id, ad.is_price_change , ad.price_status ,ad.is_price_confirm , ".
			" pr.req_id, pr.price, pr.price_amount, pr.request_price, pr.request_price_amount,  (ad.price_status - $_SESSION[user_rank]) AS t1 ".

			" FROM ".$GLOBALS['ecs']->table('category') . " AS a ".
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a1 ON a1.cat_id = a.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a2 ON a2.cat_id = a1.parent_id ". 
		 	" LEFT JOIN " .$GLOBALS['ecs']->table('category') . " AS a3 ON a3.cat_id = a2.parent_id ". 
			" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad').   " AS ad ON ad.city_id = a.cat_id ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('project_request').   " AS pr ON pr.city_id = a.cat_id  AND pr.ad_id  = ad.ad_id ".
			
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city'). 		' AS c  ON c.city_id = a.cat_id '.
			//" LEFT JOIN " .$GLOBALS['ecs']->table('city_ad'). 	' AS ad ON ad.city_id = a.cat_id '.
			"$where $order_sql ".
			$limit_sql;
	//echo $sql;	 //GROUP BY ad.ad_id 
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val)
	{
		/*
		$ad_list = get_ad_list_by_cityid($val['cat_id']);//用做弹窗 
		$ad_summary = get_ad_summary($ad_list);
		$res[$key]['status_summary'] = get_ad_status_summary($ad_list);
		
		$res[$key]['photo_summary'] = $ad_summary['photo_summary']/4; //照片总量
		$res[$key]['ad_count'] = count($ad_list) ;		//上传条数
		$res[$key]['time_summary'] = $ad_summary['time_summary']; 	//最新上传时间
		$res[$key]['audit_status_summary'] = $ad_summary['audit_status_summary']; //已经审核数量
		$res[$key]['audit_confirm_summary'] = $ad_summary['audit_confirm_summary']; //审核通过数量
		*/

	}
	$arr = array('citys' => $res, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count'],'sql' => $sql,'count_sql' => $count_sql, 'page_size' => $filter['page_size']);
    return $arr;
}

function act_select_request_city($project_id,$ad_id,$is_add){
	$final_ad =  get_final_ad($ad_id);
	if(count($final_ad)){
		if($is_add){
			$request_info['project_id'] = $project_id;
			$request_info['user_id'] = $_SESSION['user_id'];
			$request_info['price'] = $final_ad['col_30'] ; //['col_30'] = "每平米制作费"
			$request_info['price_amount'] = $final_ad['col_32'] ; //['col_30'] = "每平米制作费"
			$request_info['city_id'] = $final_ad['city_id'] ;
			$request_info['ad_id'] = $final_ad['ad_id'];
			
			$request_info['request_price'] = ""; 	//以后需要填写 第一次不用填写
			$request_info['request_price_amount'] = "";		//以后需要填写 第一次不用填写
			$request_info['request_note'] = "";			//前两项有变化则填写
			
			
			//$final_ad['col_31'] ; //['col_31'] = "安装费"
			//$final_ad['col_32'] ; //['col_32'] = "制作费合计"
		
			
			$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('project_request'), $request_info, 'INSERT');
			
		}else{
			$GLOBALS['db']->query("DELETE FROM" . $GLOBALS['ecs']->table('project_request') . " WHERE city_id = $final_ad[city_id] AND ad_id = $final_ad[ad_id]");
			$sql_2 = "UPDATE " . $GLOBALS['ecs']->table('city_ad') . 
					 " SET is_price_change = 0,price_status = 0,is_price_confirm = 0 WHERE ad_id = '$final_ad[ad_id]' ";
			$GLOBALS['db']->query($sql_2);
			
			
		}
		
	}
	
	
}

function get_final_ad($ad_id){
	$sql = "SELECT ad.*,c.* FROM ".$GLOBALS['ecs']->table('city_ad') . " AS ad ".
	 		" LEFT JOIN " .$GLOBALS['ecs']->table('city') . " AS c ON c.ad_id = ad.ad_id ". 
			" WHERE ad.ad_id =  $ad_id AND ad.audit_status = 5 AND ad.is_audit_confirm = 1 ";
	$res =  $GLOBALS['db']->getRow($sql);
	return $res;
}


function get_req_info($project_id,$ad_id){
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project_request') .
			" WHERE ad_id =  $ad_id AND project_id = $project_id ";
	$res =  $GLOBALS['db']->getRow($sql);
	return $res;
}


/**/
function get_picture_list($project_id){
	$ext_sql = $project_id ? " pic.project_id = $project_id " : " 1 " ;
	
	$sql = "SELECT p.*, pic.* ".
			" FROM ".$GLOBALS['ecs']->table('project_picture') . " AS pic ".
			" LEFT JOIN " .$GLOBALS['ecs']->table('project') . " AS p ON p.project_id = pic.project_id ". 
			"WHERE $ext_sql ORDER BY pic.upload_time DESC ";
	//echo $sql;//	 GROUP BY ad.ad_id
	
	$res = $GLOBALS['db']->getAll($sql);
	foreach($res AS $key => $val){
		$res[$key]['upload_time'] = date('Y-m-d',$val['upload_time']);
		
	}
	return $res;
}

function get_picture_info($picture_id){
	if($picture_id){
		$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('project_picture') .	" WHERE picture_id =  $picture_id ";
		$res =  $GLOBALS['db']->getRow($sql);
		return $res;
	}else{
		return false;
	}
}



?>