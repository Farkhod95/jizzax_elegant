<?php
use yii\helpers\Url;
use app\models\Client;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\grid\GridView;
use app\models\OrderAccount;
use app\models\ElegantHistoryUpdate;
use app\models\OrderAccountHistory;

return [
    // [
    //     'class' => 'kartik\grid\SerialColumn',
    //     'width' => '30px',
    // ],
    [
        'class' => 'kartik\grid\DataColumn',
        'label' => '№',
        'content' => function($model, $key, $index, $column) use ($dataProvider) {
            // Joriy modelning sanasini oling
            $currentDate = $model->date;
    
            // Hozirgi sanaga tegishli barcha modellardan iborat massivni toping
            $filteredData = array_filter($dataProvider->getModels(), function($item) use ($currentDate) {
                return $item->date === $currentDate; // Faqat bitta sanaga mos keluvchi elementlar
            });
    
            // Umumiy qatorlar sonini hisoblash
            $totalRows = count($filteredData);
    
            // Joriy modelning sanaga mos indeksini topish
            $rowIndex = array_search($model, array_values($filteredData)) + 1;
    
            // Teskari tartibda raqamni hisoblash
            return $totalRows - $rowIndex + 1;
        },
    ],
    
    
    [
        'attribute' => 'date',
        'width' => '100px',
        'group' => true, // Sanalar bo'yicha guruhlash
        'format' => ['date', 'php:d.m.Y'],
        'label' => 'Sana',
        'visible' => \Yii::$app->user->identity->permission == 1 ? true : false,
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'client_id',
        'width' => '250px',

        'filter' => \yii\helpers\ArrayHelper::map(
            \app\models\Client::find()->select(['id','fio'])->orderBy(['fio' => SORT_ASC])->asArray()->all(),
            'id',
            'fio'
        ),
        'filterType' => \kartik\grid\GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['prompt' => Yii::t('app','Tanlang...')],
            'pluginOptions' => ['allowClear' => true, 'multiple' => false],
        ],

        'format' => 'raw',
        'content' => function ($data) {
            // client relatsiya null bo'lishi mumkin
            $fio = $data->client ? $data->client->fio : null;

            // Agar client topilmasa — hech bo'lmasa client_id ni ko'rsatib qo'yamiz
            if ($fio === null) {
                return $data->client_id
                    ? '<span class="text-muted">Client topilmadi (ID: '.$data->client_id.')</span>'
                    : '<span class="text-muted">—</span>';
            }

            // ranglar
            if ((int)$data->update_status === 2 && !empty($data->date)) {
                return '<b style="font-size:14px;color:#f59c1a">'.$fio.'</b>';
            }

            if ((int)$data->order_account_status === 1) {
                return '<b style="font-size:14px">'.$fio.'</b>';
            }

            return '<b style="font-size:14px;color:red">'.$fio.'</b>';
        },
    ],

    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'created_by',
        'width' => '250px',
        'filter' => ArrayHelper::map(
        User::find()
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
            if ($data->update_status == 2){
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:#f59c1a">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
                }
            }elseif ($data->order_account_status == 1) {
                if ($data->created_by) {
                    return '<b style="font-size: 14px">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
                }
            }else{
                if ($data->created_by) {
                    return   '<b style="font-size: 14px;color:red">'.$data->createdBy->surname.' '. $data->createdBy->name.'</b>';
                }
            }
        },
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'number_of_orders',
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_product_sum',
        'content'=> function($data){
            if ($data->update_status == 2){
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:#f59c1a">'.Yii::$app->formatter->asDecimal($data->all_product_sum??0, 2).'</b>';
                }
            }elseif ($data->order_account_status == 1) {
                if ($data->all_product_sum) {
                    return '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_product_sum??0, 2).'</b>';
                }
            }else{
                if ($data->all_product_sum) {
                    return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($data->all_product_sum??0, 2).'</b>';
                }
            }
            
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_summ_dollar',
        'width' => '150px',
        // 'format'=>['decimal',2],
        'content'=> function($data){
            if ($data->update_status == 2){
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:#f59c1a">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar, 2).'</b>';
                }
            }elseif ($data->order_account_status == 1) {
                if ($data->all_summ_dollar) {
                    return '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar, 2).'</b>';
                }
            }else{
                if ($data->all_summ_dollar) {
                    return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($data->all_summ_dollar, 2).'</b>';
                }
            }
        }
    ],
    // [
    //     'class'=>'\kartik\grid\DataColumn',
    //     'attribute'=>'sum_som',
    //     'width' => '200px',
    //     // 'format'=>['decimal',2],
    //     'content'=> function($data){
    //         if ($data->order_account_status == 1) {
    //             if ($data->sum_som) {
    //                 return   $data->sum_som?'<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->sum_som,2).'</b><b style="font-size: 14px; color: #f59c1a"> ('.Yii::$app->formatter->asDecimal($data->dollar_sumda,2).' $)'.' </b>':'';
    //             }
    //         }else{
    //             if ($data->sum_som) {
    //                 return $data->sum_som?'<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($data->sum_som,2).'</b><b style="font-size: 14px; color: #f59c1a"> ('.Yii::$app->formatter->asDecimal($data->dollar_sumda,2).' $)'.' </b>':'';
    //             }
    //         }
    //     }
    // ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'order_commit',
        'width' => '250px',
        'content'=> function($data){
            $commit = $data->order_commit;
            if (empty($commit)) {
                return '';
            }
            $commitShort = substr($commit, 0, 20); // 40 ta xarflik qismini olamiz
            $commitId = uniqid(); // Modal ID uchun noyob identifikator yaratamiz
    
            if ($data->update_status == 2 || $data->order_account_status == 1) {
                $style = ($data->update_status == 2) ? 'color:#f59c1a' : ''; // Rang shartga qarab
                $commitColor = '<b style="font-size: 14px;'.$style.'">'.$commitShort.'...</b>';
            } else {
                $commitColor = '<b style="font-size: 14px;color:red">'.$commitShort.'...</b>';
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
        'attribute'=>'total_debt_today',
        'content'=> function($data){
            if ($data->order_account_status == 1) {
                if ($data->total_debt_today) {
                    return '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->total_debt_today, 2).'</b>';
                }
            }else{
                if ($data->total_debt_today) {
                    return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($data->total_debt_today, 2).'</b>';
                }
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_total_debt_sum',
        'width' => '128px',
        'content'=> function($data){
            $orderAccount = OrderAccount::find()->where(['client_id' => $data->client_id])->one();
            if ($orderAccount) {
                return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($orderAccount->total_debt??0, 2).' $</b>';
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'all_profit_dollar',
        'visible' => \Yii::$app->user->identity->isAdminRight(\Yii::$app->user->identity->id),
        'content'=> function($data){
            if ($data->update_status == 2){
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:#f59c1a">'.Yii::$app->formatter->asDecimal($data->all_profit_dollar??0, 2).'</b>';
                }
            }elseif ($data->order_account_status == 1) {
                if ($data->all_profit_dollar) {
                    return '<b style="font-size: 14px">'.Yii::$app->formatter->asDecimal($data->all_profit_dollar??0, 2).'</b>';
                }
            }else{
                if ($data->all_profit_dollar) {
                    return   '<b style="font-size: 14px;color:red">'.Yii::$app->formatter->asDecimal($data->all_profit_dollar??0, 2).'</b>';
                }
            }
        }
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'150px',
        'attribute'=>'date',
        'content' => function ($data) {
            if ($data->update_status == 2){
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:#f59c1a">'.\Yii::$app->formatter->asDate($data->date, 'php:d-m-Y').'</b>';
                }
            }elseif($data->order_account_status == 1) {
                if ($data->date) {
                    return \Yii::$app->formatter->asDate($data->date, 'php:d-m-Y');
                }
            }else{
                if ($data->date) {
                    return   '<b style="font-size: 14px;color:red">'.\Yii::$app->formatter->asDate($data->date, 'php:d-m-Y').'</b>';
                }
            }
        },
    ], 
    [
        'class'=>'\kartik\grid\DataColumn',
        'width'=>'200px',
        'attribute'=>'status_order',
        'content' => function ($data) {
            if ($data->status_order_dukon || $data->status_order_sklad){
                $dukon_status = '';
                $sklad_status = '';
                if ($data->status_order_dukon == 1) {
                    $dukon_status = '<b>Do\'kon: </b><b style="font-size: 14px;color:#ef6360">Tayyorlanmoqda</b> <br/>';
                } elseif ($data->status_order_dukon == 2) {
                    $dukon_status = '<b>Do\'kon: </b><b style="font-size: 14px;color:#398787">Tayyor<br/></b>';
                }
                
                if ($data->status_order_sklad == 1) {
                    $sklad_status = '<b>Sklad: </b><b style="font-size: 14px;color:#ef6360">&nbsp; Tayyorlanmoqda</b>';
                } elseif ($data->status_order_sklad == 2) {
                    $sklad_status = '<b>Sklad: </b><b style="font-size: 14px;color:#398787">&nbsp; Tayyor</b>';
                }
                
                // Ikkala holatni qaytarish
                return $dukon_status . $sklad_status;
            }
        },
    ], 

    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '180px',
        'header' => 'Harakatlar',
        'template' => '{leadReturn} {leadPrice} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
            return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadOrderStatus' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 6){
                    if ($model->status_order_dukon == 1){
                        $url = Url::to(['/order-account-history/order-check-dokon' , 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tayyor buyurtmani tasdiqlash','class'=>'btn btn-xs','style' => 'background-color: #a71629; color: white;']);
                    }
                }
                
                
                if(\Yii::$app->user->identity->permission == 5){
                    if ($model->status_order_sklad == 1){
                        $url = Url::to(['/order-account-history/order-check-sklad' , 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Tayyor buyurtmani tasdiqlash','class'=>'btn btn-xs','style' => 'background-color: #a71629; color: white;']);
                    }
                }
            },
           'leadUpdateStatus' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    if ($model->update_status == 2){
                        $url = Url::to(['/order-account-history/update-status', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgarishni tasdiqlash','class'=>'btn btn-warning btn-xs']);
                    }
                }
            },
            'leadUpdateStatus1' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $latestRecord = ElegantHistoryUpdate::find()
                                                        ->where(['order_account_history_id' => $model->id])
                                                        ->orderBy(['id' => SORT_DESC])  // id bo'yicha kamayish tartibida saralash
                                                        ->one();
                    $commnet = " ";                                    
                    if ($latestRecord) {
                        $commnet = $latestRecord->comment;
                    }
                    if ($model->update_status == 2){
                    
                        // Create the button to trigger the modal
                        return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-check"></span>', '#', [
                            'data-toggle' => 'modal',
                            'data-target' => '#updateModal-'.$model->id, // Unique modal ID
                            'title' => 'O\'zgarishni tasdiqlash',
                            'class' => 'btn btn-warning btn-xs',
                        ]) .
                        // Modal HTML
                        '<div class="modal fade" id="updateModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel-'.$model->id.'" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="updateModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$model->client->fio.'</b> ning buyurtmasidagi o\'zgarishlarni tasdiqlaysizmi?</h5>
                                    </div>
                                    <div class="modal-body" >
                                        <b style="font-size:16px;">Izoh:</b> <b style="font-size:16px;color:#f59c1a"> '.$commnet.'</b>
                                    
                                        '.Html::beginForm(['/order-account-history/update-status', 'id' => $model->id], 'post').'
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
                                        '.Html::submitButton('Tasdiqlash', ['class' => 'btn btn-info']).'
                                        '.Html::endForm().'
                                    </div>
                                </div>
                            </div>
                        </div>';
                    }
                }
            },
            'leadPrice' => function ($url, $model) {
                $url = Url::to(['/order-account-history/products-trash' , 'id' => $model->id]);
                return Html::a('<span class="glyphicon glyphicon-fullscreen"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Barcha mahsulotlar','class'=>'btn btn-secondary btn-xs']);
            },
            'leadView' => function ($url, $model) {
                $url = Url::to(['/order-account-history/view' , 'id' => $model->id, 'type' => 1]);
                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'Ko\'rish','class'=>'btn btn-info btn-xs']);
            },
          'leadUpdate' => function ($url, $model) {
            if($model->is_debt != 1){
                // Mijozning oxirgi buyurtmasini topish
                $lastOrder = \app\models\OrderAccountHistory::find()
                    ->where(['client_id' => $model->client_id])
                    ->orderBy(['date' => SORT_DESC, 'id' => SORT_DESC]) // Buyurtmalarni sanalar va ID bo'yicha teskari tartibda saralash
                    ->one();

                // Bugungi sana
                $today = date('Y-m-d');

                // Tugmani ko'rsatish sharti
                if (
                    ($lastOrder && $lastOrder->id == $model->id) || // Agar bu buyurtma oxirgi buyurtma bo'lsa
                    ($model->date == $today) || // Yoki bugungi sana bo'lsa 
                    ($model->order_account_status == 0) || // order_account_status = 0 bo'lgan buyurtma
                    ($model->update_status == 2)
                ) {
                    if (\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->id == $model->created_by) {
                        $url = Url::to(['/order-account-history/update', 'id' => $model->id]);
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                            'data-pjax' => 0,
                            'data-toggle' => 'tooltip',
                            'title' => 'O\'zgartirish',
                            'class' => 'btn btn-success btn-xs'
                        ]);
                    }
                }

                return ''; // Boshqa buyurtmalarda tugma ko'rinmaydi
            }
            },

            // 'leadUpdate' => function ($url, $model) {
            //     if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->id == $model->created_by){
            //         $url = Url::to(['/order-account-history/update', 'id' => $model->id]);
            //         return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
            //     }
            // },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/order-account-history/one-delete', 'id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'chirish','class'=>'btn btn-danger btn-xs']);
                }
            },
            'leadReturn' => function ($url, $model) {
               
                if(Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/order-account-history/return', 'id' => $model->id]);
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-refresh"></span>', $url, [
                        'role'=>'modal-remote','title'=>'Buyurtmani tiklash','class'=>'btn btn-warning btn-xs' ,
                        'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                        'data-request-method'=>'post',
                        'data-toggle'=>'tooltip',
                        'data-confirm-title'=>'Ishonchingiz komilmi?',
                        'data-confirm-message'=>'Haqiqatan ham shu buyurtmani qayta tiklamoqchimisiz?',
                    ]);
                }
            },
            'leadDeleteOld' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    // Create the button to trigger the modal
                    return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
                        'data-toggle' => 'modal',
                        'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
                        'title' => 'O\'chirish',
                        'class' => 'btn btn-danger btn-xs',
                    ]) .
                    // Modal HTML
                    '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;color:red">'.$model->client->fio.'</b> ning buyurtmasini oʻchirib tashlamoqchimisiz?</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Buyurtmani o\'chirishdan oldin izoh kiriting:</p>
                                    '.Html::beginForm(['/order-account-history/delete', 'id' => $model->id], 'post').'
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