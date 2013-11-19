<?php

require_once "phpExcelReader/simplexlsx.class.php";
$xlsx = new SimpleXLSX("sample_users.xls");
$countVal = count($xlsx->rows());
    print_r($xlsx->rows());
    echo '<br>';

?>