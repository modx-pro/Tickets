<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/unpublish.class.php';

class TicketUnPublishProcessor extends modResourceUnPublishProcessor {
	public $permission = 'ticket_publish';

}

return 'TicketUnPublishProcessor';