<?php

class MyTestList extends CListPageModel
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
            'correct_num'=>Yii::t('examina','correct num'),
            'wrong_num'=>Yii::t('examina','wrong num'),
            'type_name'=>Yii::t('examina','category name'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $bumen = Yii::app()->user->bumen();
		$sql1 = "select a.* from exa_quiz a 
                where (a.city ='' || a.city = '$city') AND (a.bumen=''||a.bumen LIKE '%,$bumen,%') 
			";
		$sql2 = "select COUNT(a.id) from exa_quiz a 
                where (a.city ='' || a.city = '$city') AND (a.bumen=''||a.bumen LIKE '%,$bumen,%')  
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

        $group=" GROUP BY a.id ";

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else{
            $order = " order by a.id desc";
        }

		$sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$group.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $list = $this->judgeStaffTest($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'start_time'=>date("Y-m-d",strtotime($record['start_time'])),
					'end_time'=>date("Y-m-d",strtotime($record['end_time'])),
					'name'=>$record['name'],
					'exa_num'=>$record['exa_num'],
					'color'=>$list['color'],
					'correct'=>empty($list['string'])?$list['correct']:$list['string'],
					'correct_num'=>empty($list['string'])?$list['correct_num']:$list['string'],
					'wrong_num'=>empty($list['string'])?($record['exa_num'] - $list['correct_num']):$list['string'],
					'city'=>empty($record['city'])?Yii::t('examina','all city'):CGeneral::getCityName($record["city"]),
                    'bool'=>$list['bool'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['myTest_01'] = $this->getCriteria();
		return true;
	}

	//判斷員工能否參加考試  true:允許參加  false：不允許參加
	public function judeStaff($quz_id,$staff_id){
	    $model = new TestTopForm();
        $model->retrieveData($quz_id);
        $record = $model->getAttributes();
        $list = MyTestList::judgeStaffTest($record,$staff_id);
        if($list["bool"]===false){
            return true;
        }else{
            return false;
        }
    }

	//判斷員工是否參與測驗
	private function judgeStaffTest($record,$staff_id=""){
	    if(empty($staff_id)){
            $staff_id = Yii::app()->user->staff_id();
        }
        $count = Yii::app()->db->createCommand()->select("b.judge,a.id")->from("exa_examina a")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.quiz_id=:quiz_id and a.employee_id=:employee_id", array(':quiz_id'=>$record["id"],':employee_id'=>$staff_id))->queryAll();
	    if(!$count){
            $date = date("Y-m-d");
            if($date>=date("Y-m-d",strtotime($record['start_time'])) && $date<=date("Y-m-d",strtotime($record['end_time']))){
                if($record["staff_all"] == 1){ //全部員工
                    return array(
                        "bool"=>false,
                        "string"=>Yii::t("examina","not involved"),
                        "color"=>" text-primary",
                    ); //沒有參與
                }else{ //自定義員工
                    $row = Yii::app()->db->createCommand()->select("*")->from("exa_quiz_staff")
                        ->where("quiz_id=:quiz_id and employee_id=:employee_id", array(':quiz_id'=>$record["id"],':employee_id'=>$staff_id))->queryRow();
                    if($row){
                        return array(
                            "bool"=>false,
                            "string"=>Yii::t("examina","not involved"),
                            "color"=>" text-primary",
                        ); //沒有參與
                    }else{
                        return array(
                            "bool"=>true,
                            "string"=>Yii::t("examina","No need to participate"),
                            "color"=>"",
                        ); //不需要參與
                    }
                }
            }else{
                if($date<date("Y-m-d",strtotime($record['start_time']))){
                    return array(
                        "bool"=>true,
                        "string"=>Yii::t("examina","Not started"),
                        "color"=>" text-muted",
                    ); //沒有開始
                }else{
                    return array(
                        "bool"=>true,
                        "string"=>Yii::t("examina","expired"),
                        "color"=>" text-danger",
                    ); //已過期
                }
            }
        }else{
	        $num = 0;
	        foreach ($count as $row){
                if($row["judge"] == 1){
                    $num++;
                }
            }
            return array(
                "bool"=>true,
                "string"=>'',
                "color"=>"",
                "correct"=>sprintf("%.2f",($num/count($count)*100))."%",
                "correct_num"=>$num,
            ); //已經參與測驗
        }
    }
}
