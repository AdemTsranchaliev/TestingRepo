<?php

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