<?php
$args_part_index = intval($param.part_index);//获得下标
$args_part_sub_index = $param.part_sub_index;//下一级前缀
//var_dump($args_part_sub_index);
$input_config = $param.config;
if(!is_array($input_config))
{
	$config_file=$input_config;
	$input_config=$this->table_config->read( $param.config);
	$pages = $this->input->serialize($input_config);
}else{
	$config_file = md5(serialize($input_config));
	$pages=$input_config;
}

$this->setAbsoluteDir ( __ROOT__ . '/phphand/model/action_helper');
?>
<link rel="stylesheet" type="text/css" href="http://{$_SERVER.HTTP_HOST}__TAG__/tag.css" />
<div class="zero-input-form">
	{if sizeof($pages)>1}
	<input type="hidden" name="edit_page" value="0" />
	<div class="form-bootstrapWizard" style="display:none;">
		<ul class="bootstrapWizard form-wizard">
			{loop $pages as $step => $page}
			<li{if $step==0} class="active"{/if} data-target="#step{$step}">
				<a href="#tab{$step}" step="{$step}" data-toggle="tab" aria-expanded="true"> <span class="step"><?php echo $step+1;?></span> <span class="title">{$page.title}</span> </a>
			</li>
			{/loop}
		</ul>
		<div class="clearfix"></div>
	</div>
	{/if}
	<div class="tab-content">
		{loop $pages as $step => $page}
		<div class="form-page" id="tab{$step}" index="{$step}"{if $step>0} style="display:none;"{/if}>
			{loop $page.parts as $pindex => $part}
			<?php if($args_part_index>=0 && $pindex != $args_part_index){continue;} ?>
			
			<div class="form-part{if isset($part.multi) && $part.multi} form-part-multi{/if}" index="{$pindex}" multi="{$part.multi}" double="{$part.double}"{if isset($part.hide) && $part.hide} style="display:none;"{/if}>
				<!--  
				{if $part.title!='default' && strpos($part.title,'#')!==0}<header>{$part.title}</header>{/if}
				-->
				<?php
				if(isset($part.multi) && $part.multi && isset($row) && isset($row[$part.title]) && is_array($row[$part.title]))
				{//var_dump($row[$part.title]);
					$subs = array();
					//对于复杂的可重复数据的处理
					if($args_part_sub_index===false){
						$subs = array(time().rand(1000,9999) => true);
					}else{
						foreach($row[$part.title] as $sub_key => $sub_data)
						{
							if($args_part_sub_index !==false && $args_part_sub_index !== $sub_key)
							{
							   continue;	
							}
							@$subs[$sub_data['__namefix__']] = true;
						}
					}
				}else{
					//如果没有复杂可重复数据
					$subs = array('' => true);
				}
				//var_dump($subs);
				?>
				{loop $subs as $namefix => $sub_true}
				<div class="sub">
					{if isset($part.multi) && $part.multi}<input type="hidden" name="partflag_{$step}_{$pindex}[]" value="{$namefix}" />{/if}
					{if @$part.double}<div class="row">{/if}
					<?php $fn=0;?>
					{loop $part.fields as $sh_field => $sh_config}
						<?php $sh_field.=$namefix;?>
						{if $param.action=='add' && @$sh_config.add_lock || $param.action=='edit' && @$sh_config.edit_lock}<?php continue;?>{/if}
						{if !isset($sh_config.input) || !$sh_config.input || $sh_config.input=='none'}<?php continue;?>{/if}
						<?php
						$tag_dir = PHPHAND_DIR.'/taglib/input/' . $sh_config.input;
						if($tag_dir && file_exists($tag_dir.'/input_interface.block')){
							$inc = '../../' . '../data/input/'.$this->input->build($config_file,$sh_field,$sh_config);
							?>
							{display $inc}
							<?php
							continue;
						}
						?>
						<?php $fn++;?>
						{if @$part.double}<div class="col-md-6">{/if}
						<div class="form-group" field="{$sh_field}">
							<label class="col-md-<?php if(@$part.double) echo 4;else echo 2;?> col-sm-<?php if(@$part.double) echo 4;else echo 2;?> control-label">{if isset($sh_config.is_must)}<i style="color:red;font-style:normal;font-weight:bold;margin-right:3px;">*</i>{/if}<?php echo $this->lang->get($sh_config.showname);?></label>
							<div class="col-md-<?php if(@$part.double) echo 8;else echo 10;?> col-sm-<?php if(@$part.double) echo 8;else echo 10;?>">
								<?php $inc = '../../' . '../data/input/'.$this->input->build($config_file,$sh_field,$sh_config);?>
								{display $inc }
							</div>
						</div>
						{if @$part.double}</div>{/if}
						
						<?php if(@$part.double && $fn%2==0) echo '</div><div class="row mt10">';?>
					{/loop}
					
					{if @$part.double}</div>{/if}
				</div>
				{/if}
				<!--  
				{if isset($part.multi) && $part.multi}
				<footer>
					<a href="javascript:;" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-plus"></span> 添加</a>
				</footer>
				{/if}
				-->
			</div>
			{/loop}
			
			{static:form_append}
			{if $param.show_button }
			<div class="btn-line">
				<div class="row">
					<div class="col-md-12 text-right">
						<input type="<?php if($step<sizeof($pages)-1) echo 'button';else echo 'submit';?>" value="<?php if($step<sizeof($pages)-1) echo '下一步';else echo '提交';?>" page="{$step}" check="no" class="btn btn-primary" />
					</div>
				</div>
			</div>
			{/if}
		</div>
		{/loop}
	</div>
	{if isset($include_path)}<?php include $include_path;?>{/if}
</div>
<script type="text/javascript"><!--
$(function(){
	var page=__page__;
	page.find('.btn-line input[type=button]').click(function()
	{
		if($(this).attr('check')=='no')
		{
			var form = $(this);
			while(!form.is('form'))
			{
				form = form.parent();
			}
			form.submit();
		}else{
			page.find('.form-bootstrapWizard a[href=#tab'+(parseInt($(this).attr('page'))+1)+']').click();
			$(this).attr('check','no');
		}
	});
	page.find('.form-part footer a').click(function()
	{
		var config = '{$config_file}';
		var part = $(this).parent().parent();
		var footer = $(this).parent();
		var page_index = part.parent().attr('index');
		var part_index = part.attr('index');
		
		var url = '?class={form:input_interface}&config='+config+'&page_index='+page_index+'&part_index='+part_index;
		$.get(url,function(data,textStatus)
		{
			$(data).insertBefore(footer);
		});
	});
	{if sizeof($pages)>1}
	page.find('.form-bootstrapWizard a').click(function()
	{
		page.find('input[name=edit_page]').val($(this).attr('step'));
		page.find('.form-bootstrapWizard li.active').removeClass('active');
		page.find('.form-bootstrapWizard').next().find('.form-page').hide();
		
		$(this).parent().addClass('active');
		
		page.find('.form-bootstrapWizard').next().find('.form-page'+$(this).attr('href')).show();
	});
	
	if(typeof page.attr('role')!='undefine' && page.attr('role')=='sheet-box-container')
	{
		var sheet_box_head = page.parent().prev();
		var url = page.attr('url');
		var a = sheet_box_head.find("a[url='"+url+"']");
		var after = a.parent();
		page.find('.form-bootstrapWizard li').each(function(i)
		{
			var li=$("<li"+(i==0?" class='cur'":"")+"><a href='javascript:;' url='"+url+"' refer='"+i+"'>"+$(this).find('a span:last').html()+"</a></li>");
			li.find('a').sheet();
			$(this).click(function()
			{
				li.find('a').click();
			});
			li.insertAfter(after);
			after = li;
		});
		a.remove();
	}else{
		page.find('.form-bootstrapWizard').show();
	}
	{/if}
});
//--></script>