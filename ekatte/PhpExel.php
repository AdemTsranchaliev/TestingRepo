<?php

require 'vendor/autoload.php';


function addToDatabase($query)
{
    $db = pg_connect("host=localhost port=5433 dbname=Bulgaria2 user=postgres password=34523452");

if(!$db)
{
    echo "Изникна грешка по време на свързването с базата, моля опитайте отново";
    return;
}
    $result = pg_query($query); 
    if(!$result)
  {
      echo "Изникна грешка по време на записването на данните, моля проверете какви са данните, които въвеждате";
      return;
  }


}


  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $reader->setReadDataOnly(true);
  $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_obl.xlsx");


  $arr1=$spreadsheet->getActiveSheet()->toArray();



  $query1 = 'INSERT INTO regions(name,id) VALUES';
  for ($i=1; $i < count($arr1); $i++) { 
        $query1=$query1."('".$arr1[$i][2]."','".$arr1[$i][0]."'),";   
  }

    $query1=substr($query1,0,-1).';';
    addToDatabase($query1);


  $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
  $reader->setReadDataOnly(true);
  $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_obst.xlsx");


  $arr1=$spreadsheet->getActiveSheet()->toArray();

    $query1 = 'INSERT INTO municipalities(name,id,region_id,category) VALUES';
    for ($i=1; $i < count($arr1); $i++) { 
          $query1=$query1."('".$arr1[$i][2]."','".$arr1[$i][0]."','".substr($arr1 [$i][0],0,3)."',".$arr1[$i][3]."),";   
      }


      $query1=substr($query1,0,-1).';';
      addToDatabase($query1);



 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
   $reader->setReadDataOnly(true);
   $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_atte.xlsx");


   $arr1=$spreadsheet->getActiveSheet()->toArray();



      $query1 = 'INSERT INTO allsettlements(ekatte,altitude,kind,category,municipality_id,name) VALUES';
      for ($i=2; $i < count($arr1); $i++) { 
            if($arr1[$i][8]=='')
            {
              $arr1[$i][8]='NULL'; 
            }
            if($arr1[$i][7]=='')
            {
              $arr1[$i][7]='NULL'; 
            }
            $query1=$query1."('".$arr1[$i][0]."',".$arr1[$i][8].",'".$arr1[$i][1]."',".$arr1[$i][7].",'".$arr1[$i][4]."','".$arr1[$i][2]."' ),";   

      }

        $query1=substr($query1,0,-1).';';
        addToDatabase($query1);





      $myfile = fopen("allsettlements.txt", "w") or die("Unable to open file!");


  $db = pg_connect("host=localhost port=5433 dbname=Bulgaria2 user=postgres password=34523452");

  if(!$db)
  {
      echo "Изникна грешка по време на свързването с базата, моля опитайте отново";
     return;
  }
  $txt='var countries = [';
     $result = pg_query("SELECT allsettlements.ekatte,allsettlements.kind,allsettlements.name, municipalities.name, regions.name FROM allsettlements join  municipalities on (allsettlements.municipality_id=municipalities.id) join regions on (regions.id=municipalities.region_id)"); 

    while ($row = pg_fetch_row($result)) {
     
      $txt.='"'.$row[2].' ('. $row[1].')'.' общ. '.$row[3].' обл. '.$row[4].'",';
   }

   $txt=substr($txt,0,-1).'];';
   $myfile = fopen("allsettlements.txt", "w") or die("Unable to open file!");
   fwrite($myfile, $txt);

fclose($myfile);

$myfile = fopen("allsettlements.txt", "r+") or die("Unable to open file!");
$myfile1 = fopen("index.html", "r+") or die("Unable to open file!");
$text=fread($myfile,filesize("allsettlements.txt"));
$text1=fread($myfile1,filesize("index.html"));
$num= strrpos($text1, 'var countries = [', 0);
$num2=strrpos($text1, '"];', $num);

$lat=substr($text1,$num,$num2-$num+3);
$res= str_replace($lat,$text,$text1);
fclose($myfile);
fclose($myfile1);
$myfile22 = fopen("index.html", "w") or die("Unable to open file!");
 fwrite($myfile22,$res);
 fclose($myfile22);


