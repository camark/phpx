<?php
$target = $param.target;
if($target=='__REQUEST__')
{
	$target=$_SERVER['REQUEST_URI'];
}
?>
<form action="{$target}" class="form-horizontal pt10" id="horizontal-search-form">
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
		<?php $n=0;?>
		{loop $search_config as $sh_field => $sh_config}
			{if $param.action=='add' && @$sh_config.add_lock || $param.action=='edit' && @$sh_config.edit_lock}<?php continue;?>{/if}
			{if !isset($sh_config.input) || !$sh_config.input || $sh_config.input=='none'}<?php continue;?>{/if}
			<div class="row">
				<label class="col-md-2 col-xs-12 col-sm-2 col-xs-2 control-label"><?php echo $this->lang->get($sh_config.showname);?></label>
				<div class="col-md-10 col-xs-12 col-sm-10 col-xs-10">
					<?php $inc = '../../../' . 'data/input/'.$this->input->build($config_file,$sh_field,$sh_config);?>
					{display $inc }
				</div>
			</div>
		{/loop}
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
	var page = __page__;
	page.find('#horizontal-search-form').submit(function()
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
	page.find('#horizontal-search-form input:text,#search-form select').focus(function()
	{
		var $this = $(this);
		if($(this).parent().hasClass('form-control')) $this=$(this).parent();
		else if($(this).parent().parent().hasClass('form-control')) $this=$(this).parent().parent();
		show_helper = $('<div style="background:#0cc;height:2px;width:0;position:absolute;"></div>');
		show_helper.css('left',$this.offset().left); 
		show_helper.css('top',$this.offset().top + parseInt($this.css('height')) - 2);
		show_helper.animate({width:parseInt($this.css('width'))},'fast');
		show_helper.appendTo($('body'));
	}).blur(function()
	{
		show_helper.remove();
	});
});
</script>
