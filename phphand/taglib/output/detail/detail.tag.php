<?php
$input_config = $param.config;
if(!is_array($input_config))
{

	if(is_numeric($param.config))
	{
		$config_key = '$template_' . $param.config;
	}else{
		$config_key = $param.config;
	}


	$config_file=$config_key;
	$input_config=$this->table_config->read( $config_key);
	$pages = $this->input->serialize($input_config);
}else{
	if(!isset($input_config[0]))
	{
		$input_config = $this->input->serialize($input_config);
	}
	$config_file = md5(serialize($input_config));
	$pages=$input_config;
}
$client_is_mobile = $this->client->is_mobile();
if($param.edit_support){
	if(preg_match("/(^|_)([a-z0-9]+?)$/i",$param.edit_support,$matches)){
		$id_column=$matches[2].'_id';
	}else{
		exit('edit_support参数必须是一个数据表的名称');
	}
}
?>
<base:css src="__TAG__/detail.css" />
<div class="max-detail">
	<div class="max-detail-content">
		{loop $pages as $step => $page}
		<div class="detail-page" id="tab{$step}" index="{$step}">
			{loop $page.parts as $pindex => $part}
			<div class="detail-part{if isset($part.multi) && $part.multi} form-part-multi{/if}" index="{$pindex}" multi="{$part.multi}" double="{$part.double}"{if isset($part.hide) && $part.hide} style="display:none;"{/if}>
				{if $part.title!='default' && strpos($part.title,'#')!==0}
					<div class="detail-header">
						{if isset($part.multi) && $part.multi && $param.edit_support }
						<a href="javascript:;" class="btn btn-warning btn-xs right add-sub">添加</a>
						{/if}
						{$part.title}
					</div>
				{/if}
				<?php
				if(isset($part.multi) && $part.multi && isset($row) && isset($row[$part.title]) && is_array($row[$part.title]))
				{
					$subs = array();
					//对于复杂的可重复数据的处理
					foreach($row[$part.title] as $sub_key => $sub_data)
					{
						@$subs[$sub_data['__namefix__']] = $sub_data;
					}
				}else{
					//如果没有复杂可重复数据
					$subs = array('' => isset($row[$part.title])?$row[$part.title]:array());
				}
				?>
				{loop $subs as $namefix => $sub_data}
				<div class="detail-sub pr15 pl15" part_index="{$pindex}" part_sub_index="{$namefix}">
					{if isset($part.multi) && $part.multi}<input type="hidden" name="partflag_{$step}_{$pindex}[]" value="{$namefix}" />{/if}
					{if @$part.double}<div class="row">{/if}
					<?php $fn=0;?>
					{loop $part.fields as $sh_field => $sh_config}
						{if $param.action=='add' && @$sh_config.add_lock || $param.action=='edit' && @$sh_config.edit_lock}<?php continue;?>{/if}
						{if !isset($sh_config.input) || !$sh_config.input || $sh_config.input=='none'}<?php continue;?>{/if}
						{if $param.view_power<$sh_config.detail_output}<?php continue;?>{/if}
						{if $sh_config.client_type==1 && $client_is_mobile}<?php continue;?>{/if}
						<?php
						$tag_dir = PHPHAND_DIR.'/taglib/input/' . $sh_config.input;
						/*if($tag_dir && file_exists($tag_dir.'/input_interface.block')){
							$inc = '../../' . '../data/input/'.$this->input->build($config_file,$sh_field,$sh_config);
							?>
							{display $inc}
							<?php
							continue;
						}*/
						?>
						<?php $fn++;?>
						{if @$part.double}<div class="col-md-6">{/if}
						<div class="row" field="{$sh_field}">
							<label class="col-md-<?php if(@$part.double) echo 4;else echo 2;?> col-sm-<?php if(@$part.double) echo 4;else echo 2;?> col-xs-4 control-label">
								<?php echo $this->lang->get($sh_config.showname);?>
							</label>
							<div class="col-md-<?php if(@$part.double) echo 8;else echo 10;?> col-sm-<?php if(@$part.double) echo 8;else echo 10;?> col-xs-8">
								<?php 
								if(!isset($sh_config['list']) || !$sh_config['list'] || $sh_config['list']=='none')
								{
									$sh_config['list'] = 'input';
								}
								$this->table_config->output($sh_field.$namefix,$sh_config,$sub_data);?>
							</div>
						</div>
						{if @$part.double}</div>{/if}
						
						<?php if(@$part.double && $fn%2==0) echo '</div><div class="row mt10">';?>
					{/loop}
					
					{if @$part.double}</div>{/if}
					{if $param.edit_support }
					<div  class="edit-bar text-right">
						<a href="javascript:;" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-edit"></span> 编辑</a>
					</div>
					{/if}
				</div><!--SUB.END.FLAG-->
				{/loop}

			</div>
			{/loop}
		</div>
		{/loop}
	</div>
</div>
<script>
$(function(){
	var post_data = <?php echo json_encode($_POST);?>;
	var uri = '{$_SERVER.REQUEST_URI}';

	if(uri.indexOf('get_ajax_page')<0)
	{
		uri+='&get_ajax_page=1';
	}

	function init_sub(sub)
	{
		sub.find('a:last').click(function(){
			var sub=$(this).parent().parent();
			$.get('__PHP__?class={form:input_interface}&method=part_edit&config={$config_key}&model={$param.edit_support}&{$id_column}={$param.data_id}&part_index='+sub.attr('part_index')+'&part_sub_index='+sub.attr('part_sub_index'),function(data){
				var sub_edit_box = $('<div class="part-edit-container">'+data+'</div>');
				sub_edit_box.insertAfter(sub);
				var part_index = sub.attr('part_index');
				var part_sub_index = sub.attr('part_sub_index');
				sub_edit_box.find('form').setCallback(function(msg,data_id){
					$.post(uri+'&part_index='+part_index+'&part_sub_index='+part_sub_index,post_data,function(data){
						var sub = $(data);
						sub.insertBefore(sub_edit_box);
						sub_edit_box.remove();
						init_sub(sub);
					});
				});
				sub.remove();
			});
		});
	}
	init_sub($('div.detail-sub'));

	$('.detail-header a.btn').click(function(){
		var part = $(this).parent().parent();
		var part_index = part.attr('index');
		$.get('__PHP__?class={form:input_interface}&method=part_edit&config={$config_key}&model={$param.edit_support}&{$id_column}={$param.data_id}&part_index='+part_index,function(data){
			var sub_edit_box = $('<div class="part-edit-container">'+data+'</div>');
			sub_edit_box.appendTo(part);
			sub_edit_box.find('form').setCallback(function(msg,data_id){
				$.post(uri+'&part_index='+part_index,post_data,function(data){
					var sub = $(data);
					sub.insertBefore(sub_edit_box);
					sub_edit_box.remove();
					init_sub(sub);
				});
			});
		});
	});
});
</script>
