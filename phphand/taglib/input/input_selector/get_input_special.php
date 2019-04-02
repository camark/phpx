<style type="text/css">
	.form-container{padding-top:15px;}
</style>
<div class="form-group">
	<label class="col-md-3">必填项</label>
	<div class="col-md-9"><input type="checkbox" name="is_must" value="1"{if isset($field_config.is_must)} checked="checked"{/if} /></div>
</div>
{loop $input_config as $name => $properties}
	{if isset($properties.input)}
	<div class="form-group">
		<label class="col-md-3">{$properties.showname}</label>
		<div class="col-md-9">
			{if $properties.input=='text'}
			<input type="text" name="{$name}" value="<?php if(isset($field_config[$name])) echo @( $field_config[$name]);else echo @$properties.default;?>" class="form-control" />
			{elseif $properties.input=='select'}
			<select class="form-control" name="{$name}"><?php $options = $this->hp->get_options($properties);?>
			{loop $options as $value => $title}
			<option value="{$value}"{if isset($field_config[$name]) && $value==$field_config[$name]} selected="selected"{/if}>{$title}</option>
			{/loop}
			</select>
			{elseif $properties.input=='textarea'}
			<textarea name="{$name}" class="form-control" rows="5"><?php if(isset($field_config[$name])) echo @( $field_config[$name]);else echo @$properties.default;?></textarea>
			{/if}
		</div>
	</div>
	{/if}
{/loop}
