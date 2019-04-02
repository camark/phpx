<define:ds type="*" />
<define:config type="*" />
<define:id type="string" required="false" default="" />
<define:pagesize type="int" required="false" default="100" />
<?php $ui_config = $param.config;if(!is_array($ui_config)) $ui_config = $this->config_helper->get_list_config($ui_config);$col_count=1;?>
<table class="sh_table"{if $param.id} id="{$param.id}"{/if} width="100%" cellspacing="0" border="0">
	<thead>
		<tr>
			<td width="20"><input type="checkbox" name="check-all" /></td>
			{loop $ui_config as $sh_setup}
			{if $sh_setup.list!='null'}
			<td>{$sh_setup.showname}</td>
			<?php $col_count++;?>
			{/if}
			{/loop}
		</tr>
	</thead>
	
	<tbody>
		<phphand:mainlist sql="{$param.ds}" handle="$sh_rs">
		<tr class="row<?php echo $mc%2;?>">
			<td><input type="checkbox" name="delete[]" value="<?php foreach($ui_config as $sh_setup){if($sh_setup.input=='check') echo $sh_rs[$sh_setup.row];}?>" /></td>
			{loop $ui_config as $sh_setup}
			{if @$sh_setup.list!='null'}
			<td>
				<?php
				switch(@$sh_setup.list){s
					case 'float':
						echo round($sh_rs[$sh_setup.row],2);
						break;
					case 'date':
						echo date('Y-m-d',$sh_rs[$sh_setup.row]);
						break;
					case 'date-time':
						echo date('Y-m-d H:i',$sh_rs[$sh_setup.row]);
						break;
					case 'img':
						if(file_exists($sh_setup.path .$sh_rs[$sh_setup.row]) && is_file($sh_setup.path .$sh_rs[$sh_setup.row])){
							echo '<img src="'. $sh_setup.path .$sh_rs[$sh_setup.row] .'" width="40" height="30" />';
						}else{
							echo '[无图片]';
						}
						break;
					case 'replace':
						echo preg_replace('/\[(.+?)\]/ise',"\$sh_rs['\\1']",$sh_setup.data_source);
						break;
					default:
						if($sh_rs[$sh_setup.row])
							echo str_replace('<','&lt;',$sh_rs[$sh_setup.row]);
						else
							echo '&nbsp;';
						break;
				}
				?>
			</td>
			{/if}
			{/loop}
		</tr>
		</phphand:mainlist>
		{if $___phphand_mainresult['n']==0}<tr><td colspan="{$col_count}"><p style="padding:15px;font-size:24px;color:#888;text-align:center;">暂无数据</p></td></tr>{/if}
	</tbody>
</table>