<?php

$xpdo_meta_map = array (
  'modResource' => 
  array (
    0 => 'TicketsSection',
    1 => 'Ticket',
  ),
  'xPDOSimpleObject' => 
  array (
    0 => 'TicketComment',
    1 => 'TicketThread',
    2 => 'TicketQueue',
    3 => 'TicketFile',
  ),
  'xPDOObject' => 
  array (
    0 => 'TicketVote',
    1 => 'TicketStar',
    2 => 'TicketView',
    3 => 'TicketAuthor',
    4 => 'TicketAuthorAction',
  ),
);
$this->map['modUser']['composites']['AuthorProfile'] = array(
  'class' => 'TicketAuthor',
  'local' => 'id',
  'foreign' => 'id',
  'cardinality' => 'one',
  'owner' => 'local',
);