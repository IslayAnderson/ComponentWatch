<h1>Reporters</h1>

<?php

//author: Islay
//music: horsegiirL at the boiler room live from earth
//tea: Yorkshire


$reporters = new Reporters('example');
$table = $reporters->get_table();

//music: Sub Focus x Wilkinson at Corfe Castle 

foreach($table['site'] as $site){
    include $_SERVER["DOCUMENT_ROOT"].'/partials/reporters_repeater.php';
}