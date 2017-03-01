<?php

class TicketsSectionCreateManagerController extends ResourceCreateManagerController
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
        return array('resource', 'tickets:default');
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
        $this->resourceArray['syncsite'] = 0;

        /** @var Tickets $Tickets */
        $Tickets = $this->modx->getService('Tickets');
        $Tickets->loadManagerFiles($this, array(
            'config' => true,
            'utils' => true,
            'css' => true,
            'section' => true,
        ));
        $this->addLastJavascript($Tickets->config['jsUrl'] . 'mgr/section/create.js');

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
            MODx.load(' . json_encode($ready) . ');
        });
        // ]]>
        </script>');

        // load RTE
        $this->loadRichTextEditor();
    }

}
