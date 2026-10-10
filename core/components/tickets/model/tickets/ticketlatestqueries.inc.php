<?php

/**
 * Query configs for TicketLatest comments modes.
 *
 * @package tickets
 */

if (!function_exists('tickets_ticket_latest_thread_first')) {
    /**
     * Latest comments: lead with TicketThread ordered by denormalized comment_time.
     *
     * pdoFetch primary alias equals the class name (TicketThread). Join alias Thread is used
     * when TicketComment is primary (&user path and tickets action).
     *
     * @param string $ticketSelect
     * @param string $commentSelect
     * @return array{class:string,where:array,innerJoin:array,select:array,groupby:string}
     */
    function tickets_ticket_latest_thread_first($ticketSelect, $commentSelect)
    {
        return array(
            'class' => 'TicketThread',
            'where' => array(
                'TicketThread.deleted' => 0,
                'TicketThread.comment_last:>' => 0,
            ),
            'innerJoin' => array(
                'Ticket' => array(
                    'class' => 'Ticket',
                    'on' => '`Ticket`.`id` = `TicketThread`.`resource`',
                ),
                'TicketComment' => array(
                    'class' => 'TicketComment',
                    'on' => '`TicketComment`.`id` = `TicketThread`.`comment_last` AND `TicketComment`.`deleted` = 0',
                ),
            ),
            'select' => array(
                'TicketThread' => '`TicketThread`.`comments`',
                'TicketComment' => $commentSelect,
                'Ticket' => $ticketSelect,
            ),
            'groupby' => '',
        );
    }
}

if (!function_exists('tickets_ticket_latest_user_comments')) {
    /**
     * Comments filtered by &user=: TicketComment primary, Thread join alias.
     *
     * @param string $ticketSelect
     * @param string $commentSelect
     * @return array{class:string,where:array,innerJoin:array,select:array,groupby:string}
     */
    function tickets_ticket_latest_user_comments($ticketSelect, $commentSelect)
    {
        return array(
            'class' => 'TicketComment',
            'where' => array(
                'TicketComment.deleted' => 0,
            ),
            'innerJoin' => array(
                'Thread' => array(
                    'class' => 'TicketThread',
                    'on' => '`TicketComment`.`thread` = `Thread`.`id` AND `Thread`.`deleted` = 0',
                ),
                'Ticket' => array(
                    'class' => 'Ticket',
                    'on' => '`Ticket`.`id` = `Thread`.`resource`',
                ),
            ),
            'select' => array(
                'TicketComment' => $commentSelect,
                'Ticket' => $ticketSelect,
                'Thread' => '`Thread`.`comments`',
            ),
            'groupby' => '`TicketComment`.`id`',
        );
    }
}
