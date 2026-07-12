<?php

class TicketCommentGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'TicketComment';
    public $classKey = 'TicketComment';
    public $languageTopics = array('tickets:default');


    /**
     * @return array|string
     */
    public function cleanup()
    {
        $comment = $this->object->toArray();
        $comment['createdon'] = $this->formatDate($comment['createdon']);
        $comment['editedon'] = $this->formatDate($comment['editedon']);
        $comment['deletedon'] = $this->formatDate($comment['deletedon']);
        $comment['text'] = !empty($comment['raw'])
            ? html_entity_decode($comment['raw'])
            : html_entity_decode($comment['text']);

        return $this->success('', $comment);
    }


    /**
     * Format a datetime for the manager UI.
     * tickets.date_format uses strftime tokens (e.g. %d.%m.%y <small>%H:%M</small>).
     *
     * @param string $date
     *
     * @return string
     */
    public function formatDate($date = '')
    {
        if (empty($date) || $date == '0000-00-00 00:00:00') {
            return $this->modx->lexicon('no');
        }

        $ts = strtotime($date);
        if ($ts === false) {
            return $this->modx->lexicon('no');
        }

        $format = (string)$this->modx->getOption('tickets.date_format');

        return strtr($format, array(
            '%d' => date('d', $ts),
            '%e' => date('j', $ts),
            '%m' => date('m', $ts),
            '%Y' => date('Y', $ts),
            '%y' => date('y', $ts),
            '%H' => date('H', $ts),
            '%I' => date('h', $ts),
            '%M' => date('i', $ts),
            '%S' => date('s', $ts),
            '%p' => date('A', $ts),
            '%P' => date('a', $ts),
            '%B' => date('F', $ts),
            '%b' => date('M', $ts),
            '%A' => date('l', $ts),
            '%a' => date('D', $ts),
        ));
    }

}

return 'TicketCommentGetProcessor';