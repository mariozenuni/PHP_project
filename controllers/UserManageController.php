<?php

namespace app\controllers;

use app\models\Roles;
use app\models\Users;
use yii\data\Pagination;
use yii\filters\AccessControl;

class UserManageController extends \yii\web\Controller
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
                        'actions' => ['index'],
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

    public function actionIndex()
    {


        if(empty($_SESSION["UsersManageSearch"])){
            $_SESSION["UsersManageSearch"]=[];
        }
        if(!empty($_POST["UsersManageSearch"])){
            $_SESSION["UsersManageSearch"]=$_POST["UsersManageSearch"];

        }
        $pages=new Pagination(['totalCount'=> Users::getCount($_SESSION["UsersManageSearch"]),'defaultPageSize'=> 4]);

         $models=Users::getAll([ $_SESSION["UsersManageSearch"]],$pages->limit ,$pages->offset);



         foreach($models as $model){
             if(empty($model->photo)){
                 $model->photo= "/web/ampleadmin-minimal/plugins/images/users/img2.jpg";
                }
         }



         return $this->render('index',[
             'models'=>$models,
             'pages'=>$pages,
         ]);

    }

}
