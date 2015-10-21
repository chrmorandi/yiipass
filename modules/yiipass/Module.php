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
     * Copies an empty SQlite db into application. Additionally the permissions
     * for the folder and the db-file are set.
     * @return null
     */
    public static function copyEmptySqliteDbOnInstall()
    {
        copy('modules/yiipass/assets/db/db.empty.sqlite', 'modules/yiipass/assets/db/db.sqlite');
        chmod('modules/yiipass/assets/db/db.sqlite', 0777);
        chmod('modules/yiipass/assets/db', 0777);
    }

    /**
     * Copies an empty db config template into application.
     * @return null
     */
    public static function copyDbConfigTemplateOnInstall()
    {
        copy('config/db.template.php', 'config/db.php');
    }

    /**
     * Copies an empty params config template into application.
     * @return null
     */
    public static function copyParamsConfigTemplateOnInstall()
    {
        copy('config/params.template.php', 'config/params.php');
    }

    /**
     * Creates an empty uploads folders (for XML uploads).
     * @return null
     */
    public static function createEmptyUploadsFolder()
    {
        mkdir('web/uploads');
        chmod('web/uploads', 0777);
    }
}
