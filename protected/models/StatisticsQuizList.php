<?php

class StatisticsQuizList extends CListPageModel
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
            'employee_name'=>Yii::t('examina','Employee Name'),
            'entry_time'=>Yii::t('examina','entry time'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'city'=>Yii::t('examina','City'),
            'city_name'=>Yii::t('examina','City'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $startDate = date("Y-m-d");
        $date = date("Y/m/01");
        $endDate = date("Y-m",strtotime("$date -3 month"));
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
                case 'city':
                    $clause .= ' and b.city in '.TestTopList::getCityCodeSqlLikeName($svalue);
                    break;
            }
        }
        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }
        $rows = Yii::app()->db->createCommand()->select("b.id,b.code,b.name,b.entry_time,b.city")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id=b.id")
            ->leftJoin("hr$suffix.hr_dept f","b.position=f.id")
            ->leftJoin("security$suffix.sec_user_access e","a.user_id=e.username")
            ->where("b.city in ($city_allow) and e.system_id='quiz' and e.a_read_write like '%EM02%' and b.staff_status=0 and f.technician=1 $clause $order")->queryAll();

        $this->totalRow = 0;
        $this->attr = array();
        if($rows){
            $date = date("Y/m/01");
            $date = date("Y-m",strtotime("$date -1 month"));
            foreach ($rows as $row){
                //新員工
                $entry_time = date("Y-m-d",strtotime($row["entry_time"]));
                $bool =($entry_time<=$startDate)&&($entry_time>$endDate);

                //檢查分數是否低於75分
                $username="(".$row["code"].")";
                $result = Yii::app()->db->createCommand()->select("avg(qc_result) as result")->from("swoper$suffix.swo_qc")
                    ->where("date_format(qc_dt,'%Y-%m')=:date and job_staff like '%$username'",array(":date"=>$date))->queryScalar();
                if($result!==null||$bool){
                    $result=floatval($result);
                    if($result<75||$bool){ //上月的質檢平均分低於75分
                        $this->totalRow++;
                        $this->resetRow($row,$bool);
                        $this->attr[] = array(
                            'id'=>$row['id'],
                            'employee_id'=>$row['id'],
                            'employee_name'=>$row['name'],
                            'entry_time'=>date("Y-m-d",strtotime($row['entry_time'])),
                            'city'=>CGeneral::getCityName($row["city"]),
                            'question'=>$row['question'],
                            'endDate'=>$row['endDate'],
                            'testDate'=>$row["testDate"],
                            'correctNum'=>$row["correctNum"],
                            'correct'=>$row["correct"],
                            'style'=>$row["style"],
                        );
                    }
                }

            }
        }
		$session = Yii::app()->session;
		$session['statisticsQuiz_01'] = $this->getCriteria();
		return true;
	}

	public function resetRow(&$row,$bool){
	    if($bool){//新同事
            $day = floatval(date("d",strtotime($row["entry_time"])));
            $entry_time = date("Y-m-01",strtotime($row["entry_time"]));
	        $row["question"] = "新同事";
	        if($day == 1){
                $row["endDate"] = date("Y-m-d",strtotime("$entry_time + 3 month - 1 day"));
            }else{
                $row["endDate"] = date("Y-m-d",strtotime("$entry_time + 4 month - 1 day"));
            }

            $titleList = Yii::app()->db->createCommand()->select("lcd,title_num,title_sum,MAX(title_num/title_sum) as score")->from("exa_join")
                ->where("employee_id=:employee_id",array(":employee_id"=>$row['id']))->queryRow();
        }else{//未达标
            $entry_time = date("Y-m-01");
            $row["question"] = date("m",strtotime("-1 month"))."月QC未达标";
            $row["endDate"] = date("Y-m-d",strtotime("$entry_time + 1 month - 1 day"));

            $nowMonth = date("Y-m");
            $titleList = Yii::app()->db->createCommand()->select("lcd,title_num,title_sum,MAX(title_num/title_sum) as score")->from("exa_join")
                ->where("employee_id=:employee_id and date_format(lcd,'%Y-%m')=:date",array(":employee_id"=>$row['id'],":date"=>$nowMonth))->queryRow();
        }
        if($titleList&&$titleList["score"]!==null){
            $title = floatval($titleList["score"]);
            $row["testDate"] = date("Y-m-d",strtotime($titleList["lcd"]));
            $row["correctNum"] = ($title*100)."%";
            $row["correct"] = $titleList["title_num"];
            if($title<0.85){//測驗後的正確率小於85%
                $row["style"] = " text-danger";
            }else{
                $row["style"] = " ";
            }
        }else{
            $row["testDate"] = "-";
            $row["correctNum"] = "-";
            $row["correct"] = "-";
            $row["style"] = " text-danger";
        }
    }
}
