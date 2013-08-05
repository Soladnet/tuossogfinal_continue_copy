<?php

include '../Config.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
header("Content-type: text/plain");
header("Content-Disposition: attachment; filename=sitemap.xml");
$mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
if ($mysql->connect_errno > 0) {
    throw new Exception("Connection to server failed!");
} else {
    echo '<?xml version="1.0" encoding="UTF-8"?>
    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    ';
    $sql = "SELECT unique_name FROM community WHERE `type`='Public'";
    if ($result = $mysql->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<url><loc>http://www.gossout.com/$row[unique_name]</loc></url>
                        ";
            }
        }
    }
    $sql = "SELECT username FROM user_personal_info";
    if ($result = $mysql->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<url><loc>http://www.gossout.com/user/$row[username]</loc></url>
                        ";
            }
        }
    }
    $sql = "SELECT p.id,c.unique_name FROM post as p JOIN community as c ON p.community_id=c.id WHERE c.`type`='Public'";
    if ($result = $mysql->query($sql)) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<url><loc>http://www.gossout.com/$row[unique_name]/$row[id]</loc></url>
                        ";
            }
        }
    }
    echo '</urlset>';
}
?>
    