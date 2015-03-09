<?php
	require("./includes/config.php"); 
	
	if(empty($_GET["number"]))
	{
		apologize("Please Provide a number of variables");
	}
	if(empty($_GET["nnum"]))
	{
		apologize("Please Provide a Replication Number");
	}
	for($i=1;$i<=$_GET["number"];$i++)
	{
		if(empty($_GET[$i]))
		{
			apologize("Please Name All Variables");
		}
	}
	render("permresponse_form.php", ["title" => "Response"]);
?>