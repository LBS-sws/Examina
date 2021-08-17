<?php

class FlowPlanList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('examina','Employee Name'),
            'employee_code'=>Yii::t('examina','Employee Code'),
            'employee_name'=>Yii::t('examina','Employee Name'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'city'=>Yii::t('examina','City'),
            'city_name'=>Yii::t('examina','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select d.id,d.name AS employee_name,d.city from exa_examina a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where d.city in ($city_allow)  
			";
        $sql2 = "select count(*) from exa_examina a 
                where d.city in ($city_allow)  
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('d.name',$svalue);
                    break;
                case 'city':
                    $clause .= ' and d.city in '.TestTopList::getCityCodeSqlLikeName($svalue);
                    break;
			}
		}
		$group = " group by a.employee_id";

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql1.$clause.$group;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if($rows){
            $this->totalRow = count($rows);
        }else{
            $this->totalRow = 0;
        }

		$sql = $sql1.$clause.$group.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $list = $this->getCorrect($record['id']);
				$this->attr[] = array(
					'id'=>$record['id'],
                    'employee_id'=>$record['employee_name'],
                    'city'=>CGeneral::getCityName($record["city"]),
                    'question'=>$list["question"],
                    'correct'=>$list["correct"],
                    'correctNum'=>$list["correctNum"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['statisticsTitle_01'] = $this->getCriteria();
		return true;
	}

	public function getCorrect($employee_id){
        $rows = Yii::app()->db->createCommand()->select("b.judge,a.id,a.employee_id")->from("exa_examina a")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.employee_id=:employee_id", array(':employee_id'=>$employee_id))->queryAll();
        if($rows){
            $num = 0;
            foreach ($rows as $row){
                if($row["judge"] == 1){
                    $num++;
                }
            }
            return array(
                'question'=>count($rows),//試題數量
                'correctNum'=>$num,//正確數量
                'correct'=>sprintf("%.2f",($num/count($rows)*100))."%",//正確率
            );
        }else{
            return array(
                'question'=>0,//試題數量
                'correctNum'=>0,//正確數量
                'correct'=>0,//正確率
            );
        }
    }
}
