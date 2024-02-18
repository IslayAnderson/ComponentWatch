<?php
foreach (glob("./Functions/*/*.php") as $filename) {
    include_once($filename);
}
foreach (glob("./Functions/*.php") as $filename) {
    $filename!="./Functions/functions.php"?include_once($filename):"";
}