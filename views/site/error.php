<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <!-- begin error -->
    <div class="error">
        <div class="error-code m-b-10"><?=$exception->statusCode?> <i class="fa fa-warning"></i></div>
        <div class="error-content" style="background-color:#f2f3f4">
            <div class="error-message"><?=$message?></div>
            <div class="error-desc m-b-20">
                Agar bu server xatosi bo'lsa. <br>
                Qo‘llab-quvvatlash xizmatiga murojaat qiling.
            </div>
            <div>
                <a href="/" class="btn btn-success">Asosyi sahifaga qaytish</a>
            </div>
        </div>
    </div>
    <!-- end error -->

</div>
