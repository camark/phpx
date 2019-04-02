// JavaScript Document
$.fn.extend({
	    showDialog : function(args)
	    {
		    if(typeof args=='undefined' || args==null)
		    {
			    args = {
			    	title : '默认窗口',
					content : '',
					submit : null,
					close:null,
					showFooter : true,
					width : 900
			    };
		    }
		    var master = $(this);
		    
		    if(typeof master.attr('dialog_id')=='undefined')
		    {
			    	master.attr('dialog_id',new Date().getTime()+'__'+Math.random());
		    }
		    
		    var dialog_id = master.attr('dialog_id');
		    if(typeof args.loading=='string' && $("div.modal[dialog_id='"+dialog_id+"']").size()>0) $("div.modal[dialog_id='"+dialog_id+"']").remove();
		    var dialog = $("div.modal[dialog_id='"+dialog_id+"']");
		    if(dialog.size()==0)
		    {
		    	var width;
		    	if(typeof args.width!='undefined')
		    	{
		    		width = args.width;
		    	}else{
		    		width = 900;
		    	}
				var html = '<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">';
				html+='<div class="modal-dialog modal-lg" style="width:'+width+'px;">';
				html+='<div class="modal-content">';
				html+='<div class="modal-header">';
				html+='<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
				html+='<h4 class="modal-title">窗口</h4>';
				html+='</div>';
				html+='<div class="modal-body" style="max-height:500px;overflow:auto;">';
				html+='  </div>';
				if(typeof args.showFooter!='undefined' && args.showFooter==true){
					html+=' <div class="modal-footer">';
					html+='	<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>';
					html+='  </div>';
				}
				html+='</div>';
				html+='  </div>';
				html+='</div>';
				dialog = $(html);
				dialog.attr('dialog_id',dialog_id);
				dialog.find('h4').html(args.title);
				if(typeof args.loading=='string'){
					 dialog.find('.modal-body').html(args.loading);
				 }
				if(args.submit!=null)
				{
					dialog.find('button:last').click(args.submit);
				}
				if(args.close!=null)
				{
					dialog.find('button:first span').click(args.close);
				}
				dialog.appendTo($('body'));
		    }
		 if(typeof args.content=='undefined') ;
		 else if(typeof args.content=='string'){
			 dialog.find('.modal-body').html(args.content);
		 }else{
			 dialog.find('.modal-body').append(args.content);
		  }
		  dialog.modal('show').show();
		  return dialog;
	},
	getDialog : function()
	{
		var master = $(this);
		var dialog_id = master.attr('dialog_id');
		var dialog = $("div.modal[dialog_id='"+dialog_id+"']");
		return dialog;
	}
});