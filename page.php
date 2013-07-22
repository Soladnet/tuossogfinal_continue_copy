<?php

/*
 * this is to fix hold URLS
 */
if (isset($_GET['view'])) {
    if ($_GET['view'] == "privacy") {
        header("Location: http://www.gossout.com/privacy");
    } else if ($_GET['view'] == "terms") {
        header("Location: http://www.gossout.com/terms");
    } else {
        header("Location: http://www.gossout.com/");
    }
} else {
    header("Location: http://www.gossout.com/index");
}
?>
