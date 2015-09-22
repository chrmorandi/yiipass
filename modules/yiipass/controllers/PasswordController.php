<?php

namespace app\modules\yiipass\controllers;

use app\modules\yiipass\models\User;
use Yii;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\PasswordSearch;
use app\modules\yiipass\models\XmlUploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

/**
 * PasswordController implements the CRUD actions for Password model.
 */
class PasswordController extends Controller
{
    // Use the main layout from yiipass module.
    public $layout = 'main';

    /**
     * Sets the permissions for users, after password update form was
     * submitted. This method also notifies users.
     *
     * @param $permission_id int
     * @param $all_users array
     * @param $allowed_users array|false
     *
     * @return null
     */
    private function updatePermissionsAndNotify($permission_id, $allowed_users = false)
    {
        $all_users = User::find()
            ->all();

        foreach ($all_users as $user) {
            if (isset($allowed_users) && in_array($user->id, $allowed_users)) {
               UserController::addPermissionToUser(
                   $user->id,
                   'password-id-' . $permission_id
               );
            }

            // Remove permission from user.
            if (!isset($allowed_users) || !in_array($user->id, $allowed_users)) {
                UserController::removePermissionFromUser(
                    $user->id,
                    'password-id-' . $permission_id
                );

                $this->notifyUser($user, $permission_id, 'removal');
            }
        }
    }

    /**
     * Notify user via email about password assignment or removal.
     *
     * @param $user_id
     * @param $permission_id
     * return null
     */
    private function notifyUser($user, $permission_id, $action)
    {

        $password = Password::findOne($permission_id);

        $mailer = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['system_email'])
            ->setTo($user->email);

        if ($action == 'assignment') {
            $mailer->setSubject("Assignment for Account Credential '$password->title'")
                ->setTextBody("You're now allowed to access the account credential " .
                    "with the title '$password->title'. This is an " .
                    "automatic eMail from the account credentials management system." .
                    "Don't reply on this eMail.")
                ->send();
        }

        if ($action == 'removal') {
            $mailer->setSubject("Removal for Account Credential '$password->title'")
                ->setTextBody("The access for the account credential " .
                    "with the title '$password->title' was removed
                                    from your account. This is an automatic eMail " .
                    "from the account credentials management system." .
                    "Don't reply on this eMail.")
                ->send();
        }
    }

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Allows download as passwords for KeePass programs as XML file.
     *
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\web\HttpException
     * return null
     */
    public function actionDownloadPasswordsAsKeePassXml()
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }

        $all_passwords = Password::find()
            ->asArray()
            ->all();

        $allowed_passwords = array();

        foreach ($all_passwords as $password) {

            $user_id = Yii::$app->user->identity->id;

            $is_admin = Yii::$app->user->identity->is_admin;

            if (Yii::$app->authManager
                    ->checkAccess($user_id,
                        'password-id-' . $password['id']) === true || $is_admin == 1) {
                $allowed_passwords[] = $password;
            }

        }

        /* @var $xml_service \app\modules\yiipass\services\SimpleKeePassXmlService */
        $xml_service = \Yii::$app->getModule('yiipass')->get('SimpleKeePassXmlService');

        $xml = $xml_service->createKeePassValidXml($allowed_passwords);

        // Download the passwords XML file.
        \Yii::$app
            ->getResponse()
            ->sendContentAsFile($xml, 'passwords.xml');

    }

    /**
     * Upload new KeePass XML file to import into new or existing database.
     *
     * @return string
     */
    public function actionUploadNewXml()
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }


        $model = new XmlUploadForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->upload()) {
                $import_xml = new ImportXmlController();
                $import_xml->get($model->file_path);

                \Yii::$app->getSession()->setFlash('success', 'File successfully uploaded.');
                $this->redirect(array('/'));
            }
        }

        return $this->render('upload', ['model' => $model]);
    }

    /**
     * Lists all Password models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }

        $searchModel = new PasswordSearch();

        if (Yii::$app->user->getIdentity()->is_admin !== 1) {
            $account_credential_ids = $this->getAccountCredentialIdsSetForUser(Yii::$app->user->id);
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $account_credential_ids);
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

    }

    /**
     * Displays a single Password model.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }


        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Password model. If creation is successful, the browser
     * will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }

        $model = new Password();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // The user which has created the password can access it.
            UserController::addPermissionToUser(
                $model->id,
                'password-id-' . Yii::$app->user->id
            );
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $elements_to_render = array('model' => $model);

            if (Yii::$app->user->getIdentity()->is_admin == 1) {
                $all_users = User::find()
                    ->all();
                $user_checkboxes = $this->getHtmlCheckboxesForUsers($all_users, false, $model);
                $elements_to_render['user_checkboxes'] = $user_checkboxes;
            }

            return $this->render('create', $elements_to_render);
        }
    }

    /**
     * Updates an existing Password model. If update is successful, the
     * browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }

        $model = $this->findModel($id);

        $all_users = User::find()
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            if (isset(Yii::$app->request->post()['allowed_users'])) {
                $this->updatePermissionsAndNotify(
                    $id,
                    Yii::$app->request->post()['allowed_users']
                );
            }

            \Yii::$app->getSession()->setFlash('success', 'Account credential successfully saved.');

            // Redirect to listing with all account credentials.
            return $this->actionIndex();

        } else {

            $elements_to_render = array('model' => $model);

            if (Yii::$app->user->getIdentity()->is_admin == 1) {
                // Enrich users array with account credentials info.
                foreach ($all_users as $user) {
                    $users_account_credential_ids[$user->id] = $this->getAccountCredentialIdsSetForUser($user->id);
                }
                $user_checkboxes = $this->getHtmlCheckboxesForUsers(
                    $all_users,
                    $users_account_credential_ids,
                    $model
                );
                $elements_to_render['user_checkboxes'] = $user_checkboxes;
            }

            return $this->render('update', $elements_to_render);
        }
    }

    /**
     * Deletes an existing Password model. If deletion is successful, the
     * browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest === true) {
            return $this->redirect(['/site/login']);
        }

        $this->findModel($id)->delete();

        $all_users = User::find()
            ->all();
        $this->updatePermissionsAndNotify($id, $all_users);
        $permission = \Yii::$app->authManager->getPermission('password-id-' . $id);
        if ($permission !== null) {
            \Yii::$app->authManager->remove($permission);
        }

        \Yii::$app->getSession()
            ->setFlash('success', 'Account credential successfully deleted.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Password model based on its primary key value. If the model
     * is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Password the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     * return null
     */
    protected function findModel($id)
    {
        if (($model = Password::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Gets the account credentials (the passwords) by Yii's permissions
     * functionality from auth manager.
     *
     * @param $user_id
     * @return array
     */
    public function getAccountCredentialIdsSetForUser($user_id)
    {

        $user_controller = new UserController('PasswordController',
            'app\modules\yiipass');
        $permissions = $user_controller->getPermissionsForUser($user_id);

        $account_credential_ids = array();

        foreach ($permissions as $permission) {
            if (is_numeric(strpos($permission->name, 'password-id-'))) {
                $account_credential_ids[] = str_replace('password-id-', '', $permission->name);
            }
        }

        die($account_credential_ids);

        return $account_credential_ids;
    }

    /**
     * Returns the html checkboxes for users in relation to account
     * credentials.
     *
     * @param $all_users
     * @param $users_account_credential_ids
     * @param $model
     * @return string
     */
    private function getHtmlCheckboxesForUsers($all_users, $users_account_credential_ids, $model)
    {
        $user_checkboxes = '';

        foreach ($all_users as $user) {
            $checkbox_status = null;
            if (is_array($users_account_credential_ids) &&
                in_array($model->id, $users_account_credential_ids[$user->id])
            ) {
                $checked = true;
            } else {
                $checked = false;
            }

            $username = ($user->is_admin == 1) ? $user->username . ' (admin)' : $user->username;

            $user_checkboxes .= $this->renderPartial('_user_checkboxes', [
                'checked' => $checked,
                'user_id' => $user->id,
                'username' => $username
            ]);
        }

        // Wrap user checkboxes into label and ul dom-element.
        $user_checkboxes = $this->renderPartial('_wrapper_user_checkboxes', [
            'user_checkboxes' => $user_checkboxes
        ]);

        return $user_checkboxes;
    }

}
