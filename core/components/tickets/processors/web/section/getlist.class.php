<?php

class TicketsSectionGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'TicketsSection';
    public $defaultSortField = 'pagetitle';
    public $defaultSortDirection = 'ASC';
    private $current_category = 0;


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $context = array_map('trim', explode(',', $this->getProperty('context', $this->modx->context->key)));

        $c->where(array(
            'class_key' => 'TicketsSection',
            'published' => 1,
            'deleted' => 0,
            'context_key:IN' => $context,
        ));

        $sortby = $this->getProperty('sortby');
        $sortdir = $this->getProperty('sortdir');
        if ($sortby && $sortdir) {
            $c->sortby($sortby, $sortdir);
        }

        if (!empty($_REQUEST['tid']) && $tmp = $this->modx->getObject('Ticket', (int)$_REQUEST['tid'])) {
            $this->current_category = $tmp->get('parent');
        }

        if ($parents = $this->getProperty('parents')) {
            $depth = $this->getProperty('depth', 0);
            $parents = array_map('trim', explode(',', $parents));
            $parents_in = $parents_out = array();
            foreach ($parents as $v) {
                if (!is_numeric($v)) {
                    continue;
                }
                if ($v < 0) {
                    $parents_out[] = abs($v);
                } else {
                    $parents_in[] = abs($v);
                }
            }
            if (!empty($parents_in)) {
                foreach ($parents_in as $pid) {
                    $parents_in = array_merge($parents_in, $this->modx->getChildIds($pid, $depth));
                }
            }

            $parents = array_diff($parents_in,$parents_out);

            if (!empty($parents) && !empty($this->current_category)) {
                $c->where(array('parent:IN' => $parents, 'OR:id:=' => $this->current_category));
            } else if (!empty($parents)) {
                $c->where(array('parent:IN' => $parents));
            }

            if (!empty($parents_out)) {
                $c->where(array('parent:NOT IN' => $parents_out));
            }
        }
        if ($resources = $this->getProperty('resources')) {
            $resources = array_map('trim', explode(',', $resources));
            $resources_in = $resources_out = array();
            foreach ($resources as $r) {
                if (!is_numeric($r)) {
                    continue;
                }
                if ($r < 0) {
                    $resources_out[] = abs($r);
                } else {
                    $resources_in[] = abs($r);
                }
            }

            $resources = array_diff($resources_in, $resources_out);

            if (!empty($resources))
                $c->where(array('id:IN' => $resources));

            if (!empty($resources_out))
                $c->where(array('id:NOT IN' => $resources_out));
        }
        $c->prepare();

        return $c;
    }


    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);

        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $object) {
            $check = $object instanceof modAccessibleObject &&
                !$object->checkPolicy(array('section_add_children' => true)) &&
                $object->get('id') != $this->current_category;
            if ($check) {
                continue;
            }

            $objectArray = $this->prepareRow($object);
            if (!empty($objectArray) && is_array($objectArray)) {
                $list[] = $objectArray;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);

        return $list;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        return $object->toArray();
    }

}

return 'TicketsSectionGetListProcessor';