<div class="sub">
	<input type="hidden" name="partflag_{$page_index}_{$part_index}[]" value="{$namefix}" />
	{if @$part.double}<div class="row">{/if}
	<?php $fn=0;?>
	{loop $part.fields as $sh_field => $sh_config}
		{if !isset($sh_config.input) || !$sh_config.input || $sh_config.input=='none'}<?php continue;?>{/if}
		<?php $tag_dir = __ROOT__.'/taglib/input/' . $sh_config.input;if($tag_dir && file_exists($tag_dir.'/input_interface.block')) continue;?>
		<?php $fn++;?>
		{if @$part.double}<div class="col-md-6">{/if}
		<div class="form-group" field="{$sh_field}">
			<label class="col-md-2 control-label">{if isset($sh_config.is_must)}<i style="color:red;font-style:normal;font-weight:bold;margin-right:3px;">*</i>{/if}<?php echo $this->lang->get($sh_config.showname);?></label>
			<div class="col-md-<?php if(@$part.double) echo 10;else echo 5;?>">
				<?php $inc = '../../../' . '../data/input/'.$this->input->build($config_file,$sh_field,$sh_config);?>
				{display $inc }
			</div>
		</div>
		{if @$part.double}</div>{/if}
		
		<?php if(@$part.double && $fn%2==0) echo '</div><div class="row">';?>
	{/loop}
	
	{if @$part.double}</div>{/if}
</div>