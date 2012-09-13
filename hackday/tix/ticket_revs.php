<?php
    
require_once('config.php');

$title = 'Edit Ticket :: HackyHack Tix System';

$ticket =  Ticket::find($_GET['id']);
$rev = $ticket->lastRev();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $rev = new Ticketrev();
    $rev->ticket_id = $ticket->id;
    $rev->summary = $_POST['ticket']['summary'];
    $rev->description = $_POST['ticket']['description'];
    $rev->created_date = date('Y-m-d H:i:s');
    $rev->save();
    
    if(isset($_POST['assignee']['email']) && $_POST['assignee']['email']) {
        $assignee = User::find('first', array(
            'conditions' => array(
                'email' => $_POST['assignee']['email']
            )
        ));
        if(!$assignee) {
            $assignee = new User();
            $assignee->name = $_POST['assignee']['name'];
            $assignee->email = $_POST['assignee']['email'];
            $assignee->save();
        }
        $rev->assignee_id = $assignee->id;
        $rev->save();
    }
    
    header("Status: 201 Created");
    include('views/ticket.html.php');
    
} else {

    include('views/ticket.html.php');
    // include('views/ticket_revs.html.php');

}
