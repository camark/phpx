<tbody>
	<tr data_id="{$rst[$id_column]}" level="0">
		<input type="hidden" name="ids[]" value="{$rst[$id_column]}" />
		{loop $config as $field => $field_config}
			<?php
			$tag_dir = PHPHAND_DIR.'/taglib/input/' . $field_config['input'];
			if($tag_dir && file_exists($tag_dir.'/input_interface.block'))
			{
				$config[$field]['block']=true;
			}else{
				$config[$field]['block']=false;
			}
			$field_config['block']=$config[$field]['block'];
			?>
			<?php
			$field .= '__'.$rst[$id_column];
			$row = array();
			foreach($rst as $rk => $rv)
			{
				$row[$rk . '__' . $rst[$id_column]] = $rv;
			}
			?>
			{if isset($field_config.list) && $field_config.list}
			{if !$field_config.block}<td field="{$field}"{if isset($field_config.is_virtual_field)} is_virtual_field="true"{/if}{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}"{if $field_config.input=='none'} edit_lock="true"{/if}>{/if}
				<?php $inc = '../../' . '../../data/input/'.$this->input->build($config_file,$field,$field_config);?>
				{display $inc }
			{if !$field_config.block}</td>{/if}
			{/if}
		{/loop}
		<td align="center"><a href="javascript:;">删除</a></td>
	</tr>
	
	<?php $row=isset($default_set)?$default_set:array();?>
	<textarea name="edit_table_serialized_data" style="display:none;"><?php echo serialize($row);?></textarea>
	<tr data_id="{$id}" level="0" class="add-row">
		{loop $config as $field => $field_config}
			{if isset($field_config.list) && $field_config.list}
			{if !$field_config.block}<td field="{$field}"{if isset($field_config.is_virtual_field)} is_virtual_field="true"{/if}{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}"{if $field_config.input=='none'} edit_lock="true"{/if}>{/if}
				<?php $inc = '../../' . '../../data/input/'.$this->input->build($config_file,$field,$field_config);?>
				{display $inc }
			{if !$field_config.block}</td>{/if}
			{/if}
		{/loop}
		<td align="center"><input type="button" value="添加" class="btn btn-warning btn-xs" /></td>
	</tr>
</tbody>
