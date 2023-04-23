<?php
//header('Content-Type: application/json');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('allow-origin: *');

header("Access-Control-Allow-Headers: ACCEPT, CONTENT-TYPE, X-CSRF-TOKEN");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
//header('Access-Control-Allow-Origin: http://www.foo.com', false);

$url = $_GET["url"];

if(isset($url)) {
  $m3ufile = file_get_contents($url);
} else {
  //$url = "https://iptv-org.github.io/iptv/index.m3u";
  $url = "playlist.m3u";
  $m3ufile = file_get_contents($url);
}

//$m3ufile = preg_replace('/\n#EXTVLCOPT.*/', '', $m3ufile);

$reg = '/(?:#EXTINF:(.+?)[,]\s?(.+?))(?:[\r\n]#EXTVLCOPT:(?:(http-referrer|http-user-agent)=(.*[^\r\n])))/';

//convert http-referrer and http-user-agent in #EXTVLCOPT to #EXTINF attributes
//repeat twice to process http-referrer and http-user-agent
$m3ufile = preg_replace($reg, '#EXTINF:\\1 \\3="\\4",\\2', $m3ufile);
$m3ufile = preg_replace($reg, '#EXTINF:\\1 \\3="\\4",\\2', $m3ufile);

//remove empty lines
$m3ufile = preg_replace("/[\r\n]{2,}/", "\r", $m3ufile);

//print_r($m3ufile);

//$re = '/#(EXTINF|EXTM3U):(.+?)[,]\s?(.+?)[\r\n]+?((?:https?|rtmp):\/\/(?:\S*?\.\S*?)(?:[\s)\[\]{};"\'<]|\.\s|$))/';
//$re = '/#EXTINF:(.+?)[,]\s?(.+?)[\r\n]+?((?:https?|rtmp):\/\/(?:\S*?\.\S*?)(?:[\s)\[\]{};"\'<]|\.\s|$))/';
$re = '/#EXTINF:(.+?)",\s?(.+?)[\r\n]+?((?:https?|rtmp):\/\/(?:\S*?\.\S*?)(?:[\s)\[\]{};"\'<]|\.\s|$))/';
//$attributes = '/([a-zA-Z0-9\-]+?)="([^"]*)"/';
$attributes = '/([a-zA-Z0-9\-\_]+?)="([^"]*)"/';


$m3ufile = str_replace('tvg-logo', 'thumb_square', $m3ufile);
$m3ufile = str_replace('tvg-id', 'id', $m3ufile);
//$m3ufile = str_replace('tvg-name', 'group', $m3ufile);
//$m3ufile = str_replace('tvg-name', 'name', $m3ufile);
$m3ufile = str_replace('tvg-name', 'author', $m3ufile);
$m3ufile = str_replace('group-title', 'group', $m3ufile);
$m3ufile = str_replace('tvg-country', 'country', $m3ufile);
$m3ufile = str_replace('tvg-language', 'language', $m3ufile);

$m3ufile = str_replace('http-referrer', 'referrer', $m3ufile);
$m3ufile = str_replace('http-user-agent', 'user-agent', $m3ufile);

//print_r($m3ufile);

//$m3ufile = str_replace(' ', '_', $m3ufile); // FOR GROUP

preg_match_all($re, $m3ufile, $matches);

// Print the entire match result
//print_r($matches);

$items = array();

 foreach($matches[0] as $list) {
    
     //echo "$list <br>";
	 
   preg_match($re, $list, $matchList);

   //$mediaURL = str_replace("\r\n","",$matchList[4]);
   //$mediaURL = str_replace("\n","",$matchList[4]);
   //$mediaURL = str_replace("\n","",$mediaURL);
   $mediaURL = preg_replace("/[\n\r]/","",$matchList[3]);
   $mediaURL = preg_replace('/\s+/', '', $mediaURL);
   //$mediaURL = preg_replace( "/\r|\n/", "", $matches[4] );
   

   $newdata =  array (
    //'ATTRIBUTE' => $matchList[2],
    'service' => "IPTV",
    'title' => $matchList[2],
    //'playlistURL' => (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
    //'playlistURL' => str_replace("url=","",$_SERVER['QUERY_STRING']),
    'playlistURL' => $url,
    'media_url' => $mediaURL,
    'url' => $mediaURL
    );
    
    preg_match_all($attributes, $list, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
       $newdata[$match[1]] = $match[2];
    }
    
    //array_push($newdata,$attribute);
    //$newdata[] = $attribute;
	 
	 $items[] = $newdata;
	 //$items[] = $matchList[2];
    
 }

//   $globalitem =  array (
//    //'ATTRIBUTE' => $matchList[2],
//    'item' => $items
//    );

//$globalitem[$items] ;
//$globalitems['item'] = $items;

//$globalist['list'] = $globalitems;

$tvgReg = '/#EXTM3U x-tvg-url="([^"]+)"/';
preg_match($tvgReg, $m3ufile, $tvgMatches);

preg_match_all('/https?:\/\/[^\s,]+/', $tvgMatches[1], $urls_matches);
//print_r($urls_matches[0]);

$epgUrls=$urls_matches[0];
//print_r($epgUrls);

if (empty($epgUrls)) {
	$globalitems =  array (
	 //'ATTRIBUTE' => $matchList[2],
	 'service' => "IPTV",
	 'title' => "IPTV",
	 'item' => $items,
	 );
} else {
	$globalitems =  array (
	 //'ATTRIBUTE' => $matchList[2],
	 'service' => "IPTV",
	 'title' => "IPTV",
	 'EPG' => $epgUrls,
	 'item' => $items,
	 );
}

   /*$globalitems =  array (
    //'ATTRIBUTE' => $matchList[2],
    'service' => "IPTV",
    'title' => "IPTV",
    'item' => $items,
    );*/

  $globalist['list'] = $globalitems;

//print_r($items);

$callback= $_GET['callback'];

  /* if($callback)
    echo $callback. '(' . json_encode($globalist) . ')';  // jsonP callback
  else
    echo json_encode($globalist); */

$id= $_GET['id'];
$num= $_GET['num'];
$location = '';

$item = $globalist['list']['item'];

if ($id) {
	foreach ($item as $value) {
		if($value['id'] == $id) {
			//echo($value['url']);
			$location = $value['url'];
			if (!$num || --$num == 0) {
				break;
			}
		}
	}
	if($value['url']) {
		echo($location);
		header('location:'.$location);
	}
} else {
	if($callback)
	  echo $callback. '(' . json_encode($globalist) . ')';  // jsonP callback
	else
	  echo json_encode($globalist);
}

?>