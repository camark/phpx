<base:css src="__TAG__/table.css" />
<?php
if(!is_array($param.config)){
	$config = $this->data_helper->read(__ROOT__.'/data/output/' . $param.config  .'.php','config');
	if(!$config || true){
		$config = $this->table_config->read( $param.config );
	}
}else{
	$config = $param.config;
}
$order_cols = array();
foreach($config as $field => $field_config)
{
	if(isset($field_config.list_order))
	{
		$order_cols[$field]=$field_config;
	}
}
$table_sql = $param.data_source;
if(sizeof($order_cols)>0){
	$order_col=$this->query->get('order_col');
	if(!$order_col || !isset($order_cols[$order_col]))
	{
		foreach($order_cols as $field => $field_config)
		{
			$order_col = $field;
			break;
		}
	}
	$order_method = $this->query->get('order_method');
	if(!$order_method || !in_array($order_method,array('desc','asc')))
	{
		$order_method='desc';
	}
	if(is_string($table_sql))
	{
		if(isset($update_table)){
			//如果定义了update_table，说明是虚表，只有虚表才会定义update_table
			//实表的update_table和config文件都是一样的
			$table_sql = array(
				'~' . $order_col = $order_method;
			);
		}else{
			$table_sql .=" ORDER BY `$order_col` $order_method";
		}
	}else{
		$table_sql['~'.$order_col] = $order_method;
	}
}



if($param.flag!='')
{
	$table_flag=$param.flag;
}else{
	$all = $this->query->get();
	$table_flag='';
	foreach($all as $key => $val)
	{
		switch($key)
		{
			case 'order_col':
			case 'order_method':
			case 'page':
				break;
			default:
				if($table_flag!='') $table_flag.'$';
				$table_flag.=$key.'@'.$val;
		}
	}
}
$dir = __ROOT__.'/taglib/output/table/cache/';
$table_config = $this->data_helper->read($dir.$table_flag  . '.php','config');
if(!$table_config)
{
}
	$table_config=array(
		'pagesize' => 20,
		'mode' => 'table',
	);

if(!isset($update_table))
{
	$update_table = $param.config;
}
$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';

if(isset($reflector)){
	if(preg_match('/^(\w+?)\.(\w+?)$/is',$reflector,$match))
	{
		$__reflector = explode('.',$reflector);
	}else{
		exit('Reflector定义错误');
	}
}


////////////////////////////////////////////////////////////////////////分级列表///////////////////////////////////////////////////////////////
$fid_list_column = '';
foreach($config as $field => $field_config)
{
	if(isset($field_config.as) && $field_config.as=='as_fid_column')
	{
		$fid_list_column = $field;
		break;
	}
}
if(is_array($table_sql)){
	$sql_cache_name = md5(json_encode($sql)).time().rand(1000,9999);
}else{
	$sql_cache_name = md5($sql).time().rand(1000,9999);
}
$this->data_helper->write(__ROOT__.'/data/sql/'.$sql_cache_name. '.php',$table_sql);
$dt_flag=time() . rand(1000,9999);
if(!is_array($table_sql)){
	$page = intval($this->query->get('page'));
	if(!$page) $page=1;
	?>
	<phphand:mainlist_pre sql="{$table_sql}" pagesize="{$table_config.pagesize}" />
<?php }?>
{if !$this->query->get('load_by_page_click')}
<div class="data-table" id="dt{$dt_flag}" onselectstart="return false;" style="-moz-user-select:none;">
	<div class="thead">
		<div class="left">
			<?php $button_groups=$this->data_table->get_button_groups();?>
			{loop $button_groups as $b_group}
			<div class="btn-group">
				{loop $b_group as $g_title => $g_setup}
					{if is_array($g_setup) && !isset($g_setup.style)}
						<a id="dLabel" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" href="javascript:void(0);">{$g_title}<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							{loop $g_setup as $g_setup_title => $g_setup_url}
								{if is_array($g_setup_url)}
									<li class="dropdown-submenu">
										<a>{$g_setup_title}</a>
										<ul class="dropdown-menu">
											{loop $g_setup_url as $g_final_title => $g_final_url}
											<li><a href="{$g_final_url}">{$g_final_title}</a></li>
											{/loop}
										</ul>
									</li>
								{else}
									<li><a href="{$g_setup_url}">{$g_setup_title}</a></li>
								{/if}
							{/loop}
						</ul>
					{elseif is_array($g_setup)}
						<a class="btn btn-{$g_setup.style} btn-xs" href="{$g_setup.url}">{$g_title}</a>
					{else}
						<a class="btn btn-default btn-xs" href="{$g_setup}">{$g_title}</a>
					{/loop}
				{/loop}
			</div>
			{/loop}
		</div>
		<div class="right form-inline">
			共查询到 {$___phphand_mainresult.n} 条数据&nbsp;&nbsp;&nbsp;显示 <select name="pagesize" style="vertical-align:middle;" class="form-control input-xs">
			{loop array(10,20,50,100) as $pgs}
			<option value="{$pgs}"{if $pgs==$table_config.pagesize} selected="selected"{/if}>{$pgs}</option>
			{/loop}
			</select>
			{if sizeof($order_cols)>0}
			<select name="order" style="vertical-align:middle;">
				{loop $order_cols as $field => $field_config}
				<option field="{$field}" method="desc"{if $field==$order_col && 'desc'==$order_method} selected{/if}>按{$field_config.showname}倒序</option>
				<option field="{$field}" method="asc"{if $field==$order_col && 'asc'==$order_method} selected{/if}>按{$field_config.showname}正序</option>
				{/loop}
			</select>
			{/if}
			<button class="btn btn-xs btn-default switch-mode"><span class="glyphicon glyphicon-th-{if $table_config.mode=='comp'}list{else}large{/if}"></span></button>
			<div class="btn-group" id="data-table-column-set-modal">
				<a class="btn btn-xs btn-default show-or-hide dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" href="javascript:void(0);">显示隐藏列<span class="caret"></span></a>
				<ul class="dropdown-menu">
					{loop $config as $field => $field_config}
					{if isset($field_config.list) && $field_config.list}
					<li><div class="pl10 pr10"><input type="checkbox" value="{$field}"{if isset($field_config.list_show) && $field_config.list_show} checked="checked"{/if} /> {$field_config.showname}</div></li>
					{/if}
					{/loop}
				</ul>
			</div>
		</div>
	</div>
	<script>
	$(function(){
	var page=__page__;
	page.find('.switch-mode').click(function()
	{
		$.get('?class={output:table}&method=switch_mode&flag={$table_flag}',function(data)
		{
			location.reload();
		});
	});

	/*page.find('.show-or-hide').click(function()
	{
		page.find('#data-table-column-set-modal').modal('show');
		page.find('#data-table-column-set-modal .modal-dialog').width(120);
		page.find('#data-table-column-set-modal .modal-dialog').css({
			'top' : $(this).offset().top + 30,
			'left' : $(this).offset().left,
			'position' : 'absolute',
			'margin' : 0,
		});
	});*/
	
	page.find('#data-table-column-set-modal input').click(function(){
		if(!!$(this).is(':checked'))
		{
			page.find('.data-table td[field='+$(this).val()+']').show();
		}else
		{
			page.find('.data-table td[field='+$(this).val()+']').hide();
		}
		
		var show_cols='';
		page.find('#data-table-column-set-modal input:checked').each(function(i)
		{
			if(show_cols!='') show_cols+=',';
			show_cols+=$(this).val();
		});
		$.get('?class={output:table}&method=update_show_or_hide&config={$param.config}&show_cols='+show_cols,function(data){
		});
	});
	
	/*var do_closeing = true;
	page.find('#data-table-column-set-modal').click(function()
	{
		setTimeout(function(){
			if(do_closeing)
				$('#data-table-column-set-modal').modal('hide');
		},10);
	});
	
	page.find('#data-table-column-set-modal .modal-dialog').click(function()
	{
		do_closeing = false;
		setTimeout(function()
		{
			do_closeing = true;
		},200);
	});*/
	
	function GetRequest() {
		var url = location.search; //获取url中"?"符后的字串
		var theRequest = new Object();
		if (url.indexOf("?") != -1) {
			var str = url.substr(1);
			strs = str.split("&");
			for(var i = 0; i < strs.length; i ++) {
				theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
			}
		}
		return theRequest;
	}
	page.find('#dt{$dt_flag} select[name=order]').change(function()
	{
		var request = GetRequest();
		request['order_col']=$(this).find('option:selected').attr('field');
		request['order_method']=$(this).find('option:selected').attr('method');
		var querystring = '';
		for(var i in request)
		{
			if(querystring!='') querystring+='&';
			querystring += i+'='+request[i];
		}
		location.href = '?'+querystring;
	});
	
	page.find('#dt{$dt_flag} select[name=pagesize]').change(function()
	{
		$.get('?class={output:table}&method=change_pagesize&flag={$table_flag}&pagesize='+$(this).val(),function(data)
		{
			location.reload();
		});

	});
	
	page.find('#dt{$dt_flag} .list-comp .ml130').each(function(i)
	{
		$(this).width($(this).parent().width()-130);
	});
	page.find('.thead .left a').each(function(i){
		if(typeof $(this).attr('href')=='undefined')
		{
			return;
		}
		if($(this).attr('href').indexOf('load')==0)
		{
			var url = $(this).attr('href').substring(5);
			$(this).attr('href','javascript:;');
			$(this).attr('url',url);
			$(this).click(function()
			{
				if(page.find('div.data-table input[type=checkbox]:checked').size()==0)
				{
					alert('请至少选择一条操作数据');
					return false;
				}
				var data_ids='';
				page.find('div.data-table input[type=checkbox]:checked').each(function(i)
				{
					if(!$(this).val()) return true;
					if(data_ids!='') data_ids+=',';
					data_ids+=$(this).val();
				});
				
				var a = $(this);
				a.showDialog({
					title  : $(this).html(),
					showFooter : false,
					content : '<div style="text-align:center;font-size:24px;height:150px;line-height:150px;text-align:center;color:#999;"><span class="fa-spin glyphicon glyphicon-plus"></span> LOADING</div>'
				});
				
				url += '&get_ajax_page=1';
				$.post(url,{'data_ids':data_ids},function(data)
				{
					a.showDialog({
						content : data,
					});
				});
				
			});
		}else if($(this).attr('href').indexOf('confirm')==0)
		{
			var url = $(this).attr('href').substring(8);
			$(this).attr('href','javascript:;');
			$(this).attr('url',url);
			$(this).click(function()
			{
				//alert(page.find('div.tbody input[type=checkbox][name=ids]:checked').size());
				if(page.find('div.tbody input[type=checkbox]:checked').size()==0)
				{
					alert('请至少选择一条操作数据');
					return false;
				}
				var data_ids='';
				var data_ids_array = new Array();
				page.find('div.tbody input[type=checkbox]:checked').each(function(i)
				{
					if(!$(this).val()) return true;
					if(data_ids!='') data_ids+=',';
					data_ids+=$(this).val();
					data_ids_array.push($(this).val());
				});
				url += '&get_ajax_page=1';
				$.post(url,{'data_ids':data_ids},function(data){
					data = eval('('+data+')');
					//返回1表示操作成功
					if(data.status>0)
					{
						alert(data.msg);
						//刷新
						for(var i=0;i<data_ids_array.length;i++)
						{
							page.find('table.data').refresh(data_ids_array[i]);
 						}
					}
					else
					{
						alert(data.msg);
					}
				});
				//alert('操作成功');
			});
		}
	});
	});
	</script>
	<div class="tbody">
{/if}
<?php 
$main_data = array();
if(is_array($table_sql)){
	$___phphand_page =  intval($this->query->get('page'));
	if(!$___phphand_page) $___phphand_page=1;
	$search_array = $this->solr->search($table_sql,$___phphand_page,$table_config.pagesize);
	$___phphand_pagecount = floor($search_array['response']['numFound'] / $table_config.pagesize) + ($search_array['response']['numFound'] % $table_config.pagesize==0?0:1);
	$___phphand_mainresult['n'] = $search_array['response']['numFound'];
	foreach($search_array['response']['docs'] as $search_doc)
	{
		$search_id = explode('@',$search_doc['id']);
		$search_id = $search_id[2];
		$main_data[] = $this->{ $update_table } ->get($search_id);
	}
}else{
?>
<phphand:mainlist sql="{$table_sql}" handle="$rst" pagesize="{$table_config.pagesize}">
	<?php $main_data[] = $rst;?>
</phphand:mainlist>
<?php }?>
	{if $table_config.mode=='comp'}
	<?php
	$title_cols=array();
	$author_cols=array();
	$status_cols=array();
	$rightcenter_cols=array();
	$rightbottom_cols=array();
	$data_cols=array();
	$thumb_cols=array();
	foreach($config as $field=>$field_config)
	{
		switch($field_config.list_comp)
		{
			case 'title':
				$title_cols[$field]=$field_config;
				break;
			case 'author':
				$author_cols[$field]=$field_config;
				break;
			case 'status':
				$status_cols[$field]=$field_config;
				break;
			case 'rightcenter':
				$rightcenter_cols[$field]=$field_config;
				break;
			case 'rightbottom':
				$rightbottom_cols[$field]=$field_config;
				break;
			case 'data':
				$data_cols[$field]=$field_config;
				break;
			case 'thumb':
				$thumb_cols[$field]=$field_config;
				break;
		}
	}
	?>
	<div class="list-comp">
		{loop $main_data as $rst}
		<div class="item">
			{loop array('thumb','title','status','author','rightcenter','rightbottom','data') as $field_type}<?php $field_array = $field_type.'_cols';?>
				{if $field_type!='thumb' || sizeof($$field_array)>0}
				<div class="{$field_type}">
				{/if}
				{if $field_type=='title'}<input type="checkbox" name="ids" value="{$rst[$id_column]}" />{/if}
				
				<?php 
				foreach($$field_array as $field => $field_config){
					echo ' <span class="';
					if(isset($field_config.list_background) && $field_config.list_background) echo 'bg';
					echo '" style="';
					if(isset($field_config.list_background) && $field_config.list_background) echo 'background:' . $field_config.list_background .';';
					if(isset($field_config.list_fontsize) && $field_config.list_fontsize) echo 'font-size:'. $field_config.list_fontsize .';';
					if(isset($field_config.list_fontcolor) && $field_config.list_fontsize) echo 'color:'. $field_config.list_fontcolor .';';
				 	echo '">';
					!isset($rst[$field]) && $rst[$field]='';
					 switch($field_config.list){
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
					}
					echo '</span>';
				}?>
				{if $field_type!='thumb' || sizeof($$field_array)>0}</div>{/if}
				{if $field_type=='thumb' && sizeof($thumb_cols)>0}<div class="ml130">{/if}
				{if $field_type=='status' || $field_type=='rightcenter'}<div class="clear"></div>{/if}
			{/loop}
			{if sizeof($thumb_cols)>0}</div>{/if}
			<div class="clear"></div>
		</div>
		{/loop}
	</div>
	{else}
	<table class="data" width="100%" cellpadding="0" cellspacing="0" config="{$param.config}" table="{$update_table}" sql_cache="{$sql_cache_name}">
		<thead>
			<td class="ftd"><input type="checkbox" /></td>
			{if $fid_list_column}<td edit_lock="true" width="120">&nbsp;</td>{/if}
			{loop $config as $field => $field_config}
				{if isset($field_config.list) && $field_config.list}
				<td field="{$field}"{if !isset($field_config.list_show)} style="display:none;"{/if}>{$field_config.showname}</td>
				{/loop}
			{/loop}
		</thead>
		
		<tbody>
			{loop $main_data as $rst}
				<?php
				if(isset($__reflector)){
					$rst = $this->{ $__reflector [0] }->{ $__reflector [1] }($rst);
				}?>
				<tr data_id="{$rst[$id_column]}" level="0">
					<td class="ftd" edit_lock="true"><input type="checkbox" name="ids" value="{$rst[$id_column]}" /></td>
					{if $fid_list_column}
						<td edit_lock="true" class="brantch brantch0">
						{if $this->sub_data_cache->read($update_table,$fid_list_column,$rst[$id_column])}
						<span class="tree_btn glyphicon glyphicon-chevron-right"></span>
						{else}
						&nbsp;
						{/if}
						</td>
					{/if}
					{loop $config as $field => $field_config}
						{if isset($field_config.list) && $field_config.list}
						<td field="{$field}"{if isset($field_config.is_virtual_field)} is_virtual_field="true"{/if}{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}"{if $field_config.input=='none'} edit_lock="true"{/if}>
							<?php
							switch($field_config.list){
							case 'text':
								if(isset($rst[$field])) echo $rst[$field];
								else echo '&nbsp;';
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
								if(!isset($rst[$field]))
								{
									echo '&nbsp;';
								}
								else if(isset($field_config.input) && $field_config.input && $field_config.input!='none')
								{
									if(!isset($output_class)) $output_class=array();
									if(strpos($field_config['input'],'example.')===0)
									{
										$field_config = $this->input->get_example_detail($field_config);
									}
									$class_name = strtoupper($field_config.input[0]) . substr($field_config.input,1) . 'OutputModel';
									if(!isset($output_class[$field]))
									{
										$dir = $this->routine->get_tag_dir('input',$field_config.input);
										include_once ($dir['path'] . '/input/' . $field_config.input . '/__output.php');
										$output_class[$field] = new $class_name($this);
									}
									$obj = $output_class[$field];
									$obj->output($rst[$field],$field_config);
									//$$function_name($rst[$field],$field_config);
								}
								break;
							}?>
						</td>
						{/if}
					{/loop}
				</tr>
			{/loop}
		</tbody>
	</table>
		{if !$___phphand_mainresult.n}
		<div class="text-center pt10 pb10" style="border-bottom:1px solid #eee;">
			<span class="glyphicon glyphicon-alert" style="color:#d00;"></span> 查询无数据
		</div>
		{/if}
	{/if}
	<div class="data-table-page-bar pt10">
		<div class="left">共查询到 {$___phphand_mainresult.n} 条数据</div>
		
		<div class="right"><output:pagenav size="1" /></div>
	</div>
<script type="text/javascript"><!--
$(function(){
	var page=__page__;
	
	page.find('table.data tbody tr').each(function(i)
	{
		init_tr($(this));
	});
	
	page.find('table.data thead input').change(function()
	{
		page.find('div.data-table tbody input[type=checkbox]').prop('checked',$(this).prop('checked'));
	});
	
	page_init();
	
	function init_tr(tr)
	{
		tr.find('td').dblclick(td_init);
		tr.find('span.tree_btn').click(function()
		{
			var btn = $(this);
			var data_id = $(this).parent().parent().attr('data_id');
			var tbody = $(this).parent().parent().parent();
			if(btn.hasClass('glyphicon-chevron-down'))
			{
				//关闭
				tbody.find('tr[fid='+data_id+']').hide();
				btn.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right').show();
				btn.parent().removeClass('brantch-open');
				return;
			}
			if(tbody.find('tr[fid='+data_id+']').size()>0)
			{
				tbody.find('tr[fid='+data_id+']').show();
				btn.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down').show();
				btn.parent().addClass('brantch-open');
				return;
			}
			$(this).hide();
			$(this).parent().append('<base:loading />');
			var level=$(this).parent().parent().attr('level');
			var config = $(this).parent().parent().parent().parent().attr('config');
			var table = $(this).parent().parent().parent().parent().attr('table');
			var sql_cache=$(this).parent().parent().parent().parent().attr('sql_cache');
			var url='?class={output:table}&method=get_sub_data&config='+config+'&table='+table+'&fid='+data_id+'&level='+level+'&sql_cache='+sql_cache;
			$.get(url,function(data)
			{
				btn.parent().find('em').remove();
				if(data=='')
				{
					return;
				}
				var new_tbody = $(data);
				if(new_tbody.find('tr').size()==0) return;
				new_tbody.find('tr').each(function(i)
				{
					init_tr($(this));
					$(this).insertAfter(btn.parent().parent());
				});
				
				btn.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down').show();
				btn.parent().addClass('brantch-open');
			});
		});
		tr.mouseenter(function()
		{
			$(this).addClass('hover');
		}).mouseleave(function()
		{
			$(this).removeClass('hover');
		});
	}
	
	function td_init()
	{
		if(td!=null) return;
		if($(this).attr('edit_lock')=='true') return;
		td = $(this);
		td.attr('edit_lock','true');
		var value = encodeURIComponent(td.attr('value')).replace('--','****');
		var field = td.attr('field');
		var config = '{$param.config}';
		$.get('?class={output:table}&method=get_input&value='+value+'&field='+field+'&config='+config,function(data)
		{
			if(data=='FALSE')
			{
				//禁止列表中修改数据
				td.attr('edit_lock','true');
				td=null;
				return;
			}
			td.html(data);
		});
	}
	
	function page_init()
	{
		page.find('.data-table-page-bar .right a').each(function(i){
			var a = $(this);
			var post_data = <?php echo json_encode($_POST);?>;
			var href = a.attr('href');
			if(href.indexOf('load_by_page_click')==-1) href+='&load_by_page_click=true';
			href+='&__page_'+'_=<?php urlencode($this->view->_staticList['__page_'.'_']);?>';
			a.attr('url',href);
			a.attr('href','javascript:;');
			a.click(function()
			{
				page.find('.data-table-page-bar').prev().remove();
				var loading='<div style="height:60px;width:100px;margin:0 auto;text-align:center;"><div id="facebook" style="width:100px;margin:10px auto;">';
					loading+='<div id="block_1" class="facebook_block"></div>';
					loading+='<div id="block_2" class="facebook_block"></div>';
					loading+='<div id="block_3" class="facebook_block"></div>';
					loading+='<div class="clearfix"></div>';
				loading+='</div></div>';
				$(loading).insertBefore(page.find('.data-table-page-bar'));
				$.post($(this).attr('url'),post_data,function(data)
				{
					page.find('div.tbody').html(data);
				});
			});
		});
	}
	
	
	if(typeof $.fn.refresh=='undefined')
	{
		$.fn.extend({
			refresh : function(data_id)
			{
				var table = $(this);
				if(table.find('tr[data_id='+data_id+']').size()==1)
				{
					var data_tr = table.find('tr[data_id='+data_id+']');
					var level = data_tr.attr('level');
					var config =data_tr.parent().parent().attr('config');
					var update_table = data_tr.parent().parent().attr('table');
					var sql_cache=data_tr.parent().parent().attr('sql_cache');
					var fid = data_tr.attr('fid');
					if(typeof fid=='undefined' || !fid) fid=0;
					var url='?class={output:table}&method=get_sub_data&config='+config+'&table='+update_table+'&fid='+fid+'&id='+data_id+'&level='+level+'&sql_cache='+sql_cache+'&reflector={$reflector}';
					$.get(url,function(data)
					{
						if(data=='')
						{
							return;
						}
						
						var new_tbody = $(data);
						if(new_tbody.find('tr').size()!=1) return;
						var new_tr = new_tbody.find('tr');
						new_tr.insertAfter(data_tr).hide();
						var float = $('<div style="position:absolute;"><table width="100%" border="0" class="data" cellspacing="0" cellpadding="0"></table></div>');
						float.css({
							top : data_tr.offset().top,
							left : data_tr.offset().left,
							width : data_tr.width(),
							height : 0,
							overflow : 'hidden',
						});
						float.appendTo($('body'));
						new_tr.appendTo(float.find('table')).show();
						new_tr.find('td').addClass('glory');
						
						float.animate({height : data_tr.height()},function()
						{
							new_tr.find('td').removeClass('glory');
							new_tr.insertAfter(data_tr);
							init_tr(new_tr);
							data_tr.remove();
							float.remove();
						});
						
					});
				}
			}
		});
	}
	
	$('td *[hover]').each(function(i){
		var ppo = $(this);
		var url = ppo.attr('hover');
		var hover_id = 'hv' + new Date().getTime().toString() + Math.random().toString().replace('.','_');
		$(this).attr('hover_id',hover_id);
		ppo.popover({
			html : true,
			viewport : {selector : '[hover_id='+hover_id+']',padding : 0 },
			content : function()
			{
				if(url.match(/\?/ig)){
					var id = 'pop'+new Date().getTime().toString() + Math.random().toString().replace('.','_');
					var html='<div id="'+id+'"><span class="fa-spin glyphicon glyphicon-cog"></span></div>';
					$.get(url,function(data)
					{
						$('#'+id).html(data);
					});
					return html;
				}else{
					return url;
				}
			},
			placement : 'auto',
			trigger : 'hover'
		});
	});
	
});
if(typeof update_column=='undefined'){
	var td = null;
	function update_column(value)
	{
		var data_id=td.parent().attr('data_id');
		var field = td.attr('field');
		var is_virtual_field = typeof td.attr('is_virtual_field')=='undefined'?false:true;
		td.attr('value',value);
		var config=td.parent().parent().parent().attr('config');
		var table=td.parent().parent().parent().attr('table');
		var url = '__PHP__?class={output:table}&method=update_column&config='+config+'&table='+table+'&data_id='+data_id+'&field='+field+'&is_virtual_field='+is_virtual_field+'&value='+ encodeURIComponent(value).replace('--','****');
		$.get(url,function(data)
		{
			var level = parseInt(td.parent().attr('level'));
			if(!level) level=0;
			for(var i = 0;i<level;i++)
			{
				data = "\n"+'&nbsp;&nbsp;&nbsp;&nbsp; '+data;
			}
			td.html(data);
			td.attr('edit_lock','false');
			td=null;
		});
	}
}
//--></script>
{if !$this->query->get('load_by_page_click')}
	</div>
</div>
{/if}

