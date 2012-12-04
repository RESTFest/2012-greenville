<?php

include('config.php');

$title = 'Ticket :: HackyHack Tix System';

$ticket =  Ticket::find($_GET['id']);
$rev = $ticket->lastRev();

if(XML) {
    include('views/ticket.xml.php');
} else {
    include('views/ticket.html.php');
}