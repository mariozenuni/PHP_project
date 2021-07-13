<?php


namespace app\util;


trait DatesUtil
{


	/**
	 * @param string $dateFromString
	 * @param string|null $dateToString
	 * @param string|null $dayName
	 * @param bool|bool $startNextWeek
	 * @param string|string $format
	 * @return array
	 * @throws \Exception
	 */
	public static function getDatesInRange(string $dateFromString, string $dateToString=null, string $dayName=null, bool $startNextWeek=true, string $format='Y-m-d')
	{
		$dateFromString= self::convertDateToSql($dateFromString);
		if(!empty($dateToString)){
			$dateToString= self::convertDateToSql($dateToString);
			$dateTo = new \DateTime($dateToString);
		}
		$dayName= strtolower($dayName);
		$dateIndex= \date('N',strtotime($dateFromString));
		$dateFrom = new \DateTime($dateFromString);

		$dates = [];

		if(!empty($dayName)) {
			if ($startNextWeek) {
				//recupera il primo giorno interessato partendo dalla prossima settimana
				$dateFrom->modify('+1 week');
			}

			if ($dateFrom > $dateTo) {
				return $dates;
			}

			if ($dateIndex != $dateFrom->format('N')) {
				$dateFrom->modify("next $dayName");
			}

			while ($dateFrom <= $dateTo) {
				$dates[] = $dateFrom->format($format);
				$dateFrom->modify('+1 week');
			}
		} else {

			//aggiungo un giorno così che recupera anche quello
			if(!empty($dateToString)){
				$to = $dateTo->modify( '+1 day' );
				$interval = new \DateInterval('P1D');
				$period= new \DatePeriod($dateFrom,$interval,$to);

				foreach($period as $date){
					$dates[]= $date->format($format);
				}
			} else {
				$dates[]= $dateFrom->format($format);
			}
		}


		return $dates;
	}

	/**
	 * @param $begin
	 * @param $end
	 * @param null $interval
	 * @return array
	 * @throws \Exception
	 */
	public static function dateRange($begin, $end, $interval = null)
	{
		$begin = new \DateTime($begin);
		$end = new \DateTime($end);
		// Because DatePeriod does not include the last date specified.
		$end = $end->modify('+1 day');
		$interval = new \DateInterval($interval ? $interval : 'P1D');

		return iterator_to_array(new \DatePeriod($begin, $interval, $end));
	}

	/**
	 * @param string $type
	 * @param array $dates
	 * @return array|null
	 */
	public static function getDatesByRange($type='weekend', array $dates){

		switch ($type){
			case 'weekend':

				$return = array_filter($dates, function ($date) {
					$day = $date->format("N");
					return $day === '6' || $day === '7';
				});
				break;

			case 'saturday':
				$return = array_filter($dates, function ($date) {
					$day = $date->format("N");
					return $day === '6' ;
				});
				break;
			case 'sunday':
				$return = array_filter($dates, function ($date) {
					$day = $date->format("N");
					return $day === '7' ;
				});
				break;
			case 'working_days':
				$return = array_filter($dates, function ($date) {
					$day = $date->format("N");
					return in_array($day,array(1,2,3,4,5));
				});
				break;

			default:
				$return=null;
				break;
		}

		return $return;
	}

	/**
	 * @param $times
	 * @return float|int|mixed|string
	 */
	public static  function sum_seconds($times) {
		$i = 0;
		foreach ($times as $time) {
			$time= explode(':', $time);
			$i += ($time[0] * 60) + ($time[1]*60) + $time[2];
		}

		return $i;
	}

	/**
	 * @param $hour
	 * @return string|null
	 */
	public static function prepareHourString($hour){

		$string= explode(':', $hour);

		if(!empty($string)){

			return $string[0].'h '.$string[1].'m';
		}

		return null;
	}

	/**
	 * @param $data
	 * @return false|string
	 */
	public static function convertDateToSql($data){

		if(empty($data)){
			return "";
		}

		$date = str_replace("/", "-", $data);
		return date("Y-m-d",strtotime($date));
	}

	/**
	 * @param $date
	 * @return false|string
	 */
	public static function convertDate($date){

		if(empty($date)){
			return "";
		}

		return date("d/m/Y",strtotime($date));
	}

	/**
	 * @param $hour
	 * @return false|string
	 */
	public static function convertHour($hour){
		if(empty($hour)){
			return "";
		}

		return date("H:i",strtotime($hour));
	}

	/**
	 * @param $date
	 * @return false|string
	 */
	public static function convertDateTime($date){
		if(empty($date)){
			return "";
		}

		return date("d/m/Y H:i",strtotime($date));
	}

	/**
	 * @param $data
	 * @return false|string
	 */
	public static function convertDateTimeToSql($data){

		if(empty($data)){
			return "";
		}

		$date = str_replace("/", "-", $data);
		return date("Y-m-d H:i:s",strtotime($date));
	}

	/**
	 * @param $day
	 * @param false $timestamp
	 * @param string|string $format
	 * @return false|int|string
	 */
	public static function getDayOfThisWeek($day, $timestamp = false, string $format = 'Y-m-d')
	{
		return $timestamp ? strtotime("{$day} this week") : date($format,strtotime("{$day} this week"));
		
	}

	/**
	 * @param string|null $date
	 * @param string|string $format
	 * @param array $params
	 * @return false|string
	 */
	public static function addRemoveToDate(string $date = null , string $format= 'Y-m-d', array $params)
	{
		if(empty($date)){

			$date = date($format);
		}

		$addRemoveString = '';
		foreach ($params as $k =>  $param) {

			if(!isset($param['operation'])){
				$param['operation'] = '+';
			}
			$addRemoveString = $param['operation'].$param['value'].' '.$k;

		}


		return date($format,strtotime($addRemoveString,strtotime($date)));


	}

	/**
	 * @param string $month
	 * @param null $year
	 * @return \DateTime
	 */
	public static function getFirstDayOfMonth(string $month, $year = null)
	{
		if(empty($year)){

			$year = date('Y');

		}

		$startDate = new \DateTime("first day of {$year}-{$month}");


		return $startDate;
	}

	/**
	 * @param string $month
	 * @param null $year
	 * @return \DateTime
	 */
	public static function getLastDayOfMonth(string $month, $year = null)
	{
		if(empty($year)){

			$year = date('Y');

		}

		$endDate = new \DateTime("last day of {$year}-{$month}");


		return $endDate;
	}

	/**
	 * @param string $month
	 * @param null $year
	 * @return \DateTime[]
	 */
	public static function getFirstAndLastDayOfMonth(string $month, $year = null)
	{
		if(empty($year)){

			$year = date('Y');

		}

		$startDate = new \DateTime("last day of {$year}-{$month}");
		$endDate = new \DateTime("last day of {$year}-{$month}");

		return [
			$startDate,
			$endDate
		];
	}

	/* @return bool */
	public static function checkIfValidDate(string $date,string $delimiter = '/')
	{

		$date = explode($delimiter,$date);

		$hasIntegers = NumbersUtil::checkIfHasInt($date);

		if(!$hasIntegers){


			return false;
		}

		if(checkdate($date[1], $date[0],$date[2])){

			return true;

		}
	}

	/**
	 * @param $hour
	 * @param string $delimitator
	 * @return float|int|mixed|string
	 */
	public static function getSecondsByHour($hour, $delimitator=':'){

		$return = 0;
		if(!empty($hour)){
			$hours = explode($delimitator,$hour);

			$return = ($hours[0] * 3600) + ($hours[1] * 60);
			if(count($hours)==3){
				$return = $return + $hours[2];
			}
		}

		return $return;
	}


}
