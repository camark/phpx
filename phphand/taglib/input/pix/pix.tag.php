<?php if(!isset($widget_pix)){ $widget_pix=1;?>
<script type="text/ecmascript" src="__TAG__/ajaxfileupload.js"></script>
<script type="text/javascript"><!--
	$(function()
	{
		var page = __page__;
		page.find('.img-box-container input[type=file]').bind("change",ajaxFileUpload).click(fileClick);

		page.find('.img-box span').click(function()
		{
			$(this).parent().remove();
			if(typeof(afterPixSuccess)=='function')
			{
				afterPixSuccess();
			}
		});

		function fileClick(){
			if('{$param.max_num}'!='' && $(this).parent().parent().find('.img-box').size()==parseInt('{$param.max_num}'))
			{
				alert('已达到最大上传数量上限 [{$param.max_num}]');
				return false;
			}
		}
		function ajaxFileUpload()
		{
			var box_id = new Date().getTime();
			$('<div class="img-box" id="b'+box_id+'">上传中</div>').insertBefore($(this).parent());
			
			/*
			$("#loading").ajaxStart(function() {  
				$(this).show();  
			})//开始上传文件时显示一个图片  
			.ajaxComplete(function() {  
				$(this).hide();  
			});//文件上传完成将图片隐藏起来*/
			var fid = $(this).attr('id');
			var name= $(this).attr('col');
			var ds= $(this).attr('ds');
			var showname=$(this).attr('showname');
			$.ajaxFileUpload({  
				url : '?class={input:pix}&method=upload&box_id='+box_id+'&ds='+ds+'&showname='+showname,  
				secureuri : false,
				fileElementId : fid,
				dataType : 'text',
				success : function(data, status)
				{
					try{
						if(data.indexOf('>')>=0)
						{
							alert(data.replace(/<.+?>/ig,''));
							page.find('#b'+box_id).remove();
							return;
						}
						var ar = data.split(',');
						var bid = ar[0];
						var file=ar[1];
						if(file.indexOf('.flv')>0 || file.indexOf('.mp4')>0 || file.indexOf('.swf')>0 || file.indexOf('.mov')>0 || file.indexOf('.3gp')>0 || file.indexOf('.mpg')>0)
						{
							page.find('#b'+bid).html('<input type="hidden" name="'+name+'[]" value="'+file+'" /><span></span><com:timthumb src="__TAG__/video.jpg" width="60" height="60" />');
						}else if(file.indexOf('.mp3')>0 || file.indexOf('.wav')>0 || file.indexOf('.awd')>0 || file.indexOf('.vox')>0 || file.indexOf('.ogg')>0 || file.indexOf('.pcm')>0)
						{
							page.find('#b'+bid).html('<input type="hidden" name="'+name+'[]" value="'+file+'" /><span></span><com:timthumb src="__TAG__/audio.jpg" width="60" height="60" />');
						}else if(file.indexOf('.mp3')>0 || file.indexOf('.doc')>0 || file.indexOf('.docx')>0 || file.indexOf('.pdf')>0 || file.indexOf('.rtf')>0 || file.indexOf('.txt')>0)
						{
							page.find('#b'+bid).html('<input type="hidden" name="'+name+'[]" value="'+file+'" /><span></span><com:timthumb src="__TAG__/document.jpg" width="60" height="60" />');
						}else{
							page.find('#b'+bid).html('<input type="hidden" name="'+name+'[]" value="'+file+'" /><span></span><com:timthumb src="__WEB__[file_name]" width="60" height="60" />'.replace('[file_name]',file));
						}
						page.find('input[name=pix_change_flag]').val(1);
						page.find('#b'+bid).attr('file',file);
						page.find('#b'+bid+' span').click(function()
						{
							$(this).parent().remove();
							if(typeof(afterPixSuccess)=='function')
							{
								afterPixSuccess();
							}
						});
						if(typeof(afterPixSuccess)=='function')
						{
							afterPixSuccess();
						}
					}catch(err)
					{
						alert('False file type or file size,or your internet conection was lost,please try again');
						$('#b'+box_id).remove();
					}
				},  
				error : function(data, status, e)
				{  
					alert(e);
				}
			});
			page.find('.img-box-container input[type=file]').unbind().bind("change",ajaxFileUpload).click(fileClick);
		}
		
	});
//--></script>

<style type="text/css"><!--
.img-box{display:inline-block;position:relative;width:66px;height:66px;vertical-align:middle;border:3px solid #e9e9e9;margin:3px 5px 0 0;text-align:center;line-height:60px;color:#999;}
.img-box img{vertical-align:middle;margin:0;display:block;}
.img-box span{position:absolute;width:9px;height:9px;background:url(__TAG__/del.png);right:2px;top:2px;cursor:pointer;}
.img-box-container{}
.img-selector{vertical-align:middle;border:3px dashed #ccc;background:white url(__TAG__/add.png) center no-repeat;cursor:pointer;height:60px;width:60px;display:inline-block;}
.img-selector input{background:none;height:60px;width:60px;opacity:0;-moz-opacity:0;vertical-align:middle;cursor:pointer;}
--></style>
<input type="hidden" name="pix_change_flag" value="0" />
<?php }else{ $widget_pix++;}$pix_flag=$widget_pix . time() . rand(1000,9999);?>
<div id="widget_pix{$pix_flag}">
	<?php 
	$array = array();
	if(isset($row) && $row){
		$array = $this->input->get_value($row,$param.name,'explode');
	}
	?>
	<div class="img-box-container">
		{loop $array as $item}<?php $ia=explode('.',$item);$ext = strtolower($ia[sizeof($ia)-1]);?>
		<div class="img-box" file="{$item}">
		<input type="hidden" name="{$param.name}[]" value="{$item}" />
		<span></span>{if in_array($ext,array('mp3','wav','amr','awb','vox','ogg','pcm'))}<img src="__TAG__/audio.jpg" />{elseif in_array($ext,array('flv','mp4','swf','mov','3gp','mpg'))}<img src="__TAG__/video.jpg" />{elseif in_array($ext,array('jpg','jpeg','gif','png'))}<com:timthumb src="__WEB__{$item}" width="60" height="60" />{else}<img src="__TAG__/document.jpg" />{/if}
		</div>
		{/loop}
		<div class="img-selector">
			<input type="file" name="file" id="file{$pix_flag}" col="{$param.name}" ds="{$param.exts}" showname="{$param.showname}" />
		</div>
		{if isset($ajax_update)}
		<a href="javascript:update_pix_{$pix_flag}();">{~保存}</a>
		<script type="text/javascript"><!--
		function update_pix_{$pix_flag}()
		{
			var value = '';
			$('#widget_pix{$pix_flag} input[type=hidden]').each(function(i)
			{
				if(value!='') value+=',';
				value+=$(this).val();
			});
			update_column(value);
		}
		//--></script>
		{/if}
	</div>
</div>
