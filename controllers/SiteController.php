<?php

namespace app\controllers;

use app\modules\yiipass\controllers\PasswordController;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * The login form.
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionLogin()
    {
        // Before filled login form.
        if(!isset(Yii::$app->request->post()['LoginForm']['username'])){
            return $this->render('login', [
                'model' => new LoginForm(),
            ]);
        }

        $identity = User::findByUsername([Yii::$app->request->post()['LoginForm']['username']]);
        $inserted_password = Yii::$app->request->post()['LoginForm']['password'];

        // Successful login.
        if (Yii::$app->security->validatePassword($inserted_password, $identity->password_hash)) {
            Yii::$app->user->login($identity);
            return Yii::$app->runAction('/yiipass/password/index');
        } else {
            // Login error.
            Yii::$app->session->setFlash('error', 'Wrong password. Please check.');

            return $this->render('login', [
                'model' => new LoginForm()
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}
