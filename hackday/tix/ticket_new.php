<?php
    
require_once('config.php');

$title = 'New Ticket :: HackyHack Tix System';

$name  = isset($_COOKIES['name'])  ? $_COOKIES['name']  : null;
$email = isset($_COOKIES['email']) ? $_COOKIES['email'] : null;

include('views/ticket_new.html.php');