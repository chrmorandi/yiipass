<?php

namespace app\modules\yiipass\services;


/**
 * Class SimpleKeePassXmlService
 * @package app\modules\yiipass\services
 */
class SimpleKeePassXmlService {

    /**
     * @return \app\models\password[]
     */
    public function formXmlFileToArray($file_path){
        $keepass_password = simplexml_load_file($file_path);

        $arr_groups = array();

        foreach($keepass_password->group as $group){
            $arr_groups[] = $this->getGroupElements($group);
        }

        return $arr_groups;
    }

    /**
     * @param $group
     * @return array
     */
    private function getGroupElements($group){
        $arr_entries = array();
        foreach($group->entry as $entry){
            $obj_entry = $entry;
            $obj_entry->group = $group->title;
            $arr_entries[] = $obj_entry;
        }
        return $arr_entries;
    }

    /**
     * Loops the YiiPass specific array and creates password
     * entries out of it. This method assumes that the complete
     * password entry information is in 1 array item. The groups
     * are not separated from the password entry.
     *
     * @param array $arr The passwords entry, which contains the entire infos
     * which are associated with a password.
     * @return string Returns the created XML.
     * @throws \yii\base\InvalidConfigException
     */
    public function createKeePassValidXml($arr){
        /* @var $kpx \app\modules\yiipass\services\KeepassXDbDoc */
        $kpx = \Yii::$app->getModule('yiipass')->get('KeepassXDbDoc');

        $group = null;
        $prev_group = null;
        foreach($arr as $entry){
            // check if there's a difference in group.
            if($prev_group == null){
                $group = $kpx->addGroup($entry['group']);
                $prev_group = $entry['group'];
            }

            if ($prev_group != $entry['group']) {
                $group = $kpx->addGroup($entry['group']);
                $prev_group = $entry['group'];
            }
            $group->addEntry($entry['title'], $entry['username'], $entry['password'],
                $entry['url'], $entry['comment']);
        }

        $kpx->formatOutput = true;
        return $kpx->saveXML();

    }

    /**
     * Method to check if XML is valid.
     *
     * @param $xml The XML.
     * @return string The error/success message.
     */
    public function isXML($xml){
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument('1.0', 'utf-8');
        $doc->loadXML($xml);

        $errors = libxml_get_errors();

        if(empty($errors)){
            return true;
        }

        $error = $errors[0];
        if($error->level < 3){
            return true;
        }

        $explodedxml = explode("r", $xml);
        $badxml = $explodedxml[($error->line)-1];

        $message = $error->message . ' at line ' . $error->line . '. Bad XML: ' . htmlentities($badxml);
        return $message;
    }

}