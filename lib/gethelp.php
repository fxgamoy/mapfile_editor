<?php
$link = pg_connect("host=localhost port=5432 dbname=dicomf user=postgres password=jtgacdt");
if($link && $_POST["obj"]!="" && $_POST["prop"]!="")
{
$query="select * from defmod where object='".$_POST["obj"]."' AND nom='".$_POST["prop"]."'";
//echo $query;
$result=pg_query($link,$query);
$line=pg_fetch_assoc($result);
//print_r($line);// $returnvalue;
echo htmlentities($line["helpfr"]);
}
?>