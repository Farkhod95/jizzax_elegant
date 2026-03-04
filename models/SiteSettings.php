<?php

namespace app\models;

use Yii;


class SiteSettings extends Settings
{
    public $img;

    public $logo;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $telegram_bot_token;
    const DIR_NAME = 'settings';
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['address','telegram_bot_token'], 'string'],
            [['logo','email','name'], 'string', 'max' => 255],
            [['phone'],'ValidatePhones'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'settings';
    }


    /**
     * ushbu modelga tegisli barcha maydonlar
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'logo' => 'Логотип',
            'img' => 'Логотип',
            'name' => 'Название компании',
            'email' => 'E-mail',
            'phone' => 'Телефон номер',
            'address' => 'Адрес',
            'telegram_bot_token' => 'Токен телеграм бота',
        ];
    }


    /**
     * bazaga settingsga saqlanishi kerak bo'lgan maydonar
     * @return string[]
     */
    public function getNames()
    {
        return [
            'logo' => 'string',
            'name' => 'string',
            'email' => 'string',
            'phone' => 'array',
            'address' => 'string',
            'telegram_bot_token' => 'string'
        ];
    }


    /**
     * konstruktor
     * SiteSettings constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $values = Settings::find()->all();
        foreach ($values as $key => $value) {
            $this->{$value->key} = ($value->type == 'array') ? explode(",",$value->value) : $value->value;
        }
        parent::__construct();
    }


    /**
     * telefon nomerni validatsiya qilish
     * @param $attribute
     * @return bool
     */
    public function ValidatePhones($attribute)
    {
        $items = $this->$attribute;
        if (!is_array($items)) {
            $items = [];
        }
        $arr = [];
        foreach ($items as $index => $item) {
            $x = preg_replace('/\d/', '#', $item);
            if ($x != '+#####-###-##-##') {
                $key = $attribute . '[' . $index . ']';
                $this->addError($key, "Введите полностью");
            }else{
                $arr [] = $item;
            }
        }
        $this->$attribute = $arr;
        return true;
    }

    /**
     * soxranit qilish
     * @param $post
     * @throws \Throwable
     */
    public function saveModel($post)
    {
        $rows = Settings::getDb()->cache(function ($db) {
            return Settings::find()->all();
        });
        $rows = Settings::find()->all();
        foreach ($rows as $value) {
            if(isset($post['SiteSettings'][$value->key]) && $post['SiteSettings'][$value->key] != ''){
                $value->value = ($value->type == 'array') ? implode(",",$post['SiteSettings'][$value->key]) : $post['SiteSettings'][$value->key];
                $value->save();
            }
        }
    }


    /**
     * logoni olish
     * @param false $small
     * @param string $width
     * @param string $height
     * @return string
     */
    public function getImg($small = false, $width = "",$height = "")
    {
        $img = $this->logo;

        if ($this->logo == '' || !file_exists('uploads/'.self::DIR_NAME.'/'.$this->logo)){
            $path = '/web/img/noimg.jpg';
        }else{
            $path = Yii::$app->params['siteName']   . "/web/uploads/" . self::DIR_NAME . "/" . $this->logo;
        }

        if($width != "" && $height != "")
            return '<img style="width:'.$width.'px;height:'.$height.'" src="'.$path.'">';
        else
            return '<img style="width:80px;height:80px" src="'.$path.'">';
    }


    /**
     * rasm upload qilish
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function upload()
    {
        $path = self::DIR_NAME;
        $fileName ='logo'. '.' . $this->img->extension;
        if(!empty($this->img))
        {
            if(file_exists('uploads/'.$path.'/'.$this->logo) && $this->logo != null)
            {
                unlink('uploads/'.$path.'/'.$this->logo);
            }

            $this->img->saveAs('uploads/'.$path.'/'.$fileName);
            Yii::$app->db->createCommand()->update('settings', ['value' => $fileName], [ 'key' => 'logo' ])->execute();
        }
    }
}

