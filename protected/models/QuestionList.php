<?php

class QuestionList extends CListPageModel
{
    public $index = 0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('examina','ID'),
			'title_code'=>Yii::t('examina','question code'),
			'name'=>Yii::t('examina','question name'),
            'city'=>Yii::t('examina','City'),
            'city_name'=>Yii::t('examina','City'),
            'type_name'=>Yii::t('examina','category name'),
            'show_int'=>Yii::t('examina','show bool'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $index = $this->index;
		$sql1 = "select a.* from exa_title a 
                where a.quiz_id = $index 
			";
        $sql2 = "select count(*) from exa_title a 
                where a.quiz_id = $index 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'title_code':
					$clause .= General::getSqlConditionClause('a.title_code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and a.city in '.WordForm::getCityCodeSqlLikeName($svalue);
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
					'title_code'=>$record['title_code'],
					'name'=>$record['name'],
					'show_int'=>empty($record['show_int'])?Yii::t("examina","none"):Yii::t("examina","show"),
				);
			}
		}
		$session = Yii::app()->session;
		$session['question_01'] = $this->getCriteria();
		return true;
	}

}
