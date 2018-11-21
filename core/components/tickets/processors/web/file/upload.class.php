<?php

class TicketFileUploadProcessor extends modObjectProcessor
{
    public $classKey = 'TicketFile';
    public $languageTopics = array('tickets:default');
    public $permission = 'ticket_file_upload';
    /** @var modMediaSource $mediaSource */
    public $mediaSource;
    /** @var Ticket $ticket */
    private $ticket = 0;
    private $class = 'Ticket';


    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        $tid = (int)$this->getProperty('tid');
        if (!$this->ticket = $this->modx->getObject('Ticket', $tid)) {
            $this->ticket = $this->modx->newObject('Ticket');
            $this->ticket->set('id', 0);
        }

        if ($source = $this->getProperty('source')) {
            /** @var modMediaSource $mediaSource */
            $mediaSource = $this->modx->getObject('sources.modMediaSource', (int)$source);
            $mediaSource->set('ctx', $this->modx->context->key);
            if ($mediaSource->initialize()) {
                $this->mediaSource = $mediaSource;
            }
        }

        if (!$this->mediaSource) {
            return $this->modx->lexicon('ticket_err_source_initialize');
        }

        $this->class = $this->getProperty('class', 'Ticket');

        return true;
    }


    public function process()
    {
        if (!$data = $this->handleFile()) {
            return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
        }

        $properties = $this->mediaSource->getPropertyList();
        $tmp = explode('.', $data['name']);
        $extension = strtolower(end($tmp));

        $image_extensions = $allowed_extensions = array();
        if (!empty($properties['imageExtensions'])) {
            $image_extensions = array_map('trim', explode(',', strtolower($properties['imageExtensions'])));
        }
        if (!empty($properties['allowedFileTypes'])) {
            $allowed_extensions = array_map('trim', explode(',', strtolower($properties['allowedFileTypes'])));
        }
        if (!empty($allowed_extensions) && !in_array($extension, $allowed_extensions)) {
            @unlink($data['tmp_name']);
            return $this->failure($this->modx->lexicon('ticket_err_file_ext'));
        } elseif (in_array($extension, $image_extensions)) {
            $type = 'image';
        } else {
            $type = $extension;
        }

        $path = '0/';
        $filename = !empty($properties['imageNameType']) && $properties['imageNameType'] == 'friendly'
            ? $this->ticket->cleanAlias($data['name'])
            : $data['hash'] . '.' . $extension;
        if (strpos($filename, '.' . $extension) === false) {
            $filename .= '.' . $extension;
        }
        // Check for existing file
        $where = $this->modx->newQuery($this->classKey, array('class' => $this->class));
        if (!empty($this->ticket->id)) {
            $where->andCondition(array('parent:IN' => array(0, $this->ticket->id)));
        } else {
            $where->andCondition(array('parent' => 0));
        }
        $where->andCondition(array('file' => $filename, 'OR:hash:=' => $data['hash']), null, 1);
        if ($this->modx->getCount($this->classKey, $where)) {
            @unlink($data['tmp_name']);

            return $this->failure($this->modx->lexicon('ticket_err_file_exists', array('file' => $data['name'])));
        }

        /** @var TicketFile $uploaded_file */
        $uploaded_file = $this->modx->newObject('TicketFile', array(
            'parent' => 0,
            'name' => $data['name'],
            'file' => $filename,
            'path' => $path,
            'source' => $this->mediaSource->get('id'),
            'type' => $type,
            'createdon' => date('Y-m-d H:i:s'),
            'createdby' => $this->modx->user->id,
            'deleted' => 0,
            'hash' => $data['hash'],
            'size' => $data['size'],
            'class' => $this->class,
            'properties' => $data['properties'],
        ));

        $this->mediaSource->createContainer($uploaded_file->get('path'), '/');
        $this->mediaSource->errors = array();
        if ($this->mediaSource instanceof modFileMediaSource) {
            $upload = $this->mediaSource->createObject($uploaded_file->get('path'), $uploaded_file->get('file'), '');
            if ($upload) {
                copy($data['tmp_name'], urldecode($upload));
            }
        } else {
            $data['name'] = $filename;
            $upload = $this->mediaSource->uploadObjectsToContainer($uploaded_file->get('path'), array($data));
        }
        @unlink($data['tmp_name']);

        if ($upload) {
            $url = $this->mediaSource->getObjectUrl($uploaded_file->get('path') . $uploaded_file->get('file'));
            $uploaded_file->set('url', $url);
            $uploaded_file->save();
            $uploaded_file->generateThumbnails($this->mediaSource);

            return $this->success('', $uploaded_file);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                '[Tickets] Could not save file: ' . print_r($this->mediaSource->getErrors(), 1));

            return $this->failure($this->modx->lexicon('ticket_err_file_save'));
        }
    }


    /**
     * @return array|bool
     */
    public function handleFile()
    {
        $tf = tempnam(MODX_BASE_PATH, 'tkt_');

        if (!empty($_FILES['file']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
            $name = $_FILES['file']['name'];
            move_uploaded_file($_FILES['file']['tmp_name'], $tf);
        } else {
            $file = $this->getProperty('file');
            if (!empty($file) && (strpos($file, '://') !== false || file_exists($file))) {
                $tmp = explode('/', $file);
                $name = end($tmp);
                if ($stream = fopen($file, 'r')) {
                    if ($res = fopen($tf, 'w')) {
                        while (!feof($stream)) {
                            fwrite($res, fread($stream, 8192));
                        }
                        fclose($res);
                    }
                    fclose($stream);
                }
            }
        }

        clearstatcache(true, $tf);
        if (file_exists($tf) && !empty($name) && $size = filesize($tf)) {
            $res = fopen($tf, 'r');
            $hash = sha1(fread($res, 8192));
            fclose($res);
            $data = array(
                'name' => $name,
                'tmp_name' => $tf,
                'hash' => $hash,
                'size' => $size,
                'properties' => array(
                    'size' => $size,
                ),
            );
            $info = @getimagesize($tf);
            if (is_array($info)) {
                $data['properties'] = array_merge(
                    $data['properties'],
                    array(
                        'width' => $info[0],
                        'height' => $info[1],
                        'bits' => $info['bits'],
                        'mime' => $info['mime'],
                    )
                );
            }

            return $data;
        } else {
            unlink($tf);

            return false;
        }
    }

}

return 'TicketFileUploadProcessor';
