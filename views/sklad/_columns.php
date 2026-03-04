<?php
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\Consignor;
use app\models\User;
use app\models\Sklad;
use app\models\MyTotalDebt;

return [
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'consignor_id',
        'width' => '250px',
        'filter' => ArrayHelper::map( Consignor::find()->all(),
            'id',
            'name' // This will use the concatenated full name for filtering
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->consignor_id) {
                if ($data->status == 1) {
                    return '<b style="font-size: 14px;color:#b76060" >'. $data->consignor0->name.'</b>';
                }elseif($data->status == 2){
                    return '<b style="font-size: 14px;" >'. $data->consignor0->name .'</b>';
                }elseif($data->status == 3){
                    return '<b style="font-size: 14px;color:#f59c1a" >'. $data->consignor0->name .'</b>';
                }else{
                    return '<b style="font-size: 14px;" >'. $data->consignor0->name .'</b>';
                }
            }
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'created_by',
        'width' => '250px',
        'filter' => ArrayHelper::map( User::find()
            ->select(['id', new \yii\db\Expression("CONCAT(surname, ' ', name) AS full_name")])
            ->asArray()
            ->all(),
            'id',
            'full_name' // This will use the concatenated full name for filtering
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...'),],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->created_by) {
                if ($data->status == 1) {
                    return '<b style="font-size: 14px;color:#b76060" >'. $data->createdBy->surname.' '.$data->createdBy->name.'</b>';
                }elseif($data->status == 2){
                    return '<b style="font-size: 14px;" >'. $data->createdBy->surname.' '.$data->createdBy->name .'</b>';
                }elseif($data->status == 3){
                    return '<b style="font-size: 14px;color:#f59c1a" >'. $data->createdBy->surname.' '.$data->createdBy->name .'</b>';
                }else{
                    return '<b style="font-size: 14px;" >'. $data->createdBy->surname.' '.$data->createdBy->name .'</b>';
                }
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'given_sum_dollar',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'width' => '180px',
        'content'=> function($data){
            // $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            // if ($orderAccount) {
            //     return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt, 2).' $</b>';
            // }
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. Yii::$app->formatter->asDecimal($data->given_sum_dollar?: 0, 2).' $</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->given_sum_dollar?: 0, 2) .' $</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. Yii::$app->formatter->asDecimal($data->given_sum_dollar?: 0, 2) .' $</b>';
            }else{
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->given_sum_dollar?: 0, 2) .' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'sum_dollar',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'width' => '170px',
        'content'=> function($data){
            // $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            // if ($orderAccount) {
            //     return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt, 2).' $</b>';
            // }
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. Yii::$app->formatter->asDecimal($data->sum_dollar?: 0, 2).' $</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->sum_dollar?: 0, 2) .' $</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. Yii::$app->formatter->asDecimal($data->sum_dollar?: 0, 2) .' $</b>';
            }else{
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->sum_dollar?: 0, 2) .' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'discount_amount',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'width' => '135px',
        'content'=> function($data){
            // $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            // if ($orderAccount) {
            //     return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt, 2).' $</b>';
            // }
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. Yii::$app->formatter->asDecimal($data->discount_amount?: 0, 2).' $</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->discount_amount?: 0, 2) .' $</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. Yii::$app->formatter->asDecimal($data->discount_amount?: 0, 2) .' $</b>';
            }else{
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->discount_amount?: 0, 2) .' $</b>';
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_total_debt_sum',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'width' => '180px',
        'content'=> function($data){
            $myTotalDebt = MyTotalDebt::find()->where(['consignor_id' => $data->consignor_id])->one();
      
            $qarz_sum = 0;
            if ($myTotalDebt) {
                $qarz_sum = $myTotalDebt->total_debt;
            }
            return '<b style="font-size: 14px;color:#b76060" >'. Yii::$app->formatter->asDecimal($qarz_sum, 2).' $</b>';
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'comment',
        'width' => '250px',
        'content'=> function($data){
            $commit = $data->comment;
            if (empty($commit)) {
                return '';
            }
            $commitShort = substr($commit, 0, 10); // 40 ta xarflik qismini olamiz
            $commitId = uniqid(); // Modal ID uchun noyob identifikator yaratamiz
    
            // if ($data->update_status == 2 || $data->order_account_status == 1) {
            //     $style = ($data->update_status == 2) ? 'color:#f59c1a' : ''; // Rang shartga qarab
            //     $commitColor = '<b style="font-size: 14px;'.$style.'">'.$commitShort.'...</b>';
            // } else {
            //     $commitColor = '<b style="font-size: 14px;color:red">'.$commitShort.'...</b>';
            // }
            if ($data->status == 1) {
                $commitColor =  '<b style="font-size: 14px;color:#b76060" >'. $commitShort.' $</b>';
            }elseif($data->status == 2){
                $commitColor =  '<b style="font-size: 14px;" >'. $commitShort .' $</b>';
            }elseif($data->status == 3){
                $commitColor =  '<b style="font-size: 14px;color:#f59c1a" >'. $commitShort .' $</b>';
            }else{
                $commitColor =  '<b style="font-size: 14px;" >'. $commitShort .' $</b>';
            }

    
            // Modal tugmachasi
            $modalButton = '<a href="#" data-toggle="modal" data-target="#commitModal-'.$commitId.'">Ko\'proq...</a>';
    
            // Modal HTML kodi
            $modalHtml = '<div class="modal fade" id="commitModal-'.$commitId.'" tabindex="-1" role="dialog" aria-labelledby="commitModalLabel-'.$commitId.'" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="commitModalLabel-'.$commitId.'">To\'liq Izoh</h5>
                                    
                                    </div>
                                    <div class="modal-body">
                                        <b>'.$commit.'</b>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Yopish</button>
                                    </div>
                                </div>
                            </div>
                        </div>';
    
            return $commitColor . ' ' . $modalButton . $modalHtml;
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'exchange_rate',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'width' => '135px',
        'content'=> function($data){
            // $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            // if ($orderAccount) {
            //     return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt, 2).' $</b>';
            // }
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. Yii::$app->formatter->asDecimal($data->exchange_rate?: 0, 2).' $</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->exchange_rate?: 0, 2) .' $</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. Yii::$app->formatter->asDecimal($data->exchange_rate?: 0, 2) .' $</b>';
            }else{
                return '<b style="font-size: 14px;" >'. Yii::$app->formatter->asDecimal($data->exchange_rate?: 0, 2) .' $</b>';
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
            }else{
                return '<b style="font-size: 14px;" >'. \Yii::$app->formatter->asDate($data->cr_date, 'php:d.m.Y') .'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        // 'width'=>'200px',
        'attribute'=>'cr_date_time',
        'content' => function ($data) {
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. date("H:i:s", strtotime($data->cr_date_time)).'</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;" >'. date("H:i:s", strtotime($data->cr_date_time)) .'</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. date("H:i:s", strtotime($data->cr_date_time)) .'</b>';
            }else{
                return '<b style="font-size: 14px;" >'. date("H:i:s", strtotime($data->cr_date_time)) .'</b>';
            }
        },
    ], 
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status',
        'width' => '200px',
        'filter' => Sklad::getStatus(),
        'content' => function($data){
            if ($data->status == 1) {
                return '<b style="font-size: 14px;color:#b76060" >'. $data->getStatusView($data->status) .'</b>';
            }elseif($data->status == 2){
                return '<b style="font-size: 14px;color:green" >'. $data->getStatusView($data->status) .'</b>';
            }elseif($data->status == 3){
                return '<b style="font-size: 14px;color:#f59c1a" >'. $data->getStatusView($data->status) .'</b>';
            }
            
        }
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'actived',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'header' => 'Harakatlar',
        'width' => '180px',
        'template' => '{leadOrderStatus} {leadUpdate} {leadView} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) {
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadOrderStatus' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 5 || \Yii::$app->user->identity->permission == 1){
                    if ($model->status == 1){
                        $url = Url::to(['/sklad/import-check' , 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Qabul qilingan mahsulotni tasdiqlash','class'=>'btn btn-xs','style' => 'background-color: #a71629; color: white;']);
                    }elseif($model->status == 3){
                        $url = Url::to(['/sklad/import-check' , 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'O\'zgargan mahsulotni tasdiqlash','class'=>'btn btn-warning btn-xs',]);
                    }
                }
            },
            'leadUpdate' => function ($url, $model) {
                if ($model->actived == 1) {
                    if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 5|| $model->created_by == \Yii::$app->user->identity->id){
                        $url = Url::to(['/sklad/update', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs']);
                    }
                }
                
            },
            'leadPrint' => function ($url, $model) {
                $url = Url::to(['/sklad/print', 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, [ 'data-pjax' => 0, 'target' =>'_blank', 'data-toggle'=>'tooltip', 'title'=>'Chop qilish','class'=>'btn btn-warning btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/sklad/sklad-view', 'id' => $model->id, 'cr_date' => $model->cr_date ]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tovarlar','class'=>'btn btn-success btn-xs']);
            },
            // 'leadDelete' => function ($url, $model) {
            //     $url = Url::to(['/orders/delete', 'id' => $model->id]);
            //     return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Buyurtmani bekor qilish','class'=>'btn btn-danger btn-xs']);
            // },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 5|| $model->created_by == \Yii::$app->user->identity->id){
                    $consignor_name = "";
                    if ($model->consignor_id) {
                        $consignor_name = $model->consignor0->name;
                    }
                    
                    // Create the button to trigger the modal
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
                        'title' => 'O\'chirish',
                        'class' => 'btn btn-danger btn-xs',
                    ]) .
                    // Modal HTML
                    '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" >
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$consignor_name.'</b> dan olingan mahsulotlarni oʻchirib tashlamoqchimisiz?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Mahsulotlarni o\'chirishdan oldin izoh kiriting:'.'</p>
                                    '.Html::beginForm(['sklad/delete', 'id' => $model->id], 'post').'
                                    <div class="form-group">
                                        '.Html::input('text', 'delete_reason', '', ['class' => 'form-control', 'placeholder' => 'Sababini kiriting...', 'required' => true]).'
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                                    '.Html::submitButton('O\'chirish', ['class' => 'btn btn-danger']).'
                                    '.Html::endForm().'
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            },
        ],
    ],

];   