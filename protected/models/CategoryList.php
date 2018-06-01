<?php

class CategoryList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('examina','ID'),
			'bumen_ex'=>Yii::t('examina','department'),
			'name'=>Yii::t('examina','category name'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from exa_type 
                where id>0 
			";
        $sql2 = "select count(*) from exa_type 
                where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'bumen_ex':
                    $clause .= General::getSqlConditionClause('bumen_ex',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
					'id'=>$record['id'],
					'bumen_ex'=>$record['bumen_ex'],
					'name'=>$record['name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['category_01'] = $this->getCriteria();
		return true;
	}

}
