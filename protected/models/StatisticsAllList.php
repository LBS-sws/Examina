<?php

class StatisticsAllList extends CListPageModel
{
    protected $startDate = "2021/01/01";//
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'city_name'=>Yii::t('examina','City'),
            'entry_time'=>Yii::t('examina','entry time'),
            'employee_name'=>Yii::t('examina','Employee Name'),
            'quiz_name'=>Yii::t('examina','test name'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'correct'=>Yii::t('examina','correct'),
            'title_sum'=>Yii::t('examina','question end num'),
            'title_num'=>Yii::t('examina','correct num'),
		);
	}

    //大陸版
    public function retrieveDataByPageAll($pageNum=1){
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $sql1 = "select a.id,a.lcd,a.title_num,a.title_sum,if(a.title_sum=0,0,a.title_num/a.title_sum) as correct,
                  g.name as quiz_name,f.code,f.name,b.name as city_name,f.entry_time  
                from exa_join a 
                LEFT JOIN exa_quiz g ON g.id=a.quiz_id 
                LEFT JOIN hr$suffix.hr_employee f ON f.id=a.employee_id 
                LEFT JOIN security$suffix.sec_city b ON f.city=b.code 
                where a.id>0 
			";
        $sql2 = "select count(a.id)  
                from exa_join a 
                LEFT JOIN exa_quiz g ON g.id=a.quiz_id 
                LEFT JOIN hr$suffix.hr_employee f ON f.id=a.employee_id 
                LEFT JOIN security$suffix.sec_city b ON f.city=b.code 
                where a.id>0 
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'quiz_name':
                    $clause .= General::getSqlConditionClause('g.name',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
                case 'city_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $orderField = $this->orderField;
            $order .= " order by ".$orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by a.lcd desc ";
        }
        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        $this->attr = array();
        if (count($records) > 0) {
            foreach ($records as $record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'lcd'=>$record['lcd'],
                    'title_num'=>$record['title_num'],
                    'title_sum'=>$record['title_sum'],
                    'entry_time'=>General::toMyDate($record['entry_time']),
                    'correct'=>round($record['correct']*100)."%",
                    'quiz_name'=>$record['quiz_name'],
                    'code'=>$record['code'],
                    'employee_name'=>$record['name'],
                    'city_name'=>$record['city_name'],
                    'style'=>"",
                );
            }
        }
        $session = Yii::app()->session;
        $session['statisticsAll_01'] = $this->getCriteria();
        return true;
    }

}
