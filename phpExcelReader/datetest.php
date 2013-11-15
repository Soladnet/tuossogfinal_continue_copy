<?php
$date = new DateTime('2001-04-30');
$interval = new DateInterval('P1M');

$date->sub($interval);
echo $date->format('Y-m-d') . "\n";

$date->sub($interval);
echo $date->format('Y-m-d') . "\n";
?>