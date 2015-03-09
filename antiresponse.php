<?php
	require("./includes/config.php"); 
	require("./includes/parsecsv.lib.php"); 
	
	
	if(empty($_GET["nnum"]))
	{
		apologize("Please Provide a Replication Number");
	}
	$feed = 'https://docs.google.com/spreadsheets/d/1hRSs1FOcUqOsDVK0r6vESSkVmDuapWq8ULgMv_F9EVs/export?gid=0&format=csv';


// Arrays we'll use later
$keys = array();
$primaryarray = array();
 
// Function to convert CSV into associative array
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j]; 
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 
 
// Do it
$data = csvToArray($feed, ',');

 
// Set number of elements (minus 1 because we shift off the first row)
$count = count($data) - 1;
 
//Use first row for names  
$labels = array_shift($data);  
 
foreach ($labels as $label) {
  $keys[] = $label;
}
//print_r($data);
 
// Add Ids, just in case we want them later
$keys[] = 'id';
 
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
 
// Bring it all together
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $primaryarray[$j] = $d;
}
 
// Print it out as JSON

 $feedsec = 'https://docs.google.com/spreadsheets/d/1hRSs1FOcUqOsDVK0r6vESSkVmDuapWq8ULgMv_F9EVs/export?gid=659925972&format=csv';

 
// Arrays we'll use later
$keys = array();
$secondaryarray = array();
 

 
// Do it
$data = csvToArray($feedsec, ',');

 
// Set number of elements (minus 1 because we shift off the first row)
$count = count($data) - 1;
 
//Use first row for names  
$labels = array_shift($data);  
 
foreach ($labels as $label) {
  $keys[] = $label;
}
//print_r($data);
 
// Add Ids, just in case we want them later
$keys[] = 'id';
 
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
 
// Bring it all together
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $secondaryarray[$j] = $d;
}
	
	$csv = new parseCSV('antibodies.csv');
	$results = $primaryarray;
	$csv = new parseCSV('secondary.csv');
	$second = $secondaryarray;
	$len = count($results);
	$hostshold = array();
	$hostnum = 0;
	for($i = 0; $i < $len; $i++) {
		$currHost = $results[$i]["Host"];
		if(!in_array($currHost, $hostshold)){
			$hostnum = $hostnum + 1;
			array_push($hostshold, $currHost);
		}
		$results[$results[$i]["Name"]] = $results[$i];
		unset($results[$i]);
	}
	//print_r($results);
	$chosen = array();
	$els = array();
	$secondary = array();
	$colors = Array();
	foreach($second as $sec){
		if(!array_key_exists($sec["Code"], $colors)){
			$colors[$sec["Excitation"]] = $sec["Code"];
		}
		$host = $sec["Host"];
		$anti = $sec["Antibody Against"];
		$excit = $sec["Excitation"];
		$code = $sec["Code"];
		if(array_key_exists($sec["Host"], $secondary)){
			if(array_key_exists($sec["Antibody Against"], $secondary[$sec["Host"]])){
				$secondary[$sec["Host"]][$sec["Antibody Against"]][$sec["Excitation"]] = $sec["Code"];
			}
			else{
				$secondary[$sec["Host"]][$sec["Antibody Against"]] = array();
				$secondary[$sec["Host"]][$sec["Antibody Against"]][$sec["Excitation"]] = $sec["Code"];
			}
			
		}
		else{
			$secondary[$sec["Host"]] = array();
			$secondary[$sec["Host"]][$sec["Antibody Against"]] = array();
			$secondary[$sec["Host"]][$sec["Antibody Against"]][$sec["Excitation"]] = $sec["Code"];
			//array_push($secondary[$sec["Antibody Against"]],$sec["Excitation"]); 
		}
			
	}
	//print_r($secondary);
	if(isset($_GET['chex']) && !empty($_GET['chex']))   {
		$z = 0;
    	foreach($_GET['chex'] as $item){ 
    		if(array_key_exists($results[$item]["Host"], $secondary)){
    			$chosen[$z] = array("Name" =>$item,"Dilution" =>$results[$item]["Dilution"],"Host" =>$results[$item]["Host"], "Colors" => $secondary[$results[$item]["Host"]], "Size" => count($secondary[$results[$item]["Host"]]));
    		}
    		else{
    			$chosen[$z] = array("Name" =>$item, "Dilution" =>$results[$item]["Dilution"],"Host" =>$results[$item]["Host"], "Colors" => array(), "Size" => 0);
    		}
    		//array_push($chosen[$z]["Colors"], 
    		
    		$z = $z + 1;
    	}
    	
    }
    
	//print_r($chosen);
	
	
	
	
	render("antiresponse_form.php", ["title" => "Antibody Response", "chosen" => $chosen, "results" => $results, "secondary" => $secondary, "colors" => $colors, "hostnum" => $hostnum, "wellsize" => $_GET["wells"], "replications" => $_GET["nnum"], "volumewell" => $_GET["vol"]]);
	
	?>