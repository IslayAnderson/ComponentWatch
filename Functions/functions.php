<?php
foreach (glob("./Functions/*.php") as $filename) {
    $filename!="./Functions/functions.php"?include_once($filename):"";
}