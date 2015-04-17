<?php

/**
 * The create manager controller for Ticket.
 *
 * @package tickets
 */
class TicketCreateManagerController extends ResourceCreateManagerController {
	/** @var TicketsSection $resource */
	public $parent;
	/** @var Ticket $resource */
	public $resource;


	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource', 'tickets:default');
	}


	/**
	 * Check for any permissions or requirements to load page
	 * @return bool
	 */
	public function checkPermissions() {
		return $this->modx->hasPermission('new_document');
	}


	/**
	 * Return the default template for this resource
	 *
	 * @return int
	 */
	public function getDefaultTemplate() {
		$properties = $this->parent->getProperties();

		return $properties['template'];
	}


	/**
	 * Register custom CSS/JS for the page
	 * @return void
	 */
	public function loadCustomCssJs() {
		$mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);

		$ticketsAssetsUrl = $this->modx->getOption('tickets.assets_url', null, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/tickets/');
		$connectorUrl = $ticketsAssetsUrl . 'connector.php';
		$ticketsJsUrl = $ticketsAssetsUrl . 'js/mgr/';

		$properties = $this->parent->getProperties();
		$this->resourceArray = array_merge($this->resourceArray, $properties);
		$this->resourceArray['properties'] = $properties;

		$this->addJavascript($mgrUrl . 'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl . 'assets/modext/sections/resource/create.js');
		$this->addJavascript($ticketsJsUrl . 'tickets.js');
		$this->addLastJavascript($ticketsJsUrl . 'misc/utils.js');
		$this->addLastJavascript($ticketsJsUrl . 'ticket/ticket.common.js');
		$this->addLastJavascript($ticketsJsUrl . 'ticket/create.js');

		$config = array(
			'assets_url' => $ticketsAssetsUrl,
			'connector_url' => $connectorUrl,
		);
		$ready = array(
			'xtype' => 'tickets-page-ticket-create',
			'record' => $this->resourceArray,
			'publish_document' => (int)$this->canPublish,
			'canSave' => (int)$this->canSave,
			'show_tvs' => (int)!empty($this->tvCounts),
			'mode' => 'create',
		);

		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		Tickets.config = ' . $this->modx->toJSON($config) . ';
		MODx.config.publish_document = ' . (int)$this->canPublish . ';
		MODx.config.default_template = ' . $this->modx->getOption('tickets.default_template', null, $this->modx->getOption('default_template'), true) . ';
		MODx.onDocFormRender = "' . $this->onDocFormRender . '";
		MODx.ctx = "' . $this->ctx . '";
		Ext.onReady(function() {
			MODx.load(' . $this->modx->toJSON($ready) . ');
		});
		// ]]>
		</script>');

		$this->loadRichTextEditor();
	}

}
