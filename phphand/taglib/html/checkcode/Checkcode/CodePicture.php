<?php
error_reporting(E_ERROR);
session_start();
class CCheckCodeFile
{
//验证码位数
private $mCheckCodeNum   = 4;
//产生的验证码
private $mCheckCode    = '';

//验证码的图片
private $mCheckImage   = '';
//干扰像素
private $mDisturbColor   = '';
//验证码的图片宽度
private $mCheckImageWidth = '80';
//验证码的图片宽度
private $mCheckImageHeight   = '20';
/**
*
* @brief   输出头
*
*/
private function OutFileHeader()
{
   header ("Content-type: image/png");
}
/**
*
* @brief   产生验证码
*
*/
private function CreateCheckCode()
{
   //$this->mCheckCode = strtoupper(substr(md5(rand()),0,$this->mCheckCodeNum));
   $string="12345678912345678912345678";
   $code='';
   for($i=0;$i<$this->mCheckCodeNum;$i++){
   	$chr=$string[rand(0,24)];
	$code.=$chr;
   }
   if(isset($_GET['gc'])){
   	$code=$_GET['gc'];
   	$this->mCheckCode=$code;
   }else{
   	$this->mCheckCode=$code;
   	$_SESSION['PHPHAND_CheckCode']=$this->mCheckCode;
   }
   //session_register('PHPHAND_CheckCode',$this->mCheckCode);
   return $this->mCheckCode;
}
/**
*
* @brief   产生验证码图片
*
*/
private function CreateImage()
{
   $this->mCheckImage = @imagecreate ($this->mCheckImageWidth,$this->mCheckImageHeight);
   imagecolorallocate ($this->mCheckImage, 250, 250, 250);
   return $this->mCheckImage;
}
/**
*
* @brief   设置图片的干扰像素
*
*/
private function SetDisturbColor()
{
   for ($i=0;$i<=128;$i++)
   {
    $this->mDisturbColor = imagecolorallocate ($this->mCheckImage, rand(0,255), rand(0,255), rand(0,255));
    imagesetpixel($this->mCheckImage,rand(4,$this->mCheckImageWidth),rand(4,$this->mCheckImageHeight),$this->mDisturbColor);
   }
}
/**
*
* @brief   设置验证码图片的大小
*
* @param   $width   宽
*
* @param   $height 高 
*
*/
public function SetCheckImageWH($width,$height)
{
   if($width=='' || $height=='') return false;
   $this->mCheckImageWidth   = $width;
   $this->mCheckImageHeight = $height;
   return true;
}
/**
*
* @brief   在验证码图片上逐个画上验证码
*
*/
private function WriteCheckCodeToImage()
{
   for ($i=0;$i<=$this->mCheckCodeNum;$i++)
   {
   	$color_base = rand(0,20);
    $bg_color = imagecolorallocate ($this->mCheckImage,$color_base,$color_base,$color_base);
    $x = floor(($this->mCheckImageWidth-10)/$this->mCheckCodeNum)*$i+5;
    $y = rand(15,$this->mCheckImageHeight);
	$angle=rand(-5,5);
    ImageTTFText ($this->mCheckImage,14,$angle,$x, $y, $bg_color,'georgiai.ttf',$this->mCheckCode[$i]);
   }
}
/**
*
* @brief   输出验证码图片
*
*/
public function OutCheckImage()
{
   $this ->OutFileHeader();
   $this ->CreateCheckCode();
   $this ->CreateImage();
   $this ->SetDisturbColor();
   $this ->WriteCheckCodeToImage();
   imagepng($this->mCheckImage);
   imagedestroy($this->mCheckImage);
}
}

$c_check_code_image = new CCheckCodeFile();
//$c_check_code_image ->SetCheckImageWH(100,50);//设置显示验证码图片的尺寸
$c_check_code_image ->OutCheckImage();
?>