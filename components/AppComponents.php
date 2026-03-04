<?php

namespace app\components;

use yii\base\Component;

class AppComponents extends Component
{
    public $format;

    public function dateTime($date)
    {
        return date($this->format, strtotime($date));
    }
}