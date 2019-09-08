<?php


if (isset($_POST['a'])&&isset($_POST['b'])&&isset($_POST['c'])&&isset($_POST['n'])) {

$n=$_POST['n'];
$a=$_POST['a'];
$b=$_POST['b'];
$c=$_POST['c'];
$arr=[];
$arr2=[];
$counter=0;
for ($i=0; $i <=$n; $i++) { 
    $arr[$i]=0;
    $arr2[$i]=0;
}

for ($i=0; $i <=$n; $i+=$a) { 
   $arr[$i]=1;
}

for ($i=$n; $i>= 1;$i-=$b) { 

    if ($arr[$i]==1) {
        $arr[$i]=3;
    }
    else{
        $arr[$i]=2;
    }
}
for ($i=0; $i < $n; $i++) { 
    if ($arr[$i]==1) {
        if ($arr[$i+$c]==2||$arr[$i+$c]==3) {
            for ($j=$i+1; $j <= $i+$c; $j++) { 
                if ($arr2[$j]==0) {
                    $arr2[$j]=1;
                }
            }
        }
    }
    else if($arr[$i]==2)
    {
        if ($arr[$i+$c]==1||$arr[$i+$c]==3) {
            for ($j=$i+1; $j <= $i+$c; $j++) { 
                if ($arr2[$j]==0) {
                    $arr2[$j]=1;
                }
            }
        }
    }
    else if($arr[$i]==3)
    {
        if ($i+$c<=20) {
            if ($arr[$i+$c]==1||$arr[$i+$c]==2) {
                for ($j=$i+1; $j <= $i+$c; $j++) { 
                    if ($arr2[$j]==0) {
                        $arr2[$j]=1;
                    }
                }
            }
        }
      
    }  
}
for ($i=0; $i < $n; $i++) { 
    echo $arr2[$i];
    if ($arr2[$i]==1) {
        
        $counter++;
    }
}
echo '<br/>';
echo $n-$counter;
}
else{
echo '
<form action="/byrzaZadacha-pravi.php" method="post">
N:<br>
<input type="number" name="n">
<br>
A:<br>
<input type="number" name="a">
<br>
B:<br>
<input type="number" name="b" >
<br>
C:<br>
<input type="number" name="c" >
<br>
<br><br>
<input type="submit" value="Submit">
</form>';

}
