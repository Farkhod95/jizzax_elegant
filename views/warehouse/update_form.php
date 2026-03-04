<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\ProductCategory;
use app\models\BrandsSize;

/* @var $this yii\web\View */
/* @var $model app\models\Warehouse */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="warehouse-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php
    // --- PRODUCT CATEGORY uchun default data: faqat tanlangan brandga tegishli bo‘lsin ---
    $categoryData = [];
    if ($model->brand_id) {
        $categoryData = ArrayHelper::map(
            ProductCategory::find()
                ->where(['brand_id' => $model->brand_id])
                ->orderBy(['sorting' => SORT_ASC, 'name' => SORT_ASC])
                ->all(),
            'id',
            'name'
        );
    }

    // --- SIZE uchun default data:
    // Faqat HOZIRGI tanlangan size ni ko‘rsatamiz.
    // Barcha variantlar har doim Ajax orqali keladi (dublikatlar siqilib ketmasligi uchun).
    $sizeData = [];
    if (!empty($model->size)) {
        $sizeData[(string)$model->size] = $model->size;
    }
    ?>

    <div class="row">
        <!-- BRAND -->
        <div class="col-md-6">
            <?= $form->field($model, 'brand_id')->widget(Select2::classname(), [
                'data' => $model->getBrands(),
                'options' => [
                    'placeholder' => 'Modelni tanlang...',
                    'id' => 'warehouse-brand_id',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>
        </div>

        <!-- PRODUCT CATEGORY: faqat brandga tegishli -->
        <div class="col-md-6">
            <?= $form->field($model, 'product_category_id')->widget(Select2::classname(), [
                'data' => $categoryData,
                'language' => 'uz',
                'options' => [
                    'placeholder' => $model->brand_id ? 'Nomini tanlang...' : 'Avval modelni tanlang...',
                    'id' => 'warehouse-product_category_id',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <!-- SIZE: ProductCategory tanlanganda BrandsSize dan keladi -->
        <div class="col-md-6">
            <?= $form->field($model, 'size')->widget(Select2::classname(), [
                'data' => $sizeData,
                'options' => [
                    'placeholder' => 'Avval model va nomini tanlang...',
                    'id' => 'warehouse-size',
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]) ?>
        </div>

        <!-- TYPE: size tanlanganda avtomatik to‘ldiriladi -->
        <div class="col-md-6">
            <?= $form->field($model, 'type')->label()->widget(Select2::classname(), [
                'data' => $model->getProductClientType(),   // [1=>'Dona',2=>'Karobka',...]
                'options' => [
                    'placeholder' => Yii::t('app','Tanlang...'),
                    'id' => 'warehouse-type',
                    'disabled' => true, // faqat o‘qish uchun, avtomatik tanlanadi
                ],
                'pluginOptions' => [
                    'tags' => true,
                    'allowClear' => true,
                ],
            ]) ?> 
        </div>
    </div>

    <?php
    // TYPE qiymatini POST'ga yuborish uchun hidden input
    echo Html::activeHiddenInput($model, 'type', [
        'id'    => 'warehouse-type-hidden',
        'value' => $model->type,
    ]);
    ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'count')->textInput(['type' => 'number']) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'worker_price')->textInput() ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'comment')
                ->textInput()
                ->label("<b>Izoh </b><b style='color:red'>(Kiritish majburiy)</b>") ?>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'status_count')
                ->checkbox(['checked' => false])
                ->label("<b style='font-size:16px;color:red'>Mahsulot sanalganligini tasdiqlaysimi?: </b>") ?>
        </div>
    </div>

    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton(
                $model->isNewRecord ? 'Create' : 'Update',
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
            ) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>

<?php
$brandUrl      = Url::to(['warehouse/brand']);             // brand → category (option lar)
$sizesByCatUrl = Url::to(['warehouse/sizes-by-category']); // brand+category → sizes+type

// Hozirgi size va type qiymatini JSga berib yuboramiz (update holatida tanlangan bo‘ladi)
$selectedSizeJs = json_encode($model->size !== null ? (string)$model->size : null);
$selectedTypeJs = json_encode($model->type !== null ? (int)$model->type : null);

$js = <<<JS
// TYPE hidden input
var \$typeHidden = $('#warehouse-type-hidden');

// BRAND o'zgarganda: product_category, size va type ni tozalaymiz, brand bo'yicha kategoriya yuklaymiz
$('#warehouse-brand_id').on('change', function() {
    var brandId    = $(this).val();
    var \$category = $('#warehouse-product_category_id');
    var \$size     = $('#warehouse-size');
    var \$type     = $('#warehouse-type');

    // Category, Size, Type ni tozalaymiz
    \$category.html('<option value="">Nomini tanlang...</option>').val(null).trigger('change');
    \$size.html('<option value="">Avval model va nomini tanlang...</option>').val(null).trigger('change');
    \$type.val(null).trigger('change');
    \$typeHidden.val('');

    if (!brandId) {
        return;
    }

    // Brand bo'yicha kategoriyalarni olish
    $.post('{$brandUrl}?id=' + brandId, function(data) {
        // actionBrand dan "<option value=\"id\">name</option>" lar keladi
        \$category.html('<option value=\"\">Nomini tanlang...</option>' + data).trigger('change');
    });
});

// PRODUCT_CATEGORY o'zgarganda: aynan shu brand+category uchun BrandsSize dan size+type larni olish
$('#warehouse-product_category_id').on('change', function() {
    var brandId    = $('#warehouse-brand_id').val();
    var categoryId = $(this).val();
    var \$size     = $('#warehouse-size');
    var \$type     = $('#warehouse-type');

    // avval size va type ni tozalaymiz
    \$size.html('<option value=\"\">Olchamni tanlang...</option>').val(null).trigger('change');
    \$type.val(null).trigger('change');
    \$typeHidden.val('');

    if (!brandId || !categoryId) {
        return;
    }

    $.post('{$sizesByCatUrl}', {
        brand_id: brandId,
        product_category_id: categoryId
    }, function(res) {
        var optionsHtml = '<option value=\"\">Olchamni tanlang...</option>';

        if (res.sizes && res.sizes.length > 0) {
            res.sizes.forEach(function(item) {
                // har bir size option'iga o'z type'ini yozib qo'yamiz
                optionsHtml += '<option value=\"' + item.size + '\" data-type=\"' + item.type + '\">' + item.size + '</option>';
            });
        } else {
            optionsHtml = '<option value=\"\">Olcham topilmadi</option>';
        }

        \$size.html(optionsHtml).trigger('change');
    }, 'json');
});

// SIZE tanlanganda: tanlangan option'dan data-type ni o'qiymiz
$('#warehouse-size').on('change', function() {
    var \$type     = $('#warehouse-type');
    var \$selected = $(this).find('option:selected');
    var typeId     = \$selected.data('type');   // masalan 1, 2, 3, 4

    if (typeId) {
        // Select2 (type) ni ham, hidden inputni ham yangilaymiz
        \$type.val(String(typeId)).trigger('change');
        \$typeHidden.val(String(typeId));
    } else {
        \$type.val(null).trigger('change');
        \$typeHidden.val('');
    }
});

// --- Modal ochilganda (update holatida) brand + category allaqachon tanlangan bo'lsa,
// shu yerning o'zida /sizes-by-category'ni chaqirib, barcha size+type ni qayta yuklaymiz:
$(function () {
    var brandIdInit    = $('#warehouse-brand_id').val();
    var categoryIdInit = $('#warehouse-product_category_id').val();
    var \$size         = $('#warehouse-size');
    var \$type         = $('#warehouse-type');

    var selectedSizeInit = {$selectedSizeJs}; // masalan "4" yoki null
    var selectedTypeInit = {$selectedTypeJs}; // masalan 2 (Karobka), 4 (Pochka) va h.k.

    if (brandIdInit && categoryIdInit) {
        $.post('{$sizesByCatUrl}', {
            brand_id: brandIdInit,
            product_category_id: categoryIdInit
        }, function (res) {
            var optionsHtml = '<option value=\"\">Olchamni tanlang...</option>';

            if (res.sizes && res.sizes.length > 0) {
                res.sizes.forEach(function (item) {
                    var sizeVal = String(item.size);
                    var typeVal = parseInt(item.type, 10);
                    var selected = '';

                    // faqat size HAM, type HAM mos bo'lsa — selected
                    if (selectedSizeInit && selectedTypeInit !== null) {
                        if (sizeVal === String(selectedSizeInit) &&
                            typeVal === parseInt(selectedTypeInit, 10)) {
                            selected = ' selected';
                        }
                    }

                    optionsHtml += '<option value=\"' + sizeVal + '\" data-type=\"' + typeVal + '\"' + selected + '>' + sizeVal + '</option>';
                });
            } else {
                optionsHtml = '<option value=\"\">Olcham topilmadi</option>';
            }

            \$size.html(optionsHtml);

            if (selectedSizeInit && selectedTypeInit !== null) {
                // 'selected' atribut allaqachon to'g'ri option'da, faqat change trigget qilamiz
                \$size.trigger('change');
            } else {
                \$type.val(null).trigger('change');
                \$typeHidden.val('');
            }
        }, 'json');
    }
});
JS;

$this->registerJs($js);
?>
