<?php

class StatisticsQuizList extends CListPageModel
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
            'employee_id'=>Yii::t('examina','Employee Name'),
            'employee_name'=>Yii::t('examina','Employee Name'),
            'entry_time'=>Yii::t('examina','entry time'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'city'=>Yii::t('examina','City'),
            'city_name'=>Yii::t('examina','City'),
            'question'=>Yii::t('examina','Quiz cause'),
            'endDate'=>Yii::t('examina','Quiz end date'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
        $city_allow = Yii::app()->user->city_allow();
        $suffix = Yii::app()->params['envSuffix'];
        $authSql = "";
        $clause = "";
        if(!Yii::app()->user->validFunction('QZ01')){
            $staff_id = Yii::app()->user->staff_id();
            $authSql = " and b.id = '$staff_id'";
        }
        $newListClause = " and replace(b.entry_time,'/', '-')>='".date("Y-m-d",strtotime($this->startDate))."' ";
        $qc_dt_sql="date_format(a.qc_dt,'%Y-%m')>='".date("Y-m",strtotime($this->startDate))."' and b.city in($city_allow)";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'employee_name':
                    $clause .= ' and a.job_staff like "%'.$svalue.'%" ';
                    $newListClause.=" and ( b.code like '%".$svalue."%' or  b.name like '%".$svalue."%')";
                    break;
                case 'endDate':
                    if(is_numeric($svalue)){
                        $qc_dt_sql="a.qc_dt like '%$svalue%'";
                        $newListClause.=" and b.entry_time like '%$svalue%'";
                    }else{
                        $qcDt=date("Y-m-01",strtotime($svalue));
                        $qcDt = date("Y-m",strtotime("$qcDt -1 day"));
                        $qc_dt_sql="date_format(a.qc_dt,'%Y-%m')='$qcDt'";

                        $qcDt=date("Y-m-01",strtotime($svalue));
                        $qcDt = date("Y-m",strtotime("$qcDt -3 month"));
                        $newListClause.=" and replace(b.entry_time,'/', '-') like '$qcDt%'";
                    }
                    break;
                case 'question':
                    if (strpos("新同事",$svalue)!==false){
                        $clause .=" and a.id <0";
                    }else{
                        $newListClause.=" and b.id <0";
                        $clause .=" and date_format(a.qc_dt,'%Y年%m月QC未达标') like '%$svalue%'";
                    }
                    break;
            }
        }
        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }
        if(!empty($order)){
            $order.=",m.qc_date desc";
        }else{
            $order.=" order by order_quiz asc,m.order_end desc";
        }
        //新同事
        $newList = Yii::app()->db->createCommand()
            ->select("b.id as employee_id,concat(' ',b.name,' (',b.code,')') as job_staff,date_format(date_add(b.entry_time,interval 3 month),'%Y-%m') as order_end,date_format(date_add(b.entry_time,interval 1 second),'%Y-%m') as order_start,'new' as qc_date,'new' as result,b.city,b.entry_time")
            ->from("hr$suffix.hr_employee b")
            ->leftJoin("hr$suffix.hr_dept p","b.position=p.id")
            ->where("b.staff_status=0 and p.technician=1 and b.city in($city_allow) $newListClause $authSql")
            ->getText();
        $overDate = date("Y-m");//只顯示上個月以前的，本月不顯示
        $sql = "select b.id as employee_id,a.job_staff,date_format(date_add(a.qc_dt,interval 1 month),'%Y-%m') as order_end,date_format(date_add(a.qc_dt,interval 1 month),'%Y-%m') as order_start,date_format(a.qc_dt,'%Y-%m') as qc_date,avg(a.qc_result) as result,b.city,b.entry_time 
            from swoper$suffix.swo_qc a 
            LEFT JOIN hr$suffix.hr_employee b ON a.job_staff = concat(' ',b.name,' (',b.code,')')
            WHERE b.id is not NULL AND date_format(a.qc_dt,'%Y-%m')<'$overDate' AND 
            $qc_dt_sql $clause $authSql 
            group by employee_id,a.job_staff,b.city,b.entry_time,qc_date,order_end,order_start";
        $staffListSql = Yii::app()->db->createCommand()->select("*")
            ->from("($sql) a")
            ->where("a.result<75")//檢查分數是否低於75分
            ->union($newList)
            ->getText();
        $staffList = Yii::app()->db->createCommand()->select("m.job_staff,m.qc_date,m.result,m.city,m.entry_time,m.order_end,m.order_start")->from("($staffListSql) m")
            ->queryAll();
        if($staffList){
            $this->totalRow = count($staffList);
        }else{
            $this->totalRow = 0;
        }
        $quizSql = Yii::app()->db->createCommand()->select("employee_id,max(lcd) as quizDate")->from("exa_join")
            ->where("(title_num/title_sum)>=0.85")->group("employee_id")->getText();

        $sql = "select m.job_staff,m.qc_date,m.result,m.city,m.entry_time,m.order_end,m.order_start,quiz.quizDate,
        CASE WHEN date_format(quiz.quizDate,'%Y-%m')>=date_format(m.order_start,'%Y-%m') THEN 1 ELSE 0 END AS order_quiz 
        from ($staffListSql) m 
        LEFT JOIN ($quizSql) quiz ON m.employee_id = quiz.employee_id
        $order ";
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $staffList = Yii::app()->db->createCommand($sql)->queryAll();
        if($staffList){
            foreach ($staffList as $staff){
                $code = end(explode("(",$staff["job_staff"]));
                $staffCode = current(explode(")",$code));
                $row = Yii::app()->db->createCommand()->select("id,code,name,entry_time,city")->from("hr$suffix.hr_employee")
                    ->where("code='$staffCode'")->queryRow();
                if($row){
                    $row["qc_dt"] = $staff["qc_date"];
                    $this->resetRow($row,$staff["result"]=="new");
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
                }else{
                    var_dump($staff);
                    var_dump($staffCode);
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

            $titleList = Yii::app()->db->createCommand()->select("lcd,title_num,title_sum,(title_num/title_sum) as score")->from("exa_join")
                ->where("employee_id=:employee_id and (title_num/title_sum)>=0.85",array(":employee_id"=>$row['id']))->order("lcd asc")->queryRow();
        }else{//未达标
            $entry_time = date("Y-m-01",strtotime($row["qc_dt"]));
            $row["question"] = date("Y年m",strtotime($row["qc_dt"]))."月QC未达标";
            $row["endDate"] = date("Y-m-d",strtotime("$entry_time + 2 month - 1 day"));

            $nowMonth = date("Y-m",strtotime($row["endDate"]));
            $titleList = Yii::app()->db->createCommand()->select("lcd,title_num,title_sum,(title_num/title_sum) as score")->from("exa_join")
                ->where("employee_id=:employee_id and date_format(lcd,'%Y-%m')>=:date and (title_num/title_sum)>=0.85",array(":employee_id"=>$row['id'],":date"=>$nowMonth))
                ->order("lcd asc")->queryRow();
            if(!$titleList){
                $titleList = Yii::app()->db->createCommand()->select("lcd,title_num,title_sum,(title_num/title_sum) as score")->from("exa_join")
                    ->where("employee_id=:employee_id and date_format(lcd,'%Y-%m')>=:date and (title_num/title_sum)<0.85",array(":employee_id"=>$row['id'],":date"=>$nowMonth))
                    ->order("lcd desc")->queryRow();
            }
        }
        if($titleList){
	        if(floatval($titleList["score"])>0.85){
                $title = floatval($titleList["score"]);
                $row["testDate"] = date("Y-m-d",strtotime($titleList["lcd"]));
                $row["correctNum"] = ($title*100)."%";
                $row["correct"] = $titleList["title_num"];
                $row["style"] = " ";
            }else{
                $title = floatval($titleList["score"]);
                $row["testDate"] = date("Y-m-d",strtotime($titleList["lcd"]));
                $row["correctNum"] = ($title*100)."%";
                $row["correct"] = $titleList["title_num"];
                $row["style"] = " text-danger";
            }
        }else{
            $row["testDate"] = "-";
            $row["correctNum"] = "-";
            $row["correct"] = "-";
            $row["style"] = " text-danger";
        }
    }
}
