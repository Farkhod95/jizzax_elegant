<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\ColorAdminAsset;



    if (Yii::$app->controller->action->id === 'login') { 

    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    ColorAdminAsset::register($this);

    $session = Yii::$app->session;

    if($session['sd_position'] != null) $sd_position = $session['sd_position'];
        else $sd_position = 1;
    if($session['header_styling'] != null) $header_styling = $session['header_styling'];
        else $header_styling = 1;

    if($session['sd_styling'] != null) $sd_styling = $session['sd_styling'];
        else $sd_styling = 1;
    if($session['cn_gradiyent'] != null) $cn_gradiyent = $session['cn_gradiyent'];
        else $cn_gradiyent = 1;

    if($session['cn_style'] != null) $cn_style = $session['cn_style'];
        else $cn_style = 1;

    if($session['boxed'] != null) $boxed = $session['boxed'];
        else $boxed = 1;


?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
</head>
<body>
<?php $this->beginBody() ?>

    <!-- begin #page-loader -->
    <div id="page-loader" class="fade in"><span class="spinner"></span></div>
    <!-- end #page-loader -->
    
    <!-- begin #page-container -->

    <!-- for sidebar menu begin -->
    <div id="page-container" class="fade page-sidebar-fixed page-header-fixed">

        <input type="hidden" name="sd_position" id="sd_position" value="<?=$sd_position?>" >
        <input type="hidden" name="header_styling" id="header_styling" value="<?=$header_styling?>" >
        <input type="hidden" name="sd_styling" id="sd_styling" value="<?=$sd_styling?>" >
        <input type="hidden" name="cn_gradiyent" id="cn_gradiyent" value="<?=$cn_gradiyent?>" >
        <input type="hidden" name="cn_style" id="cn_style" value="<?=$cn_style?>" >
        <input type="hidden" name="boxed" id="boxed" value="<?=$boxed?>" >

    <!-- for sidebar menu end -->
    
    <!-- for two-sidebar menu begin -->
    <!-- <div id="page-container" class="fade page-header-fixed page-sidebar-fixed page-with-two-sidebar"> -->
    <!-- for two-sidebar menu wnd -->

    <!-- for top-menu begin -->
    <!-- <div id="page-container" class="page-container fade page-without-sidebar page-header-fixed page-with-top-menu"> -->
    <!-- for top-menu end -->

    <!-- fro mixed menu begin -->
    <!-- <div id="page-container" class="page-container fade page-sidebar-fixed page-header-fixed page-with-top-menu"> -->
    <!-- fro mixed menu end -->

        <!-- begin #header -->
            <?= $this->render(
                'header.php'
            ) ?>  
        <!-- end #header -->

        <!-- begin #top-menu -->
            
        <!-- end #top-menu -->
        
        <!-- begin #sidebar -->
            <?= $this->render(
                'sidebar.php'
            ) ?>  
        <!-- end #sidebar -->

        <!-- begin #sidebar-right -->
           
        <!-- end #sidebar-right -->
        
        <!-- begin #content -->
            <?= $this->render(
                'content.php',
                ['content' => $content]
            ) ?>    
        <!-- end #content -->

        <!-- begin theme-panel -->

        <!-- end theme-panel -->
        
        <!-- begin scroll to top btn -->
        <a href="javascript:;" class="btn btn-icon btn-circle btn-success btn-scroll-to-top fade" data-click="scroll-top"><i class="fa fa-angle-up"></i></a>
        <!-- end scroll to top btn -->
    </div>
    <!-- end page container -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
<?php } ?>