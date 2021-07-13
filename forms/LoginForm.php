<?php

namespace app\forms;

use app\models\Users;
use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 * @property-read User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;



    /**
     * @return array the validation rules.
     */
    public function rules()
    {

        return [
            // username and password are both required
            [['username', 'password'], 'required'],
        ];

    }



    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
            $user=Users::findOne(["email"=>$this->username]);

            if(!empty($user)){
                if($user->validatePassword($this->password)){
                    if($user->checkCanLogin()){
                        Yii::$app->user->login($user);
                        $user->setSessionForlogin();

                        return [
                            "success"=> true,

                        ];

                    }else{
                        return [
                            "success"=> false,
                            "message"=> "User not active"
                        ];

                    }
                }

            }
            return [
                "success"=> false,
                "message"=> "Error email address e/o password;"
            ];
    }


}
