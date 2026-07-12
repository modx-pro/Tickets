<?php

class TicketTotal extends xPDOObject
{
    /**
     * Virtual fields stored for each class.
     *
     * @param string $class
     *
     * @return array
     */
    public static function fieldsFor($class)
    {
        switch ($class) {
            case 'TicketsSection':
                return array(
                    'comments',
                    'views',
                    'tickets',
                    'stars',
                    'rating',
                    'rating_plus',
                    'rating_minus',
                );
            case 'TicketComment':
                return array(
                    'stars',
                    'rating',
                );
            case 'Ticket':
            default:
                return array(
                    'comments',
                    'views',
                    'stars',
                    'rating',
                    'rating_plus',
                    'rating_minus',
                );
        }
    }


    /**
     * Zero defaults when a totals row cannot be created.
     *
     * @param string $class
     *
     * @return array
     */
    public static function emptyValues($class)
    {
        $values = array();
        foreach (self::fieldsFor($class) as $field) {
            $values[$field] = 0;
        }

        return $values;
    }


    /**
     * Create stub totals row, fill aggregates, persist dirty values.
     * Fail-closed on stub insert; log (but still return in-memory values) if the second save fails.
     *
     * @param xPDO $xpdo
     * @param int $id
     * @param string $class
     *
     * @return array
     */
    public static function createAndFetch(xPDO $xpdo, $id, $class)
    {
        /** @var TicketTotal $total */
        $total = $xpdo->newObject('TicketTotal');
        $total->fromArray(array(
            'id' => (int)$id,
            'class' => $class,
        ), '', true, true);

        if (!$total->save()) {
            $xpdo->log(xPDO::LOG_LEVEL_ERROR,
                '[Tickets] Could not create TicketTotal stub for ' . $class . '#' . $id);

            return self::emptyValues($class);
        }

        $total->fetchValues();
        if ($total->isDirty() && !$total->save()) {
            $xpdo->log(xPDO::LOG_LEVEL_ERROR,
                '[Tickets] Could not save TicketTotal aggregates for ' . $class . '#' . $id);
        }

        return $total->get(self::fieldsFor($class));
    }


    /**
     * Get values from database
     */
    public function fetchValues()
    {
        $values = array();

        $id = $this->get('id');
        $class = $this->get('class');
        switch ($class) {
            case 'Ticket':
                /** @var Ticket $ticket */
                if ($ticket = $this->xpdo->getObject('Ticket', $id)) {
                    $rating = $ticket->getRating();
                    $values = array(
                        'comments' => $ticket->getCommentsCount(),
                        'views' => $ticket->getViewsCount(),
                        'stars' => $ticket->getStarsCount(),
                        'rating' => $rating['rating'],
                        'rating_plus' => $rating['rating_plus'],
                        'rating_minus' => $rating['rating_minus'],
                    );
                }
                break;
            case 'TicketComment':
                if ($comment = $this->xpdo->getObject('TicketComment', $id)) {
                    $values = array(
                        'stars' => $this->xpdo->getCount('TicketStar', array('id' => $id, 'class' => 'TicketComment')),
                        'rating' => $comment->get('rating'),
                    );
                }
                break;
            case 'TicketsSection':
                /** @var TicketsSection $section */
                if ($section = $this->xpdo->getObject('TicketsSection', $id)) {
                    $rating = $section->getRating();
                    $values = array(
                        'tickets' => $section->getTicketsCount(),
                        'comments' => $section->getCommentsCount(),
                        'views' => $section->getViewsCount(),
                        'stars' => $section->getStarsCount(),
                        'rating' => $rating['rating'],
                        'rating_plus' => $rating['rating_plus'],
                        'rating_minus' => $rating['rating_minus'],
                    );
                }
                break;
        }
        $this->fromArray($values);

        return $values;
    }

}
