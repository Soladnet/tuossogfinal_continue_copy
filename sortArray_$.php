<?php

class SortMultiArray {

    var $ResultArray;

    /**
     * SortMultiArray() -- Constructor
     * sort a multi-dimmensional array
     * @param Array InArray - the array (ex. array(array("key1"=>"value","key2"=>"value",...),array("key1"=>"value","key2"=>"value",...),...)
     * @param int Column - the column number to sort by (can also be associative key name)
     * @param int SortType - 0 for ascending, 1 for descending 
     * @param int Flag - sorting type , check function sort() (optional)
     */
    function SortMultiArray($InArray, $Column, $SortType, $Flag = SORT_REGULAR) {
        //initialize variables
        $TmpArray = array();
        $ResultArray = array();
        $Index = 0;

        //create a temporary array with the column that needs 
        //sorting from the multi-dimmensional associative array
        foreach ($InArray as $Value)
            $TmpArray[$Index++] = $Value[$Column];

        //sort the temporary array
        ($SortType) ? arsort($TmpArray, $Flag) : asort($TmpArray, $Flag);

        $Index = 0;
        //create new sorted array
        while (list ($key, $val) = each($TmpArray))
            $this->ResultArray[$Index++] = $InArray[$key];
    }

    /**
     * GetSortedArray()
     * fetch the sorted array
     * @return Array - multi-dimensional array sorted
     */

    /**
     * fetch the sorted array
     * @param int start
     * @param int limit
     * @return Array - multi-dimensional array sorted
     */
    function GetSortedArray($lastupdate = FALSE) {
        $arr = array();
        for ($i = 0; $i < count($this->ResultArray); $i++) {
            if ($lastupdate) {
                $val = $this->ResultArray[$i];
                if ($val['type'] == "frq" || $val['type'] == "TW") {
//                    echo json_encode(array("val" => $val['time'], "lastupdate" => $lastupdate));
//                    exit;
                    if ($lastupdate <= $val['time']) {
                        $arr[] = $val;
                    }
                } else {
                    $arr[] = $this->ResultArray[$i];
                }
            } else {
                $arr[] = $this->ResultArray[$i];
            }
        }
        return $arr;
//        return $this->ResultArray;
    }

}

?>