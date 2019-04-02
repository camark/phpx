<define:name required="false" default="" />
<define:callback type="*" required="false" default="" />
<define:ajax type="*" required="false" default="false" />
<define:id type="string" required="false" default="" />
<define:action type="*" required="false" default="" />
<?php
$__phfFormSeed=1;
$__phfFormSeed++;
$__phfFormName=md5($_SERVER['REQUEST_URI']).rand(1000,9999).$__phfFormSeed;


if(is_string($param.name)){
	$___phfFormName= $param.name;
}else{
	$___phfFormName='phpform'.$__phfFormSeed;
}
$action=$param.action;
if($param.ajax){
$ajaxpostformparam="{";
if($param.init){
	$ajaxpostformparam.="init:". $param.init ;
}
if($param.callback){
	if($ajaxpostformparam!="{") $ajaxpostformparam.=",";
	$ajaxpostformparam.="callback:". $param.callback ;
}
$ajaxpostformparam.="}";
if(!$action) $action = $_SERVER['REQUEST_URI'];
?>
<jquery:ajaxpost /><script language="javascript"><!--
$(function(){
	$('#{if $param.id!=''}{$param.id}{else}form{$__phfFormName}{/if}').ajaxpost({$ajaxpostformparam});
});//--></script>
<?php
}
?>
<form name="{$param.name}" id="{if $param.id!=''}{$param.id}{else}form{$__phfFormName}{/if}" action="{$action}"{$tag.rest}>
<?php if($param.ajax){?><input type="hidden" name="ajaxpost" value="true" /><?php }?>
__HTML__
<?php
/*
$__phfFormSecurityString  = md5(serialize($__phfSecurityArray).'___'.$this->config->get('security_code'));
$__phfFormSeedName=md5($_SERVER['REQUEST_URI']).'___'.$__phfFormSeed;
if($param.security){
	$this->share->set('phfFormSecurityString__'.$__phfFormSeedName,$__phfFormSecurityString);
	$this->share->set('phfFormSecurityArray__'.$__phfFormSeedName,$__phfSecurityArray);
	echo "<input type=\"hidden\" name=\"__phfformsecurity__\" value=\"$__phfFormSecurityString\" />\r\n";
	echo "<input type=\"hidden\" name=\"__phfformseed__\" value=\"$__phfFormSeedName\" />\r\n";
}*/
$__phfFormSecurityString='';
?>
</form>