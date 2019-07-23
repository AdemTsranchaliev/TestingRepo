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


add_filter('the_content','takeFromDBtest');
add_action('activated_plugin','GetFromApiAndSaveInDb');


function  takeFromDBtest(){
	
	global $wpdb;

	
if( isset($_POST['myCountry']) )
{
     $place=$_POST['myCountry'];
       
	 $temp= 'SELECT * FROM mapinfo WHERE Address LIKE '.'"%'.$place.'%"';


	 $results=$wpdb->get_results($temp);

     if(count($results)==0)
	 {
		$forRes=" var locations = [['', '', '.']];";
           	
        $scripts=AddScripts($forRes,'0.0,0.0',1);
		return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 500px; height: 400px;'></div> ".$scripts."<div><h3 style='color:red'>Danger!</h3><p style='color:red'> City don't exist in out DB</p></div>");
	 }
	//initialize the array with the data for map, fo JS
	 $forRes=" var locations = [";
	
	foreach ( $results as $page )
        {
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
        }
 
        $forRes=substr($forRes,0,-1)."];";


	
         $scripts=AddScripts($forRes,(float)$results[0]->Lat.', '.(float)$results[0]->Lng,12);
	

        return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 500px; height: 400px;'></div> ".$scripts);
}
 else
    {
        //initialize empty arr
        $forRes=" var locations = [['', '', '.']];";
         	
         $scripts=AddScripts($forRes,'0.0,0.0',1);
	
	     $temp= 'SELECT town FROM mapinfo group by town';
         $forSend='';

	     $results=$wpdb->get_results($temp);

		foreach ( $results as $page )
        {
        $forSend=$forSend."'".$page->town."',";
        }
	    $frSend=substr($forSend,0,-1);
         	
        $test1=InputTextAutocompleate($forSend);
         	
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

		if(CheckIfIsUnique($placeId))
		{
			echo  $wpdb->insert('mapinfo',array( 'Name'=>$name,'Lat' => $lat, 'Lng' =>$lng,'PlaceId' => $placeId, 'PricingLevel' =>$pricingLevel, 'Rating' => $rating, 'Address' => $address  ));
		}
		
	}
}
    
	
function CheckIfIsUnique($id)
{
	global $wpdb;
	
	$results = $wpdb->get_results( "SELECT * FROM mapinfo  WHERE Id="$id );
	
	if(is_null($results))
	{
		return true;
	}
	else
	{
		return false;
	}
}
   
function InputTextAutocompleate($data)
{
	return "	
	<style>
* {
  box-sizing: border-box;
}

body {
  font: 16px Arial;  
}

/*the container must be positioned relative:*/
.autocomplete {
  position: relative;
  display: inline-block;
}

input {
  border: 1px solid transparent;
  background-color: #f1f1f1;
  padding: 10px;
  font-size: 16px;
}

input[type=text] {
  background-color: #f1f1f1;
  width: 100%;
}

input[type=submit] {
  background-color: DodgerBlue;
  color: #fff;
  cursor: pointer;
}

.autocomplete-items {
  position: absolute;
  border: 1px solid #d4d4d4;
  border-bottom: none;
  border-top: none;
  z-index: 99;
  /*position the autocomplete items to be the same width as the container:*/
  top: 100%;
  left: 0;
  right: 0;
}

.autocomplete-items div {
  padding: 10px;
  cursor: pointer;
  background-color: #fff; 
  border-bottom: 1px solid #d4d4d4; 
}

/*when hovering an item:*/
.autocomplete-items div:hover {
  background-color: #e9e9e9; 
}

/*when navigating through the items using the arrow keys:*/
.autocomplete-active {
  background-color: DodgerBlue !important; 
  color: #ffffff; 
}
</style>

<form autocomplete='off' method='post'>
  <div class='autocomplete' style='width:300px;'>
    <input id='myCountry' type='text' name='myCountry' placeholder='Sofia...'>
  </div>
  <input type='submit'>
</form>
 

<script>
function autocomplete(inp, arr) {
  /*the autocomplete function takes two arguments,
  the text field element and an array of possible autocompleted values:*/
  var currentFocus;
  /*execute a function when someone writes in the text field:*/
  inp.addEventListener(\"input\", function(e) {
      var a, b, i, val = this.value;
      /*close any already open lists of autocompleted values*/
      closeAllLists();
      if (!val) { return false;}
      currentFocus = -1;
      /*create a DIV element that will contain the items (values):*/
      a = document.createElement(\"DIV\");
      a.setAttribute(\"id\", this.id + \"autocomplete-list\");
      a.setAttribute(\"class\", \"autocomplete-items\");
      /*append the DIV element as a child of the autocomplete container:*/
      this.parentNode.appendChild(a);
      /*for each item in the array...*/
      for (i = 0; i < arr.length; i++) {
        /*check if the item starts with the same letters as the text field value:*/
        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
          /*create a DIV element for each matching element:*/
          b = document.createElement(\"DIV\");
          /*make the matching letters bold:*/
          b.innerHTML = \"<strong>\" + arr[i].substr(0, val.length) + \"</strong>\";
          b.innerHTML += arr[i].substr(val.length);
          /*insert a input field that will hold the current array item's value:*/
          b.innerHTML += \"<input type='hidden' value='\" + arr[i] + \"'>\";
          /*execute a function when someone clicks on the item value (DIV element):*/
          b.addEventListener(\"click\", function(e) {
              /*insert the value for the autocomplete text field:*/
              inp.value = this.getElementsByTagName(\"input\")[0].value;
              /*close the list of autocompleted values,
              (or any other open lists of autocompleted values:*/
              closeAllLists();
          });
          a.appendChild(b);
        }
      }
  });
  /*execute a function presses a key on the keyboard:*/
  inp.addEventListener(\"keydown\", function(e) {
      var x = document.getElementById(this.id + \"autocomplete-list\");
      if (x) x = x.getElementsByTagName(\"div\");
      if (e.keyCode == 40) {
        /*If the arrow DOWN key is pressed,
        increase the currentFocus variable:*/
        currentFocus++;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 38) { //up
        /*If the arrow UP key is pressed,
        decrease the currentFocus variable:*/
        currentFocus--;
        /*and and make the current item more visible:*/
        addActive(x);
      } else if (e.keyCode == 13) {
        /*If the ENTER key is pressed, prevent the form from being submitted,*/
        e.preventDefault();
        if (currentFocus > -1) {
          /*and simulate a click on the \"active\" item:*/
          if (x) x[currentFocus].click();
        }
      }
  });
  function addActive(x) {
    /*a function to classify an item as \"active\":*/
    if (!x) return false;
    /*start by removing the \"active\" class on all items:*/
    removeActive(x);
    if (currentFocus >= x.length) currentFocus = 0;
    if (currentFocus < 0) currentFocus = (x.length - 1);
    /*add class \"autocomplete-active\":*/
    x[currentFocus].classList.add(\"autocomplete-active\");
  }
  function removeActive(x) {
    /*a function to remove the \"active\" class from all autocomplete items:*/
    for (var i = 0; i < x.length; i++) {
      x[i].classList.remove(\"autocomplete-active\");
    }
  }
  function closeAllLists(elmnt) {
    /*close all autocomplete lists in the document,
    except the one passed as an argument:*/
    var x = document.getElementsByClassName(\"autocomplete-items\");
    for (var i = 0; i < x.length; i++) {
      if (elmnt != x[i] && elmnt != inp) {
        x[i].parentNode.removeChild(x[i]);
      }
    }
  }
  /*execute a function when someone clicks in the document:*/
  document.addEventListener(\"click\", function (e) {
      closeAllLists(e.target);
  });
}

/*An array containing all the country names in the world:*/
var countries = [

"
.
$data
.
"

];

/*initiate the autocomplete function on the 'myCountry' element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById('myCountry'), countries);
</script>
";
 
 
}




