<div class="f_left" style="width:100%;">
	<a class="back_url" href="city_project.php?act=list_city_to_select&project_id=<?php echo $this->_var['project_id']; ?>&region_name=<?php echo $this->_var['city_name']; ?>&audit_status=<?php echo $this->_var['audit_status']; ?>"></a>
</div>
<script type="text/javascript" src="js/calendar.php"></script>
<link href="js/calendar/calendar.css" rel="stylesheet" type="text/css" />
	
<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
<div class="yellow_notice" style="text-align:center;"><?php echo $this->_var['upload_message']; ?></div>
<?php endif; ?>

<?php if ($this->_var['ad_info']['is_audit_confirm'] == 1 && $this->_var['ad_info']['audit_status'] == 5): ?>
	<form method="post" action="city_project.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
	<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_40114300_1338640113');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_40114300_1338640113']):
?>
	<div class="city_info radius_5px">
		<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_40114300_1338640113']; ?></div>
		<div class="f_left right_content">		
			<span class="f_right"><a target="_blank"  class="grey666" href="city_operate.php?act=view_log&ad_id=<?php echo $this->_var['ad_detail']['ad_id']; ?>&col_name=<?php echo $this->_var['k']; ?>">
				修改记录</a></span>	
			<?php if (( $this->_var['ad_detail']['can_modify'] != 0 || $this->_var['sm_session']['user_rank'] == 2 ) && ( $this->_var['k'] != "col_1" && $this->_var['k'] != "col_2" && $this->_var['k'] != "col_3" && $this->_var['k'] != "col_4" && $this->_var['k'] != "col_5" && $this->_var['k'] != "col_6" && $this->_var['k'] != "col_7" && $this->_var['k'] != "col_8" && $this->_var['k'] != "col_9" )): ?>
				<?php if ($this->_var['k'] == "col_46" || $this->_var['k'] == "col_43" || $this->_var['k'] == "col_44" || $this->_var['k'] == "col_45"): ?>
					<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
						<?php if ($this->_var['k'] == "col_43" || $this->_var['k'] == "col_46"): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#fffead;" onclick="return showCalendar(this, '%Y-%m-%d', false, false, this);" />
						<?php else: ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#fffead;"/>
						<?php endif; ?>					
					<?php endif; ?>
				<?php elseif ($this->_var['k'] == "col_42"): ?>
					<select id="<?php echo $this->_var['k']; ?>" name="col[]">
					      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
					</select>
				<?php else: ?>
					<?php if ($this->_var['sm_session']['user_rank'] == 1): ?>
						<input type="text" name="col[]" id="<?php echo $this->_var['k']; ?>" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" size="45" style="background:#fffead;"/>
					<?php else: ?>
						<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
						<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
					<?php endif; ?>
				
				<?php endif; ?>
				<?php if ($this->_var['k'] == "col_28" || $this->_var['k'] == "col_29" || $this->_var['k'] == "col_19" || $this->_var['k'] == "col_20" || $this->_var['k'] == "col_42"): ?>
				<span class="f_right red_color"> * 必填 </span>
				<?php endif; ?>
			<?php else: ?>
				<?php if ($this->_var['k'] == "col_42"): ?>
					<select id="<?php echo $this->_var['k']; ?>" name="col[]">
					      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
					</select>
				<?php else: ?>
				<span><?php echo $this->_var['ad_detail'][$this->_var['k']]; ?></span>
				<?php endif; ?>
				<input type="hidden" name="col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
			<?php endif; ?>

		</div>	
		<div class="clear"></div>
	</div>
	<input type="hidden" name="old_col[]" value="<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>" />
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	
	<?php if ($this->_var['ad_detail']['can_modify'] != 0 || $this->_var['sm_session']['user_rank'] == 2): ?>
	
	<div style="width:500px;float:left;">
		<input type="hidden" name="act" value="act_update_ad_info" />
		<input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_detail']['ad_id']; ?>" />	
		<input type="hidden" name="project_id" value="<?php echo $this->_var['project_id']; ?>" />	
		<input type="submit" class="submitidea_btn" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
	</div>
	<?php endif; ?>
	
	</form>
<?php endif; ?>


<script type="text/javascript">
/**
 * 检查表单输入的数据
 */
function validate()
{
	var col_19 = document.getElementById('col_19');
	var col_20 = document.getElementById('col_20');
	var col_28 = document.getElementById('col_28');
	var col_29 = document.getElementById('col_29');
	var col_42 = document.getElementById('col_42');
	
    if (col_28.value == "" || col_29.value == "" || col_19.value == "" || col_20.value == ""  || col_42.value == "" )
    {
        alert("请确认必填数据都已经填写");
        return false;
    }

	var con = confirm("只有一次填写机会，请确认填写和修改的数据无错误");
	if(con == true){
		return true;
	}
	
    //return validator.passed();
}
</script>