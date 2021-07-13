<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "roles".
 *
 * @property int $roleid
 * @property string $name
 * @property int $mask
 * @property int $creation_date
 *
 * @property Users[] $users
 */
class Roles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'roles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'mask', 'creation_date'], 'required'],
            [['mask', 'creation_date'], 'integer'],
            [['name'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'roleid' => 'Roleid',
            'name' => 'Name',
            'mask' => 'Mask',
            'creation_date' => 'Creation Date',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['roleidfk' => 'roleid']);
    }

    public static function getArrayForSelect()
    {
        $model = Roles::find()->all();
        return ArrayHelper::map($model, "roleid", "name");
    }

    public static function getQuery($search = [], $limit = null, $offset = null)
    {

        $query = self::find();

        if (!empty($limit)) {
            $query->limit($limit);
        }
        if (!empty($offset)) {
            $query->offset($limit);
        }

        return $query;
    }

    public static function getAll($search = [], $limit = null, $offset = null)
    {
        $query = self::getQuery($search, $limit, $offset);
        $query->orderBy('role.name');
        return $query->all();

    }

    public static function getCount($search = [])
    {

        $query = self::getQuery($search);

        return $query->count();

    }


    public static function getNameById($id)
    {
        $model = self::findOne($id);
        if (empty($model)) {

            return '';
        }

        return $model->name;
    }

}
