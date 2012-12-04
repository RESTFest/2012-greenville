<?php
    
require_once('config.php');

$title = 'Edit Ticket :: HackyHack Tix System';

$ticket =  Ticket::find($_GET['id']);
$rev = $ticket->lastRev();

include('views/ticket_edit.html.php');