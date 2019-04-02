<base:css src="__TAG__/table.css" />
<?php
if(!is_array($param.config)){
	$config = $this->data_helper->read(__ROOT__.'/data/output/' . $param.config  .'.php','config');
	if(!$config){
		$config = $this->table_config->read( $param.config );
	}
}else{
	$config = $param.config;
}
$order_cols = array();
foreach($config as $field => $field_config)
{
	if(isset($field_config.list_order))
	{
		$order_cols[$field]=$field_config;
	}
}
$table_sql = $param.data_source;
if(sizeof($order_cols)>0)
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
	
	$table_sql .=" ORDER BY `$order_col` $order_method";
}



if($param.flag!='')
{
	$table_flag=$param.flag;
}else{
	$all = $this->query->get();
	$table_flag='';
	foreach($all as $key => $val)
	{
		switch($key)
		{
			case 'order_col':
			case 'order_method':
			case 'page':
				break;
			default:
				if($table_flag!='') $table_flag.'$';
				$table_flag.=$key.'@'.$val;
		}
	}
}
$dir = PHPHAND_DIR.'/taglib/output/table/cache/';
$table_config = $this->data_helper->read($dir.$table_flag  . '.php','config');
if(!$table_config)
{
	$table_config=array(
		'pagesize' => 20,
		'mode' => 'table',
	);
}
if(!isset($update_table))
{
	$update_table = $param.config;
}
$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';

////////////////////////////////////////////////////////////////////////分级列表///////////////////////////////////////////////////////////////
$fid_list_column = '';
foreach($config as $field => $field_config)
{
	if(isset($field_config.as) && $field_config.as=='as_fid_column')
	{
		$fid_list_column = $field;
		break;
	}
}
if($fid_list_column)
{
	$sql_cache_name = md5($sql).time().rand(1000,9999);
	$this->cache->write($sql_cache_name,$table_sql);
	//$_SESSION['sql_cache']=$table_sql;
}else{
	$sql_cache_name='';
}
$dt_flag=time() . rand(1000,9999);
?>
<div class="edit-table form-group-sm" id="dt{$dt_flag}" onselectstart="return false;" style="-moz-user-select:none;">
	<table class="data" width="100%" cellpadding="0" cellspacing="0" config="{$param.config}" table="{$update_table}" sql_cache="{$sql_cache_name}">
		<thead>
			{loop $config as $field => $field_config}
				{if isset($field_config.list) && $field_config.list}
				<?php
				$tag_dir = PHPHAND_DIR.'/taglib/input/' . $field_config['input'];
				if($tag_dir && file_exists($tag_dir.'/input_interface.block'))
				{
					$config[$field]['block']=true;
				}else{
					$config[$field]['block']=false;
				}
				if($config[$field]['block']) continue;
				?>
				<td field="{$field}"{if !isset($field_config.list_show)} style="display:none;"{/if}>{$field_config.showname}</td>
				{/loop}
			{/loop}
			<td width="60" align="center">操作</td>
		</thead>
		
		<tbody>
			<phphand:list sql="{$table_sql}" handle="$rst">
				<?php
				if(isset($reflector)){
					$__reflector = $reflector;
					$rst = $this->action->$__reflector($rst);
				}?>
				<tr data_id="{$rst[$id_column]}" level="0">
					<input type="hidden" name="ids[]" value="{$rst[$id_column]}" />
					{loop $config as $field => $field_config}
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
							<?php $inc = '../../' . '../../../data/input/'.$this->input->build($param.config,$field,$field_config);?>
							{display $inc }
						{if !$field_config.block}</td>{/if}
						{/if}
					{/loop}
					<td align="center"><a href="javascript:;">删除</a></td>
				</tr>
			</phphand:list>
			<?php $row=isset($default_set)?$default_set:array();?>
			<textarea name="edit_table_serialized_data" style="display:none;"><?php echo serialize($row);?></textarea>
			<tr data_id="0" level="0" class="add-row">
				{loop $config as $field => $field_config}
					{if isset($field_config.list) && $field_config.list}
					{if !$field_config.block}<td field="{$field}"{if isset($field_config.is_virtual_field)} is_virtual_field="true"{/if}{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}"{if $field_config.input=='none'} edit_lock="true"{/if}>{/if}
						<?php $inc = '../../' . '../../../data/input/'.$this->input->build($param.config,$field,$field_config);?>
						{display $inc }
					{if !$field_config.block}</td>{/if}
					{/if}
				{/loop}
				<td align="center"><input type="button" value="添加" class="btn btn-warning btn-xs" /></td>
			</tr>
		</tbody>
	</table>
	<div class="submit-line text-right pt10"><input type="button" value="保存" class="btn btn-success btn-sm" /></div>
</div>
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	page.find('.edit-table tr:last input:button').click(add);
	
	function add()
	{
		var button = $(this);
		$('<span class="glyphicon glyphicon-plus fa-spin" style="color:#f60;"></span>').insertBefore(button);
		button.hide();
		var url = '?class={output:edit_table}&method=add&config={$param.config}&update_table={$update_table}';
		$.post(url,page.find('.edit-table tr:last *').serialize()+'&serialized_data='+page.find('.edit-table textarea[name=edit_table_serialized_data]').val(),function(data)
		{
			if(data.match(/<tr[\s\S]+?<\/tr>/ig))
			{
				/*button.prev().remove();
				var a = $('<a href="javascript:;">删除</a>');
				a.insertBefore(button);
				a.click(remove);
				button.remove();
				*/
				button.parent().parent().remove();
				var tbody = $(data);
				
				
				var tr = tbody.find('tr:eq(0)');
				tr.find('a').click(remove);
				tr.appendTo(page.find('.edit-table table tbody'));
				
				tr = tbody.find('tr:eq(0)');
				tr.find('input:button').click(add);
				tr.appendTo(page.find('.edit-table table tbody'));
				
				page.trigger('post_success');
			}else{
				alert(data);
				button.prev().remove();
				button.show();
			}
		});
	}
	
	page.find('.edit-table a').click(remove);
	
	function remove()
	{
		if(!confirm('您确定要删除该数据吗')) return false;
		var a = $(this);
		var tr = $(this).parent().parent();
		
		a.hide();
		$('<span class="glyphicon glyphicon-plus fa-spin" style="color:#f60;"></span>').insertBefore(a);
		
		var url = '?class={output:edit_table}&method=remove&update_table={$update_table}&data_id='+tr.attr('data_id');
		$.get(url,function(data)
		{
			tr.remove();
		});
	}
	
	page.find(".edit-table input[value=保存]").click(save);
	function save()
	{
		var url = '?class={output:edit_table}&method=save&config={$param.config}&update_table={$update_table}';
		$.post(url,page.find('.edit-table table *').serialize(),function(data)
		{
			alert(data);
		});
	}
});
//--></script>
