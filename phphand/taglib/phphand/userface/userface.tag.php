<define:picture required="true" />
<define:userfacefolder required="false" default="File/Userfaces" />
{if file_exists($param.userfacefolder.'/'.$param.picture)}
<img src="{$param.userfacefolder}/{$param.picture}" />
{else}
<img src="__TAG__/notset.gif" />
{/if}