<?php

class TicketsSectionUpdateManagerController extends ResourceUpdateManagerController
{
    /** @var  TicketsSection $resource */
    public $resource;


    /**
     * Returns language topics
     *
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('resource', 'user', 'tickets:default');
    }


    /**
     * Register custom CSS/JS for the page
     *
     * @return void
     */
    public function loadCustomCssJs()
    {
        $html = $this->head['html'];
        parent::loadCustomCssJs();
        $this->head['html'] = $html;

        if (is_null($this->resourceArray['properties'])) {
            $this->resourceArray['properties'] = array();
        }
        $this->resourceArray['properties']['tickets'] = $this->resource->getProperties('tickets');
        $this->resourceArray['properties']['ratings'] = $this->resource->getProperties('ratings');
        $this->resourceArray['syncsite'] = (bool)$this->resource->getProperties('syncsite');

        /** @var Tickets $Tickets */
        $Tickets = $this->modx->getService('Tickets');
        $Tickets->loadManagerFiles($this, array(
            'config' => true,
            'utils' => true,
            'css' => true,
            'section' => true,
            'subscribe' => true,
            'comments' => true,
        ));
        $this->addLastJavascript($Tickets->config['jsUrl'] . 'mgr/section/update.js');
        $this->addLastJavascript($Tickets->config['jsUrl'] . 'mgr/misc/strftime-min-1.3.js');

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
        MODx.config.publish_document = ' . (int)$this->canPublish . ';
        MODx.onDocFormRender = "' . $this->onDocFormRender . '";
        MODx.ctx = "' . $this->ctx . '";
        Ext.onReady(function() {
            MODx.load(' . json_encode($ready) . ');
        });
        // ]]>
        </script>');

        // load RTE
        $this->loadRichTextEditor();
    }

}
