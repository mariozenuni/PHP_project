<?php


namespace app\util;
use Yii;

class SecurityUtil
{

	public static function createHash(string $passwd):string
	{
		$hash = Yii::$app->getSecurity()->generatePasswordHash($passwd);

		return $hash;
	}

	public static function validatePassword(string $password,string $hashedPassword)
	{
		return Yii::$app->getSecurity()->validatePassword($password, $hashedPassword);
	}
}