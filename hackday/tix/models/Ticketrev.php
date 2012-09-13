<?php

class Ticketrev extends ActiveRecord\Model
{

	static $table_name = 'ticket_rev';
    
    static $belongs_to = array(
        array('assignee', 'class_name' => 'User'),
        array('ticket')
    );

}