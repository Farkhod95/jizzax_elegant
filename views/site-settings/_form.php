<?php
use unclead\multipleinput\MultipleInput;

/**
 * @var $model
 * @var $form
 */
?>
    <div class="sitesettings-sitesettings-form" style="padding-right: 20px; padding-left: 20px; padding-top: 20px;">

        <?php
        $input = '<label for="avatar" title="Выберите" data-toggle="tooltip" id="image_label" onmousemove="style.cursor=\'pointer\'">'.$model->getImg('70px','70px').'</label>
        <input type="file" name="SiteSettings[img]" id="avatar" style="display: none;" accept="image/*">';

        $template = '<div class="row"><div class="col-md-2">
                {label}</div><div class="col-md-9">{input}'.$input.'{error}</div></div>
                ';
        ?>
        <?= $form->field($model, 'img',['template' => $template])->hiddenInput(['id' => 'temp_address','value' => $model->logo]) ?>
        <?php
            $template = '<div class="row"><div class="col-md-2">
                    {label}{hint}</div><div class="col-md-8">{input}{error}</div></div>';
        ?>

        <?= $form->field($model, 'name',['template' => $template])->textInput(['placeholder' => 'Название компании']) ?>
        <?= $form->field($model, 'email',['template' => $template])->textInput(['placeholder' => 'Введите email']) ?>
        <?= $form->field($model, 'phone',['template' => $template])->widget(MultipleInput::className(), [
            'max'               => 4,
            'min'               => 1,
            'allowEmptyList'    => false,
            'columns' => [
                [
                    'name' => 'phone',
                    'enableError' => true,
                    'type' => \yii\widgets\MaskedInput::className(),
                    'options' => [
                        'mask' => '+\9\9899-999-99-99',
                        'options' =>[
                            'class' => 'form-control'
                        ],
                    ],
                ],
            ],
            'addButtonPosition' => MultipleInput::POS_ROW, // show add button in the header
        ])?>
        <?= $form->field($model, 'address',['template' => $template])->textarea(['placeholder' => 'Введите адрес']) ?>

        <?= $form->field($model, 'telegram_bot_token',['template' => $template])->textInput(['placeholder' => 'Токен телеграм бота']) ?>
    </div>

<?php
$this->registerJs(<<<JS
    
$(document).ready(function(){
    var fileCollection = new Array();

    $(document).on('change', '#avatar', function(e){
        var files = e.target.files;
        $.each(files, function(i, file){
            fileCollection.push(file);
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e){
                var template = '<img style="width:70px; height:70px; object-fit: cover;"  src="'+e.target.result+'"> ';
                $('#image_label').html('');
                $('#image_label').append(template);
            };
        });
    });
});
JS
);
?>

