<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\models\Warehouse;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Url;

use app\models\Brands;

$brands = Brands::find()->orderBy(['sorting' => SORT_ASC])->all();

$this->title = 'Mahsulotlar ro\'yxati';
$allMarkCount = 0
?>
<div class="row">
	<div class="col-md-6">
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
                <h4 class="panel-title">Ombordagi mahsulotlar</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive" >
                    <label style="font-size: 14px;" for="cars">Modelni tanlang:</label>
                    <select name="cars" id="cars" style="font-size: 14px;padding:1px;width:150px">
                            <option ></option>
                        <?php  foreach ($brands as $brand) {?>
                            <option value=<?= $brand->name?>><?= $brand->name?></option>
                        <?php  } ?>
                    </select>
                    <table class="table">
                        <thead >
                            <tr >
                                <th style="background-color:#90e6e6;"><b>#</b></th>
                                <th nowrap style="background-color:#90e6e6;"><b>Model</b></th>
                                <th nowrap style="background-color:#90e6e6;"><b>Nomi</b></th>
                                <th nowrap style="background-color:#90e6e6;"><b>O'lchami</b></th>
                                <th nowrap style="background-color:#90e6e6;"><b>Soni</b></th>
                            </tr>
                        </thead>
                        <tbody id="showRes">
                            <?php  foreach ($warehouse as $model) { $i = 1; $allCount = 0;?>
                                <tr class="handle-header">
                                    <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red"><?= $model->brand->name ?></b></td>
                                    <td style="background-color:#a0d9ea;"></td>
                                </tr>
                                <?php  foreach ($warehouses = Warehouse::find()
                                        ->alias('w')
                                        ->select(["w.*", "pc.sorting"])
                                        ->where(['w.brand_id' => $model->brand->id])
                                        ->leftJoin("product_category pc", "w.product_category_id = pc.id")
                                        ->orderBy(['pc.sorting' => SORT_ASC])->all() as $model1) 
                                { ?>
                                    <tr class="handle" id="<?= $model->brand->name . "_" . $i?>">
                                        <td style="background-color:#efdfdf;"><b><?= $i ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->brand->name ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->product_category_id ? $model1->productCategory->name :'' ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->size ?></b></td>
                                        <td style="background-color:#efdfdf;"><b><?= $model1->count ?></b></td>
                                        <td style="display:none;"> <?= $model1->id ?> </b></td>
                                    </tr>
                                <?php  $i = $i +1 ; $allCount = $allCount + $model1->count; $allMarkCount = $allMarkCount + $model1->count; } ?>
                                <tr class="<?= $model->brand->name ?>-amount">
                                    <td style="background-color:#fff;" colspan="3"  ><b ></b></td>
                                    <td style="background-color:#fff;"><b style="color:#000" >Jami:</b></td>
                                    <td style="background-color:#fff;"><b style="color:#000"><?= $allCount ?></b></td>
                                </tr>
                            <?php  } ?>
                            
                         
                            <!-- <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami:</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white"><?= $allMarkCount ?></b></td>
                            </tr> -->
                        </tbody>
                    </table>
                    <!-- <a href="#modal-dialog" class="btn btn-sm btn-danger" style="width:100%" data-toggle="modal">Sotish</a> -->
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6" style="position: sticky; top: 60px;">
        <div class="panel panel-inverse" >
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">Mijoz buyurtmasi</h4>
            </div>
            <div class="panel-body" >
            <div class="table-responsive" >
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
                        <tbody id="backet" data-count="0" data-increment="0">
                           <tr id="add">
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white" id="all">0</b></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <a href="#" class="btn btn-sm btn-danger buy_product" style="width:100%">Sotish</a>

                    <!-- <button type="button" class="btn btn-danger" style="width:100%">Sotish</button> -->
                   
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-dialog2">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Mijoz buyurtmasiga qo'shish 1</h4>
                                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                                </div>
                                
                                <div class="modal-body">
                                    <form id="add_to_backet">
                                        <input type="hidden" name="product_id">
                                        <input type="hidden" name="size">
                                        <input type="hidden" name="key">
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h5><b>Model:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red" id="marka">FD-2</b ></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-4 col-form-label"><h5><b>Nomi:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red" id="name">Kosa</b></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-4 col-form-label"><h5><b>O'lchami:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red" id="size">Kosa</b></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h4><b>Soni</b></h4></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="soni" placeholder="" />
                                                <span class="error_message text-danger"></span>
                                            </div>
                                            
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                                    <a  class="btn btn-danger submit" >Buyurtmaga qo'shish</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Sotish</h4>
                                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                                </div>
                                <div class="modal-body">
                                    <form id="buy" action="<?=Url::toRoute(['orders/accept'])?>" method="post">
                                        <input type="hidden" class="form-control" placeholder="" name="product_details"/>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h4><b>Xaridor</b></h4></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" required placeholder="" name="customer_name"/>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h5><b>Tavarlar soni:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red" id="count_porduct"></b ></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-4 col-form-label"><h5><b>Umumiy soni:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red" id="total_product"></b></h5></label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                                            <button type="submit"  class="btn btn-danger">Sotishni tasdiqlash</button>
                                        </div>
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal fade" id="modal-dialog-error">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Xatolik!</h4>
                                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                                </div>
                                <div class="modal-body">
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-12 col-form-label"><h5><b style="color:red">Mijozda hech qanday buyurtma yo'q. </b></h5></label>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Yopish</a>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
<?php
$this->registerJsFile('/js/cookie.js');

$this->registerJs(<<<JS

$('#cars').on('change', function(e){
    e.preventDefault();
    const value = $(this).val().toUpperCase();;
    if (value) {
        $("#showRes .handle").filter(function() {
        const td = $(this).children('td').eq(1).text().toUpperCase();
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

$("#buy").submit(function(event){
    event.preventDefault();

    let action = $(this).attr("action");

    let payload = {
        customer_name: $('input[name="customer_name"]').val(),
        total: $("#count_porduct").text(),
        count: $("#total_product").text(),
        product_details: $('input[name="product_details"]').val(),
    };

    // console.log(payload.product_details);

    $.ajax({
        url: action,
        data: payload,
        method: "POST", 
    }).done(function(data) {
        $( this ).addClass( "done" );
        // alert(data);
    });

    onClick="this.disabled=true; this.value='Sending…';"

});

$(".buy_product").on('click', function(){

    let all_total = $("#backet").attr("data-count");
    let products_count = $("#backet").attr("data-increment");

    let product_details = [];
    
    $("#backet").children().each(function(){
        if($(this).attr("id") !== "add"){
            product_details.push({
                marka: $(this).children().eq(1).text(),
                name: $(this).children().eq(2).text(),
                size: $(this).children().eq(3).text(),
                count: $(this).children().eq(4).text(),
                product_id: $(this).children().eq(5).text()
            });
        }
    });
    console.log("product_details:", product_details);
    // console.log(product_details);

    $('input[name="product_details"]').val(JSON.stringify(product_details));
    $('#count_porduct').text(products_count);
    $("#total_product").text(all_total);
    
    if(all_total == 0){
        // alert("Mijozda hech qanday buyurtma yo'q");
        $("#modal-dialog-error").modal("toggle");
        return false;
    }

    $("#modal-dialog").modal("toggle");

});

$('.handle').on("click", function(){
    $(".error_message").text("");
    let count = $(this).children().eq(4).text();
    let mark = $(this).children().eq(1).text();
    let name = $(this).children().eq(2).text();
    let size = $(this).children().eq(3).text();
    let product_id = $(this).children().eq(5).text();

    $("#marka").text(mark);
    $("#name").text(name);
    $("#size").text(size);

    $('input[name="product_id"]').val(product_id);
    $('input[name="soni"]').val(count);
    $('input[name="size"]').val(size);
    $('input[name="key"]').val($(this).attr('id'));

    $("#modal-dialog2").modal();
});

$(".submit").on("click", function(event){
    event.preventDefault();
    $(".error_message").text("");
    let count_product = $('input[name="soni"]').val();
    let name = $('#name').text();
    let marka = $('#marka').text();
    let product_id = $('input[name="product_id"]').val();
    let size = $('input[name="size"]').val();
    let key = $('input[name="key"]').val();
    let count = parseInt($("#backet").attr("data-count"));
    let increment = parseInt($("#backet").attr("data-increment")) + 1;
    count = count + parseInt(count_product);

    // Validate data
    console.log("event:", event)
    let count_old = parseInt($("#" + key).children().eq(4).text());

    if(count_product == 0 ){
        $(".error_message").text("0 ta buyurtma berib bo'lmaydi");
        return false;
    }

    if(count_product == 0 || count_product > count_old){
        $(".error_message").text("Minimal qiymat "+ count_old +" ta bo'lishi kerak");
        return false;
    }


    count_old = count_old - count_product;

    let id = key.split("_");
    let ammount_value = $("." + id[0] + "-amount").children().eq(2).text();
    ammount_value = ammount_value - count_product;
    $("." + id[0] + "-amount").children().eq(2).text(ammount_value)

    $("#" + key).children().eq(4).text(count_old);

    $("#add").before("<tr><td style='background-color:#efdfdf;'><b>" + increment + "</b></td><td style='background-color:#efdfdf;'><b>" + marka + "</b></td><td style='background-color:#efdfdf;'><b>" + name + "</b></td><td style='background-color:#efdfdf;'><b>" + size + "</b></td><td style='background-color:#efdfdf;'><b>" + count_product + "</b></td><td style='display:none;'>" + product_id + "</td></tr>");
    $("#all").text(count);
    $("#backet").attr("data-increment", increment)
    $("#backet").attr("data-count", count)
    $("#modal-dialog2").modal('toggle');
});

JS
) ?>