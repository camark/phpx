<?php
if(isset($row[$param.name]))
{
	$value = $this->input->get_value($row,$param.name);
	$list_config = unserialize($value);
}else{
	$list_config=array();
}
$list_selector_flag = 'list' . '_' . time() .'_' . rand(1000,9999);
?>
<select name="list" class="form-control" flag="{$list_selector_flag}">
	<option value="">不输出到列表</option>
	<option value="text"{if @$list_config.list=='text'} selected{/if}>直接输出数据</option>
	<option value="date-time"{if @$list_config.list=='date-time'} selected{/if}>日期和时间</option>
	<option value="date"{if @$list_config.list=='date'} selected{/if}>日期</option>
	<option value="time"{if @$list_config.list=='time'} selected{/if}>时间</option>
	<option value="replace"{if @$list_config.list=='replace'} selected{/if}>格式化</option>
	<option value="input"{if @$list_config.list=='input'} selected{/if}>和输入接口类型匹配</option>
</select>
<div class="form-container" style="display:none;">
	<div class="form-group">
		<label class="col-md-3">复杂列表显示</label>
		<div class="col-md-9">
			<select name="list_comp" class="form-control">
				<option value="">不输出到复杂列表</option>
				<option value="thumb"{if @$list_config.list_comp=='thumb'} selected{/if}>图片栏</option>
				<option value="title"{if @$list_config.list_comp=='title'} selected{/if}>标题栏</option>
				<option value="status"{if @$list_config.list_comp=='status'} selected{/if}>状态栏</option>
				<option value="author"{if @$list_config.list_comp=='author'} selected{/if}>作者栏</option>
				<option value="rightcenter"{if @$list_config.list_comp=='rightcenter'} selected{/if}>右侧中栏</option>
				<option value="data"{if @$list_config.list_comp=='data'} selected{/if}>数据栏</option>
				<option value="rightbottom"{if @$list_config.list_comp=='rightbottom'} selected{/if}>右下栏</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3">列表细节1</label>
		<div class="col-md-9 form-inline">
			显示 <input type="checkbox" name="list_show" value="1"{if @$list_config.list_show} checked="checked"{/if} />
			&nbsp;&nbsp;&nbsp;作为排序项 <input type="checkbox" name="list_order" value="1"{if @$list_config.list_order} checked="checked"{/if} />
			&nbsp;&nbsp;&nbsp;排列顺序 <input type="text" name="list_arrange_order" class="form-control" style="display:inline-block;width:50px;" value="{$list_config.list_arrange_order}" />
			&nbsp;&nbsp;&nbsp;宽度 <input type="text" name="list_width" class="form-control" style="display:inline-block;width:50px;" value="{$list_config.list_width}" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3">列表细节2</label>
		<div class="col-md-9 form-inline">
			前置图标 <select name="list_pre_icon" class="form-control"><option value="">无</option>
			{loop array('eur','envelope','plus','star','user','cog','time') as $icon}
			<option value="{$icon}"{if $icon==$list_config.list_pre_icon} selected="selected"{/if}><span class="glyphicon glyphicon-{$icon}"></span>{$icon}</option>
			{/loop}
			</select>
			后置图标 <select name="list_follow_icon" class="form-control"><option value="">无</option>
			{loop array('eur','envelope','plus','star','user','cog','time') as $icon}
			<option value="{$icon}"{if $icon==$list_config.list_follow_icon} selected="selected"{/if}><span class="glyphicon glyphicon-{$icon}"></span>{$icon}</option>
			{/loop}
			</select>
			背景颜色 <select name="list_background" class="form-control"><option value="">无</option>
			{loop array('#6cf','#78d18b','#FF6666','#555') as $color}
			<option value="{$color}" style="background:{$color};color:white;"{if $color==$list_config.list_background} selected="selected"{/if}>{$color}</option>
			{/loop}
			</select>
			<div class="mt10"></div>
			字体大小
			<select name="list_fontsize" class="form-control"><option value="">正常</option><option value="15px"{if '15px'==$list_config.list_fontsize} selected="selected"{/if}>大字体</option></select>
			字体颜色
			<select name="list_fontcolor" class="form-control"><option value="">默认</option><option value="#f03"{if '#f03'==$list_config.list_fontcolor} selected="selected"{/if}>红色</option></select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-md-3">替换规则</label>
		<div class="col-md-9">
			<textarea name="list_replacement" class="form-control" cols="50">{$list_config.list_replacement}</textarea>
		</div>
	</div>
</div>
<script type="text/javascript"><!--
$(function()
{
	var page=__page__;
	page.find('select[flag={$list_selector_flag}]').change(function()
	{
		if($(this).val()=='')
		{
			$(this).next().hide();
		}else{
			$(this).next().show();
		}
	}).change();
});
//--></script>