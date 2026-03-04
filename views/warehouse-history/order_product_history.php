<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use app\models\OrderProducts;
use app\models\ProductCategory;
use app\models\Brands;

$warehouseHistory = OrderProducts::find()->all();
$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0;

$brands = new Brands();
$productCategory = new ProductCategory();
?>

<div class="row">
	<div class="col-md-12">
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
                <h4 class="panel-title">Sotilgan tovarlar tarixi : <?= $start_date?> dan <?= $end_date ?> gacha</h4>
            </div>
            <div class="panel-body">
                <div class="x_panel">
                    <div class="x_content">
                        <?php $form = ActiveForm::begin(['action' => '/warehouse-history/product',]);?>
                            <div class="form-group">
                                <div class="row"> 
                                    <div class="col-md-2 ">
                                        <input style= "font-size: 14px;padding:3px;" type="date" name="start_date">
                                        <label style= "font-size: 14px;padding:4px;">dan</label>
                                    </div>
                                    <div class="col-md-2 ">
                                        <input style= "font-size: 14px;padding:3px;" type="date" name="end_date">
                                        <label style= "font-size: 14px;padding:4px;">gacha</label>
                                    </div>
                                    <div class="col-md-1 ">
                                        <?php echo Html::submitButton(Yii::t('app', 'Qidirish'), ['class' => 'btn btn-primary btn-round']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php ActiveForm::end();?>
                    </div>
                    
                </div>
                <div class="row row-space-10">
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
                            <?php
                            function group_by($array, $keys, $ii) {
                                $return = array();
                                $append = (sizeof($keys) > 1 ? "_" : null);
                                $i = 0;
                                foreach($array as $val){
                                    $final_key = "";
                                    foreach($keys as $theKey){
                                        $final_key .= $val[$theKey] . $ii;
                                    }
                                    $return[$final_key][] = $val;
                                    
                                }
                                $i = $i + 1;
                                return $return;
                            }
                            function group_by_summ($data) {
                                $return = array();
                                foreach($data as $val){
                                    $count_all = 0;
                                    
                                    foreach($val as $val_one){
                                        $count_all = $count_all + $val_one['count'];
                                    }
                                    $return [] =[
                                        'brand_id' => $val[0]['brand_id'], 
                                        'product_category_id' => $val[0]['product_category_id'],
                                        'size' => $val[0]['size'],
                                        'count' => $count_all, 
                                    ];
                                    
                                }
                                return $return;
                            }
                            $ii = 0;
                            $allPriceSum = 0; foreach ($orderProducts as $model) { $i = 1; $allCount = 0; $priceSum = 0;?>
                                <tr class="handle-header">
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  
                                
                                $warehouses = OrderProducts::find()->andWhere(['brand_id' =>$model->brand_id ])->andWhere(['between', 'cr_date', $start_date, $end_date])->all();
                                $ordersArray = array();
                                foreach ($warehouses as $model2) {
                                    $ordersArray [] = [
                                        'brand_id' => $brands->getBrands($model2->brand_id), 
                                        'product_category_id' => $productCategory->getProductCategory($model2->product_category_id), 
                                        'size' => $model2->size, 
                                        'count' => $model2->count, 
                                    ];
                                }
                                $data = group_by($ordersArray, ['brand_id','product_category_id','size'], $ii);
                                $data_all = group_by_summ($data);
                                foreach ($data_all as $model1) 
                                    { 
                                    ?>
                                    <tr class="handle">
                                        <td style="background-color:#efdfdf;"><b><?= $i ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1['brand_id']?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1['product_category_id'] ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1['size'] ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1['count'] ?></b></td>

                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1['count']; $allMarkCount = $allMarkCount + $model1['count']; } ?>
                                <tr >
                                 
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
            </div>
        </div>
    </div>
</div>