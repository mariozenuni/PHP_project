<?php

namespace app\controllers;

use app\models\Roles;
use app\models\UserFamily;
use app\models\UserGallery;
use app\util\Util;
use Yii;
use app\models\Users;
use app\models\UsersSearch;

use yii\debug\models\search\Log;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;


/**
 * UsersController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),

                'rules' => [
                    [
                        'actions' => ['index','create','update','delete','view','deletegallery'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],

                    //Callable function when user is denied


                'denyCallback' => function($rule,$data){

                        $this->redirect(['/site/index']);
                }

            ]

        ];
    }

    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
       // Log::log("Lista utenti", "Visualizzazione");

        $searchModel = new UsersSearch();



        $search = array();
        $newSearch = false;
        if (isset($_GET["UsersSearch"])) {
            $search = $_GET;
            if (Yii::$app->session['UsersSearch'] != $search["UsersSearch"]) {

                Yii::$app->session['UsersSearch'] = $search["UsersSearch"];

                $newSearch = true;
            }
        } else if (!empty(Yii::$app->session['UsersSearch'])) {
            $search["UsersSearch"] = Yii::$app->session['UsersSearch'];
        }
        $dataProvider = $searchModel->search($search);

        $page = 0;
        if (!$newSearch) {
            if (isset($_GET["page"])) {
                Yii::$app->session['UsersPage'] = $_GET["page"] - 1;
                $page = $_GET["page"] - 1;
            } else if (!empty(Yii::$app->session['UsersPage'])) {
                $page = Yii::$app->session['UsersPage'];
            }
        } else {
            Yii::$app->session['UsersPage'] = 0;
        }

        $dataProvider->pagination->pageSize = 25;
        $dataProvider->pagination->page = $page;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single Users model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();



        if ($model->load(Yii::$app->request->post()) && $model->prepareToSave()&&$model->passwordToMd5($model->userid) && $model->save()) {

            $model->photo=UploadedFile::getInstance($model,'photo');

            if(!empty($model->photo)){
                $model->upload();

            }

            $model->document=UploadedFile::getInstance($model,'document');

            if(!empty($model->document)){
                $model->uploadDocument();

            }

            $model->save();

            $model->galleries=UploadedFile::getInstances($model, 'galleries');

            $model->saveGallery();

            $model->saveFamilies($_POST);



            $_SESSION['success']="Salvato con successo";


            if(!empty($_POST['save-add"'])){
                return $this->redirect(['create']);
            }

            return $this->redirect(['view', 'id' => $model->userid]);

        }


        $model->scenario="insert";

        return $this->render('create', [
            'model' => $model,
        ]);


    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);



        $model->galleries=UserGallery::getAll(['userid'=>$id]);
        $model->families=$model->getFamiliesForForm();
        $old_photo=$model->photo;
        $old_document=$model->document;

        if ($model->load(Yii::$app->request->post()) && $model->prepareToSave()&&$model->passwordToMd5($model->userid) && $model->save()) {


                $model->photo=UploadedFile::getInstance($model,'photo');

                if(!empty($model->photo)){
                    $model->upload();

                }else{
                    $model->photo=$old_photo;
                }

                $model->document=UploadedFile::getInstance($model,'document');

                if(!empty($model->document)){
                    $model->uploadDocument();

                }else{
                    $model->document=$old_document;
                }

                $model->save();


                       $model->galleries=UploadedFile::getInstances($model, 'galleries');

                        $model->saveGallery();

                       $model->saveFamilies($_POST);





                $_SESSION['success']="Salvato con successo";


                if(!empty($_POST['save-add"'])){
                    return $this->redirect(['create']);
                    }

                return $this->redirect(['view', 'id' => $model->userid]);

            }


            $model->date_in=Util::convertDate($model->date_in);
            $model->data_out=Util::convertDate($model->date_in);
            $model->salary=Util::convertDate($model->salary);
           $model->passwd="";

           return $this->render('update', [
            'model' => $model,
        ]);

        }


    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionDeleteGallery()
    {

            $model=UserGallery::findOne($_POST['id']);


                $model->delete();



            return true;
    }



    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {

            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }



}
