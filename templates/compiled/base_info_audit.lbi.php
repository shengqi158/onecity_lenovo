<div class="f_left" style="width:100%;">
	<a class="back_url" href="city_base_info.php?act=ad_list&project_id=<?php echo $this->_var['project_id']; ?>&region_name=<?php echo $this->_var['city_name']; ?>&audit_status=<?php echo $this->_var['audit_status']; ?>&has_new=<?php echo $this->_var['ad_info']['is_new']; ?>"></a>
	
</div>

<div class="radius_5px city_info" style="width:95%;height:200px;padding:0px 10px;">
	<span class="green-color font14px">未换画之前</span><br>
<?php $_from = $this->_var['old_photo_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_49020500_1328441698');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_49020500_1328441698']):
?>
	<div style="width:160px;height:160px;text-align:center;float:left;margin:10px 20px;">
	<a href="<?php echo $this->_var['item_0_49020500_1328441698']['img_url']; ?>" target="_blank" class="city_photo"><img src="<?php echo $this->_var['item_0_49020500_1328441698']['thumb_url']; ?>"></a>
	<?php echo $this->_var['lang']['city_photo'][$this->_var['item_0_49020500_1328441698']['img_sort']]; ?>
	</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>

<?php echo $this->fetch('library/audit_path.htm'); ?>						

<?php if ($this->_var['sm_session']['user_rank'] == 2): ?>
<div class="clear"></div>
<form method="post" action="city_base_info.php" name="theForm" id="theForm" enctype="multipart/form-data" onsubmit="return validate()">
  <table width="100%" border="0" cellspacing="0" class="table_standard">
	<tr>
		<td width="130">如果审核不通过,<br>请写下明确的原因:</td>
		<td><br>
			<textarea name="audit_note" id="audit_note" cols="40" rows="4"></textarea>
			<br><input name="recycle" id="recycle" type="checkbox" />&nbsp;需要重新邮寄报销材料<br><br>
			</td>
	</tr>
	
    <tr>
      <td>&nbsp;</td>
      <td>
        <input type="hidden" name="ad_id" value="<?php echo $this->_var['ad_id']; ?>" />
        <input type="hidden" name="confirm" id="confirm" value="0" />
        <input type="hidden" name="project_id" value="<?php echo $this->_var['project_id']; ?>" />
      	<input type="hidden" name="act" value="update_base_info_audit" />
		<input type="submit" class="input_s3 f_left" value="意见反馈" />
		<a onclick="update_audit()" class="cancel_lite_btn f_left" style="margin-left:20px;">通过审核</a> 
      </td>
    </tr>
  </table>
</form>

<script type="text/javascript">
function update_audit(){
	document.getElementById('confirm').value = 1;
	
	if(<?php echo $this->_var['sm_session']['user_rank']; ?> == 2){
			document.getElementById("theForm").submit();
	}else{
		alert("您无权通过改画面");
	}
}
/**
 * 检查表单输入的数据
 */
function validate()
{
	
	var obj = document.getElementById('audit_note');

    if (obj.value == "")
    {
        alert("请填写不通过原因");
        return false;
    }
	return true;
    //return validator.passed();
}
</script>
<?php endif; ?>


<?php $_from = $this->_var['city_title']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_49069600_1328441698');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_49069600_1328441698']):
?>
<div class="city_info radius_5px">
	<div class="f_left left_title left_radius_5px"><?php echo $this->_var['item_0_49069600_1328441698']; ?></div>
	<div class="f_left right_content" 
	<?php if ($this->_var['k'] == "col_28" || $this->_var['k'] == "col_29" || $this->_var['k'] == "col_42" || $this->_var['k'] == "col_43" || $this->_var['k'] == "col_44"): ?>
	style="background:#fffead;"
	<?php endif; ?>>
		<?php if ($this->_var['k'] == "col_42"): ?>
			<select id="<?php echo $this->_var['k']; ?>" name="col[]">
			      <?php echo $this->html_options(array('options'=>$this->_var['lang']['pic_type_select_lite'],'selected'=>$this->_var['ad_detail'][$this->_var['k']])); ?>
			</select>
		<?php else: ?>
		<?php echo $this->_var['ad_detail'][$this->_var['k']]; ?>
		<?php endif; ?>
		</div>	
	<div class="clear"></div>
</div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>