<base:css src="__TAG__/table.css" />
<?php

if(!is_array($param.config)){
	if($this->config->get('admin_session'))
	{
		
	}else{
		//$config = $this->data_helper->read(__ROOT__.'__TAG__/host/' . $_SERVER['HTTP_HOST']  . '/output/' . $param.config  .'.php','config');
		//if(!$config){
	}
	$config = $this->table_config->read( $param.config );
	$table_rs_config = $param.config;
}else{
	$config = $param.config;
	if(!isset($update_table)){
		$table_rs_config = base64_encode(serialize($param.config)); 
	}
	else
		$table_rs_config=$update_table;
}
$order_cols = array();
foreach($config as $field => $field_config)
{
	if(isset($field_config.list_order))
	{
		$order_cols[$field]=$field_config;
	}
}

$output_table_data_source = $param.data_source;

if(!isset($output_table_data_source_type)){
	if(is_string($output_table_data_source))
	{
		$output_table_data_source_type='sql';
	}else if(is_array($output_table_data_source)){
		$output_table_data_source_type='solr';
	}
}
#对不同数据源类型 处理排序方式
#array数组类型的数据源，是不需要处理排序方式的
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
	if($output_table_data_source_type=='sql')
	{
		if(!isset($__template_id)){
			$output_table_data_source .=" ORDER BY `$order_col` $order_method";
		}else{
			#兼容以前的写法（当搜索条件为空的时候，直接设置数据源为SQL）
			#此时虽然数据源是SQL，但一旦需要增加搜索条件，则数据源应自动切换为SOLR
			$output_table_data_source_type=='solr';
			$output_table_data_source = array();
			$output_table_data_source['~'.$order_col] = $order_method;
		}
			
	}else if($output_table_data_source_type=='solr'){
		$output_table_data_source['~'.$order_col] = $order_method;
	}
}
#table的flag是该数据列表的特有标志
#如果没有专门设置标志，则根据query参数进行自动化设置
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
			case 'get_ajax_page':
			case 'load_by_page_click':
			case '_' . '_page__':
				break;
			default:
				if($table_flag!='') $table_flag.'$';
				$table_flag.=$key.'@'.$val;
		}
	}
}
#处理pagesize和mode
$dir = PHPHAND_DIR.'/taglib/output/table/cache/';
$table_config = $this->data_helper->read($dir.$table_flag  . '.php','config');
if(!$table_config)
{
	$table_config=array(
		'pagesize' => 50,
		'mode' => 'table',
	);
}

if($output_table_data_source_type=='solr' && sizeof($output_table_data_source)==0 &&isset($update_table))
{
	#SOLR类型的数据源，当查询条件为空的时候
	#直接根据update_table从库中查询数据
	$output_table_data_source_type='sql';
	$output_table_data_source = "SELECT * FROM `$update_table` WHERE 1";
}

if(!isset($update_table))
{
	$update_table = $param.config;
}
if(is_string($update_table)){
	$id_column = preg_replace('/^.+?_([^_]+?)$/is','\\1',$update_table).'_id';
}
#数据映射
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
if(is_array($output_table_data_source)){
	$sql_cache_name = $update_table . md5(json_encode($output_table_data_source)).time().rand(1000,9999);
}else{
	$sql_cache_name = md5($sql).time().rand(1000,9999);
}
$this->data_helper->write(__ROOT__.'/data/sql/'.$sql_cache_name. '.php',$output_table_data_source);
$dt_flag=time() . rand(1000,9999);

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// $main_data 就是输出的数据的数组
// 如果是从SQL查询，先把查询结果存入这个数组
// 如果是从SOLR查询，先把查询结果，通过update_table获取到实际数据，也存入这个数组中
$main_data = array();
if($output_table_data_source_type=='sql'){
	$page = intval($this->query->get('page'));
	if(!$page) $page=1;
	?>
	<phphand:mainlist_pre sql="{$output_table_data_source}" pagesize="{$table_config.pagesize}" />
	<?php
}else if($output_table_data_source_type=='solr'){
	$___phphand_page =  intval($this->query->get('page'));
	if(!$___phphand_page) $___phphand_page=1;
	//$output_table_data_source['template_i'] = $___template_id;
	$search_array = $this->solr->search($___template_id,$output_table_data_source,$___phphand_page,$table_config.pagesize);
	if(isset($search_array['grouped']))
	{
		foreach($search_array['grouped'] as $group_key => $group_return)
		{
			$___phphand_pagecount = floor($group_return['matches'] / $table_config.pagesize) + ($group_return['matches'] % $table_config.pagesize==0?0:1);
			$___phphand_mainresult['n'] = $group_return['matches'];
			foreach($group_return['groups'] as $group)
			{
				$search_id = explode('@',$group['doclist']['docs'][0]['id']);
				@$search_id = $search_id[2];
				
				$main_data[] = $this->{ $update_table } -> get($search_id);
			}
			break;
		}
	}else{
		$___phphand_pagecount = floor($search_array['response']['numFound'] / $table_config.pagesize) + ($search_array['response']['numFound'] % $table_config.pagesize==0?0:1);
		$___phphand_mainresult['n'] = $search_array['response']['numFound'];
		foreach($search_array['response']['docs'] as $search_doc)
		{
			$search_id = explode('@',$search_doc['id']);
			@$search_id = $search_id[2];
			$output_table_search_data = $this->{ $update_table } ->get($search_id);
			if($output_table_search_data){
				$main_data[] = $output_table_search_data;
			}
		}
	}

}else if($output_table_data_source_type=='array'){
	$main_data = $output_table_data_source;
	$___phphand_mainresult['n'] = sizeof($main_data);
}
?>
{if !$this->query->get('load_by_page_click')}
<!-- 数据列表的表头，这个部分当使用AJAX刷新列表的时候，是不重复输出的 -->
<div class="data-table" id="dt{$dt_flag}">
	<div class="thead">
		<div class="left">
			<?php
			#方法下插件钩子#
			@$plugin_jobs=$this->plugin_job->get_list('table.' . str_replace('/','.',$this->env->get('app')) . '.' . strtolower($this->env->get('class')) . '.' . $this->env->get('method'));
			foreach($plugin_jobs as $job){
				include __ROOT__.$job;
			}

			$button_groups=$this->{'output.table.helper'}->get_button_groups();?>
			{loop $button_groups as $b_group}
			<div class="btn-group">
				{loop $b_group as $g_title => $g_setup}
					{if is_array($g_setup) && !isset($g_setup.style)}
						<a id="dLabel" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">{$g_title}<span class="caret"></span></a>
						<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
							{loop $g_setup as $g_setup_title => $g_setup_url}
								{if is_array($g_setup_url)}
									<li class="dropdown-submenu">
										<a data-toggle="next">{$g_setup_title}</a>
										<ul class="dropdown-menu" style="display:none">
											{loop $g_setup_url as $g_final_title => $g_final_url}
											<li><a href="{$g_final_url}">{$g_final_title}</a></li>
											{/loop}
										</ul>
									</li>
								{else}
									<li><a href="{$g_setup_url}">{$g_setup_title}</a></li>
								{/if}							{/loop}
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
			<select name="order" style="vertical-align:middle;" class="form-control input-xs">
				{loop $order_cols as $field => $field_config}
				<option field="{$field}" method="desc"{if $field==$order_col && 'desc'==$order_method} selected{/if}>按{$field_config.showname}倒序</option>
				<option field="{$field}" method="asc"{if $field==$order_col && 'asc'==$order_method} selected{/if}>按{$field_config.showname}正序</option>
				{/loop}
			</select>
			{/if}
			<a class="btn btn-xs btn-default show-or-hide dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false" href="javascript:void(0);">显示隐藏列<span class="caret"></span></a>
		</div>
	</div>
	<div id="data-table-column-set-modal" style="display:none;position:fixed;background:white;border:1px solid #bbb;border-radius:5px;z-index:99;">
		<ul>
			{loop $config as $field => $field_config}
			{if isset($field_config.list) && $field_config.list}
			<li><div class="pl10 pr10"><input type="checkbox" value="{$field}"{if isset($field_config.list_show) && $field_config.list_show} checked="checked"{/if} /> {$field_config.showname}</div></li>
			{/if}
			{/loop}
		</ul>
	</div>
<script>
$(function(){
	var page=__page__;
	init_table_a = function()
	{
		page.find('div.tbody a').each(function(i){
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
				var a = $(this);
				a.showDialog({
					title  : $(this).html(),
					showFooter : false,
					content : '<div style="text-align:center;font-size:24px;height:150px;line-height:150px;text-align:center;color:#999;"><span class="fa-spin glyphicon glyphicon-plus"></span> LOADING</div>'
				});
				
				url += '&get_ajax_page=1';
				$.get(url,function(data)
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
			var tr = $(this).parents('tr');
			$(this).click(function()
			{
				var button = $(this);
				if(button.attr('confirm') && !window.confirm(button.attr('confirm')))
				{
					return;
				}
				var u = url;
				u += '&get_ajax_page=1';
				if(typeof button.attr('position')!='undefined')
				{
					u += '&___p___='+button.attr('position');
				}
				$.get(u,function(data){
					data = eval('('+data+')');
					if(typeof data.redirect!='undefined'){
						window.open(data.redirect);
					}else if(data.status=='delete'){
						tr.animate({height:1},function(){
							$(this).remove();
						});
					}else if(data.status==12345)
					{
						//12345是一个特殊的状态码，标识当前仍要继续
						var percent = data.percent;
						if(page.find('.percent-line').size()==0)
						{
							$('<div style="height:4px;background:#999;width:100%;position:absolute" class="percent-line"><div style="height:4px;width:1px;background:#f03;"></div></div>').appendTo(page);
							page.find('.percent-line').css('top',button.offset().top + button.css('height') + 10);
						}
						page.find('.percent-line div').width(percent+'%');
						if(percent==100)
						{
							alert('操作完成');
						}else{
							button.attr('position',data.position);
							button.click();
						}
					}else if(data.status>0)
					{
						//返回1表示操作成功
						alert(data.msg);
						//刷新
						page.find('table.data').refresh(tr.find('input[type=checkbox]').val());
					}
					else
					{
						alert(data.msg);
					}
				});
				//alert('操作成功');
			});
		}else if($(this).attr('href').indexOf('post')==0){
			var url = $(this).attr('href').substring(5);
			$(this).attr('href','javascript:;');
			$(this).attr('url',url);
			$(this).click(function()
			{
				//alert(page.find('div.tbody input[type=checkbox][name=ids]:checked').size());

				
				var form = '<form method="post" target="_blank" action="' + url +'" style="display:none;">';
				form += '<input name="data_ids" type="hidden" value="'+data_ids+'" />';
				form +='</form>';
				form = $(form);
				form.submit();
				form.remove();
			});
		}
	});
	}

	init_table_a(page);
	function reload_table()
	{
		var post_data = <?php echo json_encode($_POST);?>;
			var href = '<?php echo $_SERVER.REQUEST_URI;?>';
			if(href.indexOf('load_by_page_click')==-1) href+='&load_by_page_click=true';
			href+='&__page_'+'_=<?php urlencode($this->view->_staticList['__page_'.'_']);?>';

			page.find('.data-table-page-bar').prev().remove();
			var loading='<div style="height:60px;width:100px;margin:0 auto;text-align:center;"><div id="facebook" style="width:100px;margin:10px auto;">';
				loading+='<div id="block_1" class="facebook_block"></div>';
				loading+='<div id="block_2" class="facebook_block"></div>';
				loading+='<div id="block_3" class="facebook_block"></div>';
				loading+='<div class="clearfix"></div>';
				loading+='</div></div>';
			$(loading).insertBefore(page.find('.data-table-page-bar'));
			$.post(href,post_data,function(data)
			{
				page.find('div.tbody').html(data);
				init_table_a(page);
			});
	}
	page.find('.switch-mode').click(function()
	{
		var switcher = $(this);
		if(switcher.find('span').hasClass('glyphicon-th-list'))
		{
			switcher.find('span').removeClass('glyphicon-th-list').addClass('glyphicon-th-large');
		}else{
			switcher.find('span').removeClass('glyphicon-th-large').addClass('glyphicon-th-list');
		}
		$.get('?class={output:table}&method=switch_mode&flag={$table_flag}',function(data)
		{
			reload_table();
		});
	});

	page.find('.show-or-hide').click(function()
	{
		if(page.find('#data-table-column-set-modal')[0].style.display == 'none'){
			page.find('#data-table-column-set-modal').show();
			page.find('#data-table-column-set-modal').width(120);
			page.find('#data-table-column-set-modal').css({
				'top' : $(this).position().top + 15,
				'right' : 0,
				'position' : 'absolute',
				'margin' : 0,

			});
		}else{
			page.find('#data-table-column-set-modal').hide();
		}
	});
	
	page.find('#data-table-column-set-modal input').click(function(){
		if($(this).is(':checked'))
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
		$.get('?class={output:table}&method=update_show_or_hide&config={$update_table}&show_cols='+show_cols,function(data){
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
		var url = location.href.replace('http://{$_SERVER.HTTP_HOST}__PHP__','').replace('?','/').replace('&','/').replace('=','--'); //获取url中"?"符后的字串
		var theRequest = new Object();
		var array = url.split('/');
		for(var i=1;i<array.length;i++)
		{
			var str = array[i];
			if(str.indexOf('--')==-1)
			{
				if(i==1)
				{
					theRequest['class']=str;
				}else if(i==1)
				{
					theRequest['method']=str;
				}
			}else{
				var sa = str.split('--');
				theRequest[sa[0]] = sa[1];
			}
		}
		/*var str = url.substr(1);
		strs = str.split("&");
		for(var i = 0; i < strs.length; i ++) {
			theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
		}*/
		return theRequest;
	}
	page.find('#dt{$dt_flag} select[name=order]').change(function()
	{
		var request = <?php echo json_encode($this->query->get());?>;
		request['order_col']=$(this).find('option:selected').attr('field');
		request['order_method']=$(this).find('option:selected').attr('method');
		if(typeof request['load_by_page_click']=='undefined')
		{
			request['load_by_page_click'] = 1;
		}
		if(typeof request['page']!='undefined')
		{
			delete(request['page']);
		}
		if(typeof request['get_ajax_page']=='undefined')
		{
			request['get_ajax_page']=1;
		}
		var querystring = '';
		for(var i in request)
		{
			if(querystring!='') querystring+='&';
			querystring += i+'='+request[i];
		}
		
		var url = '__PHP__?'+querystring;
		var post_data = <?php echo json_encode($_POST);?>;
		page.find('.data-table-page-bar').prev().remove();
		var loading='<div style="height:60px;width:100px;margin:0 auto;text-align:center;"><div id="facebook" style="width:100px;margin:10px auto;">';
			loading+='<div id="block_1" class="facebook_block"></div>';
			loading+='<div id="block_2" class="facebook_block"></div>';
			loading+='<div id="block_3" class="facebook_block"></div>';
			loading+='<div class="clearfix"></div>';
		loading+='</div></div>';
		$(loading).insertBefore(page.find('.data-table-page-bar'));
		$.post(url,post_data,function(data)
		{
			page.find('div.tbody').html(data);
		});
		
		//location.href = '?'+querystring;
	});
	
	
	page.find('#dt{$dt_flag} select[name=pagesize]').change(function()
	{
		$.get('?class={output:table}&method=change_pagesize&flag={$table_flag}&pagesize='+$(this).val(),function(data)
		{
			reload_table();
		});

	});
	
	page.find('#dt{$dt_flag} .list-comp .ml130').each(function(i)
	{
		$(this).width($(this).parent().width()-130);
	});
	
	page.find('.thead .left .dropdown-submenu').mouseenter(function()
	{
		$(this).find('>.dropdown-menu').show();
	}).mouseleave(function()
	{
		$(this).find('>.dropdown-menu').hide();
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
				if(page.find('div.tbody input[type=checkbox]:checked').size()==0)
				{
					alert('请至少选择一条操作数据');
					return false;
				}
				var data_ids='';
				page.find('div.data-table input[name=ids]:checked').each(function(i)
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
				var button = $(this);
				var u = url;
				u += '&get_ajax_page=1';
				if(typeof button.attr('position')!='undefined')
				{
					u += '&___p___='+button.attr('position');
				}
				$.post(u,{'data_ids':data_ids},function(data){
					data = eval('('+data+')');
					if(typeof data.redirect!='undefined'){
						window.open(data.redirect);
					}else if(data.status=='delete'){
						var array;
						if(typeof data.data_id=='string')
						{
							array = data.data_id.split(',');
						}else{
							array = new Array();
							array[0] = data.data_id;
						}
						for(var j in array){
							var data_id = array[j];
							page.find('tr[data_id='+data_id+']').animate({height:1},function(){
								$(this).remove();
							});
						}
					}else if(data.status==12345)
					{
						//12345是一个特殊的状态码，标识当前仍要继续
						var percent = data.percent;
						if(page.find('.percent-line').size()==0)
						{
							$('<div style="height:4px;background:#999;width:100%;position:absolute" class="percent-line"><div style="height:4px;width:1px;background:#f03;"></div></div>').appendTo(page);
							page.find('.percent-line').css('top',button.offset().top + button.css('height') + 10);
						}
						page.find('.percent-line div').width(percent+'%');
						if(percent==100)
						{
							alert('操作完成');
						}else{
							button.attr('position',data.position);
							button.click();
						}
					}else if(data.status>0)
					{
						//返回1表示操作成功
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
		}else if($(this).attr('href').indexOf('post')==0){
			var url = $(this).attr('href').substring(5);
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
				
				var form = '<form method="post" target="_blank" action="' + $(this).attr('url')+'" style="display:none;">';
				form += '<input name="data_ids" type="hidden" value="'+data_ids+'" />';
				form +='</form>';
				form = $(form);
				form.submit();
				form.remove();
			});
		}else{
			var url = $(this).attr('href');
			$(this).attr('href','javascript:;');
			$(this).attr('url',url);
			$(this).click(function()
			{
				var a = $(this);
				a.showDialog({
					title  : $(this).html(),
					showFooter : false,
					content : '<div style="text-align:center;font-size:24px;height:150px;line-height:150px;text-align:center;color:#999;"><span class="fa-spin glyphicon glyphicon-plus"></span> LOADING</div>'
				});
				
				url += '&get_ajax_page=1';
				$.get(url,function(data)
				{
					a.showDialog({
						content : data,
					});
				});
			});
		}
	});
	});
	</script>
	<div class="tbody">
<!-- ！数据列表的表头，以上部分当使用AJAX刷新列表的时候，是不重复输出的 -->
{/if}
<?php 
if(is_array($output_table_data_source)){
}else{
?>
	<phphand:mainlist sql="{$output_table_data_source}" handle="$rst" pagesize="{$table_config.pagesize}">
		<?php $main_data[] = $rst;?>
	</phphand:mainlist>
<?php
}
?>
	{if $table_config.mode=='comp'}
	<?php
	$title_cols=array();
	$author_cols=array();
	$status_cols=array();
	$rightcenter_cols=array();
	$rightbottom_cols=array();
	$data_cols=array();
	$thumb_cols=array();
	foreach($config as $field => $field_config)
	{
		if(!isset($field_config.list_comp) || !$field_config.list_comp) continue;
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
		<?php
		if(isset($__reflector)){
			$rst = $this->{ $__reflector [0] }->{ $__reflector [1] }($rst);
		}?>
		<div class="item">
			{loop array('thumb','title','status','author','rightcenter','rightbottom','data') as $field_type}<?php $field_array = $field_type.'_cols';?>
				{if $field_type!='thumb' || sizeof($$field_array)>0}
				<div class="{$field_type}">
				{/if}
				{if $field_type=='title'}<input type="checkbox" name="ids" value="{$rst[$id_column]}" />{/if}
				
				<?php 
				foreach($$field_array as $field => $field_config){
					echo ' <span field="' . $field .'" class="';
					if(isset($field_config.list_background) && $field_config.list_background) echo 'bg';
					echo '" style="';
					if(isset($field_config.list_background) && $field_config.list_background) echo 'background:' . $field_config.list_background .';';
					if(isset($field_config.list_fontsize) && $field_config.list_fontsize) echo 'font-size:'. $field_config.list_fontsize .';';
					if(isset($field_config.list_fontcolor) && $field_config.list_fontsize) echo 'color:'. $field_config.list_fontcolor .';';
				 	echo '">';
					!isset($rst[$field]) && $rst[$field]='';
					 switch($field_config.list){
					 case 'text':
						if(mb_strlen($rst[$field],'utf-8')>20) echo mb_substr($rst[$field],0,18,'utf-8').'..';
						else echo $rst[$field];
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
	<table data-resizable-columns-id="demo-table" class="data" width="100%" cellpadding="0" cellspacing="0" config="{$table_rs_config}" table="{$update_table}" sql_cache="{$sql_cache_name}" style="max-width:100%;">
		<thead>
			<td class="ftd"><input type="checkbox" /></td>
			{if $fid_list_column}<td edit_lock="true" width="120">&nbsp;</td>{/if}
			{loop $config as $field => $field_config}
				{if isset($field_config.list) && $field_config.list}
				<td field="{$field}"{if !isset($field_config.list_show)} style="display:none;"{/if} order="{$field_config.list_arrange_order}"{if isset($field_config.list_width)} style="width:{$field_config.list_width}px;"{/if}>
					{if isset($field_config['list_order']) && $field_config['list_order']==1}
					<div class="field-order-set">
						<span class="asc{if $field==$order_col && 'asc'==$order_method} selected{/if}"></span>
						<span class="desc{if $field==$order_col && 'desc'==$order_method} selected{/if}"></span>
					</div>
					{/if}
					{$field_config.showname}
				</td>
				{/loop}
			{/loop}
		</thead>
<?php
function curry($func, $arity) {
    return create_function('', "
        \$args = func_get_args();
        if(count(\$args) >= $arity)
            return call_user_func_array('$func', \$args);
        \$args = var_export(\$args, 1);
        return create_function('','
            \$a = func_get_args();
            \$z = ' . \$args . ';
            \$a = array_merge(\$z,\$a);
            return call_user_func_array(\'$func\', \$a);
        ');
    ");
}
function on_match($transformation, $matches)
{
    return $transformation[$matches[1]];
}
$callback = @curry(on_match, 2);
?>		
		<tbody>
			{loop $main_data as $__dk => $rst}
				<?php
				if(isset($__reflector)){
					$rst = $this->{ $__reflector [0] }->{ $__reflector [1] }($rst);
				}?>
				<tr data_id="{$rst[$id_column]}" level="0" class="tr<?php echo $__dk%2;?>">
					<td class="ftd" edit_lock="true"><input type="checkbox" name="ids" value="{$rst[$id_column]}" /></td>
					{if $fid_list_column}
						<td edit_lock="true" class="brantch brantch0">
						<?php if($this->{'output.table.helper'}->read($update_table,$fid_list_column,$rst[$id_column])){?>
						<span class="tree_btn glyphicon glyphicon-chevron-right"></span>
						<?php }else{?>
						&nbsp;
						<?php }?>
						</td>
					{/if}
					{loop $config as $field => $field_config}
						{if isset($field_config.list) && $field_config.list}
						<td field="{$field}"{if isset($field_config.is_virtual_field)} is_virtual_field="true"{/if}{if !isset($field_config.list_show)} style="display:none;"{/if} value="{$rst[$field]}"{if !isset($field_config.input) || $field_config.input=='none'} edit_lock="true"{/if}>
							<?php
							if(!isset($rst[$field])) $rst[$field] = '';
							switch($field_config.list){
							case 'text':
								if(isset($rst[$field]))
									{
										if(mb_strlen($rst[$field],'utf-8')<10)
											echo $rst[$field];
										else
											echo mb_substr($rst[$field],0,10,'utf-8').'..';
									}
								else echo '&nbsp;';
								break;
							case 'date-time':
								if($rst[$field])
									echo date('Y-m-d H:i',$rst[$field]);
								else
									echo '&nbsp;';
								break;
							case 'date':
								if($rst[$field]) echo date('Y-m-d',$rst[$field]);
								else echo '&nbsp;';
								break;
							case 'time':
								echo date('H:i',$rst[$field]);
								break;
							case 'replace':
								if(function_exists('preg_replace_callback'))
								{
									$value =  preg_replace_callback('/\[(.+?)\]/is',$callback($rst),$field_config.list_replacement);
									echo $this->input->reflect($value);
								}else{
									echo preg_replace('/\[(.+?)\]/ise',"\$rst['\\1']",$field_config.list_replacement);
								}
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
<script type="text/javascript" src="__TAG__/zh_resizable.js"></script>
<script type="text/javascript"><!--
if(typeof output_table_init=='undefined'){
	function output_table_init(page){
		page.find('.field-order-set span').mouseover(function()
		{
			$(this).addClass('cur');
		}).mouseout(function()
		{
			$(this).removeClass('cur');
		}).click(function()
		{
			var field = $(this).parent().parent().attr('field');
			var method = $(this).hasClass('asc')?'asc':'desc';
			var target_page = page;
			if(page.find('.data-table select[name=order]').size()==0)
			{
				target_page = target_page.parent();
				while(target_page.find('.data-table select[name=order]').size()==0)
				{
					target_page = target_page.parent();
				}
			}
			target_page.find('.data-table  select[name=order] option').attr('selected',false);
			target_page.find('.data-table  select[name=order] option[field='+field+'][method='+method+']').attr('selected',true);
			target_page.find('.data-table  select[name=order]').change();
		});
		page.find('table.data').zh_resizable();
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
			return;
			if(td!=null) return;
			if($(this).attr('edit_lock')=='true') return;
			td = $(this);
			td.attr('edit_lock','true');
			var value = encodeURIComponent(td.attr('value')).replace('--','****');
			var field = td.attr('field');
			var config = '{$update_table}';
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
				if(href.indexOf('_'+'_page__')==-1) href+='&__page_'+'_=<?php echo urlencode($this->query->get('__page_'.'_'))?>';
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
						init_table_a(page);
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
				},
				_delete : function(data_id)
				{
					var table = $(this);
					if(table.find('tr[data_id='+data_id+']').size()==1)
					{
						table.find('tr[data_id='+data_id+']').animate({height:1},function(){
							$(this).remove();
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
	}	
}
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
$(function(){
	if(typeof $.fn.zh_resizable=='undefined'){
		$.getScript('__TAG__/zh_resizable.js',function(){
			output_table_init(__page__);
		});
	}else{
		output_table_init(__page__);
	}
});
//--></script>
{if !$this->query->get('load_by_page_click')}
	</div>
</div>
{/if}

