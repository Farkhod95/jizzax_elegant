<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';

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
            <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th nowrap>Model</th>
                                <th nowrap>Nomi</th>
                                <th nowrap>O'lchami</th>
                                <th nowrap>Soni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" style="background-color:#a0d9ea;" ><b style="color:red">FD-2</b></td>
                                <td style="background-color:#a0d9ea;"><b style="color:red">480</b></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color:#a0d9ea;"><b style="color:red">Z</b></td>
                                <td style="background-color:#a0d9ea;"><b style="color:red">600</b></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td colspan="4" style="background-color:#a0d9ea;"><b style="color:red">M</b></td>
                                <td style="background-color:#a0d9ea;"><b style="color:red">1200</b></td>
                            </tr>
                            <tr>
                                <td>1</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <!-- <tr>
                                <td>5</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr> -->
                            <!-- <tr>
                                <td>7</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>8</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr> -->
                            <tr>
                                <td>9</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>10</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>11</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <!-- <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white">2280</b></td>
                            </tr> -->
                        </tbody>
                    </table>
                    <a href="#modal-dialog" class="btn btn-sm btn-danger" style="width:100%" data-toggle="modal">Sotish</a>
                </div>
            </div>
        </div>
    </div>
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
                <h4 class="panel-title">Mijoz buyurtmasi</h4>
            </div>
            <div class="panel-body">
            <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th nowrap>Model</th>
                                <th nowrap>Nomi</th>
                                <th nowrap>O'lchami</th>
                                <th nowrap>Soni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Z</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>M</td>
                                <td>Piyola</td>
                                <td>9</td>
                                <td>120</td>
                            </tr>
                           <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white">720</b></td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <a href="#modal-dialog2" class="btn btn-sm btn-danger" style="width:100%" data-toggle="modal">Sotish</a>
                    <!-- <button type="button" class="btn btn-danger" style="width:100%">Sotish</button> -->
                    <div class="modal fade" id="modal-dialog2">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Mijoz buyurtmasiga qo'shish</h4>
                                    <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button> -->
                                </div>
                                
                                <div class="modal-body">
                                    <form>
                                        
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h5><b>Model:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red">FD-2</b ></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-4 col-form-label"><h5><b>Nomi:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red">Kosa</b></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h4><b>Soni</b></h4></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" placeholder="" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                                    <a href="javascript:;" class="btn btn-danger">Buyurtmaga qo'shish</a>
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
                                    <form>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h4><b>Xaridor</b></h4></label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" placeholder="" />
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15">
                                            <label class="col-sm-4 col-form-label"><h5><b>Tavarlar soni:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red">6</b ></h5></label>
                                            </div>
                                        </div>
                                        <div class="form-group row m-b-15 align-items-center">
                                            <label class="col-sm-4 col-form-label"><h5><b>Umumiy soni:</b></h5></label>
                                            <div class="col-sm-8">
                                                <label ><h5><b style="color:red">150</b></h5></label>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <a href="javascript:;" class="btn btn-white" data-dismiss="modal">Bekor qilish</a>
                                    <a href="javascript:;" class="btn btn-danger">Sotishni tasdiqlash</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>