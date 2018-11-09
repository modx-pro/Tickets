<?php

/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/tickets/processors/mgr/ticket/create.class.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'components/tickets/processors/mgr/ticket/update.class.php';

class Ticket extends modResource
{
    public $showInContextMenu = false;
    public $allowChildrenResources = false;
    private $_oldAuthor = 0;


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return modAccessibleObject|null|object
     */
    public static function load(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true)
    {
        if (!is_object($criteria)) {
            $criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        /** @noinspection PhpParamsInspection */
        $xpdo->addDerivativeCriteria($className, $criteria);

        return parent::load($xpdo, $className, $criteria, $cacheFlag);
    }


    /**
     * @param xPDO $xpdo
     * @param string $className
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return array
     */
    public static function loadCollection(xPDO & $xpdo, $className, $criteria = null, $cacheFlag = true)
    {
        if (!is_object($criteria)) {
            $criteria = $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        /** @noinspection PhpParamsInspection */
        $xpdo->addDerivativeCriteria($className, $criteria);

        return parent::loadCollection($xpdo, $className, $criteria, $cacheFlag);
    }


    /**
     * @param xPDO $modx
     *
     * @return string
     */
    public static function getControllerPath(xPDO &$modx)
    {
        return $modx->getOption('tickets.core_path', null,
            $modx->getOption('core_path') . 'components/tickets/') . 'controllers/ticket/';
    }


    /**
     * @return array
     */
    public function getContextMenuText()
    {
        $this->xpdo->lexicon->load('tickets:default');

        return array(
            'text_create' => $this->xpdo->lexicon('tickets'),
            'text_create_here' => $this->xpdo->lexicon('ticket_create_here'),
        );
    }


    /**
     * @return null|string
     */
    public function getResourceTypeName()
    {
        $this->xpdo->lexicon->load('tickets:default');

        return $this->xpdo->lexicon('ticket');
    }


    /**
     * @param array|string $k
     * @param null $format
     * @param null $formatTemplate
     *
     * @return int|mixed|string
     */
    public function get($k, $format = null, $formatTemplate = null)
    {
        $fields = array('comments', 'views', 'stars', 'rating', 'date_ago');

        if (is_array($k)) {
            $values = array();
            foreach ($k as $v) {
                $values[$v] = $this->get($v, $format, $formatTemplate);
            }

            return $values;
        } else {
            switch ($k) {
                case 'comments':
                    $values = $this->_getVirtualFields();
                    $value = $values['comments'];
                    break;
                case 'views':
                    $values = $this->_getVirtualFields();
                    $value = $values['views'];
                    break;
                case 'stars':
                    $values = $this->_getVirtualFields();
                    $value = $values['stars'];
                    break;
                case 'rating':
                    $values = $this->_getVirtualFields();
                    $value = $values['rating'];
                    break;
                case 'date_ago':
                    $value = $this->getDateAgo();
                    break;
                default:
                    $value = parent::get($k, $format, $formatTemplate);
            }

            if (isset($this->_fieldMeta[$k]) && $this->_fieldMeta[$k]['phptype'] == 'string') {
                $properties = $this->getProperties();
                if (!$properties['process_tags'] && !in_array($k, $fields)) {
                    $value = str_replace(
                        array('[', ']', '`', '{', '}'),
                        array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
                        $value
                    );
                }
            }
        }

        return $value;
    }


    /**
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     *
     * @return array
     */
    public function toArray($keyPrefix = '', $rawValues = false, $excludeLazy = false, $includeRelated = false)
    {
        $fields = $this->_getVirtualFields();
        if (!empty($keyPrefix)) {
            foreach ($fields as $k => $v) {
                $fields[$keyPrefix . $k] = $v;
                unset($fields[$k]);
            }
        }

        $array = array_merge(
            parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated),
            $fields
        );

        return $array;
    }


    /**
     * @return string
     */
    public function process()
    {
        if ($this->privateweb && !$this->xpdo->hasPermission('ticket_view_private') && $id = $this->getOption('tickets.private_ticket_page')) {
            $this->xpdo->sendForward($id);
            die;
        } else {
            //$this->xpdo->setPlaceholders($this->_getVirtualFields(), 'ticket_');

            return parent::process();
        }
    }


    /**
     * @param array $options
     *
     * @return mixed
     */
    public function getContent(array $options = array())
    {
        $content = parent::get('content');
        $properties = $this->getProperties();

        if (!$properties['disable_jevix']) {
            $content = $this->Jevix($content, false);
        }
        if (!$properties['process_tags']) {
            $content = str_replace(
                array('[', ']', '`', '{', '}'),
                array('&#91;', '&#93;', '&#96;', '&#123;', '&#125;'),
                $content
            );
        }
        $content = preg_replace('/<cut(.*?)>/i', '<a name="cut"></a>', $content);

        return $content;
    }


    /**
     * Html filter and typograf
     *
     * @var mixed $text for processing
     * @var bool $replaceTags
     *
     * @return mixed Filtered text
     */
    function Jevix($text, $replaceTags = true)
    {
        /** @var Tickets $Tickets */
        if ($Tickets = $this->xpdo->getService('Tickets')) {
            return $Tickets->Jevix($text, 'Ticket', $replaceTags);
        }

        return 'Error on loading class "Tickets".';
    }


    /**
     * Generate intro text from content buy cutting text before tag <cut/>
     *
     * @param string $content Any text for processing, with tag <cut/>
     * @param boolean $jevix
     *
     * @return mixed $introtext
     */
    function getIntroText($content = null, $jevix = true)
    {
        if (empty($content)) {
            $content = parent::get('content');
        }
        $content = preg_replace('/<cut(.*?)>/i', '<cut/>', $content);

        if (!preg_match('/<cut\/>/', $content)) {
            $introtext = $content;
        } else {
            $tmp = explode('<cut/>', $content);
            $introtext = reset($tmp);
            if ($jevix) {
                $introtext = $this->Jevix($introtext);
            }
        }

        return $introtext;
    }


    /**
     * @param string $alias
     * @param null $criteria
     * @param bool $cacheFlag
     *
     * @return array
     */
    public function & getMany($alias, $criteria = null, $cacheFlag = true)
    {
        if ($alias == 'Files' || $alias == 'Votes') {
            $criteria = array('class' => $this->class_key);
        }

        return parent::getMany($alias, $criteria, $cacheFlag);
    }


    /**
     * @param mixed $obj
     * @param string $alias
     *
     * @return bool
     */
    public function addMany(& $obj, $alias = '')
    {
        $added = false;
        if (is_array($obj)) {
            foreach ($obj as $o) {
                /** @var xpdoObject $o */
                if (is_object($o)) {
                    $o->set('class', $this->class_key);
                    $added = parent::addMany($obj, $alias);
                }
            }

            return $added;
        } else {
            return parent::addMany($obj, $alias);
        }
    }


    /**
     * Shorthand for getting virtual Ticket fields
     *
     * @return array $array Array with virtual fields
     */
    protected function _getVirtualFields()
    {
        /** @var TicketTotal $total */
        if (!$total = $this->getOne('Total')) {
            $total = $this->xpdo->newObject('TicketTotal');
            $total->fromArray(array(
                'id' => $this->id,
                'class' => 'Ticket',
            ), '', true, true);
            $total->fetchValues();
            $total->save();
        }

        return $total->get(array(
            'comments',
            'views',
            'stars',
            'rating',
            'rating_plus',
            'rating_minus',
        ));
    }


    /**
     * Returns count of views of Ticket by users
     *
     * @return integer $count Total count of views
     */
    public function getViewsCount()
    {
        return $this->xpdo->getCount('TicketView', array('parent' => $this->id));
    }


    /**
     * Returns count of comments to Ticket
     *
     * @return integer $count Total count of comment
     */
    public function getCommentsCount()
    {
        $q = $this->xpdo->newQuery('TicketThread', array('name' => 'resource-' . $this->id));
        $q->leftJoin('TicketComment', 'TicketComment',
            "`TicketThread`.`id` = `TicketComment`.`thread` AND `TicketComment`.`published` = 1");
        $q->select('COUNT(`TicketComment`.`id`) as `comments`');

        $count = 0;
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->xpdo->startTime += microtime(true) - $tstart;
            $this->xpdo->executedQueries++;
            $count = (int)$q->stmt->fetchColumn();
        }

        return $count;
    }


    /**
     * Returns number of stars for Ticket
     *
     * @return integer
     */
    public function getStarsCount()
    {
        return $this->xpdo->getCount('TicketStar', array('id' => $this->id, 'class' => 'Ticket'));
    }


    /**
     * Return formatted date of ticket creation
     *
     * @return string
     */
    public function getDateAgo()
    {
        $createdon = parent::get('createdon');
        /** @var Tickets $Tickets */
        if ($Tickets = $this->xpdo->getService('Tickets')) {
            $createdon = $Tickets->dateFormat($createdon);
        }

        return $createdon;
    }


    /**
     * Returns vote of current user for this ticket
     *
     * @return int|mixed
     */
    public function getVote()
    {
        $q = $this->xpdo->newQuery('TicketVote');
        $q->where(array(
            'id' => $this->id,
            'createdby' => $this->xpdo->user->id,
            'class' => 'Ticket',
        ));
        $q->select('`value`');

        $vote = 0;
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->xpdo->startTime += microtime(true) - $tstart;
            $this->xpdo->executedQueries++;
            $vote = $q->stmt->fetchColumn();
        }

        return $vote;
    }


    /**
     * Get rating
     *
     * @return array
     */
    public function getRating()
    {
        $rating = array('rating' => 0, 'rating_plus' => 0, 'rating_minus' => 0);

        $q = $this->xpdo->newQuery('TicketVote');
        $q->innerJoin('Ticket', 'Ticket', 'Ticket.id = TicketVote.id');
        $q->where(array(
            'class' => 'Ticket',
            'id' => $this->id,
            'Ticket.deleted' => 0,
            'Ticket.published' => 1,
        ));
        $q->select('value');
        $tstart = microtime(true);
        if ($q->prepare() && $q->stmt->execute()) {
            $this->xpdo->startTime += microtime(true) - $tstart;
            $this->xpdo->executedQueries++;
            $rows = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($rows as $value) {
                $rating['rating'] += $value;
                if ($value > 0) {
                    $rating['rating_plus'] += $value;
                } elseif ($value < 0) {
                    $rating['rating_minus'] += abs($value);
                }
            }
        }

        return $rating;
    }


    /**
     * Build custom uri with respect to section settings
     *
     * @param string $alias
     *
     * @return string|bool
     */
    public function setUri($alias = '')
    {
        /*
        if (!$this->get('published')) {
            $this->set('uri', '');
            $this->set('uri_override', 0);
            return true;
        }
        */

        if (empty($alias)) {
            $alias = $this->get('alias');
        }
        /** @var TicketsSection $section */
        if ($section = $this->xpdo->getObject('TicketsSection', $this->get('parent'))) {
            $properties = $section->getProperties();
        } else {
            return false;
        }
        $template = $properties['uri'];
        if (empty($template) || strpos($template, '%') === false) {
            return false;
        }

        if ($this->get('pub_date')) {
            $date = $this->get('pub_date');
        } else {
            $date = $this->get('published')
                ? $this->get('publishedon')
                : $this->get('createdon');
        }
        $date = strtotime($date);

        $pls = array(
            'pl' => array('%y', '%m', '%d', '%id', '%alias', '%ext'),
            'vl' => array(
                date('y', $date),
                date('m', $date),
                date('d', $date),
                $this->get('id')
                    ? $this->get('id')
                    : '%id',
                $alias,
            ),
        );

        /** @var modContentType $contentType */
        if ($contentType = $this->xpdo->getObject('modContentType', $this->get('content_type'))) {
            $pls['vl'][] = $contentType->getExtension();
        } else {
            $pls['vl'][] = '';
        }

        $uri = rtrim($section->getAliasPath($section->get('alias')), '/') . '/' . str_replace($pls['pl'], $pls['vl'],
                $template);
        $this->set('uri', $uri);
        $this->set('uri_override', true);

        return $uri;
    }


    /**
     * Get the properties for the specific namespace for the Resource
     *
     * @param string $namespace
     *
     * @return array
     */
    public function getProperties($namespace = 'tickets')
    {
        $properties = parent::getProperties($namespace);

        // Convert old settings
        if (empty($this->reloadOnly)) {
            $flag = false;
            $tmp = array('disable_jevix', 'process_tags', 'rating');
            if ($old = parent::get('properties')) {
                foreach ($tmp as $v) {
                    if (array_key_exists($v, $old)) {
                        $properties[$v] = $old[$v];
                        $flag = true;
                        unset($old[$v]);
                    }
                }
                if ($flag) {
                    $old['tickets'] = $properties;
                    $this->set('properties', $old);
                    $this->save();
                }
            }
        }
        // --

        if (empty($properties)) {
            /** @var TicketsSection $parent */
            if (!$parent = $this->getOne('Parent')) {
                $parent = $this->xpdo->newObject('TicketsSection');
            }
            $default_properties = $parent->getProperties($namespace);
            if (!empty($default_properties)) {
                foreach ($default_properties as $key => $value) {
                    if (!isset($properties[$key])) {
                        $properties[$key] = $value;
                    } elseif ($properties[$key] === 'true') {
                        $properties[$key] = true;
                    } elseif ($properties[$key] === 'false') {
                        $properties[$key] = false;
                    } elseif (is_numeric($value) && $key == 'disable_jevix' || $key == 'process_tags') {
                        $properties[$key] = boolval(intval($value));
                    }
                }
            }
        }

        return $properties;
    }


    /**
     * @param string $k
     * @param null $v
     * @param string $vType
     *
     * @return bool
     */
    public function set($k, $v = null, $vType = '')
    {
        if (is_string($k) && $k == 'createdby' && empty($this->_oldAuthor)) {
            $this->_oldAuthor = parent::get('createdby');
        }

        return parent::set($k, $v, $vType);
    }


    /**
     * @param null $cacheFlag
     *
     * @return bool
     */
    public function save($cacheFlag = null)
    {
        $isNew = $this->isNew();
        $action = $isNew || $this->isDirty('deleted') || $this->isDirty('published');
        $enabled = $this->get('published') && !$this->get('deleted');
        $new_parent = $this->isDirty('parent');
        $new_author = $this->isDirty('createdby');
        if ($new_parent || $this->isDirty('alias') || $this->isDirty('published') || ($this->get('uri_override') && !$this->get('uri'))) {
            $this->setUri($this->get('alias'));
        }
        $save = parent::save();

        /** @var TicketAuthor $profile */
        if ($new_author && $profile = $this->xpdo->getObject('TicketAuthor', $this->_oldAuthor)) {
            $profile->removeAction('ticket', $this->id, $this->get('createdby'));
        }
        if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('createdby'))) {
            if (($action || $new_author) && $enabled) {
                $profile->addAction('ticket', $this->id, $this->id, $this->get('createdby'));
            } elseif (!$enabled) {
                $profile->removeAction('ticket', $this->id, $this->get('createdby'));
            }
        }
        if ($new_parent && !$isNew) {
            $this->updateAuthorsActions();
        }

        return $save;
    }


    /**
     * @param array $ancestors
     *
     * @return bool
     */
    public function remove(array $ancestors = array())
    {
        $collection = $this->xpdo->getIterator('TicketThread', array('name' => 'resource-' . $this->id));
        /** @var TicketThread $item */
        foreach ($collection as $item) {
            $item->remove();
        }

        /** @var TicketAuthor $profile */
        if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('createdby'))) {
            $profile->removeAction('ticket', $this->id, $this->get('createdby'));
        }

        /** @var TicketTotal $total */
        if ($total = $this->xpdo->getObject('TicketTotal', array('id' => $this->id, 'class' => 'Ticket'))) {
            $total->remove();
        }
        if ($total = $this->xpdo->getObject('TicketTotal', array('id' => $this->parent, 'class' => 'TicketsSection'))) {
            $total->set('children', $total->get('children') - 1);
            $total->save();
        }

        return parent::remove($ancestors);
    }


    /**
     * Update ratings for authors actions in section
     */
    public function updateAuthorsActions()
    {
        if (!$section = $this->getOne('Section')) {
            $section = $this->xpdo->newObject('TicketsSection');
        }

        $ratings = $section->getProperties('ratings');
        $table = $this->xpdo->getTableName('TicketAuthorAction');
        foreach ($ratings as $action => $rating) {
            $sql = "
                UPDATE {$table} SET `rating` = `multiplier` * {$rating}, `section` = {$section->id}
                WHERE `ticket` = {$this->id} AND `action` = '{$action}';
            ";
            $this->xpdo->exec($sql);
        }

        $c = $this->xpdo->newQuery('TicketAuthorAction', array('ticket' => $this->id));
        $c->select('DISTINCT(owner)');
        $owners = array();
        if ($c->prepare() && $c->stmt->execute()) {
            $owners = $c->stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $authors = $this->xpdo->getIterator('TicketAuthor', array('id:IN' => $owners));
        /** @var TicketAuthor $author */
        foreach ($authors as $author) {
            $author->updateTotals();
        }
    }


    /**
     * @param string $context
     */
    public function clearCache($context = '')
    {
        if (!$context) {
            $context = $this->get('context_key');
        }
        parent::clearCache($context);
    }

}
