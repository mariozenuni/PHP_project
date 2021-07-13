<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\util\Util;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="fix-header">
<?php $this->beginBody() ?>
<div class="preloader">
    <svg class="circular" viewBox="25 25 50 50">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
    </svg>
</div>
<!-- ============================================================== -->
<!-- Wrapper -->
<!-- ============================================================== -->
<div id="wrapper">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header">
            <div class="top-left-part">
                <!-- Logo -->
                <a class="logo" href="index.html">
                    <b>
                        <img src="<?=Yii::$app->homeUrl?>images/dieffe.png" width="100px" alt="home"  />
                      <b/>


                </a>
            </div>
            <!-- /Logo -->

            <!-- Search input and Toggle icon -->
            <ul class="nav navbar-top-links navbar-left">
                <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu"></i></a></li>

            </ul>
            <ul class="nav navbar-top-links navbar-right pull-right">

                <li class="dropdown">
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> <img src="../images/varun.jpg" alt="user-img" width="36" class="img-circle"><b class="hidden-xs"><?=Yii::$app->user->identity->name?></b><span class="caret"></span> </a>
                    <ul class="dropdown-menu dropdown-user animated flipInY">
                        <li>s
                            <div class="dw-user-box">
                                <div class="u-img"><img src="<?=Yii::$app->user->identity->photo?>" alt="user" /></div>
                                <div class="u-text">
                                    <h4><?=Yii::$app->user->identity->name.' '.Yii::$app->user->identity->surname?><h4>
                                    <p class="text-muted"><?=Yii::$app->user->identity->email?></p><a href="profile.html" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                            </div>
                        </li>
                      <!--  <li role="separator" class="divider"></li>
                        <li><a href="#"><i class="ti-user"></i> My Profile</a></li>
                        <li><a href="#"><i class="ti-wallet"></i> My Balance</a></li>
                        <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>
                        <li role="separator" class="divider"></li>-->
                        <li><a href="<?=\yii\helpers\Url::to(['site/logout'])?>"><i class="fa fa-power-off"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
        </div>
        <!-- /.navbar-header -->
        <!-- /.navbar-top-links -->
        <!-- /.navbar-static-side -->
    </nav>
    <!-- End Top Navigation -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav slimscrollsidebar">
            <div class="sidebar-head">
                <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span></h3> </div>

            <ul class="nav" id="side-menu" style="margin-top: 60px">
                <li >
                    <a href="<?=\yii\helpers\Url::to(['users/index'])?>" class="waves-effect">
                        <i class="mdi mdi-account-multiple fa-fw"></i>
                        <span class="hide-menu">Utenti</span></a>

                </li>
                    <li >
                        <a href="<?=\yii\helpers\Url::to(['user-manage/index'])?>" class="waves-effect">
                            <i class="mdi mdi-account-multiple fa-fw"></i>
                            <span class="hide-menu">Lista Utenti</span></a>

                    </li>


             </ul>

        </div>
       </div>

    <div >
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?=$this->title?></h4> </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <!--<button class="right-side-toggle waves-effect waves-light btn-info btn-circle pull-right m-l-20"><i class="ti-settings text-white"></i></button>-->
                        <a href="javascript: void(0);" class="btn btn-default pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">Back</a>
                        <!--<ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="active"></li>
                        </ol>-->
                    </div>
                <?php if(!empty($_SESSION['success'])){
                    echo Util::getAlert($_SESSION['success'],true);


                }else{
                    unset($_SESSION['success']);
                }?>





                  <?=$content?>




            </div>

              <!--  <footer class="footer text-center"> <?=date('Y')?> &copy;Dieffetech</footer>-->
        </div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
