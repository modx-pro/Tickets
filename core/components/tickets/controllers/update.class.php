<?php
/**
 * The update manager controller for Tickets.
 *
 * @package tickets
 */
class TicketUpdateManagerController extends ResourceUpdateManagerController {
	public function getLanguageTopics() {
		return array('resource','tickets:default');
	}
}
