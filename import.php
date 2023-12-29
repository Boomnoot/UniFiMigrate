<?php


function UploadFile($File) {
	$ch = login();
	curl_setopt_array($ch, array(
	  CURLOPT_URL => "<unifiurl>/upload/backup",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 0,
	  CURLOPT_FOLLOWLOCATION => true,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => array('filename'=> new CURLFILE('/UnifiMigrate/up.unf')),
	  CURLOPT_HTTPHEADER => array(
		"X-Requested-With: XMLHttpRequest",
		"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36",
		"Accept: */*"
	  ),
	));
	$content = curl_exec($ch);
	$json = ProcesReturn($ch, $content);
	curl_close($ch);
	return $json;
}


?>