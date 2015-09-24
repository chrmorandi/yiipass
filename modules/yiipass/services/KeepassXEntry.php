<?php
/**
 * Created by PhpStorm.
 * User: jepster
 * Date: 25.07.15
 * Time: 12:52
 */

namespace app\modules\yiipass\services;


/**
 * keepassx dom element for adding an entry
 *
 * @project keepassx
 */
class KeepassXEntry extends \DOMElement
{
    public function setTitle($title)
    {
        $element = new \DOMElement('title', $this->ampersandify($title));
        $this->appendChild($element);
        return $element;
    }

    public function setUsername($username)
    {
        $element = new \DOMElement('username', $this->ampersandify($username));
        $this->appendChild($element);
        return $element;
    }

    public function setPassword($password)
    {
        $element = new \DOMElement('password', $this->ampersandify($password));
        $this->appendChild($element);
        return $element;
    }

    public function setUrl($url = null)
    {
        $element = new \DOMElement('url', $this->ampersandify($url));
        $this->appendChild($element);
        return $element;
    }

    public function setComment($comment = null)
    {
        $element = new \DOMElement('comment', $this->ampersandify($comment));
        $this->appendChild($element);
        return $element;
    }

    public function setCreation($timestamp = null)
    {
        $element = new \DOMElement('creation',
            $this->formatDate($timestamp));
        $this->appendChild($element);
        return $element;
    }

    public function setLastAccess($timestamp = null)
    {
        $element = new \DOMElement('lastaccess',
            $this->formatDate($timestamp));
        $this->appendChild($element);
        return $element;
    }

    public function setLastMod($timestamp = null)
    {
        $element = new \DOMElement('lastmod',
            $this->formatDate($timestamp));
        $this->appendChild($element);
        return $element;
    }

    public function setExpire($timestamp = null)
    {
        $element = new \DOMElement('expire',
            $this->formatDate($timestamp));
        $this->appendChild($element);
        return $element;
    }

    /**
     * Replaces ampersands with html entity
     *
     * @param string $message
     * @return string
     */
    protected function ampersandify($message)
    {
        return preg_replace('/&(?!#?\w+;)/', '&amp;', $message);
    } // end function ampersandify()

    protected function formatDate($timestamp = null)
    {
        $return_val = 'Never';
        if (isset($timestamp)) {
            $return_val = date('c', $timestamp);
        }

        return $return_val;
    }
}