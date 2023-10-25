<?php
foreach (glob("./Classes/*.php") as $filename) {
    echo $filename!="./Classes/classes.php"?include_once($filename):"";
}