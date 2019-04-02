<?php
class Request_helperModel{
	function parse($string){
		$array1=explode('&',$string);
		$array=array();
		foreach($array1 as $item){
			$array2=explode('=',$item);
			switch(sizeof($array2)){
				case 1:
					$array[$array2[0]]='';
					break;
				case 2:
					$array[$array2[0]]=$array2[1];
					break;
			}
		}
		return $array;
	}
}