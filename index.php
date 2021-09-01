<?php

// © Avishkar Patil

$url = $_GET["c"];
if($url !=""){

$pid = str_replace('https://www.hoichoi.tv/', '/', $url); 


$hlink ="https://prod-api-cached-2.viewlift.com/content/pages?path=$pid&site=hoichoitv&includeContent=true&moduleOffset=0&moduleLimit=4&languageCode=en&countryCode=IN";

$auth = file_get_contents("auth.php");
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $hlink,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "authorization: $auth",
    "Content-Type: application/json"
  ),
));
$response = curl_exec($curl);
curl_close($curl);

$a1 =json_decode($response, true);

$id = $a1['modules'][1]['contentData'][0]['gist']['id'];

$hclink ="https://prod-api.viewlift.com/entitlement/video/status?id=$id&deviceType=web_browser&contentConsumption=web";

$xurl = curl_init();
curl_setopt_array($xurl, array(
  CURLOPT_URL => $hclink,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json, text/plain, */*",
    "Authorization: $auth",
    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
    "Origin: https://www.hoichoi.tv",
    "Host: prod-api.viewlift.com",
    "Referer: https://www.hoichoi.tv/",
    "Accept-Language: en-US,en;q=0.9",
    "Connection: keep-alive"
  ),
));
$result = curl_exec($xurl);
curl_close($xurl);

$hoichoi =json_decode($result, true);

$title = $hoichoi['video']['gist']['title'];
$des = $hoichoi['video']['gist']['description'];
$lang = $hoichoi['video']['gist']['languageCode'];

$srt = $hoichoi['video']['contentDetails']['closedCaptions'][0]['url']; //srt subtitle for vtt change 0 to 1

$pro = $hoichoi['video']['gist']['posterImageUrl']; // portrait poster
$land = $hoichoi['video']['gist']['videoImageUrl']; // Landscape Poster

$hls = $hoichoi['video']['streamingInfo']['videoAssets']['hls']; // auto all qualities included
$h270 = $hoichoi['video']['streamingInfo']['videoAssets']['mpeg'][0]['url']; // 270p
$h360 = $hoichoi['video']['streamingInfo']['videoAssets']['mpeg'][0]['url']; // 360p
$h720 = $hoichoi['video']['streamingInfo']['videoAssets']['mpeg'][0]['url']; // 720p


 $apii = array("created_by" => "Avishkar Patil", "id" => $id, "lang" => $lang, "title" => $title, "description" => $des, "landscape" => $land, "portrait" => $pro, "hls" => $hls, "270p" => $h270, "360p" => $h360, "720p" => $h720, "subtitle" => $srt);

 $api =json_encode($apii, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);


header("X-UA-Compatible: IE=edge");
header("Content-Type: application/json");
echo $api;

}
else{
  $ex= array("error" => "Something went wrong, Check URL and Parameters !", "created_by" => "Avishkar Patil" );
  $error =json_encode($ex);

  echo $error;
}

?>
