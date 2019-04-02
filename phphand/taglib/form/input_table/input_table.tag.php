<define:config type="*" />
<define:action type="string" required="false" default="add" />
<define:button_text type="string" required="false" default="提交" />
<define:data type="var" required="false" default="$input_table_unset_var" />
<base:css src="__TAG__/tag.css" />
<?php $input_config = $param.config;if(!is_array($input_config)) $input_config=$this->config_helper->get_form_config($input_config);?>
<div class="zero-input-form">
	{loop $input_config as $sh_field => $sh_config}
	{if $param.action=='add' && @$sh_config.add_lock || $param.action=='edit' && @$sh_config.edit_lock}<?php continue;?>{/if}
	<?php switch($sh_config.input){?>
	<?php case 'textarea':?>
	<div class="form-group" id="input_row_{$sh_field}">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<textarea name="{$sh_field}" cols="40" rows="7">{if isset($param.data)}{$param.data[$sh_field]}{/if}</textarea>
		</div>
	</div>
	<?php break;?>

	<?php case 'text':?>
	<div class="form-group" id="input_row_{$sh_field}">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<input type="text" name="{$sh_field}" value="{if isset($param.data)}{$param.data[$sh_field]}{/if}" class="input form-control" />
		</div>
	</div>
	<?php break;?>
	
	<?php case 'password':?>
	<div class="form-group" id="input_row_{$sh_field}">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<input type="password" name="{$sh_field}" class="input form-control" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2 col-md-3 control-label">确认密码</label>
		<div class="col-sm-10 col-md-9">
			<input type="password" name="repeat_{$sh_field}" class="input form-control" />
		</div>
	</div>
	<?php break;?>
	
	<?php case 'editor':?>
	<div class="form-group" id="input_row_{$sh_field}">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<html:ckeditor name="{$sh_field}">{if isset($param.data)}{$param.data[$sh_field]}{/if}</html:ckeditor>
		</div>
	</div>
	<?php break;?>
	
	<?php case 'file':?>
	<div class="form-group">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<input type="file" name="{$sh_field}" />
		</div>
	</div>
	<?php break;?>
	
	<?php
	case 'select':
	$sh_options=$this->hp->get_options($sh_config);
	?>
	<div class="form-group">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<select name="{$sh_field}">{loop $sh_options as $sh_value => $sh_title}<option value="{$sh_value}"{if isset($param.data) && $param.data[$sh_field]==$sh_value} selected="selected"{/if}>{$sh_title}</option>{/loop}</select>
		</div>
	</div>
	<?php break;?>
	
	<?php
	case 'ui-selector':
	preg_match("/(^|_)([^_]+?)$/",$sh_config.data_source,$match);
	$name_last=$match[2];
	$ds_id_column=$name_last.'_id';
	$ds_config=PHPHand_Action::getInstance()->ds->get_config($sh_config.data_source);
	$ds_title_column='';
	foreach($ds_config as $ds_field => $c_config){
		$ds_title_column=$ds_field;
		if(isset($c_config['as_title']) && $c_config['as_title']) break;
	}
	if(!$ds_title_column) exit('title column of data source `'.$ds_config.data_source .'` required');
	?>
	<div class="form-group">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<ui:selector name="{$sh_field}" default_value="{if isset($param.data)}{$param.data[$sh_field]}{else}{$sh_config.default}{/if}" from_table="{$sh_config.data_source}" show_column="{$ds_title_column}" value_column="{$ds_id_column}" state1="{$sh_config.data_source_state}" state2="{$sh_config.data_source_state2}" />
		</div>
	</div>
	<?php break;?>

	<?php
	case 'tree-selector':
	preg_match("/(^|_)([^_]+?)$/",$sh_config.data_source,$match);
	$name_last=$match[2];
	$ds_id_column=$name_last.'_id';
	$ds_config=PHPHand_Action::getInstance()->ds->get_config($sh_config.data_source);
	$ds_title_column='';
	foreach($ds_config as $ds_field => $c_config){
		$ds_title_column=$ds_field;
		if(isset($c_config['as_title']) && $c_config['as_title']) break;
	}
	if(!$ds_title_column) exit('title column of data source `'.$ds_config.data_source .'` required');
	?>
	<div class="form-group">
		<label class="col-sm-2 col-md-3 control-label">{if isset($sh_config.null) && $sh_config.null==false}<i>*</i>{/if}{$sh_config.showname}</label>
		<div class="col-sm-10 col-md-9">
			<form:tree_selector name="{$sh_field}" default_value="{if isset($param.data)}{$param.data[$sh_field]}{else}{$sh_config.default}{/if}" from_table="{$sh_config.data_source}" show_column="{$ds_title_column}" value_column="{$ds_id_column}" fid_column="{$sh_field}" state1="{$sh_config.data_source_state}" state2="{$sh_config.data_source_state2}" />
		</div>
	</div>
	<?php break;?>
	
	<?php case 'session':?>
	<input type="hidden" name="{$sh_field}" value="{$sh_config.data_source}" />
	<?php break;?>

	<?php case 'set':?>
	<input type="hidden" name="{$sh_field}" value="<?php if(isset($param.data[$sh_field])) echo $param.data[$sh_field];else echo $this->_var[$sh_field];?>" />
	<?php break;?>

	<?php }?>
	{/loop}
	<div class="form-group">
		<div class="col-sm-2 col-md-9"></div>
		<div class="col-sm-10 col-md-9">
			<input class="btn" type="submit" name="submit2" value="{$param.button_text}" />
		</div>
	</div>
</div>

<?php if(isset($input_table_unset_var)) unset($input_table_unset_var);?>