<?php


namespace app\util;

use Yii;
use yii\helpers\Url;

class Util
{

    const OPTIONS_MINIMUM_CONDOMINIUM = [
        "0.33" => "1/3",
        "0.66" => "2/3",
        "0.5" => "1/2",
        "1" => "1/1"
    ];

    public static function sendEmail($obj, $content, $email, $attachment = null, $reply_to = null)
    {

        //$attachment = array
        //file => il file
        //name => il nome da dare al file
        //type => il formato del file

        if (!empty(Yii::$app->params["EMAIL_BLOCK"])) {
            $email = Yii::$app->params["EMAIL_BLOCK"];
            $reply_to = Yii::$app->params["EMAIL_BLOCK"];
            $obj = "AMBIENTE TEST - " . $obj;
        }

        $mail = Yii::$app->mailer->compose()
            ->setFrom([Yii::$app->params["EMAIL_FROM"] => "EdiliziAcrobatica"])
            ->setTo($email)
            ->setSubject($obj)
            ->setHtmlBody($content);

        if (!empty($reply_to)) {
            $mail->setReplyTo($reply_to);
        }

        if (!empty($attachment)) {
            $mail->attachContent($attachment["file"], ["fileName" => $attachment["name"], "contentType" => $attachment["type"], "Content-Transfer-Encoding" => "7bit"]);
        }

        $mail->send();
    }

    public static function sendEmailTemplate($obj, $template, $emails, $params = [], $reply_to = null, $cc = "", $ccn = "", $attachment = null)
    {

        if (!empty(Yii::$app->params["EMAIL_BLOCK"])) {
            $emails = [Yii::$app->params["EMAIL_BLOCK"]];
            $reply_to = Yii::$app->params["EMAIL_BLOCK"];
            $cc = "";
            $ccn = "";
            $obj = "AMBIENTE TEST - " . $obj;
        }

        $mail = Yii::$app->mailer->compose($template, $params)
            ->setFrom([Yii::$app->params["EMAIL_FROM"] => "EdiliziAcrobatica"])
            ->setTo($emails)
            ->setSubject($obj);

        if (!empty($cc)) {
            $mail->setCc($cc);
        }

        if (!empty($ccn)) {
            $mail->setBcc($ccn);
        }

        if (!empty($attachment)) {
            $mail->attachContent($attachment["file"], ["fileName" => $attachment["name"], "contentType" => $attachment["type"], "Content-Transfer-Encoding" => "7bit"]);
        }

        if (!empty($reply_to)) {
            $mail->setReplyTo($reply_to);
        }

        return $mail->send();
    }

    public static function getPreferredLanguage(array $languages = [])
    {
        if (empty($languages)) {
            return Yii::$app->language;
        }

        foreach ($languages as $acceptableLanguage) {
            $acceptableLanguage = str_replace('_', '-', strtolower($acceptableLanguage));
            foreach (Yii::$app->request->getAcceptableLanguages() as $language) {
                $normalizedLanguage = str_replace('_', '-', strtolower($language));
                if (
                    $normalizedLanguage === $acceptableLanguage // en-us==en-us
                    || strpos($acceptableLanguage, $normalizedLanguage . '-') === 0 // en==en-us
                    || strpos($normalizedLanguage, $acceptableLanguage . '-') === 0 // en-us==en
                ) {
                    return substr($language, 0, 2);
                }
            }
        }
        return Yii::$app->language;
    }

    public static function createHash($value)
    {
        return md5($value . Yii::$app->params["ENCRYPTED_KEY"]);
    }

    public static function checkHash($value, $hash)
    {
        $newHash = md5($value . Yii::$app->params["ENCRYPTED_KEY"]);

        if ($newHash == $hash) {
            return true;
        }

        return false;
    }

    public static function getValute()
    {
        $valute = Yii::$app->params["VALUTE"];

        foreach ($valute as $key => $value) {
            $valute[$key] = Yii::t("app", $value);
        }

        return $valute;
    }

    public static function translateArray($array)
    {
        foreach ($array as $key => $value) {
            $array[$key] = Yii::t("app", $value);
        }

        return $array;
    }

    public static function translateItemParams($params, $value)
    {
        $array = Yii::$app->params[$params];

        $array = Util::translateArray($array);

        return $array[$value];
    }

    public static function convertArraySelectToJson($array)
    {
        $results = [];
        foreach ($array as $author => $docArray) {
            $docs = [];
            foreach ($docArray as $id => $title) {
                $docs[] = ['id' => $id, 'text' => $title];
            }
            $results[] = ['text' => $author, 'children' => $docs];
        }

        return json_encode($results);
    }

    public static function getSingleValueFromArraySelectWithOpt($array)
    {
        if (count($array) == 1) {
            foreach ($array as $key => $value) {
                if (count($value) == 1) {
                    foreach ($value as $k => $v) {
                        return $k;
                    }
                }
            }
        }

        return null;
    }

    public static function convertDateToSql($data)
    {

        if (empty($data)) {
            return "";
        }

        $date = str_replace("/", "-", $data);
        return date("Y-m-d", strtotime($date));
    }

    public static function convertDate($date, $convert = null)
    {

        if (empty($date)) {
            return "";
        }

        if (!empty($convert)) {
            return strtoupper(strftime("%d %B %Y", strtotime($date)));
        }

        return date("d/m/Y", strtotime($date));
    }

    public static function convertHour($hour)
    {
        if (empty($hour)) {
            return "";
        }

        return date("H:i", strtotime($hour));
    }

    public static function convertDateTime($date)
    {
        if (empty($date)) {
            return "";
        }

        return date("d/m/Y H:i", strtotime($date));
    }

    public static function convertDateTimeToSql($data)
    {

        if (empty($data)) {
            return "";
        }

        $date = str_replace("/", "-", $data);
        return date("Y-m-d H:i:s", strtotime($date));
    }

    public static function getAlert($message, $success)
    {
        if ($message != null) {

            $class = "alert-danger";

            if ($success) {
                $class = "alert-success";
            }

            $html = '<div class="myadmin-alert myadmin-alert-icon myadmin-alert-click ' . $class . ' myadmin-alert-top alerttop" style="display:block"> 
                        <i class="ti-check"></i> 
                        ' . $message . '
                        <a href="#" class="closed">&times;</a> 
                    </div>
                    <script>
                        $(document).ready(function(){
                            $(".myadmin-alert").fadeToggle(350);

                            $(".closed").on("click",function(){
                                $(this).parent().remove();
                            })
                            
                            setTimeout(function(){
                                $(".closed").click();
                            },1000);
                        });
                    </script>';

            return $html;
        }

        return "";
    }

    public static function generateRandomString($length = 8, $number = true, $alphabet = true)
    {

        $characters = "";

        if ($number) {
            $characters .= "0123456789";
        }

        if ($alphabet) {
            $characters .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function bindValue($text, $params)
    {
        foreach ($params as $key => $param) {
            $text = str_replace($key, $param, $text);
        }

        return $text;
    }

    public static function formatTextFromTextArea($text)
    {
        return str_replace("\n", "<br>", $text);
    }

    public static function preparePeriodFromDates($from_date, $to_date, $lenght = null, $uppercase = null)
    {

        $from_date = self::convertDateToSql($from_date);
        $to_date = self::convertDateToSql($to_date);

        $string = date("d", strtotime($from_date));

        if (!empty($to_date) && $to_date != $from_date) {
            $string .= " - ";
            $string .= date("d", strtotime($to_date));
            $string .= "/";
            $string .= self::getMonthName(intval(date("m", strtotime($to_date))), $lenght, $uppercase);
        } else {
            $string .= "/";
            $string .= self::getMonthName(intval(date("m", strtotime($from_date))), $lenght, $uppercase);
        }

        return $string;
    }

    public static function getMonthName($n, $lenght = null, $uppercase = false)
    {
        $array = [
            1 => "Gennaio",
            2 => "Febbraio",
            3 => "Marzo",
            4 => "Aprile",
            5 => "Maggio",
            6 => "Giugno",
            7 => "Luglio",
            8 => "Agosto",
            9 => "Settembre",
            10 => "Ottobre",
            11 => "Novembre",
            12 => "Dicembre"
        ];

        $name = $array[intval($n)];

        if (!empty($lenght)) {
            $name = substr($name, 0, $lenght);
        }

        if ($uppercase) {
            $name = strtoupper($name);
        }

        return $name;
    }

    public static function convertModelsToArray($models)
    {
        $array = [];

        foreach ($models as $model) {
            $tmpArray = [];
            foreach ($model as $key => $value) {
                echo $key . "<br>";
                $tmpArray[$key] = $value;
            }
            exit();
            $array[] = $tmpArray;
        }

        return $array;
    }

    public static function prepareJsonForSelect($array, $id, $text)
    {
        $return = [];
        $return["results"] = [];

        foreach ($array as $value) {
            $return["results"][] = ["id" => $value[$id], "text" => $value[$text]];
        }

        return $return;
    }

    public static function filterArray($array, $fields)
    {
        $return = [];
        foreach ($array as $value) {
            $tmp = [];
            foreach ($fields as $field) {
                $tmp[$field] = $value[$field];
            }
            $return[] = $tmp;
        }


        return $return;
    }

    public static function prepareJsonForFileInput($array, $pathField, $nameField, $idField, $urlDelete, $single = false, $vimeo = false, $internal = false, $attachmentModel = null)
    {

        if ($single) {
            if (empty((string)$array[$pathField])) {
                return ["names" => [], "config" => []];
            }
            $tmp = $array;
            $array = [];
            $array[] = $tmp;
        }

        $nomi = [];
        $return_json = [];
        foreach ($array as $value) {

            if (empty($nameField)) {
                $arrayPath = explode("/", $value[$pathField]);
                $name = end($arrayPath);
            } else {


                $name = $value[$nameField];

            }

            $fileExtension = $value[$pathField];

            $arrayExtension = explode(".", $fileExtension);

            $extension = end($arrayExtension);


                if (in_array($extension, Yii::$app->params["IMAGE_EXTENSION"])) {
                    $type = "image";
                } else if (in_array($extension, Yii::$app->params["VIDEO_EXTENSION"])) {
                    $type = "video";
                }else {
                    $type = 'other';
                }


            $return_json[] = [
                'caption' => $name,
                'width' => '100px',
                'url' => \yii\helpers\Url::to([$urlDelete]),
                'key' =>  $value[$idField],
                'extra' => ['id' => $value[$idField]],
                'type' => $type,
                'downloadUrl' => $value[$idField],
                'filetype' => "",
            ];

            $nomi[] = $value[$pathField];

      }



        if (!$single) {
            return ["names" => $nomi, "config" => $return_json];
        }

        return ["names" => $nomi, "config" => $return_json];
    }

    public static function getDistinctValueFromArray($array, $field, $resultHaveArray = false)
    {
        $oldField = null;
        $result = [];

        foreach ($array as $key => $value) {
            if ($value[$field] != $oldField) {
                if ($resultHaveArray) {
                    $result[$value[$field]] = [];
                } else {
                    $result[] = $value[$field];
                }

                $oldField = $value[$field];
            }
        }

        return $result;
    }

    public static function createMultiArrayFromField($values, $array, $fieldCompare)
    {
        foreach ($array as $key => $group) {
            foreach ($values as $value) {
                if ($key == $value[$fieldCompare]) {
                    $array[$key][] = $value;
                }
            }
        }

        return $array;
    }

    public static function convertDateWithNameMonth($date)
    {
        if (empty($date)) {
            return "";
        }

        return date("d", strtotime($date)) . " " . self::getMonthName(date("m", strtotime($date)), null, true) . " " . date("Y", strtotime($date));
    }

    public static function createICS($id, $title, $date, $hour, $date_finish, $hour_finish, $description = '')
    {

        $uid = md5($id);
        $date = date("Ymd", strtotime($date));
        $date_finish = date("Ymd", strtotime($date_finish));

        $date .= "T" . date("His", strtotime($hour));
        $date_finish .= "T" . date("His", strtotime($hour_finish));

        $text = "BEGIN:VCALENDAR\n";
        $text .= "VERSION:2.0\n";
        $text .= "METHOD:PUBLISH\n";
        $text .= "PRODID:-//WebinarJam/WebinarJam//EN\n";
        $text .= "X-MS-OLK-FORCEINSPECTOROPEN:TRUE\n";
        $text .= "BEGIN:VEVENT\n";
        $text .= "UID:" . $uid . "\n";
        $text .= "SUMMARY:" . $title . "\n";
        $text .= "DESCRIPTION:" . $description . "\n";
        $text .= "DTSTAMP:" . $date . "\n";
        $text .= "DTSTART:" . $date . "\n";
        $text .= "DTEND:" . $date_finish . "\n";
        $text .= "TZID:Europe/Rome\n";
        $text .= "END:VEVENT\n";
        $text .= "END:VCALENDAR";

        return $text;
    }

    public static function generate_signature_zoom($api_key, $api_secret, $meeting_number, $role)
    {

        $time = time() * 1000 - 30000;//time in milliseconds (or close enough)

        $data = base64_encode($api_key . $meeting_number . $time . $role);
        $hash = hash_hmac('sha256', $data, $api_secret, true);

        $_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);

        //return signature, url safe base64 encoded
        return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
    }

    public static function convertFloatToSql($float)
    {
        $float = str_replace(".", "", $float);
        $float = str_replace(",", ".", $float);

        return $float;
    }

    public static function convertFloat($float, $decimal = 2)
    {
        return number_format($float, $decimal, ",", ".");
    }

    public static function getOptionMinimumCondominium($value)
    {

        if (empty(self::OPTIONS_MINIMUM_CONDOMINIUM[$value])) {
            return 0;
        }

        return self::OPTIONS_MINIMUM_CONDOMINIUM[$value];
    }

    public static function convertByte($size)
    {
        return $bytes = number_format($size / 1024, 2) . ' KB';
        //return sprintf("%4.2f MB", $size/1048576);
    }

    public static function array_flatten(array &$array, $preserve_keys = true)
    {
        $flattened = array();

        array_walk_recursive($array, function ($value, $key) use (&$flattened, $preserve_keys) {

            if ($preserve_keys && !is_int($key)) {
                $flattened[$key] = $value;
            } else {
                $flattened[] = $value;
            }
        });


        return $flattened;
    }
}

