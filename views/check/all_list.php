<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\CheckWarehouse;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use app\models\Brands;

$brands = Brands::find()->orderBy(['sorting' => SORT_ASC])->all();
$this->title = 'Mahsulotlar ro\'yxati';

$allMarkCount = 0
?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="/check/index" class="btn btn-xs  btn-warning"> <i class="fa fa-reply"></i> Orqaga qaytish </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title"><?= $date ?> - sanadagi tekshirilgan mahsulotlar Ro'yxati</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <label style="font-size: 14px;" for="cars">Modelni tanlang:</label>
                    <select name="cars" id="cars" style="font-size: 14px;padding:1px;width:150px">
                            <option ></option>
                        <?php  foreach ($brands as $brand) {?>
                            <option value=<?= $brand->name?>><?= $brand->name?></option>
                        <?php  } ?>
                    </select>
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="background-color:#90e6e6;"><b>#</b></th>
                                <th style="background-color:#90e6e6;" nowrap><b>Model</b></th>
                                <th style="background-color:#90e6e6;" nowrap><b>Nomi</b></th>
                                <th style="background-color:#90e6e6;" nowrap><b>O'lchami</b></th>
                                <th style="background-color:#90e6e6;" nowrap><b>Soni</b></th>
                            </tr>
                        </thead>
                        <tbody id="showRes">
                            <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0;?>
                                <tr class="handle-header">
                                        <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                        <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($warehouses = CheckWarehouse::find()
                                    ->alias('w')
                                    ->select(["w.*", "pc.sorting"])
                                    ->andWhere(['w.brand_id' => $model->brand->id])
                                    ->andWhere(['w.check_id' => $check_id])
                                    ->leftJoin("product_category pc", "w.product_category_id = pc.id")
                                    ->orderBy(['pc.sorting' => SORT_ASC])->all()as $model1) 
                                    { ?>
                                    <tr class="handle" id="<?= $model->brand->name . "_" . $i?>">
                                        <td style="background-color:#efdfdf;"><b><?= $i ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->brand->name ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->size ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->count ?></b></td>
                                   
                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1->count; $allMarkCount = $allMarkCount + $model1->count;} ?>
                                <tr class="<?= $model->brand->name ?>-amount">
                                
                                        <td colspan="3"  ><b ></b></td>
                                        <td ><b style="color:#000" >Jami:</b></td>
                                        <td ><b style="color:#000"><?= $allCount ?></b></td>
                                </tr>
                            <?php } ?>
                            
                         
                            <tr>
                                    <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                    <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            <!-- end panel-body -->
        </div>
    </div>
</div>

<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$('#cars').on('change', function(e){
    e.preventDefault();
    const value = $(this).val().toUpperCase();
    if (value) {
        $("#showRes .handle").filter(function() {
        const td = $(this).children('td').eq(1).text().toUpperCase();
        // console.log("td:", td);
        $(this).toggle(td === value)
        });

        $("#showRes .handle-header").filter(function() {
            const td = $(this).children('td').eq(0).text().toUpperCase();
            $(this).toggle(td === value)
        });

        $("#showRes tr").filter(function() {
            const className = $(this).attr('class').toUpperCase();
            if(className.indexOf('AMOUNT') > -1){
                $(this).toggle(className == value + '-AMOUNT')
            }
        });
    }else{
        $("#showRes tr").filter(function() {
        $(this).show()
        });
    }
});

JS
) ?>