<?php
/**
 * The create manager controller for Ticket.
 *
 * @package tickets
 */
class TicketCreateManagerController extends ResourceCreateManagerController {
	public function getLanguageTopics() {
		return array('resource','tickets:default');
	}
}
