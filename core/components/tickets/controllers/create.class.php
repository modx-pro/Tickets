<?php
/**
 * The create manager controller for Tickets.
 *
 * @package tickets
 */
class TicketCreateManagerController extends ResourceCreateManagerController {
	public function getLanguageTopics() {
		return array('resource','tickets:default');
	}
}
