<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/publish.class.php';

class TicketPublishProcessor extends modResourcePublishProcessor
{
    public $permission = 'ticket_publish';

    public function fireAfterPublish() {
        parent::fireAfterPublish();
        $this->sendTicketMails();
    }

    /**
     * Call method for notify users about publish ticket
     */
    protected function sendTicketMails()
    {
        /** @var Tickets $Tickets */
        if ($Tickets = $this->modx->getService('Tickets')) {
            $Tickets->config['tplTicketEmailBcc'] = 'tpl.Tickets.ticket.email.bcc';
            $Tickets->config['tplTicketEmailSubscription'] = 'tpl.Tickets.ticket.email.subscription';
            $Tickets->config['tplAuthorEmailSubscription'] = 'tpl.Tickets.author.email.subscription';
            $Tickets->sendTicketMails($this->resource->toArray(),true);
        }
    }
}

return 'TicketPublishProcessor';