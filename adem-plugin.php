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
	
	 $forRes=" var features = [";
	
	foreach ( $result as $page )
        {
          $forRes=$forRes."{
                   position: new google.maps.LatLng(".$page->Len.", ".$page->Lng."),
                   type: 'info'
                 },";
         }
  
  
    $forRes=substr($forRes, 0, -1)."];";
    $scripts=AddScripts($forRes);

    add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');

	$asyncDefender="<script async defersrc='https://maps.googleapis.com/maps/api/js?key=AIzaSyC6v5-2uaq_wusHDktM9ILcqIrlPtnZgEk&callback=initMap'> </script>";
	
    return ("<div id='map'></div>".$scripts.$asyncDefender);
}



function callback_for_setting_up_scripts() {
	wp_enqueue_style( 'styles', 'file:///C:/xampp/htdocs/wordpress/wp-content/plugins/styles.css' );
}

add_filter('the_content','takeFromDBtest');


function AddScripts($param)
{
	$str=  " 
	<script>
	
	var map;
      function initMap() {
        map = new google.maps.Map(
            document.getElementById('map'),
            {center: new google.maps.LatLng(-33.91722, 151.23064), zoom: 16});

        var iconBase =
            'https://developers.google.com/maps/documentation/javascript/examples/full/images/';

        var icons = {
          parking: {
            icon: iconBase + 'parking_lot_maps.png'
          },
          library: {
            icon: iconBase + 'library_maps.png'
          },
          info: {
            icon: iconBase + 'info-i_maps.png'
          }
        };
         "         
         .
         $param
         .
         "   
        // Create markers.
        for (var i = 0; i < features.length; i++) {
          var marker = new google.maps.Marker({
            position: features[i].position,
            icon: icons[features[i].type].icon,
            map: map
          });
        };
      }
	  
	 </script>
	  ";
}




