<?php

use app\models\Roles;
use SebastianBergmann\CodeCoverage\Util;
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\select2\Select2;
use app\models\Sedi;
use app\util\DatesUtil;


/* @var $this yii\web\View */

/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title ="Users";
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="white-box">

    <?php

    $gridColumns = [

        [
            'attribute' => 'photo',
            'label' => "Foto",
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {
                if(!empty($model->photo)){

                    return "<img src='".$model->photo."'width='150'>";

                }
                return '';
         }


            
        ],

        [
            'attribute' => 'document',
            'label' => "Documents",
            'format' => 'raw',
            'filter' => true,
            'value' => function ($model) {


        if(!empty($model->document)){

                   return Html::a("Visualizza",$model->getDocumentLink(),["target"=>"_blank"]);

                }

                return '';

            }




        ],



            [
        'attribute' => 'surname',
        'label' => "Surname",
        'format' => 'raw',
        'filter' => true,
        'value' => function($model){
            if(!empty($model->surname)){
                return Html::a($model->surname,['view', 'id'=>$model->userid]);
            }
        },
    ],

        [
            'attribute' => 'name',
            'label' => "Name",
            'format' => 'raw',
            'filter' => true,
            'value' => function($model){
                if(!empty($model->name)){
                    return Html::a($model->name,['view', 'id'=>$model->userid]);
                }
            },
        ],

        [
            'attribute' => 'email',
            'label' => "Email",
            'format' => 'raw',
            'filter' => true,
            'value' => function($model){

        if(!empty($model->email)){

          return Html::a($model->email,['view', 'id'=>$model->userid]);

        }else{
            return '';
        }

            },
        ],


        [
            'attribute' => 'roleidfk',
            'label' => "Role",
            'format' => 'raw',
            'filter' => true,
            'value' => function($model){
                return $model->role_name;
            },

            'filterType'=>Select2::className(),
            'filterWidgetOptions'=>[
                    'data'=> Roles::getArrayForSelect(),
                    'pluginOptions'=>[
                            'allowClear'=>true,
                    ],
            ],

            'filterInputOptions'=> ['placeholder'=>'Role','id'=>'select-ruolo'],
            'headerOptions'=> ['style'=>'min-width:250px']
        ],
        [
            'attribute' => 'data_out',
            'label' => "Expiring date",
            'format' => 'raw',
            'filter' => true,
            'value' => function($model){
                        return DatesUtil::convertDate($model->data_out);
                      },



         'filterType'=>GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'pluginOptions' => [
                    'format' => 'dd/mm/yyyy',
                    'separator' => ' TO ',
                    'opens' => 'left',
                ],


              ],
            'headerOptions'=>['style'=>'min-width:150px'],
          ],

        [
            'class'=>'kartik\grid\ActionColumn',
            'template'=> '{update} {delete}',
            'mergeHeader' => false,
             'headerOptions' => ['style' => 'min-width:150px'],
            'contentOptions' => false,
            'header'=>'',
            'updateOptions' => ['label'=>'<span class="label label-info action-size"><i class="fa fa-pencil"></i></span>'],
            'deleteOptions' => ['label' => '<span class="label label-danger action-size"><i class="fa fa-trash"></i></span>'],

        ],

    ];
    ?>



    <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $gridColumns,
            'pjax' => false,
            'responsiveWrap' => false,

            'panel' => [
                //'type' => GridView::TYPE_PRIMARY,
                'type' => GridView::TYPE_DEFAULT,
                'heading' => false,
                'footer' => '',
                'afterOptions' => ['class' => ''],
                'before' => '',
                'beforeOptions' => ['class' => 'box-header with-border'],
            ],
            "tableOptions" => ["class" => "table table-stripped table-hover"],
            'export' => false,
            'toggleData' => false,
            'summary' => '<span class="label label-success pull-right"> {totalCount} record trovati </span>',
            'toolbar' => [
               'content' => '<div class="box-title">'." &nbsp;" . Html::a('<i class="glyphicon glyphicon-plus"></i>', ['/users/create'], ['class' => '', 'title'=>'Create User']).'</div>'
            

            
            ]
          
        ]);?>


    </div>
</div>

