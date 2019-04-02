<?php
$target = $param.target;
if($target=='__REQUEST__')
{
	$target=$_SERVER['REQUEST_URI'];
}
?>
<base:css src="__TAG__/tag.css" />
<form action="{$target}" class="form-horizontal pt10 advanced-search form-sm">
	<?php $querys = $this->query->get();
	foreach($querys as $q_key => $q_val){
		echo '<input type="hidden" name="' . $q_key .'" value="' . $q_val .'" />';
	}

	$search_config = $param.config;
	if(!is_array($search_config))
	{
		$config_file=$search_config;
		$search_config=$this->table_config->read($param.config);
	}else{
		$config_file = md5(serialize($search_config));
		$pages=$search_config;
	}

	$this->setAbsoluteDir ( __ROOT__ . '/phphand/model/action_helper');
	?>
	<div class="container-fluid search-container" name="">
		<div class="row search-item form-group-sm pb10" style="display:none;">
			<div class="col-md-3">
				<div class="">
					<div class="col-xs-2 col-sm-2 col-md-2 option-status">
						<span class="glyphicon glyphicon-ok"></span>
						<input type="hidden" name="" value="1" />
					</div>
					<div class="col-xs-9 col-sm-9 col-md-9">
						<?php $items = $this->{'form.advanced_search.helper'}->get_all_input_item($param.template_id);?>
						<select class="form-control field">
							<option value="">请选择搜索项</option>
							{loop $items as $key => $title}{if preg_match('/_(s|is|i)$/i',$key)}
							<option value="{$key}">{$title}</option>
							{/if}{/loop}
						</select>
					</div>
				</div>
			</div>
			<div class="col-md-7">
				
			</div>
			<div class="col-md-2 text-right">
				<span class="glyphicon glyphicon-remove remove-line"></span>
			</div>
		</div>
	</div>
	<div class="form-actions">
		<div class=" container-fluid">
			<div class="row">
				<input type="hidden" name="__search__" value="1" />
				<div class="col-md-2">
					<a href="javascript:;" class="add-search-item"><span class="glyphicon glyphicon-plus"></span> 添加搜索项</a>
				</div>
				<div class="col-md-10 text-right form-group-sm">
					<input type="submit" class="btn btn-sm btn-success" value="搜索" />
					<a href="javascript:;" class="btn btn-sm btn-default" style="display:none;">保存搜索器</a>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
$(function(){
	var page=__page__;
	/*page.find('.advanced-search select.field').change(function()
	{
		var config='$template_{$param.template_id}';
		var field = $(this).val();
		
		var con = $(this).parent().parent().parent().next();
		
		$.get('__PHP__?class={form:advanced_search}&method=get_input&config='+config+'&field='+field,function(data)
		{
			con.html(data);
		});
	});*/

	page.find('.advanced-search').submit(function()
	{
		var container = page.find('.search_result_container');
		container.html('<div class="text-center pt50"><span class="fa-spin glyphicon glyphicon-asterisk" style="font-size:40px;"></span></div>');
		$.get($(this).attr('action'),$(this).serialize(),function(data)
		{
			container.html(data);
		});
		return false;
	});
	
	page.find('.add-search-item').click(function()
	{
		var tr = page.find('.search-item:last').clone();
		tr.insertBefore(page.find('.search-item:last')).show();
		search_item_init(tr);
	});
	
	page.find('.advanced-search .form-actions .btn-default').click(function()
	{
		var name = page.find('.advanced-search .search-container').attr('name');
		if(!name)
		{
			
		}
	});
	
	
	function search_item_init(tr)
	{
		tr.find('select.field').change(function()
		{
			var config='$template_{$param.template_id}';
			var field = $(this).val();
			
			var con = $(this).parent().parent().parent().next();
			
			if(field!=''){
				$.get('__PHP__?class={form:advanced_search}&method=get_input&config='+config+'&field='+field,function(data)
				{
					con.html(data);
				});
			}else{
				con.html('');
			}
			
			$(this).parent().prev().find('input').attr('name','prefix$'+field);
		});
		
		tr.find('.option-status span').click(function()
		{
			if($(this).hasClass('glyphicon-ok'))
			{
				$(this).removeClass('glyphicon-ok').addClass('glyphicon-minus');
				$(this).next().val('BETTER');
			}else if($(this).hasClass('glyphicon-minus'))
			{
				$(this).removeClass('glyphicon-minus').addClass('glyphicon-remove');
				$(this).next().val('NOT');
			}else if($(this).hasClass('glyphicon-remove'))
			{
				$(this).removeClass('glyphicon-remove').addClass('glyphicon-ok');
				$(this).next().val(1);
			}
		});
		
		tr.find('.remove-line').click(function()
		{
			$(this).parent().parent().remove();
		}).mouseover(function()
		{
			$(this).addClass('hover');
		}).mouseout(function()
		{
			$(this).removeClass('hover');
		});
	}
});
</script>
