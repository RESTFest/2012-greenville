<?php

class Ticket extends ActiveRecord\Model
{

	static $table_name = 'ticket';
    
    static $belongs_to = array(
        array('author', 'class_name' => 'User', 'foreign_key' => 'author_id')
    );
    
    static $has_many = array(
        array('ticketrevs')
    );
    
    public static function latest($offset = 0, $limit = 10) {
        return self::find('all', array(
            'order' => 'created_date desc',
            'offset' => $offset,
            'limit' => $limit
        ));
    }
    
    public function lastRev() {
        return Ticketrev::find('first', array(
            'order' => 'created_date desc',
            'conditions' => array(
                'ticket_id' => $this->id
            )
        ));
    }

}