<?php
    
include('config.php');

$title = 'HackyHack Tix System';

if(XML) {
    include('views/index.xml.php');
} else {
    include('views/index.html.php');
}