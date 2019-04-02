<define:sql type="*" required="true" />
<define:handle type="var" required="true" />
<?php
$___phphand_list2_query=$this->db->query($param.sql);
$empty=true;
$n1=0;
while($param.handle=$this->db->fetchArray($___phphand_list2_query)){
$empty=false;
$n1++;
?>
__HTML__
<?php
}
?>