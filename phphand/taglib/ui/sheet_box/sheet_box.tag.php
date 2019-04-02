<base:script src="__TAG__/sheet_box.js" />
<base:css src="__TAG__/sheet_box.css" />

<script type="text/javascript">
$(function()
{
 
	var page=__page__;
	page.find('.sheet_box .sheet_box_head a').sheet();
	page.find('.sheet_box .sheet_box_head li[cur=1] a').click();
	page.find(".sheet_box_head1").find("ul").eq(0).each(function(index) { 
		 $(this).width($(".sheet_container").width()-100);
		 if($(this).width()==0){
               $(this).css("width","96%");
			 }
	     var numLi = $(this).find("li").length+2;
		 $(this).find("li").css("width",100/numLi + "%")
  });
   /* page.find(".job-candidate-list li").each(function(){
		    $(this).css("width",100+"px");
		})*/
});
</script>
<div class="sheet_box {$param.style}">
	<div class="sheet_box_head1 sheet_box_head">
		<ul>
			{loop $param.sheets as $pn => $sheet}
			<li{if isset($sheet.cur) && $sheet.cur} cur="1"{/if}{if $pn==0} class="first"{/if}><a href="javascript:;" url="{$sheet.url}">{$sheet.title}</a></li>
			{/loop}
		</ul>
	</div>
	
	<div class="sheet_container">
		
	</div>
</div>