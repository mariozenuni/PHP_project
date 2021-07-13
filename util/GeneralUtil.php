<?php


namespace app\util;

use Yii;

class GeneralUtil
{
	use ArrayUtil;
	use DatesUtil;
	use GeolocationUtil;
	use HtmlUtil;
	use ImageUtil;
	use NumbersUtil;
	use ObjectsUtil;
	use UrlUtil;
	public static function deleteDirectory($dir) {
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? deleteDirectory("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public static function openSession(){
	    if(!Yii::$app->session->isActive){
	        Yii::$app->session->open();
        }
	}

    public static function encryptByKey(string $stringToEncrypt){

        return Yii::$app->security->encryptByKey($stringToEncrypt,\Yii::$app->params['SECURE_TOKEN']);
    }

    public static function decryptString($stringToDecrypt){

        return Yii::$app->security->decryptByKey($stringToDecrypt,\Yii::$app->params['SECURE_TOKEN']);
    }
}