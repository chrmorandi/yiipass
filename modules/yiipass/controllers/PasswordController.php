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
     * submitted.
     *
     * @param $permission_id
     * @param $all_users
     * @internal param $post_request
     * @internal param $user_controller
     * @internal param $model
     */
    private function setPermissionsForUsers($permission_id, $all_users)
    {
        $post_request = Yii::$app->request->post();
        $user_controller = new UserController('PasswordController',
            'app\modules\yiipass');

        foreach ($all_users as $user) {
            // Add permission to user.
            if (isset($post_request['allowed_users']) &&
                in_array($user->id, $post_request['allowed_users']) &&
                \Yii::$app->authManager
                    ->checkAccess($user->id,
                        'password-id-' . $permission_id) === false
            ) {
                /**
                 * Add permission for password. Mark the permission name with
                 * "password-id" to be flexible about saving different types of
                 * permissions in future. "password-id-" can be later on
                 * replaced to the get only the id for further handling.
                 */
                $user_controller->addPermissionToUser($user->id,
                    'password-id-' . $permission_id);
            }

            // Remove permission from user.
            if ((!isset($post_request['allowed_users'])
                    || !in_array($user->id, $post_request['allowed_users']))
                && \Yii::$app->authManager
                    ->checkAccess($user->id,
                        'password-id-' . $permission_id) === true
            ) {
                $role_obj = \Yii::$app->authManager->getRole("password-id-$permission_id-r4uid-$user->id");
                \Yii::$app->authManager->remove($role_obj);
            }
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

        /* @var $xml_service \app\modules\yiipass\services\SimpleKeePassXmlService */
        $xml_service = \Yii::$app->getModule('yiipass')->get('SimpleKeePassXmlService');

        $xml = $xml_service->createKeePassValidXml($all_passwords);

        // Download the passwords XML file.
        \Yii::$app->getResponse()->sendContentAsFile($xml, 'passwords.xml');

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

        if(Yii::$app->user->getIdentity()->is_admin !== 1){
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
        $all_users = User::find()
                                ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->setPermissionsForUsers($model->id, $all_users);
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $user_checkboxes = $this->getHtmlCheckboxesForUsers($all_users, false, $model);
            return $this->render('create', [
                'model' => $model,
                'user_checkboxes' => $user_checkboxes
            ]);
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

            $this->setPermissionsForUsers($id, $all_users);

            \Yii::$app->getSession()->setFlash('success', 'Account credential successfully saved.');

            // Redirect to listing with all account credentials.
            return $this->actionIndex();
        } else {
            // Enrich users array with account credentials info.
            $account_credential_ids = array();
            foreach($all_users as $user){
                $users_account_credential_ids[$user->id] = $this->getAccountCredentialIdsSetForUser($user->id);
            }

            $user_checkboxes = $this->getHtmlCheckboxesForUsers($all_users, $users_account_credential_ids, $model);

            return $this->render('update', [
                'model' => $model,
                'user_checkboxes' => $user_checkboxes
            ]);
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
        $this->setPermissionsForUsers($id, $all_users);
        $permission = \Yii::$app->authManager->getPermission('password-id-' . $id);
        \Yii::$app->authManager->remove($permission);
        \Yii::$app->getSession()->setFlash('success', 'Account credential successfully deleted.');
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
    public function getAccountCredentialIdsSetForUser($user_id){

        $user_controller = new UserController('PasswordController',
                                                'app\modules\yiipass');
        $permissions = $user_controller->getPermissionsForUser($user_id);

        $account_credential_ids = array();

        foreach($permissions as $permission){
            if(is_numeric(strpos($permission->name, 'password-id-'))){
                $account_credential_ids[] = str_replace('password-id-', '', $permission->name);
            }
        }

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
            $user_checkboxes .= $this->renderPartial('_user_checkboxes', [
                'checked' => $checked,
                'user_id' => $user->id,
                'username' => $user->username
            ]);
        }
        return $user_checkboxes;
    }
}
