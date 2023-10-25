<?php
foreach (glob("*.php") as $filename) {
    include_once $filename;
}