<?php
/**
 * Created by PhpStorm.
 * User: jepster
 * Date: 25.07.15
 * Time: 12:51
 */

namespace app\modules\yiipass\services;


/**
 * keepassx dom element for adding a group
 *
 * @project keepassx
 */
class KeepassXGroup extends \DOMElement
{
    public function setTitle($title)
    {
        $element = new \DOMElement('title', $title);
        $this->appendChild($element);
    }

    public function addGroup($title)
    {
        $class = __CLASS__;
        $element = new $class('group');
        $this->appendChild($element);
        $element->setTitle($title);

        return $element;
    }

    public function addEntry($title, $username, $password,
                             $url = null, $comment = null,
                             $creation = null, $last_access = null, $last_mod = null,
                             $expire = null)
    {
        $entry = new KeepassXEntry('entry');
        $this->appendChild($entry);

        $entry->setTitle($title);
        $entry->setUsername($username);
        $entry->setPassword($password);
        $entry->setUrl($url);
        $entry->setComment($comment);
        $entry->setCreation($creation);
        $entry->setLastAccess($last_access);
        $entry->setLastMod($last_mod);
        $entry->setExpire($expire);

        return $entry;
    }
}