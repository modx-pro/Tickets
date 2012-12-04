<?php
/**
 * The update manager controller for TicketsSection.
 *
 * @package tickets
 */
class TicketsSectionUpdateManagerController extends ResourceUpdateManagerController {
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
		return $this->modx->hasPermission('edit_document');
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

		$this->addJavascript($mgrUrl.'assets/modext/util/datetime.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/element/modx.panel.tv.renders.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.grid.resource.security.local.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.tv.js');
		$this->addJavascript($mgrUrl.'assets/modext/widgets/resource/modx.panel.resource.js');
		$this->addJavascript($mgrUrl.'assets/modext/sections/resource/update.js');
		$this->addJavascript($ticketsJsUrl.'tickets.js');
		$this->addJavascript($ticketsJsUrl.'section/section.common.js');
		$this->addJavascript($ticketsJsUrl.'section/section.grid.js');
		$this->addJavascript($ticketsJsUrl.'comment/comments.common.js');
		$this->addJavascript($ticketsJsUrl.'comment/comments.grid.js');
		$this->addLastJavascript($ticketsJsUrl.'section/update.js');
		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		Tickets.assets_url = "'.$ticketsAssetsUrl.'";
		Tickets.connector_url = "'.$connectorUrl.'";
		MODx.config.publish_document = "'.$this->canPublish.'";
		MODx.onDocFormRender = "'.$this->onDocFormRender.'";
		MODx.ctx = "'.$this->ctx.'";
		Ext.onReady(function() {
			MODx.load({
				xtype: "tickets-page-section-update"
				,resource: "'.$this->resource->get('id').'"
				,record: '.$this->modx->toJSON($this->resourceArray).'
				,publish_document: "'.$this->canPublish.'"
				,preview_url: "'.$this->previewUrl.'"
				,locked: '.($this->locked ? 1 : 0).'
				,lockedText: "'.$this->lockedText.'"
				,canSave: '.($this->canSave ? 1 : 0).'
				,canEdit: '.($this->canEdit ? 1 : 0).'
				,canCreate: '.($this->canCreate ? 1 : 0).'
				,canDuplicate: '.($this->canDuplicate ? 1 : 0).'
				,canDelete: '.($this->canDelete ? 1 : 0).'
				,show_tvs: '.(!empty($this->tvCounts) ? 1 : 0).'
				,mode: "update"
			});
		});
		// ]]>
		</script>');
		/* load RTE */
		$this->loadRichTextEditor();
	}

	/**
	 * Used to set values on the resource record sent to the template for derivative classes
	 *
	 * @return void
	 */
	/*
	public function prepareResource() {
		$settings = $this->resource->getProperties('tickets');
		if (is_array($settings) && !empty($settings)) {
			foreach ($settings as $k => $v) {
				$this->resourceArray['setting_'.$k] = $v;
			}
		}
	}
	*/

}
