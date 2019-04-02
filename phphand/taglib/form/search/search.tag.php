<?php
$target = $param.target;
if($target=='__REQUEST__')
{
	$target=$_SERVER['REQUEST_URI'];
}
define('FORM_SEARCH',1);
?>
<link rel="stylesheet" type="text/css" href="__TAG__/easing.css?3" />
<form action="{$target}" class="form-horizontal pt10" id="search-form">
	<?php $querys = $this->query->get();
	foreach($querys as $q_key => $q_val){
		echo '<input type="hidden" name="' . $q_key .'" value="' . $q_val .'" />';
	}
	?>
	<?php
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
	<div>
		<div class="container-fluid">
			<div class="row">
				<?php $n=0;?>
				{loop $search_config as $sh_field => $sh_config}
					{if $param.action=='add' && @$sh_config.add_lock || $param.action=='edit' && @$sh_config.edit_lock}<?php continue;?>{/if}
					{if !isset($sh_config.input) || !$sh_config.input || $sh_config.input=='none'}<?php continue;?>{/if}
					<div class="col-lg-4 col-md-<?php if($param.cols==3) echo 4;else echo 6;?> col-sm-6 col-xs-6 form-group-sm mb10">
						<label class="col-md-2 col-xs-12 col-sm-2 col-xs-2 control-label"><?php echo $this->lang->get($sh_config.showname);?></label>
						<div class="col-md-10 col-xs-12 col-sm-10 col-xs-10">
							<?php $inc = '../../../' . 'data/input/'.$this->input->build($config_file,$sh_field,$sh_config);?>
							{display $inc }
						</div>
					</div>
				{/loop}
			</div>
		</div>
	</div>
	<div class="form-actions">
		<div class="container-fluid">
			<div class="row">
				<input type="hidden" name="__search__" value="1" />
				<div class="col-md-12 text-right form-group-sm">
					<button type="submit" name="search" class="btn btn-danger btn-xs"><span class="glyphicon glyphicon-search"></span> 搜索</button>
				</div>
			</div>
		</div>
	</div>
</form>
<script>
$(function(){
	if('{$param.post_method}'=='ajax'){
		var page = __page__;
		page.find('#search-form').submit(function()
		{
			var container = page.find('.search_result_container');
			container.html('<div class="text-center pt50"><span class="fa-spin glyphicon glyphicon-asterisk" style="font-size:40px;"></span></div>');
			$.get($(this).attr('action'),$(this).serialize(),function(data)
			{
				container.html(data);
			});
			return false;
		});
		
		var show_helper;
		function show_show_helperfunction()
		{
			if(show_helper) show_helper.remove();
			var $this = $(this);
			if($(this).parent().hasClass('form-control')) $this=$(this).parent();
			else if($(this).parent().parent().hasClass('form-control')) $this=$(this).parent().parent();
			var theFlag = (new Date().getTime()+'_'+Math.random()).replace('.','_');
			$(this).attr('helper_flag',theFlag);
			show_helper = $('<div style="background:#2b98f0;height:2px;width:0;position:absolute;z-index:101;" flag="'+theFlag+'"></div>');
			show_helper.css('left',$this . offset().left- $('#content').offset().left-10); 
			show_helper.css('top',$this . offset().top + parseInt($this.css('height')) - 2 - $('#content').offset().top-10);
			show_helper.animate({width:parseInt($this.css('width'))},'fast');
			show_helper.appendTo(page);
		}
		page.find('#search-form input:text,#search-form textarea,#search-form select').focus(show_show_helperfunction).blur(function()
		{
			if(show_helper) show_helper.remove();
		});
	}
});
</script>
