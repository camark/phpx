($.extend({
	/*
	* JQUERY 加载CSS插件
	* 原理：读取CSS内容，并且将CSS存放在<style>标签内
	* style标签做flag标记，如果CSS已读取过，则无须重复读取
	*/
	requireCss : function(src,folder_path)
	{
		if(folder_path==null){
			folder_path = '/';
		}
		

		src = $.canonical_uri(src,folder_path);


		if($("style[flag='"+src+"']").size()>0) return;
	
		$.get(src,function(data){
			data = $.parseCss(data,src);
			var style=$('<style type="text/css" flag="'+src+'" media="all" />');
			style.insertAfter($('title'));
			if(style[0].styleSheet){
				//console.log(data);
				var rules = data.replace(/\/\*[^\*]*\*\//g, "").replace(/@[^{]*\{/g, '').match(/[^\{\}]+\{[^\}]+\}/g);
				var cssText = '';
				for(var i = 0; i < rules.length; i++) {
				    var m = rules[i].match(/(.*)\s*\{\s*(.*)\}/);
				    if(m) {
				        cssText += ' '+m[1]+'{'+m[2]+'}';
				    }
				}
				//.addRule('.model-body','font-family:\'microsoft yahei\'');
				console.log(cssText);
				style[0].styleSheet.cssText = cssText;//data.toString().replace(/\/\*[\s\S]*?\*\//ig,'');
			}else{
				style[0].appendChild(document.createTextNode(data));
			}
		});
	},
	parseCss : function(content,src)
	{
		var matches = content.match(/@import url\('(.+?)'\);/ig);
		if(matches){
			for(var i in matches)
			{
				var match = matches[i];
				var _url = match.toString().replace('@import url(\'','').replace('\')','');
				var url = _url;
				$.requireCss(url,src);
				content = content.replace(match.toString(),'');
			}
		}

		matches =  content.match(/url\((.+?)\)/ig);
		if(matches)
		{
			for(var i in matches)
			{
				if(i==matches.length-1) break;
				var match = matches[i];
				var _url = match.toString().replace('url(','').replace(')','');
				var url = $.canonical_uri(_url,src);
				content = content.replace('('+_url+')','---'+url+')');
			}
		}

		content = content.replace('---','(');

		return content;
	},

	canonical_uri : function(src, base_path) 
	{ 
		var root_page = /^[^?#]*\//.exec(location.href)[0], 
		root_domain = /^\w+\:\/\/\/?[^\/]+/.exec(root_page)[0], 
		absolute_regex = /^\w+\:\/\//; 

		// is `src` is protocol-relative (begins with // or ///), prepend protocol 
		//console.log(src);
		if (/^\/\/\/?/.test(src)) 
		{ 
		src = location.protocol + src; 
		} 
		// is `src` page-relative? (not an absolute URL, and not a domain-relative path, beginning with /) 
		else if (!absolute_regex.test(src) && src.charAt(0) != "/") 
		{ 
		// prepend `base_path`, if any 
		src = (base_path || "") + src; 
		} 

		// make sure to return `src` as absolute 
		return absolute_regex.test(src) ? src : ((src.charAt(0) == "/" ? root_domain : root_page) + src); 
	} 
}))();