<?php

if(count($_POST)>0){
	$category = new CategoryData();
	$category->name = $_POST["name"];
	$category->add();

print "<script>window.location='index.php?view=categories';</script>";


}

?>