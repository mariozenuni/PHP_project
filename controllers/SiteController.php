<?php

namespace app\controllers;

use app\models\Users;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\forms\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','download-document'],
                'rules' => [
                    [
                        'actions' => ['logout','download-document'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * {@inheritdoc}
     */
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()


    {



        if(!Yii::$app->user->isGuest){
            return $this->redirect(["users/index"]);
        }

        $model=new LoginForm();




        $error=null;


        if($model->load(Yii::$app->request->post())){

            $result=$model->login();

           if($result["success"]){

               return $this->redirect(['users/index']);
           }

            $model->password="";  // se fallisce e' meglio svoutare la password;

            $error= $result["message"];



        }


        $this->layout="login";
        return $this->render('index',[
            'model'=>$model,
             'error'=>$error


        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */


    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }




public function actionDownloadDocument($id,$token){

        if(md5($id.Yii::$app->params['ENCRYPTED_KEY'])==$token){

            $model=Users::findOne($id);
            $file=__DIR__."/../media/".$model->document;
            // $type=explode(".",$file);
            $exp=explode("/",$file);
            $name=end($exp);
            return Yii::$app->response->sendContentAsFile(
                file_get_contents($file),
                $name,
                ['inline'=>true,'mimeType'=>mime_content_type($file)]
            );

        }


              echo 'Non hai il permesso per visuallizzare il doc';
        }





}
