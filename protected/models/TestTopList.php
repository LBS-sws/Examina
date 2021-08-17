<?php

class TestTopList extends CListPageModel
{
    public $searchTimeStart;//開始日期
    public $searchTimeEnd;//結束日期
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
            'bumen_ex'=>Yii::t('examina','department'),
            'join_must'=>Yii::t('examina','Test Type'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTimeStart, searchTimeEnd','safe',),
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
				case 'bumen_ex':
					$clause .= General::getSqlConditionClause('a.bumen_ex',$svalue);
					break;
			}
		}
        if (!empty($this->searchTimeStart) && !empty($this->searchTimeStart)) {
            $svalue = str_replace("'","\'",$this->searchTimeStart);
            $clause .= " and a.start_time >='$svalue' ";
        }
        if (!empty($this->searchTimeEnd) && !empty($this->searchTimeEnd)) {
            $svalue = str_replace("'","\'",$this->searchTimeEnd);
            $clause .= " and a.start_time <='$svalue' ";
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
			    $bumenList = explode(",",$record['bumen_ex']);
			    if(count($bumenList)>3){
                    $bumenList = array_slice($bumenList,0,2);
                    $record['bumen_ex'] = implode(",",$bumenList).".....";
                }
                //$datetime = new DateTime($date);
				$this->attr[] = array(
					'id'=>$record['id'],
					'start_time'=>CGeneral::toMyDate($record['start_time']),
					'end_time'=>CGeneral::toMyDate($record['end_time']),
					'name'=>$record['name'],
					'exa_num'=>$record['exa_num'],
					'bumen_ex'=>$record['bumen_ex'],
					'join_must'=>self::getTestType($record['join_must'],true),
				);
			}
		}
		$session = Yii::app()->session;
		$session['testTop_01'] = $this->getCriteria();
		return true;
	}

//获取地区編號（模糊查詢）
    public function getCityCodeSqlLikeName($code)
    {
        $from =  'security'.Yii::app()->params['envSuffix'].'.sec_city';
        $rows = Yii::app()->db->createCommand()->select("code")->from($from)->where(array('like', 'name', "%$code%"))->queryAll();
        $arr = array();
        foreach ($rows as $row){
            array_push($arr,"'".$row["code"]."'");
        }
        if(empty($arr)){
            return "('')";
        }else{
            $arr = implode(",",$arr);
            return "($arr)";
        }
    }

    public static function getTestType($type="",$bool=false){
        $arr = array(Yii::t("examina","general Test"),Yii::t("examina","must Test"));
        if($bool){
            if(key_exists($type,$arr)){
                return $arr[$type];
            }else{
                return $type;
            }
        }else{
            return $arr;
        }

    }
}
