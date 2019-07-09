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


function takeFromDBtest(){
	
	global $wpdb;
	
	 $result=$wpdb->get_results("SELECT * FROM mapinfo");
	
	 $forRes=" var locations = [";
	
	foreach ( $result as $page )
        {
          $forRes=$forRes."['Bondi Beach', ".(float)$page->Len.", ".(float)$page->Lng."],";
        }
  
    $forRes=substr($forRes,0,-1)."];";

    $scripts=AddScripts($forRes);
	
    return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 500px; height: 400px;'></div> ".$scripts);
}




add_filter('the_content','takeFromDBtest');
add_action('activated_plugin','GetFromApiAndSaveInDb');

function AddScripts($param)
{
	$str=  "

<script type='text/javascript'>
    

	"
	.
	$param
	.
	"
	

        var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(-33.92, 151.25),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
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


function GetFromApiAndSaveInDb($param)
{
	global $wpdb;
	

	for($x = 1; $x <= 875; $x++)
	{
		$json = file_get_contents('file:///D:test123/index'.$x.'.html');

          $arr=(array)json_decode($json,true);
	       for ($y = 0; $y < count($arr['results']); $y++)
	      {
			  
			  $name=$arr['results'][$y]['name'];
			  $lng=float)$arr['results'][$y]['geometry']['location']['lng']
			  $lat=(float)($arr['results'][$y]['geometry']['location']['lat'])
			  
			  if(CheckIfIsUnique($name,$lng,$lat))
			  {
				   $wpdb->insert('mapinfo',array( 'Name'=>$name,'Lng' => $lng, 'Len' =>$lat  ));
			  }
			  
			  
          echo 
	      }
	}
    }
    
	
	function bool CheckIfIsUnique($name,$lng,$lat)
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
  




