<?php


namespace app\util;


use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait ObjectsUtil
{

	/**
	 * @param $model
	 * @param array $arrayKeys
	 * @param string $modifyValue
	 */
	public static function modifyEmptyValuesObjects(&$model, array $arrayKeys, $modifyValue = '')
	{

		if(!empty($arrayKeys)){

			foreach ($arrayKeys as $arrayKey) {

				if(empty($model->$arrayKey)){
					$model->$arrayKey = $modifyValue;
				}

			}

		}

	}

	/**
	 * @param Model $model
	 * @param false $forApi
	 * @return array
	 */
	public static function getValidationErrors(Model $model, $forApi = false)
	{

		$errors = $model->getErrors();

		$returnErrors = ArrayUtil::array_flatten($errors,false);


		if(!$forApi){

			return $returnErrors;

		}

		return [
			'success' => false,
			'message' => \Yii::t('app','Si sono verificati i seguenti errori: {errors}',['errors' => implode(',',$returnErrors)])
		];

	}

	/**
	 * @param ActiveRecord $model
	 * @return array
	 */
	public static function getObjectVars(ActiveRecord  $model)
	{
		return get_object_vars($model);
	}
	/**
	 * @param array $models
	 * @param int $type_return
	 * @param string $key_name : nome della primary key
	 * @param string $text_name : nome del campo di testo, esempio titolo
	 * @param array $selected : un array dove passare gli id degli elementi da selezionare nella select
	 * @param array $disabled : un array dove passare gli id degli elementi da settare a disabled
	 * @return array
	 */
	public static function getArrayForSelectFromObjects(array $models, int $type_return = 0,string $key_name = '',string $text_name = '',array $selected = [],array $disabled = [])
	{

		$modelsAsArray= ArrayHelper::toArray($models);
		$return=array();


		switch ($type_return){

			case 0:

				foreach ($modelsAsArray as $k => $model){

					$objectVars = self::getObjectVars($models[$k]);
					$key = $model[$key_name??'id']??null;
					$text = $model[$text_name??'name']??null;

					if(!empty($objectVars[$key_name])){

						$key = $models[$k]->$key_name;

					}

					if(!empty($objectVars[$text_name])){


						$text = $models[$k]->$text_name;

					}

					$return[$key]= $text;
				}

				break;
			case 1:

				foreach ($modelsAsArray as $model){

					$return[] = [
						"id" => $model[$key_name??'id'],
						"name" => $model[$text_name??'name'],
						'selected'=> !empty($selected) && in_array($model[$key_name??'id'], $selected),
						'disabled'=> !empty($disabled) && in_array($model[$key_name??'id'], $disabled)
					];
				}

				break;
			default:

				$return[] = [
					"id" => '',
					"text" => '',
				];
				foreach ($modelsAsArray as $model){

					$return[] = [
						"id" => $model[$keyName??'id'],
						"text" => $model[$textName??'name']?? $model['name'],
						'selected'=> !empty($selected) && in_array($model[$key_name??'id'], $selected),
						'disabled'=> !empty($disabled) && in_array($model[$key_name??'id'], $disabled)
					];
				}
				break;

		}


		return $return;
	}
}