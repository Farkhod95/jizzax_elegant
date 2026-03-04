<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">
            <div id="image" class="col-md-12">
                <?=Html::img($model->getAvatar(), [
                    'style' => 'width:220px; height:220px;object-fit: cover;',
                    'class'=>'img-circle',
                ])?>
            </div>
            <br>
            <div class="col-md-12">
                <?= $form->field($model, 'image')->fileInput(['class'=>"image_input", 'accept'=> 'image/*']); ?>
            </div>
        </div>
        <div class="col-md-8">

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $model->isNewRecord ? $form->field($model, 'password')
                            ->passwordInput(['maxlength' => true, 'class' => 'form-control']) : $form->field($model, 'new_password')
                            ->passwordInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'phone')->widget(\yii\widgets\MaskedInput::className(), [
                        'mask' => '+\9\9899-999-99-99',
                        'options' => [
                            'placeholder' => '+998-99-999-99-99',
                            'class'=>'form-control',
                        ]
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'permission')->dropDownList(\app\models\Users::getRole(), ['prompt' => 'Rolni tanlang','disabled' => \app\models\Users::SUPER_ADMIN_ID == $model->id ? true : false]) ?>
                </div>
            </div>
        </div>
    </div>


    <?php if (!Yii::$app->request->isAjax){ ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(<<<JS
    
$(document).ready(function(){
    var fileCollection = new Array();

    $(document).on('change', '.image_input', function(e){
        var files = e.target.files;
        $.each(files, function(i, file){
            fileCollection.push(file);
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e){
                var template = '<img style="width:220px; height:220px; object-fit: cover;" class="img-circle"  src="'+e.target.result+'"> ';
                $('#image').html('');
                $('#image').append(template);
            };
        });
    });
});
JS
);
?>
