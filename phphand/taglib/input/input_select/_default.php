<ul>
	<phphand:list sql="$sql" handle="$rs">
	<li _value="{$rs.value_column}" show="{$rs.show_column}"><?php echo str_replace($key,'<font color="red">'.$key.'</font>',$rs.show_column);?></li>
	</phphand:list>
</ul>