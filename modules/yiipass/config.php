<?php
/**
 * Created by PhpStorm.
 * User: jepster
 * Date: 20.07.15
 * Time: 01:26
 */

$config = [
    'components' => [
        'xml' => function () {
            $xml = new app\modules\yiipass\XmlComponent();
            return $xml;
        },
        ]
    ];

return $config;