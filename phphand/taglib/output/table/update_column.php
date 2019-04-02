<?php switch($field_config.list){
	case 'text':
		echo $rst[$field];
		break;
	case 'date-time':
		echo date('Y-m-d H:i',$rst[$field]);
		break;
	case 'date':
		echo date('Y-m-d',$rst[$field]);
		break;
	case 'time':
		echo date('H:i',$rst[$field]);
		break;
	case 'replace':
		echo preg_replace('/\[(.+?)\]/ise',"\$rst['\\1']",$field_config.list_replacement);
		break;
	case 'input':
		if(isset($field_config.input) && $field_config.input && $field_config.input!='none')
		{
			if(!isset($output_classes)) $output_class=array();
			$class_name = strtoupper($field_config.input[0]) . substr($field_config.input,1) . 'OutputModel';
			if(!isset($output_class[$field_config.input]))
			{
				$dir = $this->routine->get_tag_dir('input',$field_config.input);
				include_once ($dir['path'] . '/input/' . $field_config.input . '/__output.php');
				$output_class[$field_config.input] = new $class_name($this);
			}
			$obj = $output_class[$field_config.input];
			$obj->output($rst[$field],$field_config);
			//$$function_name($rst[$field],$field_config);
		}
		break;
	}?>