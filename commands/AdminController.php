<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\modules\yiipass\controllers\PasswordController;
use yii\console\Controller;

/**
 * This commands offers some actions for administration.
 */
class AdminController extends Controller
{
    /**
     * Removes all auth assignments.
     *
     * @return null
     */
    public function actionRemoveAllAuthData()
    {
        \Yii::$app->getAuthManager()->removeAll();
    }

    /**
     * This command removes all auth assignments for a given
     * auth id, which can be part of an account credential.
     *
     * @param id $id The auth item id.
     * @return null
     */
    public function actionRemoveAuthAssignmentsForId($id)
    {
        PasswordController::removeAllAuthAssignments($id);
    }
}
