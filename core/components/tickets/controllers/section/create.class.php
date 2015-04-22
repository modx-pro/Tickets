<?php

/**
 * The create manager controller for TicketsSection.
 *
 * @package tickets
 */
class TicketsSectionCreateManagerController extends ResourceCreateManagerController {
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
		return $this->modx->hasPermission('new_document');
	}


	/**
	 * Register custom CSS/JS for the page
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->resourceArray['properties'] = array(
			'tickets' => $this->resource->getProperties('tickets')
		);
		$this->resourceArray['syncsite'] = 0;

		/** @var Tickets $Tickets */
		$Tickets = $this->modx->getService('Tickets');
		$ticketsJsUrl = $Tickets->config['jsUrl'] . 'mgr/';
		$mgrUrl = $this->modx->getOption('manager_url', null, MODX_MANAGER_URL);

		$Tickets->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			//'css' => true,
			'section' => true,
		));
		$this->addJavascript($mgrUrl . 'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl . 'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl . 'assets/modext/sections/resource/create.js');
		$this->addLastJavascript($ticketsJsUrl . 'section/create.js');

		$ready = array(
			'xtype' => 'tickets-page-section-create',
			'record' => $this->resourceArray,
			'publish_document' => (int)$this->canPublish,
			'canSave' => (int)$this->canSave,
			'show_tvs' => (int)!empty($this->tvCounts),
			'mode' => 'create',
		);

		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
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
