<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Brands;
use app\models\Warehouse;
use app\models\ProductCategory;
use app\models\Prices;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use kartik\grid\GridView;

// ==== MUHIM QISM: joriy brand_id filtrini olish ==== //
$brandId = null;
$params  = Yii::$app->request->queryParams;

// Agar sizning search model nomingiz boshqa bo‘lsa (masalan WarehouseSearch emas),
// shu yerda 'WarehouseSearch' o‘rnini mos ravishda almashtiring.
if (isset($params['WarehouseSearch']['brand_id']) && $params['WarehouseSearch']['brand_id'] !== '') {
    $brandId = (int)$params['WarehouseSearch']['brand_id'];
}

// ProductCategory uchun filter ro‘yxati:
// brand tanlangan bo‘lsa – faqat shu brand’ga tegishli kategoriyalar,
// aks holda – barcha aktiv kategoriyalar.
if ($brandId) {
    $categoryFilter = ProductCategory::listActiveByBrand($brandId);
} else {
    $categoryFilter = ArrayHelper::map(
        ProductCategory::find()
            ->where(['sup_status' => 1])
            ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
            ->all(),
        'id',
        'name'
    );
}

return [

    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // ================= BRAND FILTRI =================
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'brand_id',
        'width' => '250px',
        'filter' => ArrayHelper::map(
            Brands::find()->where(['sup_status' => 1])->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])->all(),
            'id',
            'name'
        ),
        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => [
                'prompt' => Yii::t('app','Tanlang...'),
                // xohlasangiz ID berib qo‘ysangiz ham bo‘ladi:
                // 'id' => 'brand-filter',
            ],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->count > 0) {
                return '<b style="font-size: 14px">'.$data->brand->name.'</b>';
            } else {
                return '<b style="color:red;font-size: 14px">'.$data->brand->name.'</b>';
            }
        },
    ],

    // ================= PRODUCT_CATEGORY FILTRI =================
    [
        'class'=>'\kartik\grid\DataColumn', 
        'attribute'=>'product_category_id',
        'width' => '250px',

        // ENG MUHIM O‘ZGARISH:
        // filter oldingi kabi hammasini emas, balki brand tanlangan bo‘lsa —
        // faqat shu brand’ga tegishli kategoriyalarni ko‘rsatadi
        'filter' => $categoryFilter,

        'filterType' => GridView::FILTER_SELECT2,
        'filterWidgetOptions' => [
            'size' => Select2::SMALL,
            'options' => [
                'prompt' => Yii::t('app','Tanlang...'),
                // 'id' => 'category-filter',
            ],
            'pluginOptions' => ['allowClear' => true,'multiple' => false],
        ],
        'content' => function ($data) {
            if ($data->count > 0) {
                return '<b style="font-size: 14px">'.$data->productCategory->name.'</b>';
            } else {
                return '<b style="color:red;font-size: 14px">'.$data->productCategory->name.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'size',
        'content' => function ($data) {
            if ($data->size) {
                if ($data->count > 0) {
                    return '<b style="font-size: 14px">'.$data->size.'</b>';
                }else{
                    return '<b style="color:red;font-size: 14px">'.$data->size.'</b>';
                }
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'type',
        'width' => '200px',
        'filter' => Warehouse::getProductTypeForClient(),
        'content' => function($model){
            if ($model->type) {
                if ($model->count > 0) {
                    return '<b style="font-size: 14px">'.$model->getProductTypeForClientView($model->type).'</b>';
                }else{
                    return '<b style="color:red;font-size: 14px">'.$model->getProductTypeForClientView($model->type).'</b>';
                }
            }
        }
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'count',
        'content' => function ($data) {
            if ($data->count > 0) {
                return '<b style="font-size: 14px">'.$data->count.'</b>';
            }else{
                return '<b style="color:red;font-size: 14px">'.$data->count.'</b>';
            }
        },
    ],
    
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'price',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'content' => function ($data) {
            // if ($data->count > 0) {
                $price = Prices::find()->where(['warehouse_id' => $data->id])->one();
                if ($price) {
                    return '<b style="font-size: 14px">'. $price->price. ' $' . '</b>';
                }else{
                    return '<b style="font-size: 14px">'. 0 . '</b>';
                }
                
            // }else{
            //     return '<b style="color:red;font-size: 14px">0</b>';
            // }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'worker_price',
        'visible' => \Yii::$app->user->identity->permission == 1,
        'content' => function ($data) {
            // if ($data->count > 0) {
            if ($data->worker_price>0) {
                return '<b style="font-size: 14px">'. $data->worker_price. ' $' . '</b>';
            }else{
                return '<b style="font-size: 14px">'. 0 . '</b>';
            }
                
            // }else{
            //     return '<b style="color:red;font-size: 14px">0</b>';
            // }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'cr_date',
        'content' => function ($data) {
            if ($data->count > 0) {
                return '<b style="font-size: 14px">'.$data->cr_date.'</b>';
            }else{
                return '<b style="color:red;font-size: 14px">'.$data->cr_date.'</b>';
            }
        },
    ],
    [
        'class'=>'\kartik\grid\DataColumn',
        'attribute'=>'status_count',
        'label' => 'Mahsulotlar sanalganligi',
        'filter' => Warehouse::getStatusCount(),
        'content' => function ($data) {
            if ($data->status_count == 1) {
                return '<b style="font-size: 14px;color:#00acac;">'.$data->getStatusCountView($data->status_count).'</b>';
            }else{
                return '<b style="font-size: 14px;color:#f59c1a;">'.$data->getStatusCountView($data->status_count).'</b>';
            }
        },
    ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'created_by',
    // ],
    // [
        // 'class'=>'\kartik\grid\DataColumn',
        // 'attribute'=>'update_by',
    // ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => false,
        'width' => '150px',
        'header' => 'Harakatlar',
        'template' => '{leadPrice} {leadUpdate} {leadDelete}',
        'urlCreator' => function($action, $model, $key, $index) { 
                return Url::to([$action,'id'=>$key]);
        },
        'buttons'=>[
            'leadImage' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/warehouse-file/index' , 'warehouse_id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-picture"></span>', $url, [ 'data-pjax' => 0, 'data-toggle'=>'tooltip', 'title'=>'Rasm yuklash','class'=>'btn btn-info btn-xs']);
                }
            },
            'leadPrice' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/prices/update' , 'warehouse_id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-usd"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'Narx','class'=>'btn btn-warning btn-xs']);
                }
            },
            'leadUpdate' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1 || \Yii::$app->user->identity->permission == 5){
                    $url = Url::to(['/warehouse/update', 'id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-success btn-xs']);
                 }
             },
            'leadDelete' => function ($url, $model) {
                if(\Yii::$app->user->identity->permission == 1){
                    $url = Url::to(['/warehouse/one-delete', 'id' => $model->id]);
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [ 'role'=>'modal-remote', 'data-toggle'=>'tooltip', 'title'=>'O\'zgartirish','class'=>'btn btn-danger btn-xs']);
                }
            }
            // 'leadDelete' => function ($url, $model) {
            //     if(\Yii::$app->user->identity->permission == 1){
            //         // Create the button to trigger the modal
            //         return Html::a('<span style="font-size: 12px;" class="glyphicon glyphicon-trash"></span>', '#', [
            //             'data-toggle' => 'modal',
            //             'data-target' => '#deleteModal-'.$model->id, // Unique modal ID
            //             'title' => 'O\'chirish',
            //             'class' => 'btn btn-danger btn-xs',
            //         ]) .
            //         // Modal HTML
            //         '<div class="modal fade" id="deleteModal-'.$model->id.'" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel-'.$model->id.'" aria-hidden="true">
            //             <div class="modal-dialog" role="document">
            //                 <div class="modal-content">
            //                     <div class="modal-header">
            //                         <h5 class="modal-title" id="deleteModalLabel-'.$model->id.'"><b style="font-size:16px;"></b>Haqiqatdan ham <b style="font-size:16px;color:red">'.$model->brand->name.'</b> modelli mahsulotni oʻchirib tashlamoqchimisiz?</h5>
            //                     </div>
            //                     <div class="modal-body">
            //                         <p>Mahsulotni o\'chirishdan oldin izoh kiriting:</p>
            //                         '.Html::beginForm(['/warehouse/delete', 'id' => $model->id], 'post').'
            //                         <div class="form-group">
            //                             '.Html::input('text', 'delete_reason', '', ['class' => 'form-control', 'placeholder' => 'Sababini kiriting...', 'required' => true]).'
            //                         </div>
            //                     </div>
            //                     <div class="modal-footer">
            //                         <button type="button" class="btn btn-secondary" data-dismiss="modal">Bekor qilish</button>
            //                         '.Html::submitButton('O\'chirish', ['class' => 'btn btn-danger']).'
            //                         '.Html::endForm().'
            //                     </div>
            //                 </div>
            //             </div>
            //         </div>';
            //     }
            // },
        ],
    ],

];   