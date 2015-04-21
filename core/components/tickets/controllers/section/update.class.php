<?php

/**
 * The update manager controller for TicketsSection.
 *
 * @package tickets
 */
class TicketsSectionUpdateManagerController extends ResourceUpdateManagerController {
	/** @var  TicketsSection $resource */
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
		return $this->modx->hasPermission('edit_document');
	}

	/**
	 * Register custom CSS/JS for the page
	 * @return void
	 */
	public function loadCustomCssJs() {
		$mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);

		/** @var Tickets $Tickets */
		$Tickets = $this->modx->getService('Tickets');

		$ticketsAssetsUrl = $Tickets->config['assetsUrl'];
		$connectorUrl = $Tickets->config['connectorUrl'];
		$ticketsCssUrl = $Tickets->config['cssUrl'] . 'mgr/';
		$ticketsJsUrl = $Tickets->config['jsUrl'] . 'mgr/';

		$this->resourceArray['properties'] = array(
			'tickets' => $this->resource->getProperties('tickets')
		);
		$this->resourceArray['syncsite'] = 0;

		$this->addCss($ticketsCssUrl . 'tickets.css');
		$this->addCss($ticketsCssUrl . 'bootstrap.buttons.css');
		if (!$Tickets->systemVersion()) {
			$this->addCss($ticketsCssUrl . 'font-awesome.min.css');
		}

		$this->addJavascript($mgrUrl . 'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl . 'assets/modext/sections/resource/update.js');
		$this->addJavascript($ticketsJsUrl . 'tickets.js');
		$this->addLastJavascript($ticketsJsUrl . 'misc/utils.js');
		$this->addLastJavascript($ticketsJsUrl . 'misc/combos.js');
		$this->addLastJavascript($ticketsJsUrl . 'section/section.common.js');
		$this->addLastJavascript($ticketsJsUrl . 'section/section.grid.js');
		$this->addLastJavascript($ticketsJsUrl . 'comment/comments.common.js');
		$this->addLastJavascript($ticketsJsUrl . 'comment/comments.grid.js');
		$this->addLastJavascript($ticketsJsUrl . 'section/update.js');

		$config = array(
			'assets_url' => $ticketsAssetsUrl,
			'connector_url' => $connectorUrl,
		);
		$ready = array(
			'xtype' => 'tickets-page-section-update',
			'resource' => $this->resource->get('id'),
			'record' => $this->resourceArray,
			'publish_document' => (int)$this->canPublish,
			'preview_url' => $this->previewUrl,
			'locked' => (int)$this->locked,
			'lockedText' => $this->lockedText,
			'canSave' => (int)$this->canSave,
			'canEdit' => (int)$this->canEdit,
			'canCreate' => (int)$this->canCreate,
			'canDuplicate' => (int)$this->canDuplicate,
			'canDelete' => (int)$this->canDelete,
			'show_tvs' => (int)!empty($this->tvCounts),
			'mode' => 'update',
		);

		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		Tickets.config = ' . $this->modx->toJSON($config) . ';
		MODx.config.publish_document = ' . (int)$this->canPublish . ';
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
