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
      if(isset($_POST['filter1']))
	  {
		  $place=$_POST['filter1'];
	  }
	    if(isset($_POST['filter2']))
	  {
		   $place=$_POST['filter2'];
	  }
	    if(isset($_POST['filter3']))
	  {
		   $place=$_POST['filter3'];
	  }
	     if(isset($_POST['filter4']))
	  {
		   $place=$_POST['filter4'];
	  }
	     if(isset($_POST['filter5']))
	  {
		   $place=$_POST['filter5'];
	  }
	 $temp= 'SELECT * FROM mapinfo WHERE town LIKE '.'"%'.$place.'%"';


	 $results=$wpdb->get_results($temp);

     if(count($results)==0)
	 {
		  echo NotFound();
		  return;
	
	 }
	 if(count($results)>40)
	 {
		 $results=[];
		  echo NotFound();
		return;
	 }
	 $forRes=" var locations = [";

	if($_POST['filter1'])
	{

	    foreach ( $results as $page )
        {
			if($page->Rating>=4.9)
			{
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
			}
        }
		if(strlen($forRes)<19)
		{
	
			echo NotFoundRating($results,$place);
				return;
			
		}
	}
	else if($_POST['filter2'])
	{

    foreach ( $results as $page )
        {
			
			if($page->Rating>=4.5&&$page->Rating<=4.8)
			{
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
			}
        }
		if(strlen($forRes)<21)
		{
				echo NotFoundRating($results,$place);
					return;
			
		}
	}
		else if($_POST['filter3'])
	{

    foreach ( $results as $page )
        {
			if($page->Rating>4.2&&$page->Rating<=4.4)
			{
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
			}
        }
			if(strlen($forRes)<21)
		{
			
			echo NotFoundRating($results,$place);
				return;
		
		}
	}
		else if($_POST['filter4'])
	{

    foreach ( $results as $page )
        {
			if($page->Rating>=4.0&&$page->Rating<=4.2)
			{
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
			}
        }
			if(strlen($forRes)<21)
		{
			echo NotFoundRating($results,$place);
				return;
			
		}
	}
		else if($_POST['filter5'])
	{

    foreach ( $results as $page )
        {
			if($page->Rating<4.0)
			{
          $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
			}
        }
			if(strlen($forRes)<21)
		{
				echo NotFoundRating($results,$place);
				return;
			
		}
	}
	else{
	foreach ( $results as $page )
    {
      $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
    }
	}
 
        $forRes=substr($forRes,0,-1)."];";


	
             $scripts=AddScripts($forRes,(float)$results[0]->Lat.', '.(float)$results[0]->Lng,12,$place);
	         $filter=AddFilterBut();

        return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."<div id='map' style='width: 1110px; height: 500px; position: relative; overflow: hidden; margin: 40px'></div> ".$filter.$scripts);
}
 else
    {
        //initialize empty arr
        $forRes=" var locations = [['', '', '.']];";
         	
         $scripts=AddScripts($forRes,'0.0,0.0',1,'');
	
	     $temp= 'SELECT town FROM mapinfo group by town';
         $forSend='';

	     $results=$wpdb->get_results($temp);

		foreach ( $results as $page )
        {
        $forSend=$forSend."\"".$page->town."\",";
        }
         	
        $test1=InputTextAutocompleate($forSend);
         	
        return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>".$test1."<div id='map' style='width: 1110px; height: 500px; position: relative; overflow: hidden; margin: 40px'></div> ".$scripts);
         	      
         }
	


}


function NotFound()
{
	global $wpdb;
	$forRes=" var locations = [['', '', '.']];";
        $temp= 'SELECT town FROM mapinfo group by town';
         $forSend='';

	     $results=$wpdb->get_results($temp);

		foreach ( $results as $page )
        {
        $forSend=$forSend."\"".$page->town."\",";
        }
         	
        $test1=InputTextAutocompleate($forSend);
        $scripts=AddScripts($forRes,'0.0,0.0',1,'');
		return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."
<div class='alert alert-danger'>
<strong>Внимание!</strong> Градът, който търсите не бе намерен в нашата база данни. Направете <a href='http://109.121.216.41:8022/wordpress/#formSearch'>ново търсене</a>.
</div>".$test1."<div id='map' style='width: 1110px; height: 500px; position: relative; overflow: hidden; margin: 40px'></div> ".$scripts);
return;
}


function NotFoundRating($results,$place)
{
	global $wpdb;
	$forRes=" var locations = [";
		foreach ( $results as $page )
    {
      $forRes=$forRes.'["'.$page->Name.'", '.(float)$page->Lat.', '.(float)$page->Lng.'],';
    }
	   $forRes=substr($forRes,0,-1)."];";


	
             $scripts=AddScripts($forRes,(float)$results[0]->Lat.', '.(float)$results[0]->Lng,12,$place);
	         $filter=AddFilterBut();

		return ("  <script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>"."
<div class='alert alert-danger'>
<strong>Внимание!</strong> В ".$place.", не бяха намерени ресторанти с търсения рейтинг от вас.
</div>"."<div id='map' style='width: 1110px; height: 500px; position: relative; overflow: hidden; margin: 40px'></div> ".$filter.$scripts);
return;
}

function AddFilterBut()
{
	$str="
   
	<div style='margin:0 0 5% 86%'>
<a href='.'><button>НОВО ТЪРСЕНЕ</button></a>
	</div>
  <div style='background-color:white;  margin: 3%;padding:2% 62% 2% 2%' >
  <h4>Филтрирате по рейтинг</h4>
  <form method='post'>
    <input type='hidden' value='' name='myCountry' id='myCountry'/>
   <input type='hidden' value='' name='filter5' id='filter5'/>
    <input type='image' height=30px; width=140px; src='https://scontent.fsof1-2.fna.fbcdn.net/v/t1.0-9/69180626_2533980676661868_905102278174703616_o.jpg?_nc_cat=106&_nc_oc=AQkUkTikt9bCqs-2ZZx6xi21k8v1_SPGVN6C-K83nO9-er41kU_6nR3Cs1CInunN_Hg&_nc_ht=scontent.fsof1-2.fna&oh=d27e64132c220da74523c291cc83fa90&oe=5E125175'/>
  </form>
    <form method='post'>
    <input type='hidden' value='' name='myCountry' id='myCountry'/>
   <input type='hidden' value='' name='filter4' id='filter4'/>
    <input type='image' height=30px; width=140px; src='https://scontent.fsof1-2.fna.fbcdn.net/v/t1.0-9/68922329_2533980573328545_8365646907267088384_o.jpg?_nc_cat=108&_nc_oc=AQl2R32yLHHi04A4xkut5qPXqZLJ3RjUG3Ivrxx6UxExsvmjnskW87MTXePcFsI25b8&_nc_ht=scontent.fsof1-2.fna&oh=24ddc6b0c4214d79bb3394d983111e2f&oe=5DC9F9AC'/>
  </form>
    <form method='post'>
    <input type='hidden' value='' name='myCountry' id='myCountry'/>
   <input type='hidden' value='' name='filter3' id='filter3'/>
    <input type='image' height=30px; width=140px; src='https://scontent.fsof1-2.fna.fbcdn.net/v/t1.0-9/69990232_2533980473328555_2472993700330864640_o.jpg?_nc_cat=104&_nc_oc=AQlml2B5BZCpd70g2dbcXos5n1Meu3mv3IovZ-2kOa7yFdymcj300kHmUIzevwdHmPs&_nc_ht=scontent.fsof1-2.fna&oh=25f1b19a66ae1b8b8e9a727e0ba88e66&oe=5DD34FDB'/>
  </form>
    <form method='post'>
    <input type='hidden' value='' name='myCountry' id='myCountry'/>
   <input type='hidden' value='' name='filter2' id='filter2'/>
    <input type='image' height=30px; width=140px; src='https://scontent.fsof1-2.fna.fbcdn.net/v/t1.0-9/69092466_2533980489995220_6411422639964815360_o.jpg?_nc_cat=110&_nc_oc=AQkf9em0QZtRQ7u5-ndHTA0VXMnSFLFO06oO7IDa8Xtusz1897cUcvGYNFddKTsaU-M&_nc_ht=scontent.fsof1-2.fna&oh=102f2598795c7aaf130299d95020d07d&oe=5DCC6F5E'/>
  </form>
    <form method='post'>
    <input type='hidden' value='' name='myCountry' id='myCountry'/>
   <input type='hidden' value='' name='filter1' id='filter1'/>
    <input type='image' height=30px; width=140px; src='https://scontent.fsof1-1.fna.fbcdn.net/v/t1.0-9/68874291_2533980496661886_2704033172437336064_o.jpg?_nc_cat=111&_nc_oc=AQlX6B5Ca_pQ8apezRD8POKwICZsrw-7z8Lp2w7xJumcpI_xwJRBvj3gMINLHmqwTQo&_nc_ht=scontent.fsof1-1.fna&oh=28139b4141be5118131c362a765e18de&oe=5E063E55'/>
  </form>
	</div>
";

return $str;

}

function AddScripts($param,$latAndLng,$zoom,$place)
{
	$str=  "

  <input type='hidden' value='".$place."' id='hidIn123'>
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
	    var val =document.getElementById('hidIn123').value;
	document.getElementById('filter1').value=val;
    document.getElementById('filter2').value=val;
    document.getElementById('filter3').value=val;
	document.getElementById('filter4').value=val;
	document.getElementById('filter5').value=val;
	document.getElementById('myCountry').value=val;
	
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
</head>     
<body>


<!--Make sure the form has the autocomplete function switched off:-->
<form autocomplete=\"off\" method='post' id='formSearch'>
  <div class=\"autocomplete\" style=\"width:35%; margin:0 0 1% 30%;\">
  <h3>Локализирай ресторанти</h3>
    <input id=\"myInput\" type=\"text\" name=\"myCountry\" placeholder=\"Въведете град: Chicago, Washington...\" style=' height:50px;'>
  </div>
 <div>
  <input type=\"submit\" style=\"width:35%; margin:0 0 0 30%;\" value='Търсене...'>
 </div>
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
.$data.
"];

/*initiate the autocomplete function on the \"myInput\" element, and pass along the countries array as possible autocomplete values:*/
autocomplete(document.getElementById(\"myInput\"), countries);
</script>
";
 
 
}




