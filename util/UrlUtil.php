<?php


namespace app\util;


use yii\base\Exception;
use yii\helpers\ArrayHelper;

trait UrlUtil
{

	public static function buildTranslatedUrl($language)
	{

		$currentUrl=\Yii::$app->request->url;

		$explodedUrl= explode('/',$currentUrl);
		$explodedUrl= array_filter($explodedUrl);
		$explodedUrl[1]= $language;

		return \Yii::$app->params['BASE_URL'].'/'.implode('/',$explodedUrl);
	}


	/**
	 * decodifica i parametri in get, ad esempio se in get metti uno / viene encodato in %2F
	 * @param $parameters array|string
	 * @return array|string
	 */
	public static function decodeUriParameters($parameters)
	{
		if(is_string($parameters)){

			$q = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($parameters));
			$q = html_entity_decode($q,null,'UTF-8');

			return $q;
		}

		if(is_array($parameters)){

			$return = [];

			foreach ($parameters as $index => $parameter) {

				$return[$index] = self::decodeUriParameters($parameter);
			}

		}


		return $return;
	}

    /**
     * decodifica gli attributi del passati come stringa dal get li restituisce in un array
     * da usare nel load/costruttore di un model
     * @param $model
     * @return object
     */
    public static function restoreModelFromGet($model,$for_key = false) {
        try {
            if (!empty($model)) {
                $return = unserialize(urldecode($model));

                if($for_key){
                    $return = implode("-",$return);
                }

                return $return;
            }

            return [];
        }
        catch (Exception $ex) {
            return null;
        }
    }

    /**
     * prepara gli attributi del model passato per tornare una stringa da usare in get
     * @param $model
     * @return string|null
     */
    public static function prepareModelForGet($model) {
        if (!empty($model) && !empty($model)) {
            return urlencode(serialize($model));
        }
        else {
            return null;
        }
    }
}