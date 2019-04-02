{if !isset($__datetime_pick)}
<?php $__datetime_pick=1;?>
<script src="__TAG__/tag.js"></script>
<base:css src="__TAG__/tag.css"></base:css>
{/if}
<input type="text" readonly="" name="{$param.name}" class="form-control" />
<script>
$(function(){
	$('input[name={$param.name}]').mdatepick();
});
</script>