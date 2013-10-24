<?php
/**
 * The create manager controller for Ticket.
 *
 * @package tickets
 */
class TicketCreateManagerController extends ResourceCreateManagerController {

	/**
	 * Custom logic code here for setting placeholders, etc
	 * @param array $scriptProperties
	 * @return mixed
	 */
	public function process(array $scriptProperties = array()) {
		$this->scriptProperties['template'] = $this->modx->getOption('tickets.default_template',null,$this->modx->getOption('default_template',null,1), true);
		return parent::process($scriptProperties);
	}


	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource','tickets:default');
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
		$mgrUrl = $this->modx->getOption('manager_url',null,MODX_MANAGER_URL);

		$ticketsAssetsUrl = $this->modx->getOption('tickets.assets_url',null,$this->modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/tickets/');
		$connectorUrl = $ticketsAssetsUrl.'connector.php';
		$ticketsJsUrl = $ticketsAssetsUrl.'js/mgr/';

		$this->resourceArray['disable_jevix'] = $this->modx->getOption('tickets.disable_jevix_default', null, "false");
		$this->resourceArray['process_tags'] = $this->modx->getOption('tickets.process_tags_default', null, "false");

		$this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl.'assets/modext/sections/resource/create.js');
		$this->addJavascript($ticketsJsUrl.'tickets.js');
		$this->addLastJavascript($ticketsJsUrl.'ticket/widgets.js');
		$this->addLastJavascript($ticketsJsUrl.'ticket/create.js');
		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		Tickets.config = {
			assets_url: "'.$ticketsAssetsUrl.'"
			,connector_url: "'.$connectorUrl.'"
		}
		MODx.config.publish_document = "'.$this->canPublish.'";
		MODx.config.default_template = '.$this->modx->getOption('tickets.default_template', null, $this->modx->getOption('default_template'), true).';
		MODx.onDocFormRender = "'.$this->onDocFormRender.'";
		MODx.ctx = "'.$this->ctx.'";
		Ext.onReady(function() {
			MODx.load({
				xtype: "tickets-page-ticket-create"
				,record: '.$this->modx->toJSON($this->resourceArray).'
				,publish_document: "'.$this->canPublish.'"
				,canSave: "'.($this->modx->hasPermission('save_document') ? 1 : 0).'"
				,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
				,mode: "create"
			});
		});
		// ]]>
		</script>');

		$this->loadRichTextEditor();
	}

}
