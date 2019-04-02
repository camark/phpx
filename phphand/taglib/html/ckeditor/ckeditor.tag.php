<define:allowuploadfile default="false" />
<define:allowuploadimage default="true" />
<define:allowuploadflash default="false" />
<define:allowbrowerflash default="false" />
<define:allowbrowerimage default="false" />
<define:width default="100%" />
<define:toolbar default="full" />
<define:skin default="kama" />
<define:color default="#ccc" />
<define:resize type="bool" default="false" />
<?php
$__phfHtml="<textarea style=\"display:none;\"";
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
if($__phfName!=''){
	$__phfHtml.=" name=\"$__phfName\"";
	@$__phfFormSecurityString.=$__phfName;
}
$__phfHtml .=">";
echo $__phfHtml;
?>
__HTML__
</textarea>
{if !defined('FCK_SCRIPT_FILE_IMPORTED')}
<? define('FCK_SCRIPT_FILE_IMPORTED',true);?>
<script language="javascript" src="__TAG__/ckeditor/ckeditor.js"></script>
{/if}
<script language="javascript">
CKEDITOR.replace( '{$__phfName}',
{
{if $param.allowbrowerimage } filebrowserImageBrowseUrl : '__TAG__/ckfinder/ckfinder.html?Type=Images',{/if}
{if $param.allowbrowerflash } filebrowserFlashBrowseUrl : '__TAG__/ckfinder/ckfinder.html?Type=Flash',{/if}
{if $param.allowuploadfile } filebrowserUploadUrl : '__TAG__/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',{/if}
{if $param.allowuploadimage } filebrowserImageUploadUrl : '__TAG__/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',{/if}
{if $param.allowuploadflash } filebrowserFlashUploadUrl : '__TAG__/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',{/if}
{if $param.toolbar=='basic'} toolbar : [ ['Bold', 'Italic', 'Underline', 'Strike','-','Link','-','Smiley','Image']],{/if}
{if $param.width} width: '{$param.width}',{/if}
{if $param.resize} resize_enabled : true,{else}resize_enabled :false,{/if}
uiColor: '{$param.color}',
skin : '{$param.skin}'
});
</script>