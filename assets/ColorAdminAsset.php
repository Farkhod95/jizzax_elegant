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
class ColorAdminAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        // Base style begin
       // 'fonts.googleapis.com/css?family=Open+Sans:300,400,600,700',
       'web/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css',
       // 'plugins/bootstrap/css/bootstrap.min.css',
       'web/plugins/font-awesome/css/font-awesome.min.css',
       'web/css/animate.min.css',
       'web/css/style.min.css',
       'web/css/style-responsive.min.css',
       'web/css/theme/default.css',
       'web/plugins/jquery-jvectormap/jquery-jvectormap.css',
       'web/plugins/isotope/isotope.css',
       'web/plugins/lightbox/css/lightbox.css',

       // Page style end
    ];
    public $js = [
        // 'plugins/pace/pace.min.js',
        // Base js begin
         // 'plugins/jquery/jquery-1.9.1.min.js',
        'web/plugins/jquery/jquery-migrate-1.1.0.min.js',
        'web/plugins/jquery-ui/ui/minified/jquery-ui.min.js',
        'web/plugins/bootstrap/js/bootstrap.min.js',
        'web/plugins/slimscroll/jquery.slimscroll.min.js',
        'web/plugins/jquery-cookie/jquery.cookie.js',
        'web/plugins/flot/jquery.flot.min.js',
        'web/plugins/flot/jquery.flot.time.min.js',
        'web/plugins/flot/jquery.flot.resize.min.js',
        'web/plugins/flot/jquery.flot.pie.min.js',
        'web/plugins/sparkline/jquery.sparkline.js',
        'web/plugins/jquery-knob/js/jquery.knob.js',
        'web/js/page-with-two-sidebar.demo.min.js',
        'web/plugins/jquery-jvectormap/jquery-jvectormap.min.js',
        'web/plugins/jquery-jvectormap/jquery-jvectormap-world-mill-en.js',
        'web/plugins/isotope/jquery.isotope.min.js',
        'web/plugins/lightbox/js/lightbox.min.js',
        'web/js/gallery.demo.min.js',
        'web/js/dashboard.min.js',
        'web/js/apps.min.js',
        'web/js/myjs.js',
        'web/js/new.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];

    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
