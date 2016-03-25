<?php
/**
 * Created by PhpStorm.
 * User: jepster
 * Date: 25.07.15
 * Time: 12:50
 */

namespace app\modules\yiipass\services;

/**
 * keepassx dom document
 *
 * @project keepassx
 *
 */
class KeepassXDbDoc extends \DOMDocument
{
    /**
     * @var DOMElement
     */
    protected $db;

    public function __construct()
    {
        parent::__construct('1.0', 'utf-8');
        $this->registerNodeClass('DOMElement', 'app\modules\yiipass\services\KeepassXGroup');
        $db = $this->createElement('database');
        $this->appendChild($db);
        $this->db = $db;
    }

    public function addGroup($title)
    {
        $element = $this->createElement('group');
        $this->db->appendChild($element);
        $element->setTitle($title);

        return $element;
    }
}
