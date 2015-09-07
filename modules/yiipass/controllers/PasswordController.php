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
        $searchModel = new PasswordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Password();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
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
        $model = $this->findModel($id);

        $user_model = new User();

        $all_users = User::find()
                            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $post_request = Yii::$app->request->post();

            $user_controller = new UserController('PasswordController',
                                                    'app\modules\yiipass');

            foreach ($post_request['allowed_users'] as $username=>$user_id) {
                /**
                 * Add permission for password. Mark the permission name with
                 * "password-id" to be flexible about saving different types of
                 * permissions in future. "password-id-" can be later on
                 * replaced to the get only the id for further handling.
                 */
                $user_controller->addPermissionToUser($user_id,
                    'password-id-' . $model->id);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            // Enrich users array with account credentials info.
            $account_credential_ids = array();
            foreach($all_users as $user){
                $users_account_credential_ids[$user->id] = $this->getAccountCredentialIdsSetForUser($user->id);
            }

            $user_checkboxes = '';

            foreach($all_users as $user){
                $checkbox_status = null;
                if(is_array($users_account_credential_ids) &&
                    in_array($model->id, $users_account_credential_ids[$user->id])){
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

            return $this->render('update', [
                'model' => $model,
                'all_users' => $all_users,
                'user_model' => $user_model,
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
        $this->findModel($id)->delete();

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
}
