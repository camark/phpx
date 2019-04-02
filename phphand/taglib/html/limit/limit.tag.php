<define:max type="int" required="false" default="10" />
<define:value type="string" required="true" />
<?php if(mb_strlen($param.value,'utf-8')<$param.max) echo $param.value;
else echo mb_substr($param.value,0,$param.max,'utf-8').'..';?>