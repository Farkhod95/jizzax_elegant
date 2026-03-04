<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Brands;
use app\models\ProductCategory;

$brands = ArrayHelper::map(Brands::find()->where(['sup_status' => 1])->orderBy('name')->all(), 'id', 'name');
$categories = ArrayHelper::map(ProductCategory::find()->where(['sup_status' => 1])->orderBy('name')->all(), 'id', 'name');

if ($model->isNewRecord && $model->type === null) {
    $model->type = 1;
}
?>

<div class="brand-categories-img-form">
    <?php $form = ActiveForm::begin(); ?>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?= $form->field($model, 'type')->dropDownList([
                1 => 'Rasm',
                // 2 => 'Video',
            ], ['id' => 'type-select']) ?>
        </div>
    </div>
    <div class="col-md-6">
        <div id="image-upload-block" style="<?= $model->type == 2 ? 'display: none;' : '' ?>">
            <div class="form-group" id="image">
                <?= Html::img($model->getImage(), [
                    'style' => 'width:220px; height:220px; object-fit: cover;',
                    'class' => 'img-thumbnail',
                ]) ?>
            </div>
            <div class="form-group">
                <?= $form->field($model, 'image')->fileInput(['class' => "image_input", 'accept' => 'image/*']); ?>
            </div>
        </div>
    </div>
</div>
        

    <div class="form-group" id="video-upload-block" style="<?= $model->type == 1 ? 'display: none;' : '' ?>">
        <?= $form->field($model, 'video')->fileInput(['accept' => 'video/*']); ?>
    </div>

    <?php if (!Yii::$app->request->isAjax): ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'
            ]) ?>
        </div>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>
</div>

<style>
    .custom-dropdown { position: relative; }
    .dropdown-menu.show-on-focus {
        display: none;
        position: absolute;
        z-index: 1055;
        background: #fff;
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        border-radius: 0.25rem;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        padding: 6px 12px;
        cursor: pointer;
    }
    .dropdown-item:hover {
        background-color: #f0f0f0;
    }
</style>

<?php
$jsBrands = json_encode($brands);
$jsCategories = json_encode($categories);
$this->registerJs(<<<JS
window.brandsData = $jsBrands;
window.categoriesData = $jsCategories;

function setupDropdown(inputId, dropdownId, hiddenId, items) {
    const input = $(inputId);
    const dropdown = $(dropdownId);
    const hidden = $(hiddenId);

    input.off('focus input');
    input.on('focus', () => {
        dropdown.addClass('show');
        filterItems();
    });
    input.on('input', filterItems);

    function filterItems() {
        const search = input.val().toLowerCase();
        let html = '';
        for (const id in items) {
            if (items[id].toLowerCase().includes(search)) {
                html += `<div class='dropdown-item' data-id='\${id}'>\${items[id]}</div>`;
            }
        }
        dropdown.html(html);
    }

    dropdown.off('click').on('click', '.dropdown-item', function () {
        input.val(\$(this).text());
        hidden.val(\$(this).data('id'));
        dropdown.removeClass('show');
    });

    \$(document).on('click', function (e) {
        if (!\$(e.target).closest(input).length && !\$(e.target).closest(dropdown).length) {
            dropdown.removeClass('show');
        }
    });
}

function toggleUploadBlocks() {
    const type = \$('#type-select').val();
    if (type === '1') {
        \$('#image-upload-block').show();
        \$('#video-upload-block').hide();
    } else {
        \$('#image-upload-block').hide();
        \$('#video-upload-block').show();
    }
}

function initBrandFormDropdowns() {
    setupDropdown('#brand-search', '#brand-dropdown', '#brand_id_hidden', window.brandsData);
    setupDropdown('#category-search', '#category-dropdown', '#category_id_hidden', window.categoriesData);
    \$('#type-select').off('change').on('change', toggleUploadBlocks);
    toggleUploadBlocks();

    \$(document).off('change', '.image_input').on('change', '.image_input', function (e) {
        const files = e.target.files;
        if (!files.length) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = `<img src="\${e.target.result}" style="width:220px; height:220px; object-fit:cover;" class="img-thumbnail">`;
            \$('#image').html(preview);
        };
        reader.readAsDataURL(files[0]);
    });
}

\$(document).on('loaded.bs.modal shown.bs.modal', '#ajaxCrudModal', function () {
    initBrandFormDropdowns();
});

\$(document).ready(function () {
    initBrandFormDropdowns();
});
JS
);
?>
