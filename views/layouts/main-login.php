<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="uz">
<!--<![endif]-->
<head>
    <meta charset="utf-8" />
    <title>Авторизация</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    
    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <link href="/web/plugins/jquery-ui/themes/base/minified/jquery-ui.min.css" rel="stylesheet" />
    <link href="/web/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="/web/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <link href="/web/css/animate.min.css" rel="stylesheet" />
    <link href="/web/css/style.min.css" rel="stylesheet" />
    <link href="/web/css/style-responsive.min.css" rel="stylesheet" />
    <link href="/web/css/theme/default.css" rel="stylesheet" id="theme" />
    <!-- ================== END BASE CSS STYLE ================== -->
    
    <!-- ================== BEGIN BASE JS ================== -->
    <script src="/web/plugins/pace/pace.min.js"></script>
    <!-- ================== END BASE JS ================== -->
</head>
<body class="pace-top">
<!-- begin #page-loader -->
<div id="page-loader" class="fade in"><span class="spinner"></span></div>
<!-- end #page-loader -->

    <div class="login-cover">
        <div class="login-cover-image"><img src="/web/img/login-bg/bg-6.jpg" data-id="login-cover-image" alt="" /></div>
        <div class="login-cover-bg"></div>
    </div>
<!-- begin #page-container -->
<div id="page-container" class="fade">
    <!-- begin login -->
    <?=$content?>
    <!-- end login -->
<ul class="login-bg-list clearfix">
        </ul>
</div>
<!-- end page container -->

    <!-- ================== BEGIN BASE JS ================== -->
    <script src="/web/plugins/jquery/jquery-1.9.1.min.js"></script>
    <script src="/web/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
    <script src="/web/plugins/jquery-ui/ui/minified/jquery-ui.min.js"></script>
    <script src="/web/plugins/bootstrap/js/bootstrap.min.js"></script>
    <!--[if lt IE 9]>
        <script src="/crossbrowserjs/html5shiv.js"></script>
        <script src="/crossbrowserjs/respond.min.js"></script>
        <script src="/crossbrowserjs/excanvas.min.js"></script>
    <![endif]-->
    <script src="/web/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="/web/plugins/jquery-cookie/jquery.cookie.js"></script>
    <!-- ================== END BASE JS ================== -->
    
    <!-- ================== BEGIN PAGE LEVEL JS ================== -->
    <script src="/web/js/login-v2.demo.min.js"></script>
    <script src="/web/js/apps.min.js"></script>
    <!-- ================== END PAGE LEVEL JS ================== -->

    <script>
        $(document).ready(function() {
            App.init();
            LoginV2.init();
        });
    </script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-53034621-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html>
