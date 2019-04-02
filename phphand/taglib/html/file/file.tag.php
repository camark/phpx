<?php
if(!$__phfSecurityArray || !is_array(!$_phfSecurityArray)) trigger_error('<phf:hidden> should written inner <phf:form>');
$__phfHtml="<input type=\"file\"";
if(is_string($param.name)){
	$__phfName=$param.name;
}else{
	$__phfName='';
	if(is_string($param.column)) $__phfName.="column{". $param.column ."}";
	if(is_string($param.type))   $__phfName.="type{". $param.type ."}";
	if(is_string($null))    $__phfName.="null{". $param.null ."}";
	if(is_string($path))    $__phfName.="path{". $param.path ."}";
}
if($__phfName!=''){
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
if(is_string($param.class))     $__phfHtml .=" class=\"{$param.class}\"";
if(is_string($param.title))     $__phfHtml .=" title=\"{$param.title}\"";
if(is_string($param.alt))       $__phfHtml .=" alt=\"{$param.alt}\"";
if(is_string($param.border))    $__phfHtml .=" border=\"{$param.border}\"";
if(is_string($param.checked))   $__phfHtml .=" checked=\"{$param.checked}\"";
if(is_string($param.disabled))  $__phfHtml .=" disabled=\"{$param.disabled}\"";
if(is_string($param.onfocus))   $__phfHtml .=" value=\"{$param.onfocus}\"";
if(is_string($param.onkeydown))    $__phfHtml .=" onkeydown=\"{$param.onkeydown}\"";
if(is_string($param.onkeyup))    $__phfHtml .=" onkeyup=\"{$param.onkeyup}\"";
if(is_string($param.onchange))     $__phfHtml .=" onchange=\"{$param.onchange}\"";
if(is_string($param.onclick))     $__phfHtml .=" onclick=\"{$param.onclick}\"";
if(is_string($param.ondbclick))     $__phfHtml .=" ondbclick=\"{$param.ondbclick}\"";
if(is_string($param.onmousedown))     $__phfHtml .=" onmousedown=\"{$param.onmousedown}\"";
if(is_string($param.onmouseup))     $__phfHtml .=" onmouseup=\"{$param.onmouseup}\"";
if(is_string($param.onmouseover))     $__phfHtml .=" onmouseover=\"{$param.onmouseover}\"";
if(is_string($param.onmouseout))     $__phfHtml .=" onmouseout=\"{$param.onmouseout}\"";
$__phfHtml .=" />";
echo $__phfHtml;
?>