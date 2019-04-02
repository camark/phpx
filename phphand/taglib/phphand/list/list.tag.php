<define:sql type="*" required="true" />
<define:handle type="var" required="true" />
<define:switch_lang type="bool" default="false" required="false" />
<?php
$___phphand_list_query=$this->db->query($param.sql,$param.switch_lang);
$empty=true;
$n=0;
while($param.handle=$this->db->fetchArray($___phphand_list_query)){
$empty=false;
$n++;
?>
__HTML__
<?php
}
?>