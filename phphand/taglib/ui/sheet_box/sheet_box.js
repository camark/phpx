if(typeof $.fn.sheet=='undefined'){
	$.fn.extend({
		sheet : function()
		{
		    var a = $(this);

		    a.click(function(){
			    var a = $(this);
			    var sheet_container = a.parent().parent().parent().next();
			    var url = a.attr('url');
			    			    
			    if(url=='lock') return false;
			    
			    var absolute_url;
			    if(a.parent().parent().find('li.cur a').size()==1){
				    absolute_url = a.parent().parent().find('li.cur a').attr('url');
			    }else{
			    	absolute_url = '!';
			    }
			    a.parent().parent().find('li').removeClass('cur');
			    a.parent().addClass('cur');
			    if(url == absolute_url){
				    var container = sheet_container.find(">div[url='"+url+"']");
				    if(typeof a.attr('refer')!='undefined' && a.attr('refer')!=container.find('input[name=edit_page]').val())
				    {
					    container.find('.form-bootstrapWizard a:eq('+a.attr('refer')+')').click();
				    }
				    return false;
			    }
			    
			    sheet_container.find('>div').hide();
			    var container = sheet_container.find(">div[url='"+url+"']");
			    if(container.size()==1)
			    {
				    container.show();
					if(container.find('.table-wrapper').size()==1 && container.find('.table-wrapper .mCustomScrollBox').size()==1)
					{
						//列表滚动条居左
						container.find('.table-wrapper').mCustomScrollbar('scrollTo','left',{
						    scrollInertia:10
						});
					}
				    if(typeof a.attr('refer')!='undefined' && a.attr('refer')!=container.find('input[name=edit_page]').val())
				    {
					    container.find('.form-bootstrapWizard a:eq('+a.attr('refer')+')').click();
				    }
				    return;
			    }
			    
			    var container_id = new Date().getTime()+'_'+Math.random().toString().replace('.','_');
			    container = $('<div url="'+url+'" id="'+container_id+'" role="sheet-box-container"><div style="height:150px;line-height:150px;text-align:center;"><img src="/phphand/taglib/base/loading/loading.gif" /></div></div>');
			    container.appendTo(sheet_container);
			    
			    url += '&get_ajax_page--1&__page__='+encodeURIComponent('$("#'+container_id+'")');
				
				
				function loadSheetUrl(url,container){
					container.attr('__url',url);
					$.get(url,function(data)
					{
						//将局部刷新的按钮去掉
						//var refresher = '<div style="height:0;text-align:right;"><span class="page-refresher glyphicon glyphicon-repeat" style="display:inline-block;cursor:pointer;"></span></div>';
						var refresher = '';
						data = refresher + data;
						container.html(data);
						if(typeof a.attr('refer')!='undefined' && a.attr('refer')!=container.find('input[name=edit_page]').val())
						{
							container.find('.form-bootstrapWizard a:eq('+a.attr('refer')+')').click();
						}
						
						 container.find('span.page-refresher').click(function()
						 {
							 var container = $(this).parent().parent();
							 var url = container.attr('__url');
							 loadSheetUrl(url,container);
						 });
					});
				}
				
				loadSheetUrl(url,container);
		     });
		}
	});
}