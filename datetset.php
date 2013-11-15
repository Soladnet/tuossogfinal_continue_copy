<?php
$date = new DateTime('2000-01-20');
$date->sub(new DateInterval('P2D'));
$date->format('Y-m-d');
?>