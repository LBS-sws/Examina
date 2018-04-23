<?php

class StatisticsViewList extends CListPageModel
{
    public $examinaName;
    public $qui_id;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'employee_id'=>Yii::t('examina','Employee Name'),
            'employee_name'=>Yii::t('examina','Employee Name'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'city'=>Yii::t('examina','city all'),
            'city_name'=>Yii::t('examina','City'),
		);
	}

	public function retrieveDataByPage($index,$pageNum=1)
	{
	    $this->qui_id = $index;
	    $this->examinaName = TestTopForm::getQuizTitleName($index);
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select d.id,d.name AS employee_name,d.city,a.lcd from exa_examina a 
                LEFT JOIN hr$suffix.hr_employee d ON a.employee_id = d.id
                where a.quiz_id = '$index' 
			";
        $sql2 = "select count(*) from exa_examina a 
                where id>0 
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
					'employee_name'=>$record['employee_name'],
					'city'=>CGeneral::getCityName($record["city"]),
					'lcd'=>$record['lcd'],
                    'correct'=>$list["correct"],
                    'correctNum'=>$list["correctNum"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['statisticsView_02'] = $this->getCriteria();
		return true;
	}

	public function getCorrect($employee_id){
        $rows = Yii::app()->db->createCommand()->select("b.judge,a.id,a.employee_id")->from("exa_examina a")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.quiz_id=:quiz_id and a.employee_id=:employee_id", array(':quiz_id'=>$this->qui_id,':employee_id'=>$employee_id))->queryAll();
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
                'correctNum'=>$num,//正確數量
                'correct'=>sprintf("%.2f",($num/count($rows)*100))."%",//正確率
            );
        }else{
            return array(
                'correctNum'=>0,//正確數量
                'correct'=>0,//正確率
            );
        }
    }
}
