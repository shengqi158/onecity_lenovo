<div class="font20px"><span class="red-block f_left" style="padding:5px 20px;">新增</span>城市上传确认率实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>

	<table width="80%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="6">全部级别</td>
		</tr>
		<tr>
			<td></td>
			<td><span class="red-block f_left">新增</span>城市数量</td>
			<td>确认城市数量</td>
			<td>城市确认率</td>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_17546700_1329144124');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_17546700_1329144124']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_17546700_1329144124']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_17546700_1329144124']['lv_6']['amount']; ?></td><td><?php echo $this->_var['item_0_17546700_1329144124']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_17546700_1329144124']['lv_6']['percent']; ?>%</td>

		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>
	</table>