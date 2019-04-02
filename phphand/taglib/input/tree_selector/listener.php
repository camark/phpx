<ul<?php if(isset($level) && $level) echo ' class="level' . $level . '"';?>>
	<?php if(isset($level)) $now_level = $level;?>
	<phphand:list sql="$sql" handle="$rs">
	<?php
	if(!$multi_parent){
		$count_child = $this->{"".$from_table}->none_pre()->count("`". $fid_column . "`='" . $rs['id'] ."'");
	}else{
		$count_child = $this->{"".$from_table}->none_pre()->count("CONCAT(',',`". $fid_column . "`,',') LIKE '%," . $rs['id'] .",%'");
	}
	?>
	<li vl="{$rs[$value_column]}" data_id="{$rs.id}">{$child_count}
		{if $count_child>0}<span class="glyphicon glyphicon-triangle-right"></span>{else}<span>&nbsp;</span>{/if}
		<div>
			<input type="checkbox"{if in_array($rs[$value_column],$value_array)} checked="checked"{/if}  />
			<label>{$rs[$show_column]}</label>
		</div>
		<?php if(in_array($rs['id'],$all_chains)){
				$sql = str_replace('[fid]',$rs['id'],$sql_model);
				$this->view->sign('sql',$sql);
				$this->view->sign('level',$now_level+1);
				echo $this->view->display('listener','','return');
			}?>
	</li>
	</phphand:list>
</ul>