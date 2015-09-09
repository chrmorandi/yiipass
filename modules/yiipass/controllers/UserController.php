<?php

namespace app\modules\yiipass\controllers;

use Yii;
use app\modules\yiipass\models\User;
use app\modules\yiipass\models\UserSearch;
use yii\helpers\BaseHtml;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    // Use the main layout from yiipass module.
    public $layout = 'main';

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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
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
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        $model->load(['username' => 'foo',
                        'auth_key' => '46344hsgdsgsa8',
                        'password_hash' => 'adsfhsd6363',
                        'password_reset_token' => 'adsfhsd6363',
                        'email' => 'adsfhsd6363'
                    ]);

        if ($model->load(Yii::$app->request->post())) {

            $arr_request = Yii::$app->request->post()['User'];

            $this->formInputDataValidation($arr_request, $model);

            $model->auth_key = Yii::$app->security->generateRandomString();

            $model->email = $arr_request['email'];

            $model->created_at = time();

            $model->updated_at = time();

            $model->username = $arr_request['username'];

            $model->password_hash = Yii::$app->security->generatePasswordHash($arr_request['password']);

            $model->status = 10;

            $model->save();

            $found_model = $this->findModel($model->id);

            return $this->render('view', [
                'model' => $this->findModel($model->id),
            ]);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Validates the data for user creation or user update.
     *
     * @param $arr_request
     * @param $model
     * @return mixed
     */
    protected function formInputDataValidation($arr_request, $model){

        /**
         * Init the error variable with false and if any error appears,
         * set it with true.
         */
        $error = false;

        // Are all password fields filled?
        if ($arr_request['password'] == '' && $arr_request['password_repeat'] == '') {
            Yii::$app->session->setFlash('passwordFieldNotFilled');
            $error = true;
        }

        // Is the repeated password the same like the password?
        if (!Yii::$app->security->compareString($arr_request['password'], $arr_request['password_repeat'])) {
            Yii::$app->session->setFlash('passwordsNotTheSame');
            $error = true;
        }

        if($arr_request['email'] == ''){
            Yii::$app->session->setFlash('emailEmpty');
            $error = true;
        }

        // Refresh the page and display error, if there is any.
        if($error === true){
            return $this->refresh();
        }

    }

    /**
     * This method creates a permission. There's one permission per password.
     *
     * @param $name
     * @param $description
     * @throws \yii\base\InvalidConfigException
     */
    public function createPermission($name, $description){
        $auth = Yii::$app->authManager;

        $new_permission = $auth->createPermission($name);
        $new_permission->description = $description;

        $auth->add($new_permission);
    }

    /**
     * This method creates a role. There's one role per permission.
     *
     * @param $role_name
     * @param $user_id_or_name int|string
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreateRole($role_name, $user_id_or_name){
        $user_id = $this->getUserId($user_id_or_name);

        $auth = Yii::$app->authManager;
        $role = $auth->createRole($role_name . '-user-id-' . $user_id);
        $auth->add($role);
    }

    /**
     * Returns Yii's authManager.
     *
     * This method sets the mother db active, because the authorization
     * data is in the mother db.
     *
     * @return \yii\rbac\ManagerInterface
     */
    public static function authManager(){
        return Yii::$app->authManager;
    }

    /**
     * This method assigns a permission to an user.
     *
     * Please note: It's speciality relies in the fact that 1 permission
     * has "1 role". Because the permissions are assigned to users and
     * not to roles. Yii's role system is used with a little work-around here.
     * For this "-r4uid- + the user id is added to the role name.
     *
     * @param $user_id_or_name
     * @param $permission_name
     * @throws \Exception
     */
    public function addPermissionToUser($user_id, $permission_name){

        if(self::authManager()->getPermission($permission_name) == NULL) {
            $this->createPermission($permission_name, 'The permission for one account\'s credentials data.');
        }
        $role = self::authManager()->createRole($permission_name . '-r4uid-' . $user_id);
        // add parent item.
        self::authManager()->add($role);
        $permission = self::authManager()->getPermission($permission_name);
        // add child item.
        self::authManager()->addChild($role, $permission);

        // assign role to user by id.
        self::authManager()->assign($role, $user_id);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Gets all permissions which have been set for a given user.
     *
     * @param $user_id
     * @return array
     */
    public function getPermissionsForUser($user_id){
        $all_permissions = self::authManager()->getPermissions();
        $all_permissions_for_user = array();

        foreach($all_permissions as $permission){
            if(self::authManager()->checkAccess($user_id, $permission->name) === true){
                $all_permissions_for_user[] = $permission;
            }
        }

        return $all_permissions_for_user;
    }
}
