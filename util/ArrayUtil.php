<?php


namespace app\util;


use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

trait ArrayUtil
{

	/**
	 * type_return: 0: select2 normale,1: depDrop,2: Select2 ajax
	 * $whereCondition: array con 2 chiavi: ['condition'=>condizione, 'params'=>'valori su qui eseguire la where']
	 * $joinCondition: array con 4 chiavi: ['type'=> 'inner,left o right' ,'table'=>nome tabella su cui andare in join, 'join_clause'=>'Condizione della join','params'=>'parametri della join'] esempio : ['table'=>'courses','join_clause'=>'courses.id=lessons.courseidfk']
	 * model: da passare con get_class()
	 * keyName: nome della primary key
	 * textName: nome del campo di testo, esempio titolo
	 * selected: un array dove passare gli id degli elementi da selezionare nella select
	 * disabled: un array dove passare gli id degli elementi da settare a disabled
	 * printRawSql: stampa a schermo la query
	 *
	 * @return array;
	 */
	public static function getArrayForSelect(
		$model,
		$fieldsToSelect=[],
		$whereCondition=[],
		$joinCondition=[],
		$keyName=null,
		$textName='',
		$type_return= 0,
		$orderBy=null,
		$distinct=false,
		$selected=array(),
		$disabled=array(),
		$printRawSql=false
	){


		if(!empty($model)){
			$models= $model::find();
		} else {

			return [];
		}
		/* @var $models  ActiveRecord */
		if(!empty($fieldsToSelect)){
			$models->select($fieldsToSelect);
		}

		if(!empty($whereCondition)){
			foreach ($whereCondition as $where){
				$models->andWhere($where['condition'],$where['params']??null);
			}
		}
		if(!empty($joinCondition)){
			foreach ($joinCondition as $join){
				switch ($join['type']) {
					case 'inner':
						$models->innerJoin($join['table'],$join['join_clause'],$join['params']??null);
						break;
					case 'left':
						$models->leftJoin($join['table'],$join['join_clause'],$join['params']??null);
						break;
					case 'right':
						$models->rightJoin($join['table'],$join['join_clause'],$join['params']??null);
						break;
					default:
						$models->innerJoin($join['table'],$join['join_clause'],$join['params']??null);
						break;
				}
			}
		}
		if(!empty($orderBy)){
			if(is_array($orderBy)){
				$models->orderBy(implode(',', $orderBy));
			} else {
				$models->orderBy($orderBy);
			}
		}
		if($distinct){
			$models->distinct(true);
		}

		if($printRawSql){

			echo '<pre>';
			print_r($models->createCommand()->getRawSql());
			exit();

		}


		$models=$models->all();



		$models= ArrayHelper::toArray($models);
		$return=array();

		if($type_return == 0){


			foreach ($models as $model){

				$return[$model[$keyName??'id']]= $model[$textName??'name'];
			}
		}else if($type_return == 1){
			foreach ($models as $model){
				$selectedCondition=false;
				if(!empty($selected)){
					if(in_array($model[$keyName??'id'], $selected)){
						$selectedCondition=true;
					}
				}
				$disableCondition=false;
				if(!empty($disabled)){
					if(in_array($model[$keyName??'id'], $disabled)){
						$disableCondition=true;
					}
				}
				$return[] = [
					"id" => $model[$keyName??'id'],
					"name" => $model[$textName??'name'],
					'selected'=>$selectedCondition,
					'disabled'=>$disableCondition
				];
			}
		}else{
			$return[] = [
				"id" => '',
				"text" => '',
			];
			foreach ($models as $model){

				$selectedCondition=false;
				if(!empty($selected)){
					if(in_array($model[$keyName??'id'], $selected)){
						$selectedCondition=true;
					}
				}

				$disableCondition=false;
				if(!empty($disabled)){
					if(in_array($model[$keyName??'id'], $disabled)){
						$disableCondition=true;
					}
				}

				$return[] = [
					"id" => $model[$keyName??'id'],
					"text" => $model[$textName??'name']?? $model['name'],
					'selected'=>$selectedCondition,
					'disabled'=>$disableCondition
				];
			}
		}

		return $return;

	}

	/**
	*	@param $models array array di model da trasformare nel formato della select
	 * @param int $type_return    0: select2 normale,1: depDrop,2: Select2 ajax
	 * @param string $keyName nome della chiave primaria
	 * @param string $textName nome della chiave del model testuale da visualizzare nella select
	 * @param array $disabled array di id da mettere come disabilitati
	 * @param array $selected array di id da mettere ad abilitati
	 *
	 * @return array;
	 */
	public static function transformArrayModelsToSelect(array $models, $type_return = 0,$keyName='id',$textName='name',$selected = array(),$disabled = array()){

		if($type_return == 0){


			foreach ($models as $model){

				$return[$model[$keyName??'id']]= $model[$textName??'name'];
			}
		}else if($type_return == 1){
			foreach ($models as $model){
				$selectedCondition=false;
				if(!empty($selected)){
					if(in_array($model[$keyName??'id'], $selected)){
						$selectedCondition=true;
					}
				}
				$disableCondition=false;
				if(!empty($disabled)){
					if(in_array($model[$keyName??'id'], $disabled)){
						$disableCondition=true;
					}
				}
				$return[] = [
					"id" => $model[$keyName??'id'],
					"name" => $model[$textName??'name'],
					'selected'=>$selectedCondition,
					'disabled'=>$disableCondition
				];
			}
		}else{
			$return[] = [
				"id" => '',
				"text" => '',
			];
			foreach ($models as $model){

				$selectedCondition=false;
				if(!empty($selected)){
					if(in_array($model[$keyName??'id'], $selected)){
						$selectedCondition=true;
					}
				}

				$disableCondition=false;
				if(!empty($disabled)){
					if(in_array($model[$keyName??'id'], $disabled)){
						$disableCondition=true;
					}
				}

				$return[] = [
					"id" => $model[$keyName??'id'],
					"text" => $model[$textName??'name']?? $model['name'],
					'selected'=>$selectedCondition,
					'disabled'=>$disableCondition
				];
			}
		}

		return $return;
	}

	/**
	* @param array $array array da ridurre
	 * @param bool $preserve_keys mantenere le stesse chiavi o meno, serve nei casi di array con chiavi literal
	 *
	 * ritorna un array ridotto di una dimensione
	 *
	 * @return array;
	 */
	public static function array_flatten(array &$array, $preserve_keys = true)
	{
		$flattened = array();

		array_walk_recursive($array, function($value, $key) use (&$flattened, $preserve_keys) {

			if ($preserve_keys && !is_int($key)) {
				$flattened[$key] = $value;
			} else {
				$flattened[] = $value;
			}
		});


		return $flattened;
	}

	/**
	 * @param array $array
	 */
	public static function addQuotesToArrayElements(array &$array)
	{
		array_walk($array,function (&$item,$key){
			$item= '\''.$item.'\'';
		});

	}

	/**
	 * @param array $array
	 */
	public static function downCaseArrayElements(array &$array)
	{
		array_walk($array,function (&$item,$key){
			$item= strtolower($item);
		});

	}

	/**
	 * @param array $array
	 */
	public static function trimArrayElements(array &$array)
	{
		array_walk($array,function (&$item,$key){
			$item= trim($item);
		});

	}

	/**
	 * @param string $utilFunction nome della funzione DatesUtil da applicare
	 * @param array $dates array di date da convertire
	 *
	 * essendo una util con referenza non è necessario il return, l'array dates verrà modificato direttamente
	 *
	 * */
	public static function convertArrayOfDate(string $utilFunction, array &$dates)
	{
		array_walk($dates,function (&$item,$key)use($utilFunction){

			$item= GeneralUtil::$utilFunction($item);
		});

	}

	/**
	 * @param $arr array da ordine con referenza, verrà modificato direttamente
	 * @param $col nome colonna di riferimento per ordinare
	 * @param int $dir SORT_ASC o SORT_DESC
	 * */
	public static function usortByColValue(&$arr, $col,$dir = SORT_ASC) {


		usort($arr, function($a,$b) use($col,$dir){

			if($dir == SORT_DESC){
				return $a[$col] > $b[$col] ? -1 : 1;
			} else {
				return $a[$col] > $b[$col] ? 1 : -1;
			}

		});


		return $arr;

	}

	/**
	 * @param $arr
	 * @param $col
	 * @param array $value
	 * @param int $dir
	 * @param false $alphabetic
	 * @return array
	 */
	public static function array_sort_by_column_value(&$arr, $col, $value=array(), $dir = SORT_ASC, $alphabetic=false) {

		$sort_col = array();
		$first_to_sort=array();
		foreach ($arr as $key=> $row) {

			if(in_array($row[$col],$value)){

				$first_to_sort[]= $row;

			} else {
				$sort_col[] = $row;
			}


		}

		if($alphabetic){

			sort($first_to_sort);
			sort($sort_col);

		}

		$totalArray=array_merge($first_to_sort,$sort_col);


		return  $totalArray;


	}

	/**
	 * @param array $cities
	 */
	public static function ucFirstToArrayElements(array &$cities)
	{
		array_walk($cities,function (&$item,$key){
			$item= ucfirst($item);
		});

	}

	/**
	 *
	 * permette di rimuovere da un array le chiavi scelte per un array monodimensionale,
	 *
	 *
	 *
	 * @param array $array array sulla quale rimuovere le chiavi
	 * @param array $keysToUnset array di chiavi da rimuovere
	 *
	 */
	public static function remove_element_recursive(array &$array, array $keysToUnset)
	{

		if(!empty($array)){

			if( isset($array[0]) && is_array($array[0])){

				foreach ($array as $index => $item) {

					//se non è settato item in posizione 0 vuol dire che è questo l'array da scomporre
					if(!isset($item[0])){
						foreach ($item as $key => $value) {

							if(in_array($key,$keysToUnset)){

								unset($array[$index][$key]);

							}

						}
					} else {

						foreach ($item as $subkey => $subItem) {


							foreach ($subItem as $k =>  $item) {

								if(in_array($k,$keysToUnset)){

									unset($array[$index][$subkey][$k]);

								}
							}

						}

					}

				}

			} else {

				foreach ($keysToUnset as $item) {

					unset($array[$item]);

				}

			}

		}

	}

	/**
	 * @param $elements array array di elementi
	 * @param $key string|int chiave che si vuole considerare
	 * @param $value string|int valore
	 *
	 * rimuove tutti gli elementi che hanno in quella chiave quel determinato valore.
	 *
	 * */
	public static function unsetElementsWithSameKey(&$elements,$key,$value)
	{


		if(!empty($elements)){

			foreach ($elements as $k=>$element) {


				if(empty($element["$key"]) && is_array($element["$k"])){

					self::unsetElementsWithSameKey($element["$k"],$key,$value);

				} else {
					if($element["$key"] === $value){

						unset($elements[$k]);
						continue;

					}
				}

			}


		}


	}

	/**
	 * @param $array
	 * @param $field
	 * @param false $resultHaveArray
	 * @return array
	 */
	public static function getDistinctValueFromArray($array, $field, $resultHaveArray = false){
		$oldField = null;
		$result = [];

		foreach ($array as $key => $value){
			if($value[$field] != $oldField){
				if($resultHaveArray){
					$result[$value[$field]] = [];
				}else{
					$result[] = $value[$field];
				}

				$oldField = $value[$field];
			}
		}

		return $result;
	}

	/**
	 * @param $values
	 * @param $array
	 * @param $fieldCompare
	 * @return mixed
	 */
	public static function createMultiArrayFromField($values, $array, $fieldCompare){
		foreach ($array as $key => $group){
			foreach ($values as $value){
				if($key == $value[$fieldCompare]){
					$array[$key][] = $value;
				}
			}
		}

		return $array;
	}

	/**
	 * @param $array
	 * @param $fields
	 * @return array
	 */
	public static function filterArray($array, $fields){
		$return = [];
		foreach ($array as $value){
			$tmp = [];
			foreach ($fields as $field){
				$tmp[$field] = $value[$field];
			}
			$return[] = $tmp;
		}

		return $return;
	}

	/**
	 * @param array $array
	 * @return int|string|null
	 */
	public static function array_first_key(array $array)
	{
		reset($array);

		return key($array);
	}
	/*
	 * rende monodimensionale un array
	 * */
	/**
	 * @param $array
	 * @return mixed
	 */
	public static function reduceToOneDimension($array )
	{
		$result = call_user_func_array('array_merge', $array);

		return $result;
	}

	/**
	 * @param array $array
	 * @param array $arrayKeys
	 * @param string $modifyValue
	 * @param false $allKeys
	 */
	public static function modifyEmptyValues(array &$array, array $arrayKeys, $modifyValue = '', $allKeys = false)
	{

		if($allKeys){

			foreach ($array as $k => $item) {

				if(gettype($item) !== 'boolean'){
					if(empty($array[$k])){
						$array[$k] = $modifyValue;
					}
				}

			}

			return;
		}

		if(!empty($arrayKeys)){

			foreach ($arrayKeys as $arrayKey) {

				if(empty($array[$arrayKey])){
					$array[$arrayKey] = $modifyValue;
				}

			}

		}

	}

	/**
	 * @param $model
	 * @return array|array[]|object|object[]|string|string[]
	 */
	public static function toArray($model)
	{
		$return = array();
		if(is_array($model)){

			foreach ($model as $k => $item) {


				$modelVars = get_object_vars($item);

				$return[$k] = ArrayHelper::toArray($model);

				$return[$k] += $modelVars;

			}

		} else {

			$modelVars = get_object_vars($model);

			$return = ArrayHelper::toArray($model);

			$return += $modelVars;
		}


		return $return;

	}

	/**
	 * @param array $array
	 * @param array $orderList
	 * @return array
	 */
	public static function orderArrayByList(array $array, array $orderList)
	{
		$ordered = array();

		foreach ($array as $k => $item) {

			foreach ($orderList as $listItem) {

				if(array_key_exists($listItem,$item)){

					$ordered[$k][$listItem] = $item[$listItem];

					unset($item[$listItem]);
				}

			}

		}

		return $ordered + $array;

	}

    public static function getValueDefaultByModel($model, $key, $default = ''){

        return isset($model[$key]) ? $model[$key] : $default;
    }

    public static function getItemsByModel($model, $items){

        $return = '';

        if (!empty($model)) {
            if (is_array($items)) {
                foreach ($items as $i => $item) {
                    $space = count($items) == ($i + 1) ? '' : ', ';
                    $return .= self::getValueDefaultByModel($model, $item) . $space;
                }
            } else {
                $return = self::getValueDefaultByModel($model, $items);
            }
        }

        return $return;
    }

    public static function getDbSchemaByModel($model,$dbComponent=null){

        if(!class_exists($model)){
            return false;
        }

        if(empty($dbComponent)){
            $dbComponent = \Yii::$app->params['DB_COMPONENT'];
            $db = \Yii::$app->get($dbComponent, false);
        }

        if(empty($db)){
            return false;
        }

        return $db->getTableSchema($model::tableName());
    }

    public static function getItemsByIdAndModel($id=[],$class,$fieldsToSelect=[]){

        if(!empty($class)){
            $query = $class::find();
        } else {
            return false;
        }

        $tableSchema = self::getDbSchemaByModel($class);

        if(empty($tableSchema)){
            return false;
        }

        /* @var $models ActiveRecord */
        if(count($tableSchema->primaryKey) == 1){

            if(!is_array($id)){
                $primaryKey = $tableSchema->primaryKey[0];

                $whereCondition = "$primaryKey = :id";
                $whereParams = [':id'=>$id];
            }else{
                return false;
            }

        }else{

            // Da gestire bene se ha due chiavi primarie

            $whereCondition = "";
            $whereParams = [];

            foreach ($tableSchema->primaryKey as $i => $primaryKey) {
                $idAttribute = ":id".$i;
                $whereCondition .= "$primaryKey = $idAttribute ";
                $whereParams += ["$idAttribute"=>$id[$i]];
            }
        }

        $query->where($whereCondition,$whereParams);

        $model = $query->one();

        return self::getItemsByModel($model,$fieldsToSelect);
    }

    public static function getArrayKeyByItem($array,$key){

        $return = [];

        if(!empty($array)){
            foreach ($array as $arr) {
                $return[] = isset($arr[$key]) ? $arr[$key] : '';
            }
        }

        return $return;
    }

    public static function getArrayLabelByKey($array,$item,$class){

        $return = [];

        if (is_array($array)){
            foreach ($array as $key => $arr) {
                $return[$key] = $class::getLabelByColumn($arr[$item]);
            }
        }

        return $return;
    }
}