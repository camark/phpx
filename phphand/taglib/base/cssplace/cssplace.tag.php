<?php
echo str_replace('_'.'_TAG__','__TAG__',file_get_contents($_SERVER['DOCUMENT_ROOT'].'__TAG__/php.php'));
?>