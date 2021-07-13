<?php

//use app\models\Roles;

use app\util\Util;


use kartik\file\FileInput;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\select2;
use yii\widgets\MaskedInput;

use yii\helpers\Url;

use app\models\Roles;
use app\models\Users;

use wbraganca\dynamicform\DynamicFormWidget;



/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */

if($model->isNewRecord){
    $model->status=1;
}
?>

<div class="users-form">

    <div class="white-box">

          <div class="box-title">
              <?=$this->title?>
          </div>

    <?php $form = ActiveForm::begin([

        "validationUrl" => ["validate","id"=>$model->userid],
        "id" => "user-form"
    ]); ?>



            <div class="col-sm-12">
                          <div class="col-sm-6">
                             <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                          </div>
                         <div class="col-sm-6">
                             <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
                         </div>
            </div>
        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
            </div>

        </div>

        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'passwd')->passwordInput(['maxlength' => true]) ?>
            </div>

            <div class="col-sm-6">
                <?= $form->field($model, 'conferma_passwd')->passwordInput(['maxlength' => true]) ?>
            </div>


        </div>


        <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'roleidfk')->widget(Select2::ClassName(),[

                                "data"=>Roles::getArrayforSelect(),
                                "options"=>[
                                        'placeholder'=>'Seleziona un Ruolo'
                                        ],
                            ])?>
                        </div>
                        <div class="col-sm-6">
                            <?= $form->field($model, 'internet')->dropDownList(Users::INTERNET_OPTIONS,['prompt' => 'Seleziona un opzione']) ?>
                            </div>
                        </div>


            <div class="col-sm-12">
                    <div class="col-sm-6">
                        <?= $form->field($model, 'date_in')->widget(MaskedInput::className(), [
                            'clientOptions' => ['alias' => 'date'],
                              'options'=>['class'=>'date form-control']
                        ]) ?>
                    </div>
                <div class="col-sm-6">
                    <?= $form->field($model, 'data_out')->widget(MaskedInput::className(), [
                        'clientOptions' => ['alias' => 'date'],
                        'options'=>['class'=>'date form-control']
                    ]) ?>
                </div>
          </div>




        <div class="col-sm-12">
            <div class="col-sm-6">
                <?= $form->field($model, 'hour')->widget(MaskedInput::className(), [

                    'mask' => 'h:m', // basic year
                    'definitions' => [
                            'h' => [
                        'cardinality' => 2,
                        'prevalidator' =>  [
                            ['validator' => '^([0-2])$','cardinality'=>1],
                            ['validator'=>'^([0-9]|0[0-9]|1[0-9]|2[0-3])$','cardinality'=>2],

                        ],

                                'validator'=>'^([0-9]|0[0-9]|1[0-9]|2[0-3])$'
                    ],

                 'm' => [
                     'cardinality' => 2,
                     'prevalidator' =>  [
                         ['validator' => '^(0|[0-5])$','cardinality'=>1],
                         ['validator'=> '^(0|[0-5]?\d)$','cardinality'=>2],

                        ],
                  ],

                ],  'options'=>['class'=>'form-control form-masked-input']]) ?>
            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'children')->textInput() ?>

            </div>
        </div>


    <div class="col-sm-12">

    <div class="col-sm-12">
        <?= $form->field($model, 'note')->textarea(['class'=>'summernote']) ?>


    </div>
</div>



        <div class="col-sm-12">
            <div class="col-sm-12">

                <?php

                $config = Util::prepareJsonForFileInput($model,'photo', "", "userid", "#",true);


                echo $form->field($model, 'photo',['validateOnBlur'=>false])->widget(FileInput::classname(), [
                    'options' => [
                        'multiple'=>false,
                        'placeholder' => 'Carica foto',

                    ],
                    'pluginOptions' => [
                        'initialPreview' => $config["names"],
                        'previewFileType' => 'any',
                        'showPreview' => true,
                        'dropZoneEnabled' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'showCancel' => false,
                        'uploadUrl'=>Url::to(['/global/upload-file']),
                        'uploadExtraData'=>[
                            'meetingid' => $model->meetingid??null,
                            'model' => $model,
                            'instance_name'=>"Meeting[attachments]",
                            'session_name'=>'MeetingAttachment',
                            'folder_name'=>'meeting-files'
                        ],
                        'initialPreviewAsData' => true,
                        'initialPreviewConfig' => $config["config"],
                        'overwriteInitial' => FALSE,
                        //'dropZoneEnabled' => false,
                        //'autoReplace' => true
                        //'actionUpload' => true,
                        'uploadAsync' => false,
                        'fileActionSettings' => [
                            'showRemove' => false,
                            'showDrag' => false,
                            'showUpload' => false,
                            "showDownload" => false
                        ]
                    ]
                ])

                ?>
            </div>
        </div>


        <div class="col-sm-12">
        <div class="col-sm-12">

            <?php

            $config = Util::prepareJsonForFileInput($model,'document', "", "userid", "#",true);


            echo $form->field($model, 'document',['validateOnBlur'=>false])->widget(FileInput::classname(), [
                'options' => [
                    'multiple'=>false,
                    'placeholder' => 'Carica Documento',

                ],
                'pluginOptions' => [
                    'initialPreview' => $config["names"],
                    'previewFileType' => 'any',
                    'showPreview' => true,
                    'dropZoneEnabled' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'showCancel' => false,
                    'uploadUrl'=>Url::to(['/global/upload-file']),
                    'uploadExtraData'=>[
                        'meetingid' => $model->meetingid??null,
                        'model' => $model,
                        'instance_name'=>"Meeting[attachments]",
                        'session_name'=>'MeetingAttachment',
                        'folder_name'=>'meeting-files'
                    ],
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => $config["config"],
                    'overwriteInitial' => FALSE,
                    //'dropZoneEnabled' => false,
                    //'autoReplace' => true
                    //'actionUpload' => true,
                    'uploadAsync' => false,
                    'fileActionSettings' => [
                        'showRemove' => false,
                        'showDrag' => false,
                        'showUpload' => false,
                        "showDownload" => false
                    ]
                ]
            ])

            ?>

        </div>

    </div>



    <div class="col-sm-12">

        <div class="col-sm-6">

            <?= $form->field($model, 'salary')->textInput(['class'=>'float form-control']) ?>

        </div>

        <div class= "col-sm-6 switch-padding" >
            <?= $form->field($model, 'status')->checkbox(['class'=>'js-switch']) ?>
        </div>

    </div>


        <div class="col-sm-12">
            <div class="col-sm-12">

                <?php

               $config = Util::prepareJsonForFileInput($model->galleries,'photo', "", "galleryid", "deletegallery",false);



                echo $form->field($model, 'galleries',['validateOnBlur'=>false])->widget(FileInput::classname(),


                    [
                    'options' => [
                        'multiple'=>true,
                        'placeholder' => 'Carica Foto',

                    ],
                    'pluginOptions' => [
                        'initialPreview' => $config["names"],
                        'previewFileType' => 'any',
                        'showPreview' => true,
                        'dropZoneEnabled' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'showCancel' => false,
                        'initialPreviewAsData' => true,
                        'initialPreviewConfig' => $config["config"],
                        'overwriteInitial' => FALSE,
                        //'dropZoneEnabled' => false,
                        //'autoReplace' => true
                        //'actionUpload' => true,
                        'uploadAsync' => false,
                        'fileActionSettings' => [
                            'showRemove' => false,
                            'showDrag' => false,
                            'showUpload' => false,
                            "showDownload" => false
                        ]
                    ]
                ]);


                ?>

                <div class="panel panel-default">
                    <div><h3 class="box-title">Familiari</h3></div>
                    <div class="panel-body">

                        <?php
                        DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper_servicing', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                            'widgetBody' => '.container-items-servicing', // required: css class selector
                            'widgetItem' => '.item', // required: css class                        'limit' => 4, // the maximum times, an element can be cloned (default 999)
                            'min' => 0, // 0 or 1 (default 1)
                            'insertButton' => '.add-item-servicing', // css class
                            'deleteButton' => '.remove-item-servicing', // css class
                            'model' => $model->families[0],
                            'formId' => 'user-form',
                            'formFields' => [
                                    'name',
                                    'cognome',

                            ],
                        ]);
                        ?>
                        <div class="container-items-servicing">
                            <?php foreach ( $model->families as $i=>$family): ?>
                                <div class="item panel panel-default"><!-- widgetBody -->
                                    <div class="panel-heading">
                                        <span class="panel-title-address-servicing"><?= Yii::t("app", "familiare") ?>: <?= ($i + 1) ?></span>
                                        <div class="pull-right">
                                            <!--button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button-->
                                            <button type="button" class="remove-item-servicing btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel-body">

                                      <div class="col-sm-12">
                                          <div class="col-sm-6">

                                              <?=$form->field($family,"[".$i."]name")->textInput(['maxlength'=>true])  ?>

                                          </div>
                                          <div class="col-sm-6">

                                              <?=$form->field($family,"[".$i."]cognome")->textInput(['maxlength'=>true])  ?>

                                          </div>
                                      </div>

                                      </div>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                        <div class="panel-footer">

                            <button type="button" class="pull-left add-item-servicing btn btn-success btn-xs"><i class="fa fa-plus"></i> Aggiungi Famigliare</button>

                            <div style="clear:both"></div>

                        </div>

                        <?php DynamicFormWidget::end(); ?>

                    </div>

                </div>
            </div>
        </div>



        <div class="form-group" style="text-align: right">
            <?= Html::submitButton('Salva', ['class' => 'btn btn-success']) ?>
            <?= Html::submitButton('Salva & Aggiungi', ['class' => 'btn btn-success','name' => "save-add","value" => 1]) ?>
            <?= Html::a('Torna Indietro', Yii::$app->request->referrer, ['class' => 'btn btn-default']) ?>

        </div>
    <?php ActiveForm::end(); ?>





    </div>

</div>



<script>
    $(document).ready(function(){

        jQuery(".dynamicform_wrapper_servicing").on("afterInsert", function(e, item) {

            let i = 0;

            jQuery(".dynamicform_wrapper_servicing .panel-title-address-servicing").each(function(index) {
                jQuery(this).html('<?= Yii::t("app", "Familiare") ?>: ' + (index + 1))

                i = index;

            });
        });



    });


    jQuery(".dynamicform_wrapper_servicing").on("afterDelete", function(e, item) {

        let i = 0;

        jQuery(".dynamicform_wrapper_servicing .panel-title-address-servicing").each(function(index) {
            jQuery(this).html('<?= Yii::t("app", "Familiare") ?>: ' + (index + 1))

            i = index;

        });
    });


</script>







