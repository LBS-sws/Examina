<?php

class StatisticsTestList extends CListPageModel
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
			'name'=>Yii::t('examina','test name'),
			'start_time'=>Yii::t('examina','start time'),
			'end_time'=>Yii::t('examina','end time'),
			'exa_num'=>Yii::t('examina','question num'),
            'city'=>Yii::t('examina','city all'),
            'city_name'=>Yii::t('examina','City'),
            'type_name'=>Yii::t('examina','category name'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.* from exa_quiz a 
                where a.id>0 
			";
        $sql2 = "select count(*) from exa_quiz a 
                where a.id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'exa_num':
				    if(is_numeric($svalue)){
                        $clause .= ' and a.exa_num = "'.$svalue.'"';
                    }
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
                case 'city_name':
                    $clause .= ' and a.city in '.TestTopList::getCityCodeSqlLikeName($svalue);
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
			    $list = $this->getCorrect($record['id']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'start_time'=>date("Y-m-d",strtotime($record['start_time'])),
					'end_time'=>date("Y-m-d",strtotime($record['end_time'])),
					'name'=>$record['name'],
					'exa_num'=>$record['exa_num'],
					'already'=>$list['already'],
					'correct'=>$list['correct'],
					'city'=>empty($record['city'])?Yii::t('examina','all city'):CGeneral::getCityName($record["city"]),
				);
			}
		}
		$session = Yii::app()->session;
		$session['statisticsTest_01'] = $this->getCriteria();
		return true;
	}

	public function getCorrect($que_id){
        $rows = Yii::app()->db->createCommand()->select("b.judge,a.id,a.employee_id")->from("exa_examina a")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.quiz_id=:quiz_id", array(':quiz_id'=>$que_id))->queryAll();
        if($rows){
            $arr = array();
            $num = 0;
            foreach ($rows as $row){
                $arr[$row["employee_id"]] = 1;
                if($row["judge"] == 1){
                    $num++;
                }
            }
            return array(
                'already'=>count($arr),//參與人數
                'correct'=>sprintf("%.2f",($num/count($rows)*100))."%",//正確率
            );
        }else{
            return array(
                'already'=>0,//參與人數
                'correct'=>0,//正確率
            );
        }
    }
}
