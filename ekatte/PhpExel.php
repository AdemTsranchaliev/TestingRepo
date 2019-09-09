<?php

require 'vendor/autoload.php';

echo "<style>

button {

background-color: blue;
height: 40px;
width: 30%;
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
  background-color: #cc0000;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>";

function htmlError($ser)
{
    $str="
<table>
  <tr>
    <th>".$ser." не беше намерено!</th>
  </tr>
</table>

";

return $str;
}


echo "<a href='http://109.121.216.41:8022/settlemets/index.html'><button>Към търсачка</button></a>";

  $db = pg_connect("host=localhost port=5433 dbname=Bulgaria2 user=postgres password=");


   $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
   $reader->setReadDataOnly(true);
   $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_obl.xlsx");


   $arr1=$spreadsheet->getActiveSheet()->toArray();



   $query1 = 'INSERT INTO regions(name,id) VALUES';
   for ($i=1; $i < count($arr1); $i++) { 

  
    if(!$db)
    {
        echo "Изникна грешка по време на свързването с базата, моля опитайте отново";
        return;
    }
 
      $result = pg_query("SELECT * FROM regions WHERE id='".$arr1[$i][0]."' OR name='".$arr1[$i][2]."'"); 
      $res=FALSE;

      while ($row = pg_fetch_row($result)) {
   
         $res=TRUE;
       echo  htmlError("Вашият запис с id: '".$arr1[$i][0]."' не можа да бъде записан в базата regions, понеже съществува друг с това id или name");
     }
   
      if(!$res)
         {
      $query1=$query1."('".$arr1[$i][2]."','".$arr1[$i][0]."'),"; 
         }  
   }

    

     if ('INSERT INTO regions(name,id) VALUES'!=$query1) {
      $query1=substr($query1,0,-1).';';
      pg_query($query1); 
     }
    


   $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
   $reader->setReadDataOnly(true);
   $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_obst.xlsx");


   $arr1=$spreadsheet->getActiveSheet()->toArray();

     $query1 = 'INSERT INTO municipalities(name,id,region_id,category) VALUES';
     for ($i=1; $i < count($arr1); $i++) { 



      if(!$db)
      {
          echo "Изникна грешка по време на свързването с базата, моля опитайте отново";
          return;
      }
   
        $result = pg_query("SELECT * FROM municipalities WHERE id='".$arr1[$i][0]."'"); 
        $res=FALSE;
  
        while ($row = pg_fetch_row($result)) {
     
           $res=TRUE;
         echo  htmlError("Вашият запис с id: '".$arr1[$i][0]."' не можа да бъде записан в базата municipalities, понеже съществува друг с това id или name");
       }
     
        if(!$res)
           {
        
            $query1=$query1."('".$arr1[$i][2]."','".$arr1[$i][0]."','".substr($arr1 [$i][0],0,3)."',".$arr1[$i][3]."),"; 
           }  
       }


      
       if ('INSERT INTO municipalities(name,id,region_id,category) VALUES'!=$query1) {
        $query1=substr($query1,0,-1).';';
        pg_query($query1); 
       }
       




 $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
   $reader->setReadDataOnly(true);
   $spreadsheet = $reader->load("C:/Users/Asus/Desktop/Ekatte/ek_atte.xlsx");


   $arr1=$spreadsheet->getActiveSheet()->toArray();



   $query1 = 'INSERT INTO allsettlements(ekatte,altitude,kind,category,municipality_id,name) VALUES';
     $res=FALSE;
   for ($i=2; $i < count($arr1); $i++) { 

      
        if(!$db)
        {
            echo "Изникна грешка по време на свързването с базата, моля опитайте отново";
            return;
        }
     
          $result = pg_query("SELECT * FROM allsettlements WHERE ekatte='".$arr1[$i][0]."'"); 
          $res=FALSE;

          while ($row = pg_fetch_row($result)) {
       
             $res=TRUE;
           echo  htmlError("Вашият запис с ekatte: '".$arr1[$i][0]."' не можа да бъде записан, понеже съществува друг с това ekatte");
         }
       
                $QR="SELECT * FROM allsettlements WHERE name='".$arr1[$i][2]."' AND kind='".$arr1[$i][1]."' AND municipaLIty_id='".$arr1[$i][4]."'";
        
          $result = pg_query($QR); 
       
          while ($row = pg_fetch_row($result)) {
            $res=TRUE;
          
            echo htmlError("Вашият запис с ekatte: '".$arr1[$i][0]."' не можа да бъде записан, понеже съществува друг с това име");
         }
         
          if(!$result)
          {
              echo "Изникна грешка по време на записването на данните, моля проверете какви са данните, които въвеждате";
              return;
          }
          

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
  
       pg_connect("host=localhost port=5433 dbname=Bulgaria2 user=postgres password=");
      if ($query1!='INSERT INTO allsettlements(ekatte,altitude,kind,category,municipality_id,name) VALUES') {
        $query1=substr($query1,0,-1);
        pg_query($query1);
      }
      $myfile = fopen("allsettlements.txt", "w") or die("Unable to open file!");



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

   $result = pg_query("SELECT COUNT(*) FROM regions");
   $countRegions=0;
   while ($row = pg_fetch_row($result)) {
    $countRegions=$row[0];
 }
 $result = pg_query("SELECT COUNT(*) FROM municipalities");
 $countMunicipalities=0;
 while ($row = pg_fetch_row($result)) {
  $countMunicipalities=$row[0];
}
$result = pg_query("SELECT COUNT(*) FROM allsettlements");
$countSettlements=0;
while ($row = pg_fetch_row($result)) {
 $countSettlements=$row[0];
}





$myfile = fopen("allsettlements.txt", "r+") or die("Unable to open file!");
$myfile1 = fopen("index.html", "r+") or die("Unable to open file!");
$text=fread($myfile,filesize("allsettlements.txt")).'var countsOfDb =["'.$countRegions.'","'.$countMunicipalities.'","'.$countSettlements.'"]; document.getElementById("cnt1").innerHTML=countsOfDb[0];document.getElementById("cnt2").innerHTML=countsOfDb[1];document.getElementById("cnt3").innerHTML=countsOfDb[2];';
$text1=fread($myfile1,filesize("index.html"));
$num= strrpos($text1, 'var countries = [', 0);
$num2=strrpos($text1, '];', $num);

$lat=substr($text1,$num,$num2-$num+3);

$res= str_replace($lat,$text,$text1);

 fclose($myfile);
 fclose($myfile1);
 $myfile22 = fopen("index.html", "w") or die("Unable to open file!");
 fwrite($myfile22,$res);
 fclose($myfile22);

 exec("toFile.php");


