<base:css src="__TAG__/list.css" />
<?php
$output_list_id_column = $param.id_column;
if(!$output_list_id_column)
{
	$output_list_id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$param.table).'_id';
}
$output_list_show_column = $param.show_column;
$output_list_sql = "SELECT $output_list_show_column,$output_list_id_column FROM ". $param.table ." ORDER BY $output_list_id_column DESC";
?>
<a href="javascript:void(0);" id="output-list-add-item" class="btn btn-primary btn-block"> <strong>添加{$param.title}</strong> </a>

<h6> {$param.title} <a href="javascript:void(0);" rel="tooltip" title="" data-placement="right" data-original-title="Refresh" class="pull-right txt-color-darken"><i class="fa fa-refresh"></i></a></h6>
<ul class="output-list">
	<phphand:list sql="$output_list_sql" handle="$rst">
	<li data_id="{$rst[$output_list_id_column]}"><i class="glyphicon glyphicon-pencil"></i><a href="<?php echo str_replace('[id]',$rst[$output_list_id_column],$param.href);?>">{$rst[$output_list_show_column]}</a></li>
	</phphand:list>
</ul>
<script type="text/javascript"><!--
pageSetUp();
$(function()
{
	function init_li(li)
	{
		li.mouseover(function()
		{
			if($(this).attr('lock')=='yes') return;
			$(this).find('i').show();
		}).mouseout(function()
		{
			$(this).find('i').hide();
		});
		
		li.find('a').click(function()
		{
			$(this).parent().parent().find('li.active').removeClass('active');
			$(this).parent().addClass('active');
		});
		
		li.find('i').click(function()
		{
			$(this).parent().find('a,i').hide();
			$(this).parent().attr('lock','yes');
			$(this).parent().append('<input style="margin-left:14px;" type="text" value="' + $(this).parent().find('a').html() + '" />');
			$(this).parent().find('input').focus().blur(function()
			{
				var input = $(this);
				$.get('?class={output:list}&method=update_column&table={$param.table}&field={$param.show_column}&data_id=' + $(this).parent().attr('data_id') + '&value=' + $(this).val(),function(data){
					input.parent().find('a').html(input.val());
					input.parent().find('a,i').show();
					input.parent().attr('lock','no');
					input.remove();
				});
			});
		});
		if(typeof output_list_init=='function')
		{
			output_list_init(li);
		}
	}
	
	$('.output-list li').each(function(i)
	{
		init_li($(this));
	});
	
	$('#output-list-add-item').click(function()
	{
		$.get('?class={output:list}&method=add_column&table={$param.table}&field={$param.show_column}&value=新的{$param.title}',function(data){
			var id=data;
			var str = "{$param.href}";
			var li=$('<li data_id="' + id + '"><i class="glyphicon glyphicon-pencil"></i><a href="' + str.replace('[id]',id) + '">新的{$param.title}</a></li>');
			init_li(li);
			li.appendTo($('ul.output-list'));
		});
	});
});
//--></script>