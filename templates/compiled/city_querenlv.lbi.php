<div class="font20px">城市上传确认率实时统计表 当前时间:<?php echo $this->_var['date']; ?></div>

	<table width="90%" style="margin:10px auto;background:url(themes/default/images/container_bg.png) #ffffff;" class="table_standard table_border_dark" border="1">
		<tr>
			<td rowspan="1">分区</td>
			<td colspan="3">4级</td>
			<td colspan="3">5级</td>
			<td colspan="3">6级(不含百强镇)</td>
			<td colspan="3">百强镇</td>
		</tr>
		<tr>
			<td></td>
			<td>城市数量</td>
			<td>确认城市数量</td>
			<td>城市确认率</td>
			<td>城市数量</td>
			<td>确认城市数量</td>
			<td>城市确认率</td>
			<td>城市数量</td>
			<td>确认城市数量</td>
			<td>城市确认率</td>
			<?php if ($this->_var['sm_session']['user_id'] == 59): ?>
			<td>城市数量</td>
			<td>确认城市数量</td>
			<td>城市确认率</td>
			<?php endif; ?>
		</tr>
		<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'item_0_99971000_1329130930');if (count($_from)):
    foreach ($_from AS $this->_var['k'] => $this->_var['item_0_99971000_1329130930']):
?>
		<tr>
			<td><?php echo $this->_var['item_0_99971000_1329130930']['col_1']; ?></td>
			<td><?php echo $this->_var['item_0_99971000_1329130930']['lv_4']['amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_4']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_4']['percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_99971000_1329130930']['lv_5']['amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_5']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_5']['percent']; ?>%</td>
			<td><?php echo $this->_var['item_0_99971000_1329130930']['lv_6']['amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_6']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_6']['percent']; ?>%</td>
			<?php if ($this->_var['sm_session']['user_id'] == 59): ?>
			<td><?php echo $this->_var['item_0_99971000_1329130930']['lv_7']['amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_7']['confirm_amount']; ?></td><td><?php echo $this->_var['item_0_99971000_1329130930']['lv_7']['percent']; ?>%</td>
			<?php endif; ?>
			
		</tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			
	</table>