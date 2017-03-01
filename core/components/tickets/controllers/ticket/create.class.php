<?php

class TicketCreateManagerController extends ResourceCreateManagerController
{
    /** @var TicketsSection $resource */
    public $parent;
    /** @var Ticket $resource */
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
     * Return the default template for this resource
     *
     * @return int
     */
    public function getDefaultTemplate()
    {
        $properties = $this->parent->getProperties();

        return $properties['template'];
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
        $properties = $this->parent->getProperties('tickets');
        $this->resourceArray = array_merge($this->resourceArray, $properties);
        $this->resourceArray['properties']['tickets'] = $properties;

        /** @var Tickets $Tickets */
        $Tickets = $this->modx->getService('Tickets');
        $Tickets->loadManagerFiles($this, array(
            'config' => true,
            'utils' => true,
            'css' => true,
            'ticket' => true,
        ));
        $this->addLastJavascript($Tickets->config['jsUrl'] . 'mgr/ticket/create.js');

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
        MODx.config.publish_document = ' . (int)$this->canPublish . ';
        MODx.config.default_template = ' . $this->modx->getOption('tickets.default_template', null,
                $this->modx->getOption('default_template'), true) . ';
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
