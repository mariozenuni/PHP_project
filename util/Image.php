<?php

namespace app\util;

use Yii;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use Da\QrCode\QrCode;
use PhpOffice\PhpWord\TemplateProcessor;

class Image {

    public static function formatImage($folder,$id,$width,$height,$name,$external = false){
        if($external){
            $img = new \Imagick(\Yii::getAlias('@webroot') . "/../".$folder."/" . $id . '/' . $name);
        }else{
            $img = new \Imagick(\Yii::getAlias('@webroot') . "/".$folder."/" . $id . '/' . $name);
        }


        $w = $img->getImageWidth();
        $h = $img->getImageHeight();

        //$img->setImageAlphaChannel(8);
        $img->setImageBackgroundColor(new \ImagickPixel('transparent'));
        $img->setimageformat("png");

        if($height == null){
            $ratio = $w / $h;
            $height = $width / $ratio;
        }

        $img->thumbnailImage($width, $height, true, true);

        if($external){
            $img->writeImage(\Yii::getAlias('@webroot') . "/../".$folder."/" . $id . '/' . $name);
        }else{
            $img->writeImage(\Yii::getAlias('@webroot') . "/".$folder."/" . $id . '/' . $name);
        }

        if($external){
            return realpath(\Yii::getAlias('@webroot') . "/../".$folder."/" . $id . '/' . $name);
        }

        return Yii::$app->homeUrl.$folder."/" . $id . '/' . $name;
    }

    public static function convertBase64ToImage($base){
        $text = str_replace("data:image/png;base64,", "", $base);
        return base64_decode($text);
    }

    public static function convertImageToBase64($path){
        $type = pathinfo($path, PATHINFO_EXTENSION);
        try{
            $data = file_get_contents($path);
        }catch(\Exception $e){
            return "";
        }
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public static function createFolder($name,$id,$external = false){

        if(!$external){
            if (!is_dir(\Yii::getAlias('@webroot') . "/".$name."/" . $id)) {
                FileHelper::createDirectory(\Yii::getAlias('@webroot') . "/".$name."/" . $id);
            }
        }else{
            if (!is_dir(\Yii::getAlias('@webroot') . "/../media/".$name."/" . $id)) {
                FileHelper::createDirectory(\Yii::getAlias('@webroot') . "/../media/".$name."/" . $id);
            }
        }

    }

    public static function uploadImageExternal($name,$id,$image,$nameFile = "firma"){
        file_put_contents(\Yii::getAlias('@webroot') . "/../".$name."/" . $id."/".$nameFile.date("YmdHis").".png", $image);

        return realpath(\Yii::getAlias('@webroot') . "/../".$name."/" . $id."/".$nameFile.date("YmdHis").".png");
    }

    public static function uploadImageEditor(){
        $file = UploadedFile::getInstanceByName('file');

        $folder = "editor";

        Image::createFolder($folder,date("Y-m-d"));

        $image = $file->baseName . date("YmdHis") . Yii::$app->params["EXTENSION_IMAGE_TO_SAVE"];

        $file->saveAs($folder.'/' . date("Y-m-d"). '/' . $image);

        return Yii::$app->params["BASE_URL"]."/web/".$folder.'/' . date("Y-m-d"). '/' . $image;
    }

    public static function generateQrCode($text,$size = 300){
        $qrCode = (new QrCode($text))
            ->setSize($size)
            ->setMargin(5)
            ->useLogo("../web/images/logo-qr-code.png")
            ->setLogoWidth(60)
            ->useForegroundColor(50,50,50);

        return $qrCode->writeDataUri();
    }

    public static function wordReplaceText($path,$array){
        $word = new TemplateProcessor($path);

        foreach ($array as $key => $value){
            if(!empty($value)){
                if(strpos($value, "\n") !== FALSE){
                    $value = strtr($value, [
                        "\n" => "</w:t>\n<w:br />\n<w:t xml:space=\"preserve\">"
                    ]);
                    $word->setValue($key,$value);
                }else{
                    $word->setValue($key,$value);
                }
            }else{
                $word->setValue($key,"");
            }

        }

        return $word;
    }
}

?>

