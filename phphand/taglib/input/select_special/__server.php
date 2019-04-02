<?php
class Server extends PHPHand_Action
{
	function _default()
	{
		$url = $_POST['url'];
		$url = preg_replace('/^\/.+?\.php\?/is','',$url);
		$item = $_POST['item'];
		$key_query = $_POST['key'];
		$value_query = $_POST['value'];
		$cookie_path = $_POST['cookie_path'];

		
		if($cookie_path)
		{
			if(preg_match('/^(.+?)\.(.+?)\{(.+?)\}$/is',$cookie_path,$match)){
				$table = $match[1];
				$col = $match[2];
				$statement = $match[3];
				$sql="SELECT `$col` FROM `$table` WHERE $statement";
				$rs = $this->db->getOne($sql);
				if(!$rs)
				{
					$cookies = array();
				}else{
					try{
						$cookies=unserialize($rs[$col]);
					}catch(Exception $e)
					{
						$cookies=array();
					}
				}
			}else{
				$cookies = $this->data_helper->read(__ROOT__.$cookie_path,'cookies');
			}
			foreach($cookies as $ck => $cv)
			{
				$this->snoopy->cookies[$ck]=$cv;
			}
		}
		$this->snoopy->fetch($url);
		$html = $this->snoopy->results;
		
		if(strpos($item,'[get]')>0)
		{
			if(preg_match('/'.$this->format($item).'/is',$html,$match))
			{
				$str = $match[1];
				preg_match_all('/'.$this->format($key_query).'/is',$str,$matches,PREG_SET_ORDER);
				foreach($matches as $match)
				{
					echo '<option value="'.$match[1].'">'.$match[2].'</option>';
				}
			}
			
		}else{
			include dirname(__FILE__).'/phpquery.php';
			$documentID = phpQuery::createDocumentWrapper($html,null);
			new phpQueryObject($documentID);
			
			$targets = pq($item);
			
			
			foreach($targets as $target)
			{
				if($key_query=='HTML'){
					$key = preg_replace('/<[^>]+?>/is','',pq($target)->html());
				}else{
					$key = pq($target)->attr($key_query);
				}
				$value = pq($target)->attr($value_query);
				echo '<option value="'.$value.'">'.$key.'</option>';
			}
		}
	}
	
	
	function format($format){
		$format=str_replace("\\","\\\\",$format);
		$format=str_replace("(","\\(",$format);
		$format=str_replace(")","\\)",$format);
		$format=str_replace("{","\\{",$format);
		$format=str_replace("}","\\}",$format);
		$format=str_replace("[","\\[",$format);
		$format=str_replace("]","\\]",$format);
		$format=str_replace("\\[get\\]","[get]",$format);
		//$format=str_replace("\"","\\\"",$format);
		$format=str_replace("/","\\/",$format);
		$format=str_replace("-","\\-",$format);
		$format=str_replace("?","\\?",$format);
		$format=str_replace("&",'.',$format);
		$format=str_replace("[get]",'([\s\S]+?)',$format);
		$format=str_replace("123",'[0-9]+?',$format);
		$format=str_replace("...","[\s\S]+?",$format);
		$format=str_replace("..","\s+?",$format);
		$format=str_replace("~~",".+?",$format);
		$format=str_replace("[dot]","\\.",$format);
		$format=str_replace("[page]","\S+?",$format);
		$format=str_replace("**","([\s\S]+?)",$format);

		//if($charset=='gb2312' || $charset=='gbk') $format=iconv('utf-8',$charset,$format);
		return $format;
	}
}