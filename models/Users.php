<?php

namespace app\models;


use app\util\RelationTrait;
use app\util\Util;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Yii;
use yii\base\BaseObject;
use yii\helpers\Url;
use yii\web\IdentityInterface;
use yii\web\User;
use app\util\Image;
use app\models\UserFamily;

/**
 * This is the model class for table "users".
 *
 * @property int $userid
 * @property int $roleidfk
 * @property string $email
 * @property string $internet
 * @property string $name
 * @property string $surname
 * @property string $date_in
 * @property string|null $data_out
 * @property int|null $children
 * @property string $hour
 * @property string|null $note
 * @property string|null $photo
 * @property string|null $document
 * @property float $salary
 * @property int $creation_date
 * @property int $status
 *
 * @property string $passwd
 *
 * @property Roles $roleidfk0
 */

class Users extends \yii\db\ActiveRecord implements IdentityInterface
{

    use RelationTrait;
    public $authKey;

    public $role_name="";
    public $conferma_passwd;

    public $galleries=[];
    public $families=[];



    const INTERNET_OPTIONS=[ 'Presente e affidabile' => 'Presente e affidabile',
                              'Presente e ma lento' => 'Presente e ma lento',
                              'Assente' => 'Assente',
                                      '' => ''
                            ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['roleidfk', 'internet', 'name', 'surname', 'date_in', 'hour', 'salary', 'status','email'], 'required'],
            [['photo','document','passwd'],'required','on'=>'insert'],
            [['email'],'unique'],
            [['email'],'email'],
            [['roleidfk', 'children', 'status'], 'integer'],
            [['internet', 'note'], 'string'],
            [['date_in', 'data_out', 'hour','creation_date','galleries'], 'safe'],
            [['salary'], 'safe'],
            [['name', 'surname', 'photo', 'document','passwd'], 'string', 'max' => 250],
            [['roleidfk'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['roleidfk' => 'roleid']],

            ["conferma_passwd","required","whenClient" => "function (attribute, value) {
                return $('input[name=\'Users[passwd]\']').val() != '';
            }","when" => function($model){ return !empty($model->passwd); }],
            ['conferma_passwd','compare','compareAttribute' => "passwd", "message" => "Le password non corrispondono"],

            [['photo'], 'file', 'maxFiles' => 1, 'extensions' => implode(", ", Yii::$app->params["IMAGE_EXTENSION"]), 'maxSize' => Yii::$app->params["MAX_FILE_UPLOAD"]],
            [['document'], 'file', 'maxFiles' => 1, 'extensions' => implode(", ", Yii::$app->params["DOCUMENT_EXTENSION"]), 'maxSize' => Yii::$app->params["MAX_FILE_UPLOAD"]],
            [['galleries'], 'file', 'maxFiles' =>99999, 'extensions' => implode(", ", Yii::$app->params["IMAGE_EXTENSION"]), 'maxSize' => Yii::$app->params["MAX_FILE_UPLOAD"]]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'userid' => 'Userid',
            'roleidfk' => 'Ruolo',
            'email'=>'Email',
            'passwd'=>'Password',
            'conferma_passwd'=>'Conferma Password',
            'internet' => 'Internet',
            'name' => 'Nome',
            'surname' => 'Cognome',
            'date_in' => 'Data di inizio licenza',
            'data_out' => 'Data fine licenza',
            'children' => 'N° figli',
            'hour' => 'Ore',
            'note' => 'Note',
            'photo' => 'Foto',
            'document' => 'Documenti',
            'salary' => 'Stipendio',
            'creation_date' => 'Data Creazione',
            'status' => 'Status',
            'galleries'=>'Galleria'
        ];
    }

    /**
     * Gets query for [[Roleidfk0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleidfk0()
    {
        return $this->hasOne(Roles::className(), ['roleid' => 'roleidfk']);
    }

    public function validatePassword($password){
        return $this->passwd === md5($password);
    }
    public static function findIdentity($id)
    {
        return static::findOne($id);

        if(empty($model->photo)){
            $model->photo="../images/varum.jpg";
        }


    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    public function getId()
    {
        return $this->userid;
    }


    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    public function checkCanLogin()
    {
        if($this->status==1){

            if(empty($this->data_out)){
                $date_out=date("Y-m-d");
            }else{
                $date_out=$this->data_out;
            }
            if(strtotime($this->date_in) <=strtotime(date("Y-m-d"))&&strtotime($date_out)>=strtotime(date("Y-m-d"))){

                return true;
            }

        }

        return false;
    }

    public function setSessionForlogin(){

        $role=Roles::findOne($this->roleidfk);

        if(empty($role)){
            Yii::$app->user->logout();
        }

        $_SESSION["mask"]=$role->mask;
        $_SESSION["name"]=$role->name;
    }


    public function prepareToSave(){

        if($this->isNewRecord){
            $this->creation_date=date("Y-m-d H:i:s" );
        }

        $this->date_in=Util::convertDateToSql($this->date_in);

        $this->data_out=Util::convertDateToSql($this->data_out);

        $this->salary=Util::convertDateToSql($this->salary);

        $this->hour.=':00';

        return true;
    }

    public function upload() {
        $folder = "users";
        Image::createFolder($folder, $this->userid);
        $attachment = $this->photo->baseName . date("YmdHis") . Yii::$app->params["EXTENSION_IMAGE_TO_SAVE"];
        $webRoot = Yii::getAlias('@webroot');
        $this->photo->saveAs($webRoot . '/' . $folder . '/' . $this->userid . '/' . $attachment);

        $this->photo = Image::formatImage($folder, $this->userid, 960, 540, $attachment);

        //$this->photo = S3::uploadFile("../web/" . $this->photo, $this->photo);

        return true;
    }
    public function uploadDocument(){
        $folder = "users_document";

        Image::createFolder($folder, $this->userid, true);
        $attachment = $this->document->baseName . date("YmdHis") . '.'.$this->document->extension;
       // $webRoot = Yii::getAlias('@webroot');
        $this->document->saveAs('../media/' . $folder . '/' . $this->userid . '/' . $attachment);

        $this->document = $folder . '/' . $this->userid . '/' . $attachment;

        //$this->photo = S3::uploadFile("../web/" . $this->photo, $this->photo);

        return true;



    }



public function passwordToMd5($id = null){

        if(empty($id)){
            $this->passwd= md5($this->passwd);
            $this->conferma_passwd = $this->passwd;
        }else{
            $user = self::findOne($id);
            if(!empty($this->passwd)){
                $this->passwd=$this->conferma_passwd = md5($this->passwd);
               $this->conferma_passwd = $this->passwd;
            }else{
                $this->passwd=$this->conferma_passwd = $user->passwd;
                $this->conferma_passwd = $user->passwd;
            }
        }
        return true;
    }


    public function saveGallery()
    {

        foreach($this->galleries as $gallery){
            $model=new UserGallery();
            $model->useridfk=$this->userid;

            $model->prepareToSave();
            $model->photo=$gallery;

            $model->upload();

            $model->saveOrfail();
        }

    }

    public function getFamiliesForForm()
    {

        if ($this->isNewRecord) {
            return [new UserFamily()];
        }


        $models = UserFamily::getAll(['useridfk' => $this->userid]);

        if (empty($models)) {
            return [new UserFamily()];
        }

        return $models;

    }
        function  saveFamilies($data){
                UserFamily::deleteAll("useridfk = :uid",[":uid" => $this->userid]);

                if(!empty($data["UserFamily"])){
                    foreach ($data["UserFamily"] as $value){
                        $model=new UserFamily($value);
                        $model->useridfk=$this->userid;

                        $model->prepareToSave();

                        $model->save();


                    }

                }
        }


            function getDocumentLink(){

                        $token=md5($this->userid.Yii::$app->params['ENCRYPTED_KEY']);

                        return Url::to(["site/download-document","id"=>$this->userid,"token"=>$token]);
            }


    public static function getQuery($search = [], $limit = null, $offset = null)
    {
        $query = self::find();

        if(!empty($search["name"])){

            $query->andWhere("(CONCAT(users.name, '  ' ,users.surname) LIKE :name OR CONCAT(users.surname, '  ' ,users.name) LIKE :name)",[
                    "name"=>"%".trim($search["name"])."%"
            ]);

        }

        if(!empty($search["email"])){

            $query->andWhere('users.email LIKE :email',[":email"=>"%".$search["email"]."%"]);

        }
        if(!empty($search["internet"])){
            $query->andWhere(["users.internet"=>$search["internet"]]);
        }

        if(!empty($search["month_date_out"])){
            list($month,$year)=explode("/",$search["month_date_out"]);
            $query->andWhere("MONTH(users.data_out)=:month",[":month"=>$month]);
            $query->andWhere("YEAR(users.data_out)=:year",[":year"=>$year]);
        }



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



        $query->orderBy('users.name, users.surname');



        return $query->all();



    }

    public static function getCount($search = [])
    {

        $query = self::getQuery($search);

        return $query->count();

    }
    }



