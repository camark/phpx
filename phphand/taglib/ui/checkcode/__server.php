<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$this->{'ui.checkcode.helper'}->doimg();
		$_SESSION['ui_checkcode']=$this->{'ui.checkcode.helper'}->getCode();
	}
}