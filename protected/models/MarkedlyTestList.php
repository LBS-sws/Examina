<?php

class MarkedlyTestList extends CListPageModel
{

    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="07";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
			'name'=>Yii::t('study','test name'),
			'start_time'=>Yii::t('study','start time'),
			'end_time'=>Yii::t('study','end time'),
			'exa_num'=>Yii::t('study','question num'),
            'bumen_ex'=>Yii::t('study','Article all'),
            'join_must'=>Yii::t('study','Test Type'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, menu_id, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType','safe',),
        );
    }

    public function retrieveAll($menu_id,$pageNum=1){
        $menu = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where("id =:id",array(":id"=>$menu_id))->queryRow();
        if($menu){
            $this->menu_id = $menu_id;
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->retrieveDataByPage($pageNum);
            return true;
        }
        return false;
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.* from exa_markedly a 
                where a.id>0 and a.menu_id={$this->menu_id} 
			";
        $sql2 = "select count(*) from exa_markedly a 
                where a.id>0 and a.menu_id={$this->menu_id} 
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
        $session['markedlyTest_'.$this->menu_code] = $this->getCriteria();
		return true;
	}


    public static function getTestType($type="",$bool=false){
        $arr = array(Yii::t("study","general Test"),Yii::t("study","must Test"));
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
