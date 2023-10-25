<?php
foreach (parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/.env') as $key=>$value){
    $_ENV[$key]=$value;
}