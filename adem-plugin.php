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

$result = $wpdb->get_results ( "
    SELECT * 
    FROM  $wpdb->map
       
" );

foreach ( $result as $page )
{
   echo $page->Lan.'<br/>';
   echo $page->Lng.'<br/>';
}


}

