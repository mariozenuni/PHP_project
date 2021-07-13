<?php


namespace app\util;


trait NumbersUtil
{

	/**
	 * @param $number
	 * @param false $formatNumber
	 * @param array $format
	 * @return string|string[]|null
	 */
	public static function convertFloatToSql($number, $formatNumber=false, $format=array()){
		if(!empty($number)){
			$number=str_replace('.','', $number);
			$number=str_replace(',','.', $number);

			return $number;
		}
		return null;
	}

	/**
	 * @param $number
	 * @return string|null
	 */
	public static function convertFloat($number){
		if(!empty($number)){
			$number= (float)$number;

			$number= number_format($number,2,',','');
			return $number;
		}

		return null;
	}

	/**
	 * @param $class
	 * @param $dateFieldName
	 * @param $year
	 * @return int
	 */
	public static function generateNumberByYear($class, $dateFieldName, $year){

		$model= new $class;

		$countByYear= $model->find()
			->where(' DATE_FORMAT('.$dateFieldName.',"%Y") = :year ',[':year'=>$year])
			->count();

		$countByYear++;

		return $countByYear;
	}

	/**
	 * @param $number
	 * @param $digits
	 * @param false $year
	 * @param false $for_pdf
	 * @return string
	 */
	public static function formatNumberOrders($number, $digits, $year=false, $for_pdf=false){

		if($digits<10){
			$digits= '0'.$digits;
		}

		//$formattedNumber = str_pad($number, $digits, '0', STR_PAD_LEFT);
		$formattedNumber= sprintf('%'.$digits.'d', $number);


		if($year && !$for_pdf){
			$formattedNumber.= "/".$year;
		} else {
			$formattedNumber.= "_".$year;
		}


		return $formattedNumber;

	}

	/**
	 * @param array $array
	 * @return bool
	 */
	public function checkIfHasInt(array $array){


		if(!empty($array)){

			foreach ($array as $item) {


				if(!is_numeric($item))
					return false;

			}

			return true;
		}

	}
}

