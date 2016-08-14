<?php

/**
 * @property int $id
 */
class TicketView extends xPDOObject
{

    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $new = $this->isNew();
        $parent = parent::save($cacheFlag);

        if ($new) {
            if ($uid = $this->get('uid')) {
                /** @var TicketAuthor $profile */
                if ($profile = $this->xpdo->getObject('TicketAuthor', $uid)) {
                    $profile->addAction('view', $this->get('parent'), $this->get('parent'), $this->get('uid'));
                }
            } else {
                /** @var Ticket $ticket */
                if ($ticket = $this->xpdo->getObject('Ticket', $this->get('parent'))) {
                    /** @var TicketTotal $total */
                    if ($total = $ticket->getOne('Total')) {
                        $total->set('views', $total->get('views') + 1);
                        $total->save();
                    }
                    /** @var TicketsSection $section */
                    if ($section = $ticket->getOne('Parent')) {
                        if ($total = $section->getOne('Total')) {
                            $total->set('views', $total->get('views') + 1);
                            $total->save();
                        }
                    }
                }
            }
        }

        return $parent;
    }


    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        /** @var TicketAuthor $profile */
        if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('uid'))) {
            $profile->removeAction('view', $this->get('parent'), $this->get('uid'));
        }

        return parent::remove($ancestors);
    }

}