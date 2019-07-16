<?php
/*
Plugin Name: AdemPlug
Plugin URI:  http://NOLINK
Description: My first Plug
Version:     1.0
Author:      Adem
Author URI:  http://NOWEB
License:     MIT
*/

defined ('ABSPATH') or die('Hey, what are you doing here? You silly human!');

add_filter('the_content','GetLocationOnLoad');
add_filter('the_content','takeFromDBtest');
add_action('activated_plugin','GetFromApiAndSaveInDb');


function  takeFromDBtest(){
	
	global $wpdb;

	
if( isset($_POST['demo']) )
{
   
           $latAndLng = ($_POST['demo']);
	       //url find the city/vilage name depens on cordinates 
           $url = ('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latAndLng.'&key=');
     
	       //use that way to get the information, because if i use get_content... can't escape ampersant
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
         
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $result = curl_exec($ch);
           curl_close($ch);
           		
           $arr=[];
           $output=(array) json_decode($result,true);
		   echo  $output[0];
		   
		   //exctract city/vilage name
           for($j=0;$j<count($output['results'][0]['address_components']);$j++)
		   {
               array_push($arr,$output['results'][0]['address_components'][$j]['types'][0].$output['results'][0]['address_components'][$j]['long_name']);
           }
		   
		   
	//result come as locality: city/vilage, here i take just the city name
	$place=substr($arr[3],8); 

	//removing whitespaces
	 $place=preg_replace('/\s+/', '', $place);
	
	 $temp= 'SELECT * FROM mapinfo WHERE Address LIKE '.'"%'.$place.'%"';
	echo $temp;

	$results=$wpdb->get_results($temp);

     //check if db have information for restaurants in city/vilage and if there is no info, i insert it
	 if(count($results)==0)
	 {
		   $url = ('https://maps.googleapis.com/maps/api/place/textsearch/json?query=restaurants+in+'.$place.'&key=');
   
           $ch = curl_init();
           curl_setopt($ch, CURLOPT_URL, $url);
          
           curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
           $resultF = curl_exec($ch);
           curl_close($ch);		 
		   //decode json
		   $newInput=(array) json_decode($resultF,true);

		   InsertIntoDatabase($newInput);
	   
           //get new inserted info
		   $results=$wpdb->get_results($temp);

	 }

	//initialize the array with the data for map, fo JS
	 $forRes=" var locations = [";
	
	foreach ( $results as $page )
        {
          $forRes=$forRes."['".$page->Name."', ".(float)$page->Lat.", ".(float)$page->Lng."],";
        }
  
    $forRes=substr($forRes,0,-1)."];";

	
	
    $scripts=AddScripts($forRes,$latAndLng,13);
	

    echo ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 500px; height: 400px;'></div> ".$scripts);
}
        else
           {
               //initialize empty arr
           	$forRes=" var locations = [['', '', '.']];";
           	
               $scripts=AddScripts($forRes,'0.0,0.0',1);
           	
           	$test1=GetLocationOnLoad();
           	
               echo ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 500px; height: 400px;'></div> ".$scripts.$test1);
           	      
           }

}



function AddScripts($param,$latAndLng,$zoom)
{
	$str=  "

    <script type='text/javascript'>
    

	"
	.
	$param
	.
	"
	

	 var map = new google.maps.Map(document.getElementById('map'),
     {
         zoom: ".$zoom.",
         center: new google.maps.LatLng(".$latAndLng."),
         mapTypeId: google.maps.MapTypeId.ROADMAP
     });

     var infowindow = new google.maps.InfoWindow();

     var marker, i;

	for (i = 0; i < locations.length; i++)
	 {  
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
      </script>
	  ";
	  
	  return $str;
}

//When plugin is installed, the function add first information in the database
function GetFromApiAndSaveInDb($param)
{
	global $wpdb;
	
	$cities=['New York','Washington'];

	for($x = 0; $x < count($cities); $x++)
	{
		$json = file_get_contents('https://maps.googleapis.com/maps/api/place/textsearch/xml?query=restaurants+in+'.$cities[$x].'&key=YOUR_API_KEY');

          $arr=(array)json_decode($json,true);
		  InsertIntoDatabase($arr);
	}		  
	      
	
}


function InsertIntoDatabase($newInput)
{
	for($j=0;$j<count($newInput['results']);$j++)
	{
		
		$lat=(!is_null($newInput['results'][$j]['geometry']['location']['lat'])) ? $newInput['results'][$j]['geometry']['location']['lat'] : 0;
		$lng=(!is_null($newInput['results'][$j]['geometry']['location']['lng'])) ? $newInput['results'][$j]['geometry']['location']['lng'] : 0;
		$placeId=(!is_null($newInput['results'][$j]['place_id'])) ? $newInput['results'][$j]['place_id'] : '';
		$pricingLevel=(!is_null($newInput['results'][$j]['price_level'])) ? $newInput['results'][$j]['price_level'] : 0;
		$rating=(!is_null($newInput['results'][$j]['rating'])) ? $newInput['results'][$j]['rating'] : 0;
		$name=(!is_null($newInput['results'][$j]['name'])) ? $newInput['results'][$j]['name'] : '';
		$address=(!is_null($newInput['results'][$j]['formatted_address'])) ? $newInput['results'][$j]['formatted_address'] : '';	

		if(CheckIfIsUnique($name,$lat,$lng))
		{
			echo  $wpdb->insert('mapinfo',array( 'Name'=>$name,'Lat' => $lat, 'Lng' =>$lng,'PlaceId' => $placeId, 'PricingLevel' =>$pricingLevel, 'Rating' => $rating, 'Address' => $address  ));
		}
		
	}
}
    
	
function CheckIfIsUnique($name,$lng,$lat)
{
	global $wpdb;
	
	$results = $wpdb->get_results( "SELECT * FROM mapinfo  WHERE Name=".$name." AND lng=".lng." AND len=".$lat );
	
	if(is_null($results))
	{
		return true;
	}
	else
	{
		return false;
	}
}
   
function GetLocationOnLoad()
{
	return "	
	
 <form  name='form123' id='form123' method='post'>
 <input type='hidden' id='demo' name='demo' />
 </form>
		  
 
 <script type='text/javascript'>

 var x=document.getElementById('demo');

 function getLocation()
 {
	if (navigator.geolocation)
	{
		navigator.geolocation.getCurrentPosition(showPosition);
	}
	else
	{
		x.value='Geolocation is not supported by this browser.';
	}
 }
 function showPosition(position)
 {
		 x.value=position.coords.latitude + ',' + position.coords.longitude;  
	 
		 document.getElementById('form123').submit();
 }
 

 getLocation();

 </script>";
 
 
}




