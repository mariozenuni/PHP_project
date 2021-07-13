<?php


namespace app\util;


use common\models\Festivity;
use Yasumi\Yasumi;
use yii\helpers\ArrayHelper;

class YasumiUtil
{

	/*
	 * se è festività torna il yasumi_name della festività
	 * */
	public static function checkIfFestivity(string $date,string $nation = 'Italy')
	{
		$nation = ucfirst(strtolower($nation));

		$yasumi = Yasumi::create($nation,date('Y',strtotime($date)));

		$festivitiesDates = $yasumi->getHolidayDates();
		if(in_array($date,$festivitiesDates)){


			$festivity = array_search($date,$festivitiesDates);

			return $festivity;


		}


		return false;

	}
}