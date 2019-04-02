<ul>
	<phphand:list sql="$sql" handle="$rs">
	<li vl="{$rs[$value_column]}">
		<input type="checkbox" />
		<label>{$rs[$show_column]}</label>
	</li>
	</phphand:list>
</ul>