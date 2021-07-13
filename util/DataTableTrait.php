<?php


namespace app\util;


use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

trait DataTableTrait
{
	public static function getClass()
	{
		return get_called_class();
	}

	public function forPagination(&$query){

		$calledClass = get_called_class();
		$reflection = new \ReflectionClass($calledClass);
		$shortName = $reflection->getShortName();
		$tableName = $calledClass::tableName();

		if($query instanceof ActiveQuery){

			if(!empty($this->search)){

				$searchValue = $this->search;
				$condition = $calledClass::getSearchQuery();
                $joinCondition = method_exists($calledClass, 'getInnerJoinQuery') ? $calledClass::getInnerJoinQuery() : null;

				$query->andWhere($condition,[':valueToSearch' => '%'.$searchValue.'%']);

                if(!empty($joinCondition)){
                    foreach ($joinCondition as $join){
                        switch ($join['type']) {
                            case 'inner':
                                $query->innerJoin($join['table'],$join['join_clause'],$join['params']??null);
                                break;
                            case 'left':
                                $query->leftJoin($join['table'],$join['join_clause'],$join['params']??null);
                                break;
                            case 'right':
                                $query->rightJoin($join['table'],$join['join_clause'],$join['params']??null);
                                break;
                            default:
                                $query->innerJoin($join['table'],$join['join_clause'],$join['params']??null);
                                break;
                        }
                    }
                }
			}

			if(!empty($_SESSION[$shortName]['sort'])){

				$query->orderBy("{$_SESSION[$shortName]['sort']['column']} {$_SESSION[$shortName]['sort']['sort']}");

			}

			if(!empty($this->offset)){

				$query->offset($this->offset);

			}

			$limit = $calledClass::ELEMENTI_PER_PAGINA;

			if(!empty($this->limit)){

				$limit = $this->limit;
			}

			$query->limit($limit);
		}

	}
}