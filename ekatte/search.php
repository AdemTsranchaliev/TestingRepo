<?php
  function readFromDb($query)
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
    
    $res=0;
    while ($row =pg_fetch_row($result) ) {
     
       $res=1;
     }
     
     if($res==1)
     {
         return '1';
     }
     else 
     {
        return '2';
     }

  }
if(isset($_POST['search']))
{
    $res=$_POST['search'];
    
    $f1= strrpos($res, '(', 0);
    $settlement=trim(substr($res,0,$f1));
    if ($settlement=='') {
       echo htmlError($res);
       return;
    }
     $f2= strrpos($res, 'общ.', 0)+7;
    $municipaty=trim(substr($res,$f2,strrpos($res, 'обл', 0)-$f2));
    if ($municipaty=='') {
        echo htmlError($res);
        return;
     }
    $f3= strrpos($res, 'обл.', 0)+7;
    $region=trim(substr($res,$f3));
    if ($region=='') {
        echo htmlError($res);
        return;
     }
  $result=  readFromDb("SELECT allsettlements.name,municipalities.name,regions.name from allsettlements join municipalities on (allsettlements.municipality_id=municipalities.id) join regions on (municipalities.region_id=regions.id) WHERE allsettlements.name='".$settlement."' and municipalities.name='".$municipaty."' AND regions.name='".$region."'");
  
  if ($result=='1') {
     echo htmlRet($region,$municipaty,$settlement);
  }
  else
  {
     echo htmlError($res. ' не беше намерен');
  }
  
}

function htmlError($ser)
{
    $str="
<!DOCTYPE html>
<html>
<head>
<style>

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
</style>
</head>
<body>

<h2>Вашето търсене</h2>

<table>
  <tr>
    <th>".$ser." не беше намерено!</th>
  </tr>
</table>
<a href='http://localhost:8022/settlemets/index.html'><button>Ново търсене</button></a>
</body>
</html>";

return $str;
}

function htmlRet($reg,$muni,$settle)
{
$str="
<!DOCTYPE html>
<html>
<head>
<style>

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
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>

<h2>Вашето търсене</h2>

<table>
  <tr>
    <th>Град/Село</th>
    <th>Община</th>
    <th>Област</th>
  </tr>
  <tr>
    <td>".$settle."</td>
    <td>".$muni."</td>
    <td>".$reg."</td>
  </tr>
</table>
<a href='http://localhost:8022/settlemets/index.html'><button>Ново търсене</button></a>
</body>
</html>";

echo $str;
}

