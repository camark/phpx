<ul>
	{loop $keys as $rs}
	<li _value="{$rs.key}" show="{$rs.key}">
		<div class="left">
			<?php echo str_replace($key,'<font color="red">'.$key.'</font>',$rs.key);?>
		</div>
		<div class="right">
			{$rs.data_num}
			<span class="glyphicon glyphicon-plus"></span>
		</div>
	</li>
	{/loop}
</ul>