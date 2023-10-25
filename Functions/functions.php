<?php
foreach (glob("./Functions/*.php") as $filename) {
    echo $filename!="./Functions/functions.php"?include_once($filename):"";
}