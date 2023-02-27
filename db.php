<?php
define ("host","localhost");
	define ("user","root");
	define ("password","");
	define ("database","fuzzy_db");
	$connect = mysqli_connect(host,user,password);
if(!$connect){
  echo "Connection was not succesful";
}
if(!mysqli_select_db($connect,database)){echo "Database selection was not successful";}

class DBController {
	private $host = "localhost";
	private $user = "root";
	private $password = "";
	private $database = "fuzzy_db";
	private $conns;
	
	function __construct() {
		$this->conns = $this->connectDB();
	}
	
	function connectDB() {
		$conns = mysqli_connect($this->host,$this->user,$this->password);
		mysqli_select_db($conns,$this->database)or die("No database");
		return $conns;
	}
	
	function runQuery($query) {
		$result = mysqli_query($this->conns,$query);
		while($row=mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}		
		if(!empty($resultset))
			return $resultset;
	}
	
	function numRows($query) {
		$result  = mysqli_query($this->conns,$query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;	
	}
}



if(!empty($_GET["action"])) {
switch($_GET["action"]) {
	case "add":
		if(!empty($_POST["quantity"])) {
			$productByCode = $db_handle->runQuery("SELECT * FROM products WHERE code='" . $_GET["code"] . "'");
			$itemArray = array($productByCode[0]["code"]=>array('imgUrl'=>$productByCode[0]["imgUrl"],'name'=>$productByCode[0]["item_name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode[0]["price1"]));
			
			if(!empty($_SESSION["cart_item"])) {
				if(in_array($productByCode[0]["code"],array_keys($_SESSION["cart_item"]))) {
					foreach($_SESSION["cart_item"] as $k => $v) {
							if($productByCode[0]["code"] == $k) {
								if(empty($_SESSION["cart_item"][$k]["quantity"])) {
									$_SESSION["cart_item"][$k]["quantity"] = 0;
								}
								$_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
							}
					}
				} else {
					$_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
				}
			} else {
				$_SESSION["cart_item"] = $itemArray;
			}
		}
	break;
	case "remove":
		if(!empty($_SESSION["cart_item"])) {
			foreach($_SESSION["cart_item"] as $k => $v) {
					if($_GET["code"] == $k)
						unset($_SESSION["cart_item"][$k]);				
					if(empty($_SESSION["cart_item"]))
						unset($_SESSION["cart_item"]);
			}
		}
	break;
	case "empty":
		unset($_SESSION["cart_item"]);
	break;	
}
}
?>