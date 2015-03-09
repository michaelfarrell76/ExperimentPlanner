<?php
	require("./includes/config.php"); 
	require("./includes/parsecsv.lib.php"); 
	require 'vendor/autoload.php';
	//require_once 'google-api-php-client/autoload.php';
	use Google\Spreadsheet\DefaultServiceRequest;
	use Google\Spreadsheet\ServiceRequestFactory;

/*$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);*/

/*$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);
//header('Content-type: application/json');
*/ 
// Set your CSV feed
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
 
// Print it out as JSON



	
	
	$csv = new parseCSV('antibodies.csv');
	//print_r($csv->data[0]);
	//$len = count($csv->data);
	$len = count($primaryarray);
	$pluri = array();
	$nsc = array();
	$bregion = array();
	$other = array();
	for ($i = 0; $i < $len; $i++) {
		//$now = $csv->data[$i];
		$now = $primaryarray[$i];
		if(strcmp($now["Organization"],"Human Pluripotent Stem Cell Markers") == 0){
			array_push($pluri, $now);
		}
		elseif(strcmp($now["Organization"],"Lancaster 2014 Organoid") == 0){
			array_push($bregion, $now);
		}
		elseif(strcmp($now["Organization"],"Neural Progenetor Markers (Hu/Rt/Mouse)") == 0){
			array_push($nsc, $now);
		}
		elseif(strcmp($now["Organization"],"Other") == 0){
			array_push($other, $now);
		}
	}
	
	
	//update
	$volumes = array(
    	6 => 3,
    	12 => 1.15,
    	24 => .6,
    	48 => .3,
    	96 => .25
		);
		
	 
 //print_r($primaryarray);


	render("antibodies_form.php", ["data" => $data, "title" => "Antibody Generator", "pluri" => $pluri, "bregion" => $bregion, "nsc"=>$nsc, "other" => $other, "volumes" => $volumes]);
?>