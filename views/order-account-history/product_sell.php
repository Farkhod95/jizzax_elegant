<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\Brands[] $brands */

$this->title = 'Mahsulot sotuv tarixi';

$categoryUrl = Url::to(['order-account-history/product-sell-categories-by-brand']);
$sizesTypesUrl = Url::to(['order-account-history/product-sell-sizes-types']);

// Default: oxirgi 1 oy
$endDate = date('Y-m-d');
$startDate = date('Y-m-d', strtotime('-1 month'));
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand">
                        <i class="fa fa-expand"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse">
                        <i class="fa fa-minus"></i>
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <h4 class="panel-title">Mahsulot sotuv tarixi</h4>
            </div>

            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'action' => ['/order-account-history/client-product-sell'],
                    'method' => 'post',
                ]); ?>

                <div class="row">
                    <div class="col-md-3">
                        <label>Model</label>
                        <?= Select2::widget([
                            'name' => 'brand_id',
                            'id' => 'brand_id',
                            'data' => ArrayHelper::map($brands, 'id', 'name'),
                            'options' => [
                                'placeholder' => 'Model tanlang',
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>

                    <div class="col-md-3">
                        <label>Product category</label>
                        <?= Select2::widget([
                            'name' => 'product_category_id',
                            'id' => 'product_category_id',
                            'data' => [],
                            'options' => [
                                'placeholder' => 'Kategoriya tanlang',
                                'disabled' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>

                    <div class="col-md-3">
                        <label>Size</label>
                        <?= Select2::widget([
                            'name' => 'size',
                            'id' => 'size',
                            'data' => [],
                            'options' => [
                                'placeholder' => 'Size tanlang',
                                'disabled' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>

                    <div class="col-md-3">
                        <label>Type</label>
                        <?= Select2::widget([
                            'name' => 'type',
                            'id' => 'type',
                            'data' => [],
                            'options' => [
                                'placeholder' => 'Type tanlang',
                                'disabled' => true,
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ]); ?>
                    </div>
                </div>

                <div class="row" style="margin-top:15px;">
                    <div class="col-md-3">
                        <label>Boshlanish sana</label>
                        <input
                            type="date"
                            name="start_date"
                            class="form-control"
                            required
                            value="<?= Html::encode($startDate) ?>"
                        >
                    </div>

                    <div class="col-md-3">
                        <label>Tugash sana</label>
                        <input
                            type="date"
                            name="end_date"
                            class="form-control"
                            required
                            value="<?= Html::encode($endDate) ?>"
                        >
                    </div>

                    <div class="col-md-3" style="padding-top:25px;">
                        <?= Html::submitButton('Qidirish', ['class' => 'btn btn-primary btn-round']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(<<<JS
function resetCategory() {
    $('#product_category_id').html('').val(null).trigger('change');
    $('#product_category_id').prop('disabled', true);
}

function resetSizeType() {
    $('#size').html('').val(null).trigger('change');
    $('#type').html('').val(null).trigger('change');
    $('#size').prop('disabled', true);
    $('#type').prop('disabled', true);
}

$('#brand_id').on('change', function () {
    var brandId = $(this).val();

    resetCategory();
    resetSizeType();

    if (!brandId) {
        return;
    }

    $.get('{$categoryUrl}', {brand_id: brandId}, function (res) {
        var options = '<option value=""></option>';
        if (res.results) {
            res.results.forEach(function(item) {
                options += '<option value="' + item.id + '">' + item.text + '</option>';
            });
        }
        $('#product_category_id').html(options).prop('disabled', false).trigger('change');
    });
});

$('#product_category_id').on('change', function () {
    var brandId = $('#brand_id').val();
    var categoryId = $(this).val();

    resetSizeType();

    if (!brandId || !categoryId) {
        return;
    }

    $.get('{$sizesTypesUrl}', {
        brand_id: brandId,
        product_category_id: categoryId
    }, function (res) {
        var sizeOptions = '<option value=""></option>';
        var typeOptions = '<option value=""></option>';

        if (res.sizes) {
            res.sizes.forEach(function(item) {
                sizeOptions += '<option value="' + item.id + '">' + item.text + '</option>';
            });
        }

        if (res.types) {
            res.types.forEach(function(item) {
                typeOptions += '<option value="' + item.id + '">' + item.text + '</option>';
            });
        }

        $('#size').html(sizeOptions).prop('disabled', false).trigger('change');
        $('#type').html(typeOptions).prop('disabled', false).trigger('change');
    });
});
JS
);
?>