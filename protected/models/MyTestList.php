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
			'lcd'=>Yii::t('examina','test time'),
			'exa_num'=>Yii::t('examina','question num'),
            'city'=>Yii::t('examina','city all'),
            'city_name'=>Yii::t('examina','City'),
            'correct_num'=>Yii::t('examina','correct num'),
            'wrong_num'=>Yii::t('examina','wrong num'),
            'type_name'=>Yii::t('examina','category name'),
            'bumen_ex'=>Yii::t('examina','department'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $staff_id = Yii::app()->user->staff_id();
		$sql1 = "select a.name,a.bumen_ex,a.exa_num,b.id,b.lcd from exa_join b
                LEFT JOIN exa_quiz a ON b.quiz_id = a.id
                where (b.employee_id = '$staff_id') 
			";
/*		$sql1 = "select a.* from exa_quiz a
                where (a.bumen=''||a.bumen LIKE '%,$bumen,%') 
			";*/
        $sql2 = "select count(b.id) from exa_join b
                LEFT JOIN exa_quiz a ON b.quiz_id = a.id
                where (b.employee_id = '$staff_id')  
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
				case 'bumen_ex':
					$clause .= General::getSqlConditionClause('a.bumen_ex',$svalue);
					break;
			}
		}

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else{
            $order = " order by b.id desc";
        }

		$sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $bumenList = explode(",",$record['bumen_ex']);
                if(count($bumenList)>3){
                    $bumenList = array_slice($bumenList,0,2);
                    $record['bumen_ex'] = implode(",",$bumenList).".....";
                }
			    $list = $this->judgeStaffTest($record);
				$this->attr[] = array(
					'id'=>$record['id'],
					'lcd'=>date("Y-m-d",strtotime($record['lcd'])),
					'name'=>$record['name'],
					'exa_num'=>$record['exa_num'],
					'bumen_ex'=>$record['bumen_ex'],
					'correct'=>$list['correct'],
					'correct_num'=>$list['correct_num'],
					'wrong_num'=>$list['wrong_num'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['myTest_01'] = $this->getCriteria();
		return true;
	}

	//獲取試題的正確率
	private function judgeStaffTest($record,$staff_id=""){
	    if(empty($staff_id)){
            $staff_id = Yii::app()->user->staff_id();
        }
        $count = Yii::app()->db->createCommand()->select("a.*,b.judge,c.name as title_name,c.remark")->from("exa_join d")
            ->leftJoin("exa_examina a","a.join_id = d.id")
            ->leftJoin("exa_title c","a.title_id = c.id")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("d.id=:join_id and a.employee_id=:employee_id", array(':join_id'=>$record["id"],':employee_id'=>$staff_id))->queryAll();
        $correct_num = 0;
        $wrong_num = 0;
        if($count){
            foreach ($count as $row){
                if ($row["judge"] == 1){
                    $correct_num++;
                }else{
                    $wrong_num++;
                }
            }
        }else{
            $count = array();
        }
        return array(
            "correct"=>sprintf("%.2f",($correct_num/count($count)*100))."%",
            "correct_num"=>$correct_num,
            "wrong_num"=>$wrong_num,
        );
    }
}
