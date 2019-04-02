// JavaScript Document
$.fn.extend({
	_ajaxpostGetType:function(o) { 
		var _t; return ((_t = typeof(o)) == "object" ? o==null && "null" || Object.prototype.toString.call(o).slice(8,-1):_t).toLowerCase(); 
	},
	_create_ajax_msg:function(o,m){
		var ajax_msg=$('<div class="ajaxmsg">'+m+'</div>');
		ajax_msg.appendTo(o.parent());
	},
	ajaxpostCallBack:function(page,callback){
		var f=$(this);
		if(!f || !f.is('iframe')){
			f=$(page);
		}
		var form=$('form[id='+f.attr('id').substring(17)+']');
		
		if(form.find('.error_hock').size()>0) form.find('.error_hock').hide();
		if(form.find('.success_hock').size()>0) form.find('.success_hock').hide();
		form.attr('working','NO');
		f=$(f[0].contentWindow.document);
		var js=f.find('#js');
		if(js.size()==1){
			js_html=js.html();
			js_html=js_html.replace(/&gt;/ig,'>').replace(/&lt;/ig,'<').replace(/&amp;/g,'&');
			$('head').append('<script type="text/javascript">'+js_html+'</'+'script>');
		}else if(f.find('#msg').size()==0){
			alert(f[0].body.innerHTML);
			return false;
		}else{
			var msg=f.find('#msg').html().replace(/<SCRIPT[\s\S]+?<\/SCRIPT>/ig,'');
			var url=f.find('#url').html();
			var form=$('form.working');
			if(msg){
				if(typeof callback=='function')
				{
					callback(msg,url);
				}else if(url.match(/^false:.*?$/ig)){
					/*
					var input_name=url.replace(/^false:/ig,'');
					var input_row=form.find('*[name='+input_name+']');
					if(input_row.size()==1){
						$.fn._create_ajax_msg(input_row,msg);
					}*/
					var hock_name=url.replace('false:','');
					if(hock_name && form.find('.error_hock[refer='+hock_name+']').size()==1){
						form.find('.error_hock[refer='+hock_name+']').show().html('<font style="color:red">'+msg+'</font>');
					}else{
						alert(msg);
					}
				}else{
					msg = msg.replace(/\(.+?\)/ig,'');
					if(form.find('.success_hock').size()>0){
						form.find('.success_hock').show().html('<font style="color:darkgreen">'+msg+'</font>');
					}else{
						alert(msg);
					}
					if(url){
						if($('.main-tab-bar li.cur i').size()==1){
							$('.main-tab-bar li.cur i').click();
						}else{
							location.href=url;
						}
						/*url=url.replace(/&amp;/g,'&');
						//location.href=url;
						window.location.hash = url.replace('#','');*/
					}
				}
			}
			form.removeClass('working');
		}
	},
	__ajucb:[],
	__ajuin:[],
	ajaxpost:function(cb){
		//alert('tag:'+this.nodeName.toLowerCase());
		
		if(!$(this).is('form')){
			//必须是表单
			return;
		}
		if($.fn._ajaxpostGetType(cb)=='function'){
			$.fn.__ajucb[$.fn.__ajucb.length]=new Array($(this).attr('id'),cb);
		}else if($.fn._ajaxpostGetType(cb)=='object'){
			if(cb.callback){
				$.fn.__ajucb[$.fn.__ajucb.length]=new Array($(this).attr('id'),cb.callback);
			}
			if(cb.init){
				$.fn.__ajuin[$.fn.__ajuin.length]=new Array($(this).attr('id'),cb.init);
			}
		}
		if($('#phphandajaxiframe'+$(this).attr('id')).size()==0){
			$('<iframe name="phphandajaxiframe'+$(this).attr('id')+'" id="phphandajaxiframe'+$(this).attr('id')+'" style="display:none;" class="p0"></iframe>').appendTo($(document.body));
		}
		$(this).attr('target','phphandajaxiframe'+$(this).attr('id'));
		$(this).bind('submit',function(){
			if($(this).attr('working')=='YES') return false;
			$(this).attr('working','YES');
			var init=null;
			for(var i=0;i<$.fn.__ajuin.length;i++){
				if($.fn.__ajuin[i][0]==$(this).attr('id')){
					init=$.fn.__ajuin[i][1];
					break;
				}
			}
			if(init){
				var r=init();
				if(r==false) return false;
			}
			if($('#phphandajaxiframe'+$(this).attr('id')).attr('class')=='p0'){
				var cb=null;
				for(var i=0;i<$.fn.__ajucb.length;i++){
					if($.fn.__ajucb[i][0]==$(this).attr('id')){
						cb=$.fn.__ajucb[i][1];
						break;
					}
				}
				$('#phphandajaxiframe'+$(this).attr('id')).load(cb?cb:$.fn.ajaxpostCallBack).attr('class','p1');
			}
			$(this).addClass('working');
			return true;
		});
	}
});