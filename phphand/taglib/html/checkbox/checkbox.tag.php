<?php
//if(!$__phfSecurityArray || !is_array(!$_phfSecurityArray)) trigger_error('<face:checkbox> should written inner <phf:form>');
$__phfHtml="<input type=\"checkbox\"";
if(is_string($param.name)){
	$__phfName=$param.name;
}else{
	$__phfName='';
	if(is_string($param.column)) $__phfName.="column{". $param.column ."}";
	if(is_string($param.datatype))   $__phfName.="type{". $param.datatype ."}";
	if(is_int($param.maxlength)) $__phfName.="maxlength{". $param.maxlength ."}";
	if(is_int($param.minlength)) $__phfName.="minlength{". $param.minlength ."}";
	if(is_string($condition))    $__phfName.="condition{". $param.condition ."}";
}
if($__phfName!='' && is_array($__phfSecurityArray)){
	if(is_bool($param.keep) && $param.keep==true){
		if($param.value){
			$__phfSecurityArray[$__phfName]=$param.value;
		}else{
			$__phfSecurityArray[$__phfName]='';
		}
	}else{
		$__phfSecurityArray[$__phfName]='P_H_F_K_E_E_P_N_A_M_E_O_N_L_Y';
	}
	$__phfHtml.=" name=\"$__phfName\"";
	$__phfFormSecurityString.=$__phfName;
}
?>{$tag.rest}<?
$__phfHtml .=" />";
echo $__phfHtml;
?>