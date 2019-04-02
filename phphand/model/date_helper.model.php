<?php
class Date_helperModel{
	function get_time_stamp($timestr){
		date_default_timezone_set('PRC');
		$array = explode("-",$timestr);
		$year = $array[0];
		$month = $array[1];
		$array = explode(":",$array[2]);
		if(sizeof($array)==2){
			$minute = $array[1];
			$second = $array[2];
		}else{
			$minute =0;
			$second =0;
		}
		$array = explode(" ",$array[0]);
		$day = $array[0];
		if(sizeof($array)==2){
			$hour = $array[1];
		}else{
			$hour = 0;	
		}
				
		$timestamp = mktime($hour,$minute,$second,$month,$day,$year);
		
		return $timestamp;
	}
}