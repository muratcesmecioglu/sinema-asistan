<?php
  include ("simple_html_dom.php");
  
  ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$self = $_SERVER["PHP_SELF"];
$gelensinema = $_GET["sinema"];


$salonadresleri = array(
/*
"1Maltepe AFM Carrefour" => "http://www.beyazperde.com/sinemalar/sinema-T0207/",
"2Cinebonus Nautilus" => "http://www.beyazperde.com/sinemalar/sinema-T0192/",
"3Kadıköy Rexx" => "http://www.beyazperde.com/sinemalar/sinema-T0195/",
"4Kadıköy Moda" => "http://www.beyazperde.com/sinemalar/sinema-T0193/",
"5Kadıköy Atlantis" => "http://www.beyazperde.com/sinemalar/sinema-T0191/",
"6Cinebonus Cevahir" => "http://www.beyazperde.com/sinemalar/sinema-T0221/",
"7Cinebonus Capacity" => "http://www.beyazperde.com/sinemalar/sinema-T0156/",
"8Metroport Cine Vip" => "http://www.beyazperde.com/sinemalar/sinema-T0152/",
"9Airport Cinemas" => "http://www.beyazperde.com/sinemalar/sinema-T0226/",
"10Kozzy" => "http://www.beyazperde.com/sinemalar/sinema-T0201/",
"11optimum" => "http://www.beyazperde.com/sinemalar/sinema-T0403/",
*/
"viaport" => "http://www.beyazperde.com/sinemalar/sinema-T0393/",
"atlantis" => "http://www.beyazperde.com/sinemalar/sinema-T0394/",
"atlaspark" => "http://www.beyazperde.com/sinemalar/sinema-T0572/"
);






if (isset($gelensinema) && $gelensinema != "") {

$araurl = $salonadresleri[$gelensinema];
$html = dlPage($araurl);

$sinema = $html->find("span[class=theater-cover-title]",0)->plaintext;
//echo "<h1>" . $sinema . "</h1>";

$movielist = $html->find("section[class=js-movie-list]",0)->outertext;
preg_match('#<section class="section js-movie-list" data-movies-showtimes="(.*?)" data-coming-soon#', $movielist,$cikti);

$gelenjson = str_replace('&quot;','"',$cikti[1]);
$tamamjson = json_decode($gelenjson,true);
$bugun = date('Y-m-d');

//print_r($tamamjson);
$oynayanfilmler = array();

foreach($tamamjson["theaters"] as $key => $val) {
  $salonidac = $val["id_ac"];
  foreach($val["movies"] as $filmler) {
    //echo "<b>Film Adı: ".$tamamjson["movies"][$filmler]["title"] . "\r\n";
    echo $tamamjson["movies"][$filmler]["title"] . "\r\n";
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

//echo json_encode($oynayanfilmler);
}
 
?>
