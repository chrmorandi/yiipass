<?php

namespace app\modules\yiipass\controllers;

use Yii;
use app\modules\yiipass\models\Password;
use app\modules\yiipass\models\PasswordSearch;
use app\modules\yiipass\models\XmlUploadForm;
use app\modules\yiipass\controllers\ImportXmlController;
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

    public function actionDownloadPasswordsAsKeePassXml(){

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
     */
    public function actionUploadNewXml(){
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
     * Creates a new Password model.
     * If creation is successful, the browser will be redirected to the 'view' page.
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
     * Updates an existing Password model.
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
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Password model.
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
     * Finds the Password model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Password the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Password::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
