<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'ampleadmin-minimal/bootstrap/dist/css/bootstrap.min.css',
        'ampleadmin-minimal/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css',
        'ampleadmin-minimal/plugins/bower_components/toast-master/css/jquery.toast.css',
        'ampleadmin-minimal/plugins/bower_components/Magnific-Popup-master/dist/magnific-popup.css',
        "ampleadmin-minimal/plugins/bower_components/morrisjs/morris.css",
        "ampleadmin-minimal/css/animate.css",
        "ampleadmin-minimal/css/style.css",
        "ampleadmin-minimal/css/colors/default.css",
        "ampleadmin-minimal/plugins/bower_components/summernote/dist/summernote.css",
        'ampleadmin-minimal/plugins/bower_components/switchery/dist/switchery.min.css',
        'ampleadmin-minimal/css/icons/material-design-iconic-font/css/materialdesignicons.min.css',
        'css/site.css',

    ];

    public $js = [
        "ampleadmin-minimal/bootstrap/dist/js/bootstrap.min.js",
        "ampleadmin-minimal/plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js",
        "ampleadmin-minimal/js/jquery.slimscroll.js",
        "ampleadmin-minimal/js/custom.min.js",
        "ampleadmin-minimal/plugins/bower_components/toast-master/js/jquery.toast.js",
        'ampleadmin-minimal/plugins/bower_components/styleswitcher/jQuery.style.switcher.js',
        "ampleadmin-minimal/plugins/bower_components/summernote/dist/summernote.min.js",
        'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.13.4/jquery.mask.min.js',
        //'ampleadmin-minimal/plugins/bower_components/switchery/dist/switchery.min.js',
        "https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js",
        "https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js",
        //"ampleadmin-minimalplugins/bower_components/jquery/dist/jquery.min.js",
        "ampleadmin-minimal/js/waves.js",
        "ampleadmin-minimal/plugins/bower_components/Magnific-Popup-master/dist/jquery.magnific-popup.min.js",
        "ampleadmin-minimal/plugins/bower_components/styleswitcher/jQuery.style.switcher.js",
        "js/script.js"

    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_BEGIN
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
