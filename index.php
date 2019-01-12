<?php 

function dlPage($href) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($curl, CURLOPT_URL, $href);
    curl_setopt($curl, CURLOPT_REFERER, $href);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.4 (KHTML, like Gecko) Chrome/5.0.375.125 Safari/533.4");
    $str = curl_exec($curl);
    curl_close($curl);
    $dom = new simple_html_dom();
    $dom->load($str);
    return $dom;
    }
$salonadresleri = array(
"viaport" => "http://www.beyazperde.com/sinemalar/sinema-T0393/",
"atlantis" => "http://www.beyazperde.com/sinemalar/sinema-T0394/",
"atlaspark" => "http://www.beyazperde.com/sinemalar/sinema-T0572/"
);





//-------------------------------------
$method = $_SERVER['REQUEST_METHOD'];

// Process only when method is POST
if($method == 'POST'){
	$requestBody = file_get_contents('php://input');
	$json = json_decode($requestBody);

	$text = $json->queryResult->parameters->salonlar;
  
	switch ($text) {
		case 'viaport':
			//$speech = "viaport'taki sinemada bugün oynayan filmler şunlar:";
			//$speech = file_get_contents('http://murat.cesmecioglu.net/sinema/indexjson.php?sinema=viaport');
			listele("viaport");
			break;

		case 'bye':
			$speech = "Bye, good night";
			break;

		case 'anything':
			$speech = "Yes, you can type anything here.";
			break;
		
		default:
			$speech = "Sorry, I didnt get that. Please ask me something else.";
			break;
	}

	$response = new \stdClass();
	$response->fulfillmentText = $speech;
	echo json_encode($response);
}
else
{
	echo "Method not allowed";
	listele("viaport");
}

 function listele($salonAdi) {
    $speech = "";
    $araurl = $salonadresleri[$salonAdi];
    $html = dlPage($araurl);
    $sinema = $html->find("span[class=theater-cover-title]",0)->plaintext;
    $movielist = $html->find("section[class=js-movie-list]",0)->outertext;
    preg_match('#<section class="section js-movie-list" data-movies-showtimes="(.*?)" data-coming-soon#', $movielist,$cikti);
    $gelenjson = str_replace('&quot;','"',$cikti[1]);
    $tamamjson = json_decode($gelenjson,true);
    $bugun = date('Y-m-d');
    foreach($tamamjson["theaters"] as $key => $val) {
    $salonidac = $val["id_ac"];
    foreach($val["movies"] as $filmler) {
      echo "<b>Film Adı: ".$tamamjson["movies"][$filmler]["title"] . "</b><br>";
      $speech = $speech . $tamamjson["movies"][$filmler]["title"] . "\r\n";
      //array_push($oynayanfilmler, $tamamjson["movies"][$filmler]["title"] );
      
        foreach($tamamjson["showtimes"][$salonidac][$bugun][$filmler] as $versiyon) {
          if ($versiyon["version"] == "translated") {
          //echo "->Dublaj<br>";
          } else {
            //echo "-> Altyazılı<br>";
          }
          
          //echo "__Seanslar__" . "<br>";
          foreach($versiyon["showtimes"] as $seans) {
            $tarih = date("d.m.Y H:i",strtotime($seans["showStart"]));
            //echo "-> ". $tarih . "<br>";
          }
        }
    }
}
 }


?>