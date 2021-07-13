<?php

namespace app\models;

use app\util\Image;
use app\util\RelationTrait;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_gallery".
 *
 * @property int $galleryid
 * @property int $useridfk
 * @property string $photo
 * @property string $name
 * @property string $creation_date
 *
 * @property UserGallery $useridfk0
 * @property UserGallery[] $userGalleries
 */
class UserGallery extends \yii\db\ActiveRecord
{

    use RelationTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_gallery';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['useridfk', 'photo', 'galleries', 'creation_date'], 'required'],
            [['useridfk'], 'integer'],
            [['creation_date'], 'safe'],
            [['photo', 'galleries'], 'string', 'max' => 250],
            [['useridfk'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['useridfk' => 'userid']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'galleryid' => 'Galleryid',
            'useridfk' => 'Useridfk',
            'photo' => 'Photo',
            'galleries' => 'galleries',
            'creation_date' => 'Creation Date',
        ];
    }

    /**
     * Gets query for [[Useridfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUseridfk()
    {
        return $this->hasOne(UserGallery::className(), ['galleryid' => 'useridfk']);
    }

    public static function getQuery($search = [], $limit = null, $offset = null)
    {

        $query = self::find();

        if(!empty($search['userid'])){
            $query->andWhere('user_gallery.useridfk = :uid',[':uid' => $search['userid']]);
        }


        if (!empty($limit)) {
            $query->limit($limit);
        }
        if (!empty($offset)) {
            $query->offset($offset);
        }

        return $query;
    }

    public static function getAll($search = [], $limit = null, $offset = null)
    {
        $query = self::getQuery($search, $limit, $offset);

        return $query->all();

    }

    public static function getCount($search = [])
    {

        $query = self::getQuery($search);

        return $query->count();

    }


    public static function getArrayForSelect($search = [])
    {

        $models=self::getAll($search);

        return ArrayHelper::map($models,"galleryid","galleries");

    }





    public function prepareToSave()
    {

        if ($this->isNewRecord) {
            $this->creation_date = date("Y-m-d H:i:s");
        }
        return true ;
    }


    public function upload() {
        $folder = "user_gallery";
        Image::createFolder($folder, $this->useridfk);

        $this->galleries=$this->photo->baseName;

        $attachment = $this->photo->baseName . date("YmdHis") . Yii::$app->params["EXTENSION_IMAGE_TO_SAVE"];
        $webRoot = Yii::getAlias('@webroot');
        $this->photo->saveAs($webRoot . '/' . $folder . '/' . $this->useridfk . '/' . $attachment);

        $this->photo = Image::formatImage($folder, $this->useridfk, 960, 540, $attachment);


        //$this->photo = S3::uploadFile("../web/" . $this->photo, $this->photo);

        return true;
    }


    /**
     * Gets query for [[UserGalleries]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserGalleries()
    {
        return $this->hasMany(UserGallery::className(), ['useridfk' => 'galleryid']);
    }
}
