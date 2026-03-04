<?php

use app\models\Users;
use app\models\OrderAccount;
use app\models\Brands;
use app\models\Client;
use app\models\DebtRepayment;
use app\models\OrderAccountHistory;

use dosamigos\chartjs\ChartJs;
$this->title = Yii::$app->name;

$user_count =  Users::find()->count();
// $productCategory_count =  OrderAccount::find()->where(['!=','total_debt',0])->count();
$productCategory_count = OrderAccount::find()->andWhere(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])->andWhere(['>', 'total_debt', 0])->orderBy([ 'total_debt' => SORT_DESC,])->count();

$brand_count =  Brands::find()->count();
$order_count =  Client::find()->count();

$results = OrderAccountHistory::find()
    ->where(['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ])
    ->all();

$results_jan = 0;
$results_feb = 0;
$results_mar = 0;
$results_apr = 0;
$results_may = 0;
$results_jun = 0;
$results_jul = 0;
$results_aug = 0;
$results_sep = 0;
$results_oct = 0;
$results_nov = 0;
$results_dec = 0;

for($i=0; $i < count($results); $i++){
    if(date("y", strtotime($results[$i]['date'])) == date("y") and date("m", strtotime($results[$i]['date'])) == 1 ){
        $results_jan = round($results_jan + $results[$i]['all_summ_dollar'], 2);     
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 2 ){
        $results_feb = round($results_feb + $results[$i]['all_summ_dollar'], 2);    
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 3 ){
        $results_mar = round($results_mar + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 4 ){
        $results_apr = round($results_apr + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 5 ){
        $results_may = round($results_may + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 6 ){
        $results_jun = round($results_jun + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 7 ){
        $results_jul = round($results_jul + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 8 ){
        $results_aug = round($results_aug + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 9 ){
        $results_sep = round($results_sep + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 10 ){
        $results_oct = round($results_oct + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 11 ){
        $results_nov = round($results_nov + $results[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($results[$i]['cr_date'])) == date("y") and date("m", strtotime($results[$i]['cr_date'])) == 12 ){
        $results_dec = round($results_dec + $results[$i]['all_summ_dollar'], 2);  
    }
}

$orderResults = DebtRepayment::find()
    ->where(['or',
        ['or',
        ['!=', 'is_worker', 1],
        ['is', 'is_worker', null]
    ],
        ['is', 'is_worker', null]
    ])
    ->all();

$orderResults_jan = 0;
$orderResults_feb = 0;
$orderResults_mar = 0;
$orderResults_apr = 0;
$orderResults_may = 0;
$orderResults_jun = 0;
$orderResults_jul = 0;
$orderResults_aug = 0;
$orderResults_sep = 0;
$orderResults_oct = 0;
$orderResults_nov = 0;
$orderResults_dec = 0;
for($i=0; $i < count($orderResults); $i++){
    if(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 1 ){
        $orderResults_jan = round($orderResults_jan + $orderResults[$i]['all_summ_dollar'], 2);      
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 2 ){
        $orderResults_feb = round($orderResults_feb + $orderResults[$i]['all_summ_dollar'], 2);    
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 3 ){
        $orderResults_mar = round($orderResults_mar + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 4 ){
        $orderResults_apr = round($orderResults_apr + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 5 ){
        $orderResults_may = round($orderResults_may + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 6 ){
        $orderResults_jun = round($orderResults_jun + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 7 ){
        $orderResults_jul = round($orderResults_jul + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 8 ){
        $orderResults_aug = round($orderResults_aug + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 9 ){
        $orderResults_sep = round($orderResults_sep + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 10 ){
        $orderResults_oct = round($orderResults_oct + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 11 ){
        $orderResults_nov = round($orderResults_nov + $orderResults[$i]['all_summ_dollar'], 2);  
    }elseif(date("y", strtotime($orderResults[$i]['date'])) == date("y") and date("m", strtotime($orderResults[$i]['date'])) == 12 ){
        $orderResults_dec = round($orderResults_dec + $orderResults[$i]['all_summ_dollar'], 2);  
    }
}
?>
<!-- <ol class="breadcrumb float-xl-right">
    <li class="breadcrumb-item"><a href="javascript:;">Home</a></li>
    <li class="breadcrumb-item active">Dashboard</li>
</ol>
<h1 class="page-header">Dashboard <small>header small text goes here...</small></h1> -->
<!-- begin row -->
<div class="row">
    <!-- begin col-3 -->
    <div class="col-md-3 col-sm-6">
        <div class="widget widget-stats bg-purple">
            <div class="stats-icon"><i class="fa fa-handshake-o"></i></div>
            <div class="stats-info">
                <h4>Mijozlar</h4>
                <p><?= $order_count ?></p>
            </div>
            <div class="stats-link">
                <a href="/client/index">Ko'proq ...<i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-md-3 col-sm-6">
        <div class="widget widget-stats bg-red">
            <div class="stats-icon"><i class="fa fa-info-circle"></i></div>
            <div class="stats-info">
                <h4>Qarzdorlar</h4>
                <p><?= $productCategory_count ?></p>
            </div>
            <div class="stats-link">
                <a href="/order-account/debtors">Ko'proq ...<i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-md-3 col-sm-6">
        <div class="widget widget-stats" style="background-color:#378a4a;">
            <div class="stats-icon"><i class="fa fa-bookmark"></i></div>
            <div class="stats-info">
                <h4>Modellar</h4>
                <p><?= $brand_count ?></p>
            </div>
            <div class="stats-link">
                <a href="/brands/index">Ko'proq ...<i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
    <!-- begin col-3 -->
    <div class="col-md-3 col-sm-6">
        <div class="widget widget-stats bg-blue">
            <div class="stats-icon"><i class="fa fa-users"></i></div>
            <div class="stats-info">
                <h4>Foydalanuvchilar</h4>
                <p><?= $user_count ?></p>
            </div>
            <div class="stats-link">
                <a href="/users/index">Ko'proq ...<i class="fa fa-arrow-circle-o-right"></i></a>
            </div>
        </div>
    </div>
    <!-- end col-3 -->
</div>
<div class="row">
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i
                                class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i
                                class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= date("Y") ?> - yil uchun To'langan mahsulotlar summasi oylar kesimida</h4>
            </div>
            <div class="panel-body">
                <div id="nv-bar-chart" class="height-base">
                <?= ChartJs::widget([
                    'type' => 'bar',
                    'data' => [ 
                    'labels' => ["Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avgust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr"],
                        'datasets' => [[ 
                            'label' => 'To\'langan mahsulotlar summasi ($)',
                            'backgroundColor' => "#5cb85c",
                            'data' => [$results_jan, $results_feb, $results_mar, $results_apr, $results_may, $results_jun, $results_jul, $results_aug, $results_sep, $results_oct, $results_nov, $results_dec]
                        ], 
                        ]
                    ],

                    'options' => [
                    'height' => 200,
                    // 'width' => 600,
                        'scales' => [
                            'yAxes' => [[
                                'display' => true,
                                'ticks' => [
                                'suggestedMin' => 1,
                                'beginAtZero' => true,
                                ],
                            ]]
                        ]
                    ],
                ]);?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= date("Y") ?> - yil uchun To'langan qarzlar summasi oylar kesimida</h4>
            </div>
            <div class="panel-body">
            <div id="nv-bar-chart" class="height-base">
                <?= ChartJs::widget([
                    'type' => 'bar',
                    'data' => [ 
                    'labels' => ["Yanvar", "Fevral", "Mart", "Aprel", "May", "Iyun", "Iyul", "Avgust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr"],
                        'datasets' => [
                        [
                            'label' => 'To\'langan qarzlar summasi ($)',
                            'backgroundColor' => "#d9534f",
                            'data' => [$orderResults_jan, $orderResults_feb, $orderResults_mar, $orderResults_apr, $orderResults_may, $orderResults_jun, $orderResults_jul, $orderResults_aug, $orderResults_sep, $orderResults_oct, $orderResults_nov, $orderResults_dec]
                        ],
                        //   [
                        //     'label' => 'Shaxsiy xarajatlar',
                        //     'backgroundColor' => "#f0ad4e",
                        //     'data' => [123, $results_feb_xarajat, $results_mar_xarajat, $results_apr_xarajat,$results_may_xarajat,$results_jun_xarajat,$results_jul_xarajat,$results_aug_xarajat,$results_sep_xarajat,$results_oct_xarajat,$results_nov_xarajat,$results_dec_xarajat,]
                        //   ],
                        //   [
                        //     'label' => 'Dividend',
                        //     'backgroundColor' => "#5bc0de",
                        //     'data' => [$results_jan_divident, $results_feb_divident, $results_mar_divident, $results_apr_divident,$results_may_divident,$results_jun_divident,$results_jul_divident,$results_aug_divident,$results_sep_divident,$results_oct_divident,$results_nov_divident,$results_dec_divident,]
                        //   ]
                        ]
                    ],

                    'options' => [
                    'height' => 200,
                    // 'width' => 600,
                        'scales' => [
                            'yAxes' => [[
                                'display' => true,
                                'ticks' => [
                                'suggestedMin' => 1,
                                'beginAtZero' => true,
                                ],
                            ]]
                        ]
                    ],
                ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end row -->

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$(document).ready(function(){

    active_chart = getCookie('type-chart');
    active_chart_users = getCookie('type-chart_users');
    if(!active_chart || active_chart == 'undefined')
        active_chart = 'line';
    if(!active_chart_users || active_chart_users == 'undefined')
        active_chart_users = 'line';
   
    
    $('#items_'+active_chart).removeClass('hide').addClass('show');
    $('#click_item_'+active_chart).addClass('active');
    $('#users_'+active_chart_users).removeClass('hide').addClass('show');
    $('#click_users_'+active_chart_users).addClass('active');

    $('#click_item_line').click(function(){
        setCookie('type-chart','line');
        $('#items_line').removeClass('hide').addClass('show');
        $('#items_bar').removeClass('show').addClass('hide');
        $(this).addClass('active');
    });
    $('#click_item_bar').click(function(){
        setCookie('type-chart','bar');
        $('#items_bar').removeClass('hide').addClass('show');
        $('#items_line').removeClass('show').addClass('hide');
        $(this).addClass('active');
    });
    
    $('#click_users_line').click(function(){
        setCookie('type-chart_users','line');
        $('#users_line').removeClass('hide').addClass('show');
        $('#users_bar').removeClass('show').addClass('hide');
        $(this).addClass('active');
    });
    $('#click_users_bar').click(function(){
        setCookie('type-chart_users','bar');
        $('#users_bar').removeClass('hide').addClass('show');
        $('#users_line').removeClass('show').addClass('hide');
        $(this).addClass('active');
    });
   
   
    $('.select').click(function(){
        var text = $(this).text() + '<span class="fa fa-caret-down"></span>';
       $('#dropdownMenuLink').html(text);
        active_chart = getCookie('type-chart');
        if(!active_chart || active_chart == 'undefined')
            active_chart = 'line';
        $.post('/statistics/items-chart', {type: $(this).attr("id")}, function(data){ 
            $("#items_chart").html(data);
            $('#items_'+active_chart).removeClass('hide').addClass('show');
        });
    });
    $('.select_user').click(function(){
        var text = $(this).text() + '<span class="fa fa-caret-down"></span>';
       $('#dropdownMenuLinkUsers').html(text);
        active_chart_users = getCookie('type-chart_users');
        if(!active_chart_users || active_chart_users == 'undefined')
            active_chart_users = 'line';
        $.post('/statistics/users-chart', {type: $(this).attr("id")}, function(data){ 
            $("#users_chart").html(data); 
            $('#users_'+active_chart_users).removeClass('hide').addClass('show');
        });
    });
    
});
JS
) ?>



