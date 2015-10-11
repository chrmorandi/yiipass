<?php

namespace app\modules\yiipass;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\yiipass\controllers';

    public function init()
    {
        parent::init();

        // Declare services.
        $this->set('SimpleKeePassXmlService', new services\SimpleKeePassXmlService());
        $this->set('KeepassXDbDoc', new services\KeepassXDbDoc());

        $this->setAliases([
            '@yiipass-assets' => __DIR__ . '/assets'
        ]);

    }

    /**
     * Copies an empty SQlite db into production.
     * @return null
     */
    public static function copyEmptySqliteDbOnInstall()
    {
        copy('db.empty.sqlite', 'db.sqlite');
    }
}
