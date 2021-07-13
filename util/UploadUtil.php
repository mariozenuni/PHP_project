<?php


namespace app\util;


use app\util\ImageUtil;
use yii\base\Model;
use yii\web\UploadedFile;

trait UploadUtil
{

    /**
     * @param $model
     * @param string|null $imageFieldName
     * @param string|null $oldPhoto
     * @param array $uploadParams
     */
    public static function savePhoto(&$model, string $imageFieldName = null, string $oldPhoto=null , array $uploadParams)
    {

        $model->$imageFieldName = UploadedFile::getInstance($model,$imageFieldName);
        if(empty($imageFieldName)){
            $imageFieldName = 'image';
        }

        if (!empty($model->$imageFieldName))
        {
            self::upload($model,...$uploadParams);
        } else
        {
            $model->$imageFieldName = $oldPhoto;
        }

        $model->save();

    }

    /**
     * @param $model
     * @param string $generalFolder
     * @param string $folder
     * @param string $imageFieldName
     * @param string $keyName
     * @param int $width
     * @param int $height
     * @param string|null $extension
     * @param bool|null $external
     * @return bool
     */
    public static function upload(
        $model,
        string $generalFolder,
        string $folder,
        string $imageFieldName,
        string $keyName,
        int $width,
        int $height,
        string $extension = null,
        bool $external = null
    )
    {

        if(empty($extension)){

            $extension = \Yii::$app->params['EXTENSION_IMAGE_TO_SAVE'];

        }

        if(empty($keyName)){

            $keyName = 'id';

        }


        Image::createFolder($generalFolder, $folder,$external);

        $image = $model->$imageFieldName->baseName . date("YmdHis") .'.'. $model->$imageFieldName->extension;
        $image = str_replace(" ", "", $image);
        $model->$imageFieldName->saveAs($generalFolder . '/' . $folder . '/' . $image);

        $model->$imageFieldName = Image::formatImage($generalFolder, $folder, (int)$width, (int)$height, $image,false,true);

        $model->save();

        return true;

    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize and post_max_size

    /**
     * @return float|int
     */
    public static function getFileUploadMaxSize() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = UploadUtil::parseSize(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = UploadUtil::parseSize(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $max_size;
    }

    /**
     * @return string
     */
    public static function getFileUploadMaxSizeLabel() {
        $maxSize = UploadUtil::getFileUploadMaxSize();

        return '(Max. ' . $maxSize / (1024 * 1024) . ' MB)';
    }

    /**
     * @return string
     */
    public static function getMbMaxFileInput() {
        $maxSize = UploadUtil::getFileUploadMaxSize();

        return $maxSize / (1024 * 1024);
    }

    /**
     * @param $size
     * @return float
     */
    public static function parseSize($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    public static function move_file($path,$to){
        if(copy($path, $to)){
            unlink($path);
            return true;
        } else {
            return false;
        }
    }
}