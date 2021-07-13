<?php


namespace app\util;


use app\util\Util;

trait GeolocationUtil
{

	public static function getBaseUrl()
	{

		return "https://maps.google.com/maps/api/geocode/";

	}

	public static function prepareAddress(&$address)
	{
		$address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
	}

	public static function getCoordinates($address){

		if(empty($address))
			return null;

		self::prepareAddress($address);

		$url = self::getBaseUrl()."json?sensor=false&address=$address&key=".\Yii::$app->params['GOOGLE_KEY'];


		$response = file_get_contents($url);


		$json = json_decode($response,TRUE); //generate array object from the response from the web
		$coordinate=array();
		if(!empty($json['results'])) {

			$coordinate[] = $json['results'][0]['geometry']['location']['lat'];
			$coordinate[] = $json['results'][0]['geometry']['location']['lng'];
		}


		return ($coordinate);

	}

	public static function getPlaceInfo(string $address,array $types = [],bool $returnCoordinates = true)
	{
		$result = array();

		$url = self::getBaseUrl()."json?key=".\Yii::$app->params['GOOGLE_KEY'];

		self::prepareAddress($address);

		$url.= '&address='.$address;

		$response = file_get_contents($url);

		$json = json_decode($response,TRUE); //generate array object from the response from the web

		if(empty($types) && $json['status']== 'OK'){

			return $json;

		} else if ($json['status'] != 'OK') {
			return false;
		}

		if(!empty($json['results'])) {

			$jsonResult = $json['results'][0];
			foreach ($types as $keyToSearch => $typeToSearch) {

				foreach ($jsonResult['address_components'] as $addressComponent) {


					if(is_array($types)){


							foreach ($addressComponent['types'] as $k => $type) {


								if(in_array($type,array_keys($typeToSearch))){

									$result[$type] = $addressComponent[$typeToSearch[$type]];

								}


							}


					}

				}

			}


			if($returnCoordinates){
				$result['location'] = $jsonResult['geometry']['location'];
			}

		}

		return $result;
	}
}