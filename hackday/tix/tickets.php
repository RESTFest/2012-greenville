<?php
    
include('config.php');

$title = 'Tickets :: HackyHack Tix System';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $author = User::find('first', array(
        'conditions' => array(
            'email' => $_POST['author']['email']
        )
    ));
    if(!$author) {
        $author = new User();
        $author->name = $_POST['author']['name'];
        $author->email = $_POST['author']['email'];
        $author->save();
        setcookie('name', $author->name, -1, '/');
        setcookie('email', $author->email, -1, '/');
    }
    
    $ticket = new Ticket();
    $ticket->author_id = $author->id;
    $ticket->created_date = date('Y-m-d H:i:s');
    $ticket->save();
    
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
    if(XML) {
        include('views/ticket.xml.php');
    } else {
        include('views/ticket.html.php');
    }
    
} else {

    $tickets =  Ticket::latest();

    if(XML) {
        include('views/tickets.xml.php');
    } else {
        include('views/tickets.html.php');
    }
    
}