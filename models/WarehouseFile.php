<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
 

class WarehouseFile extends \yii\db\ActiveRecord
{
    const DIR_IMAGE = 'product';

    public static function tableName()
    {
        return 'warehouse_file';
    }

    public $image;
    public $video;

    public function rules()
    {
        return [
            // [['type'], 'required'],
            [['warehouse_id'], 'integer'],
            [['created_at'], 'safe'],
            [['file_path'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 50],
            [['warehouse_id'], 'exist', 'skipOnError' => true, 'targetClass' => Warehouse::className(), 'targetAttribute' => ['warehouse_id' => 'id']],
            [['image'], 'file', 'extensions' => ['png', 'jpg', 'jpeg'], 'skipOnEmpty' => true],
            [['video'], 'file', 'extensions' => ['mp4', 'avi', 'mov'], 'skipOnEmpty' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Fayl Tip',
            'warehouse_id' => 'Omborxona',
            'file_path' => 'Fayl',
            'created_at' => 'Yaratilgan vaqt',
            'image' => 'Rasm',
        ];
    }

    public function beforeSave($insert)
    {   
        // $this->cr_date = date('Y-m-d H:i:s');
        // $this->cr_date_time = date('Y-m-d H:i:s');
        if ($this->isNewRecord){
            $this->created_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

    public function getWarehouse()
    {
        return $this->hasOne(Warehouse::className(), ['id' => 'warehouse_id']);
    }

    public static  function getType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Rasm',],
            ['id' => '2', 'type' => 'Video',],
        ],
        'id', 'type');
    }

    public function getTypeNameView($name)
    {
        if($name == 'Rasm') return 1;
        if($name == 'Video') return 2;
    }
    
    public function getTypeView($id)
    {
        if($id == 1) return 'Rasm';
        if($id == 2) return 'Video';
    }
    
    public static function getStatusType()
    {
        return ArrayHelper::map([
            ['id' => '1', 'type' => 'Dona',],
            ['id' => '2', 'type' => 'Karobka',],
            ['id' => '3', 'type' => 'Komplekt',],
            ['id' => '4', 'type' => 'Pochka',],
        ],
        'id', 'type');
    }

    public function getStatusTypeNameView($name)
    {
        if($name == 'Dona') return 1;
        if($name == 'Karobka') return 2;
        if($name == 'Komplekt') return 3;
        if($name == 'Pochka') return 4;
    }
    
    public function getStatusTypeView($id)
    {
        if($id == 1) return 'Dona';
        if($id == 2) return 'Karobka';
        if($id == 3) return 'Komplekt';
        if($id == 4) return 'Pochka';
    }
    public function getImage()
    {
        if (empty($this->file_path)) {
            return '/web/img/noimg.jpg';
        }

        $path = $this->type == 1 ? 'product' : 'video';
        return "/web/uploads/{$path}/" . $this->file_path;
    }

    public function upload()
    {
        $file = $this->type == 1 ? $this->image : $this->video;

        if ($file instanceof \yii\web\UploadedFile) {
            $dir = $this->type == 1 ? 'uploads/product/' : 'uploads/video/';
            $fileName = $this->id . '-' . Yii::$app->security->generateRandomString() . '.' . $file->extension;

            // Eski faylni o'chirish
            if ($this->file_path && file_exists($dir . $this->file_path)) {
                unlink($dir . $this->file_path);
            }

            // Yangi faylni saqlash
            if ($file->saveAs($dir . $fileName)) {
                Yii::$app->db->createCommand()->update('warehouse_file', [
                    'file_path' => $fileName
                ], ['id' => $this->id])->execute();
            }
        }
    }

}
