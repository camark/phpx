<?php
if(!defined('PHPHAND_DIR')) exit('direct access denied');

/**
 * PHPHand Database Connector
 *
 * this model class provides a certain set of method
 * for developers to get data more easily
 * as this model may be not so commonly used as PAC or MySQL
 * so we put this model in the PHPHand Model Dir
 * any time you want to use it in a PHPHand project
 * you just use the load method of a PHPHand Action
 * as for this model,you write like this:
 * ----------------------------------------------------------
 * class MyAction extends PHPHand_Action{
 *    function __construct(){
 *        parent::__construct();
 *        $this->load('PDC');
 *    }
 * }
 * ----------------------------------------------------------
 */
class PDCModel extends PHPHand_Model{
	/**
	 * PDC output method
	 *
	 * sometimes you need your phphand program to give a xml
	 * output so that it can be a response of a AJAX app.
	 * as PDC always give it's own output ,
	 * so you need to tell PDC how to do output so that
	 * it can work properly
	 */
	private $_output='html';
	
	/**
	 * setOutput
	 *
	 * @param method String[html,xml,json]
	 */
    function setOutput($method='html'){
		$this->$_output=$method;
	}
	
	function output(){
		
	}
	/**
	 * get
	 *
	 * this is a very basic method of PDC
	 * 
	 */
	function get(){
		
	}
}
?>
