<?php
class Text_doubleOutputModel extends PHPHand_Model
{
	function output($data,$config,$rst)
	{
		echo str_replace(',',' - ',$data).$config['unit'];
		/*()
		if($config['name_position']==2){
			echo $rst[$config['name'] . $config['name1']].'-'.$rst[$config['name'] . $config['name2']];
		}else{
			echo $rst[$config['name1'] . $config['name']].'-'.$rst[$config['name2'] . $config['name']];

		}*/
	}
}