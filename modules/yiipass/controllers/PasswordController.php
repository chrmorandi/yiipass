<?php

namespace app\modules\yiipass\controllers;

use app\modules\yiipass\models\User;
use Yii;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\TeamSecretForm;
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
    private function updatePermissionsAndNotify($permission_id)
    {
        $all_users = User::find()
            ->all();

        foreach ($all_users as $user) {
            if (isset(Yii::$app->request->post()['allowed_users'])
                && in_array($user->id, Yii::$app->request->post()['allowed_users'])
            ) {
                UserController::addPermissionToUser(
                    $user->id,
                    'password-id-' . $permission_id
                );
            }

            // Remove permission from user.
            if (!isset(Yii::$app->request->post()['allowed_users'])
                || !in_array($user->id, Yii::$app->request->post()['allowed_users'])
            ) {
                UserController::removePermissionFromUser(
                    $user->id,
                    'password-id-' . $permission_id
                );

                $this->notifyUser($user, $permission_id, 'removal');
            }
        }
    }

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
    private function setNewPermissionsAndNotify($permission_id)
    {
        $all_users = User::find()
            ->all();

        foreach ($all_users as $user) {
            if (isset(Yii::$app->request->post()['allowed_users'])
                && in_array($user->id, Yii::$app->request->post()['allowed_users'])
            ) {
                UserController::addPermissionToUser(
                    $user->id,
                    'password-id-' . $permission_id
                );
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
                'class'   => VerbFilter::className(),
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
                        'password-id-' . $password['id']) === true || $is_admin == 1
            ) {
                $password['password'] = $this->decrypt($password['password']);
                $allowed_passwords[]  = $password;
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

        PasswordController::teamSecretCheck();

        $searchModel = new PasswordSearch();

        if (intval(Yii::$app->user->getIdentity()->is_admin) !== 1) {
            $account_credential_ids = $this->getAccountCredentials(Yii::$app->user->id);
            $dataProvider           = $searchModel->search(Yii::$app->request->queryParams, $account_credential_ids);
        } else {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }

        return $this->render('index', [
            'searchModel'  => $searchModel,
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

        PasswordController::teamSecretCheck();

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

        if ($model->load(Yii::$app->request->post())) {

            $sPassword = Yii::$app->request->post()['Password']['password'];

            /* @var $model \app\modules\yiipass\models\Password */
            $model->password = $this->encrypt($sPassword);

            $model->save();

            // The user which has created the password can access it.
            UserController::addPermissionToUser(
                Yii::$app->user->id,
                'password-id-' . $model->id
            );

            // Update permissions if they have been set for any user.
            $this->setNewPermissionsAndNotify($model->id);

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $elements_to_render = array('model' => $model);

            if (Yii::$app->user->getIdentity()->is_admin == 1) {
                $all_users                             = User::find()
                    ->all();
                $user_checkboxes                       = $this->getHtmlCheckboxesForUsers($all_users, false, $model);
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

        PasswordController::teamSecretCheck();

        $model = $this->findModel($id);

        $all_users = User::find()
            ->all();

        if ($model->load(Yii::$app->request->post())) {

            $sPassword = Yii::$app->request->post()['Password']['password'];

            /* @var $model \app\modules\yiipass\models\Password */
            $model->password = $this->encrypt($sPassword);

            $model->save();

            $this->updatePermissionsAndNotify($id);

            \Yii::$app->getSession()->setFlash('success', 'Account credential successfully saved.');

            // Redirect to listing with all account credentials.
            return $this->actionIndex();

        } else {

            $elements_to_render = array('model' => $model);

            if (Yii::$app->user->getIdentity()->is_admin == 1) {
                // Enrich users array with account credentials info.
                $all_users = User::find()->all();
                foreach ($all_users as $user) {
                    $users_account_credential_ids[$user->id] = Yii::$app->getAuthManager()->getPermissionsByUser($user->id);
                }

                $user_checkboxes                       = $this->getHtmlCheckboxesForUsers(
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

        PasswordController::teamSecretCheck();

        if (Yii::$app->user->getIdentity()->is_admin == 1) {
            $this->findModel($id)->delete();

            // Remove roles and permissions.
            self::removeAllAuthAssignments($id);

            \Yii::$app->getSession()
                ->setFlash('success', 'Account credential successfully deleted.');
        }

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
    public function getAccountCredentials($user_id)
    {
        $permissions = Yii::$app->getAuthManager()->getPermissionsByUser($user_id);

        $account_credential_ids = array();

        foreach ($permissions as $permission) {
            if (is_numeric(strpos($permission->name, 'password-id-'))) {
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
        // Get data from existing acc permissions.
        if ($users_account_credential_ids !== false) {
            foreach ($users_account_credential_ids as $user_id => $all_permissions_data) {
                foreach ($all_permissions_data
                         as $one_permission_data) {
                    $users_acc_ids[$user_id][] = str_replace('password-id-', '', $one_permission_data->name);
                }
            }
        }

        $user_checkboxes = '';

        foreach ($all_users as $user) {
            $checkbox_status = null;
            if (isset($users_acc_ids[$user->id]) &&
                in_array($model->id, $users_acc_ids[$user->id])
            ) {
                $checked = true;
            } else {
                $checked = false;
            }

            $username = ($user->is_admin == 1) ? $user->username . ' (admin)' : $user->username;

            $user_checkboxes .= $this->renderPartial('_user_checkboxes', [
                'checked'  => $checked,
                'user_id'  => $user->id,
                'username' => $username
            ]);
        }

        // Wrap user checkboxes into label and ul dom-element.
        $user_checkboxes = $this->renderPartial('_wrapper_user_checkboxes', [
            'user_checkboxes' => $user_checkboxes
        ]);

        return $user_checkboxes;
    }

    public static function removeAllAuthAssignments($id)
    {

        // Remove the permission itself.
        $permission = \Yii::$app->authManager->getPermission('password-id-' . $id);
        if ($permission !== null) {
            $all_users = User::find()->all();

            foreach ($all_users as $user) {
                UserController::removePermissionFromUser($user->id, 'password-id-' . $id);
            }

            \Yii::$app->authManager->remove($permission);
        }
    }

    /**
     * Returns bool for current users account credential permissions state
     * via user id.
     *
     * @param int $acc_id The account credential id.
     *
     * @return bool
     */
    public static function checkAccessByAccId($acc_id)
    {

        $user_id = Yii::$app->user->id;

        if (Yii::$app->authManager
                ->checkAccess($user_id,
                    'password-id-' . $acc_id) === true
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Encrypts plaintext using an algorithm. Secret from params config-file
     * is used for encryption.
     *
     * @param string $plaintext
     *
     * @return string
     */
    public static function encrypt($plaintext)
    {
        # create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv      = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, Yii::$app->params['secret'],
            $plaintext, MCRYPT_MODE_CBC, $iv);

        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;

        # encode the resulting cipher text so it can be represented by a string
        return base64_encode($ciphertext);
    }

    /**
     * Decrypts plaintext using an algorithm. Secret from params config-file
     * is used for decryption.
     *
     * @param string $ciphertext_base64
     *
     * @return string
     */
    public static function decrypt($ciphertext_base64)
    {
        $ciphertext_dec = base64_decode($ciphertext_base64);

        # create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        # may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, Yii::$app->params['secret'],
            $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        // To remove NULL padding. The question marks at the end of the string.
        return rtrim($plaintext_dec, "\0");
    }

    public static function teamSecretCheck()
    {
        if (self::getTeamSecret() == null) {
            return (new PasswordController('teamSecretCheck', Yii::$app->module))
                ->redirect('yiipass/password/team-secret-form');
        }
    }

    public static function getTeamSecret()
    {
        $cookie = \Yii::$app->getRequest()->getCookies()->getValue('team_secret');
        return $cookie['value'];
    }

    public function actionTeamSecretForm()
    {
        $model = new TeamSecretForm();

        if ($model->load(\Yii::$app->request->post())) {

            if ($model->validate()) {
                // all inputs are valid
                $model->team_secret = Yii::$app->request->post()['team_secret'];
                if ($model->upload()) {
                    \Yii::$app->getSession()->setFlash('success', 'File successfully uploaded.');
                    $this->redirect(array('/'));
                }
            } else {
                // validation failed: $errors is an array containing error messages
                $errors = $model->errors;
            }
        }

        return $this->render('../team-secret/team-secret-form', [
            'model' => new TeamSecretForm()
        ]);
    }

}
