<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_family".
 *
 * @property int $familyid
 * @property int $useridfk
 * @property string $name
 * @property string $cognome
 * @property string $creation_date
 */
class UserFamily extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_family';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['useridfk', 'name', 'cognome', 'creation_date'], 'required'],
            [['useridfk'], 'integer'],
            [['creation_date'], 'safe'],
            [['name', 'cognome'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'familyid' => 'Familyid',
            'useridfk' => 'Useridfk',
            'name' => 'Name',
            'cognome' => 'Cognome',
            'creation_date' => 'Creation Date',
        ];
    }


    public static function getQuery($search = [], $limit = null, $offset = null)
    {

        $query = self::find();

        if(!empty(['useridfk'])){

            $query->andWhere('user_family.useridfk=:uid',['uid'=>$search['useridfk']]);
        }


        if (!empty($limit)) {
            $query->limit($limit);
        }
        if (!empty($offset)) {
            $query->offset($limit);
        }


        $query->orderBy("user_family.name,user_family.cognome");

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

    public function prepareToSave()
    {

        if ($this->isNewRecord) {
            $this->creation_date = date("Y-m-d H:i:s");
        }
        return true ;
    }




}
