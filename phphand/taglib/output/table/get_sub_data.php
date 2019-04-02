<?php
$config = $this->data_helper->read(__ROOT__.'/data/output/' . $config_file  .'.php','config');
if(!$config){
	$config = $this->table_config->read( $config_file );
}
$order_cols = array();
foreach($config as $field => $field_config)
{
	if(isset($field_config.list_order))
	{
		$order_cols[$field]=$field_config;
	}
}
$output_table_data_source = $sql;
if(!isset($output_table_data_source_type)){
	if(is_string($output_table_data_source))
	{
		$output_table_data_source_type='sql';
	}else if(is_array($output_table_data_source)){
		$output_table_data_source_type='solr';
	}
}

if(sizeof($order_cols)>0 && (!isset($is_virtual_table) || !$is_virtual_table))
{
	$order_col=$this->query->get('order_col');
	if(!$order_col || !isset($order_cols[$order_col]))
	{
		foreach($order_cols as $field => $field_config)
		{
			$order_col = $field;
			break;
		}
	}
	$order_method = $this->query->get('order_method');
	if(!$order_method || !in_array($order_method,array('desc','asc')))
	{
		$order_method='desc';
	}
	
	$output_table_data_source .=" ORDER BY `$order_col` $order_method";
}
if(isset($reflector) && $reflector){
	if(preg_match('/^(\w+?)\.(\w+?)$/is',$reflector,$match))
	{
		$__reflector = explode('.',$reflector);
	}else{
		exit('Reflector定义错误');
	}
}

?>
<tbody>
	<phphand:list sql="{$output_table_data_source}" handle="$rst" pagesize="{$table_config.pagesize}">
	<?php
	if(isset($__reflector)){
		$rst = $this->{ $__reflector [0] }->{ $__reflector [1] }($rst);
	}?>
	<tr data_id="{$rst[$id_column]}" fid="{$fid}" level="{$level}" class="level{$level}">
	<td class="ftd" edit_lock="true"><input type="checkbox" name="ids" value="{$rst[$id_column]}" /></td>
	{if $fid_list_column}
		<td edit_lock="true" class="brantch brantch{$level}" width="120">
		<?php if($this->{'output.table.helper'}->read($update_table,$fid_list_column,$rst[$id_column])){?>
		<?php for($step=0;$step<$level;$step++){ echo '&nbsp;&nbsp;&nbsp;&nbsp; ';}?><span class="tree_btn glyphicon glyphicon-chevron-right"></span>
		<?php }else{?>
		&nbsp;
		<?php }?>
		</td>
	{/if}
	{loop $config as $field => $field_config}
		{if isset($field_config.list) && $field_config.list}
		<td field="{$field}"{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}">
			<?php for($step=0;$step<$level;$step++){ echo '&nbsp;&nbsp;&nbsp;&nbsp; ';}?>
			<?php switch($field_config.list){
			case 'text':
				echo @$rst[$field];
				break;
			case 'date-time':
				echo @date('Y-m-d H:i',$rst[$field]);
				break;
			case 'date':
				echo @date('Y-m-d',$rst[$field]);
				break;
			case 'time':
				echo @date('H:i',$rst[$field]);
				break;
			case 'replace':
				echo @preg_replace('/\[(.+?)\]/ise',"\$rst['\\1']",$field_config.list_replacement);
				break;
			case 'input':
				if(isset($field_config.input) && $field_config.input && $field_config.input!='none')
				{
					if(!isset($output_class)) $output_class=array();
					if(strpos($field_config['input'],'example.')===0)
					{
						$field_config = $this->input->get_example_detail($field_config);
					}
					$class_name = strtoupper($field_config.input[0]) . substr($field_config.input,1) . 'OutputModel';
					if(!isset($output_class[$field]))
					{
						$dir = $this->routine->get_tag_dir('input',$field_config.input);
						include_once ($dir['path'] . '/input/' . $field_config.input . '/__output.php');
						$output_class[$field] = new $class_name($this);
					}
					$obj = $output_class[$field];
					@$obj->output($rst[$field],$field_config);
					//$$function_name($rst[$field],$field_config);
				}
				break;
			}?>
		</td>
		{/if}
	{/loop}
	</tr>
	</phphand:list>
</tbody>

