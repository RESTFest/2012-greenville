<?php

class User extends ActiveRecord\Model
{

	static $table_name = 'user';
    
    static $has_many = array(
        array('tickets'),
        array('ticketrevs')
    );

}