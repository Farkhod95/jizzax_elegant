<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'Mahsulotlar ro\'yxati';

?>
<div class="row">
	<div class="col-xl-6">
        <div class="panel panel-inverse" data-sortable-id="table-basic-4">
            <!-- begin panel-heading -->
            <div class="panel-heading">
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-default" data-click="panel-expand"><i
                                class="fa fa-expand"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                       data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger"
                       data-click="panel-remove"><i class="fa fa-times"></i></a>
                </div>
                <h4 class="panel-title">Mahsulotlar Ro'yxati</h4>
            </div>
            <!-- end panel-heading -->
            <!-- begin panel-body -->
            <div class="panel-body">
                <!-- begin table-responsive -->
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
                            <!-- <tr>
                                <td>4</td>
                                <td>FD-2</td>
                                <td>Kosa</td>
                                <td>9</td>
                                <td>120</td>
                            </tr> -->
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
                            <!-- <tr>
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
                            </tr> -->
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
                            <!-- <tr>
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
                            </tr> -->
                            <tr>
                                <td colspan="4" style="background-color:#2d353c;"><b style="color:white">Jami</b></td>
                                <td style="background-color:#2d353c;"><b style="color:white">2280</b></td>
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