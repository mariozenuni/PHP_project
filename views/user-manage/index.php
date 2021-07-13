<?php
use app\models\Users;
use kartik\date\DatePicker;
use yii\data\Pagination;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
/* @var $models Users[]*/
/* @var $pages Pagination*/
?>
<?php Pjax::begin(["id"=>"pjax"])?>
<div class="row el-element-overlay m-b-40" style="margin-top: 20px">
    <!-- /.usercard -->
    <div class="col-md-12">
        <h4>Lista Utenti<br/></small></h4>
        <hr>
        <?php ActiveForm::begin([

           'options'=>[
                   'data-pjax'=> '1'
           ],
            'id' => 'form-search',
        
        
        ]);?>
        <div class="row">

           <div class="col-sm-3">
                <input type="search" name="UsersManageSearch[name]"placeholder="Nome" class="form-control" value="<?=!empty($_SESSION['UsersManageSearch[name]'])?$_SESSION['UsersManageSearch[name]']:''?>">
           </div>
        <div class="col-sm-3">
            <input type="search" name="UsersManageSearch[email]"placeholder="Email" class="form-control" value="<?=!empty($_SESSION['UsersManageSearch[email]'])?$_SESSION['UsersManageSearch[email]']:''?>">
        </div>
        <div class="col-sm-3">

            <?php
            $selected='';
            if(!empty($_SESSION['internet'])){
                $_SESSION['internet']=$selected;
            }

            ?>

            <select  class="form-control" name="UsersManageSearch[internet]">
                <option value="">Seleziona un opzione</option>
                <?php foreach (Users::INTERNET_OPTIONS as $key=>$value){?>
                <option value="<?=$key?>"<?=$key==$selected ?'selected':''?>><?=$value?></option>
              <?php  }?>
            </select>
        </div>
        <div class="col-sm-3">
           <?=DatePicker::widget([
           
           'name'=>'UserManageSearch[month_date_out]',
               'type' => DatePicker::TYPE_COMPONENT_APPEND,
               'value' => !empty($_SESSION['UsersManageSearch[month_date_out]'])?$_SESSION['UsersManageSearch[month_date_out]']:'',
               'pluginOptions' => [
                       'autoclose'=> true,
                   'startView'=>'year',
                   'minViewMode'=>'months',
                   'format'=>'mm/yyyy'
               ],
               'options'=>[
                       'placeholder'=>'Cerca fine data licenza'
                       ]

            ]);?>

        </div>
    </div>
    <?php ActiveForm::end()?>
</div>
    <?php foreach ($models as $model) {?>
    <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <div class="white-box">
            <div class="el-card-item">
                <div class="el-card-avatar el-overlay-1"> <img src=<?=$model->photo?> />
                    <div class="el-overlay scrl-dwn">
                        <ul class="el-info">
                            <li><a class="btn default btn-outline image-popup-vertical-fit" href="<?=$model->photo?>"data-pjax="0" ><i class="icon-magnifier"></i></a></li>
                            <li><a class="btn default btn-outline" href="<?=Url::to(['users/view','id'=>$model->id])?>" data-pjax="0"><i class="icon-link"></i></a></li>
                        </ul>
                    </div>
                </div>
                <div class="el-card-content">
                    <h3 class="box-title"><?=$model->name. ' '.$model->surname?></h3> <small><?=$model->email?></small>

                    <br/> </div>
            </div>
        </div>
    </div>
    <?php } ?>


    <?=LinkPager::widget([
        'pagination' => $pages,
        'pageCssClass' => 'page-item',
        'prevPageCssClass' => 'page-item',
        'nextPageCssClass' => 'page-item',
        'maxButtonCount' => 4
    ]); ?>

    <script>
        $(document).ready(function (){
            $('input[type=search]').on('search',function (){
                $('#form-search').submit();
            });

            $('select').on('change',function(){
                $('#form-search').submit();
            })
        });

    </script>

    <?php Pjax::end()?>
</div>