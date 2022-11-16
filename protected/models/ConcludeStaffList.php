<?php

class ConcludeStaffList extends CListPageModel
{
    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="08";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
			'employee_code'=>Yii::t('study','employee code'),
			'employee_name'=>Yii::t('study','employee name'),
			'employee'=>Yii::t('study','Test Employee'),
			'markedly_name'=>Yii::t('study','test name'),
			'title_num'=>Yii::t('study','success num'),
			'title_sum'=>Yii::t('study','question sum'),
			'success_ratio'=>Yii::t('study','success ratio'),
            'join_must'=>Yii::t('study','Test Type'),
            'position'=>Yii::t('report','Position'),
            'city'=>Yii::t('report','City'),
            'lcd'=>Yii::t('study','Quiz Date'),
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
		$sql1 = "select a.employee_id,sum(a.title_num) as question_num,sum(a.title_sum) as question_sum,(sum(a.title_num)/sum(a.title_sum)) as question_ratio from exa_take a 
                LEFT JOIN hr{$suffix}.hr_employee f ON a.employee_id=f.id
                where a.menu_id={$this->menu_id} and f.city in ({$city_allow}) 
			";
        $sql2 = "select a.employee_id from exa_take a 
                LEFT JOIN hr{$suffix}.hr_employee f ON a.employee_id=f.id
                where a.menu_id={$this->menu_id} and f.city in ({$city_allow}) 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'employee_code':
					$clause .= General::getSqlConditionClause('f.code',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
				case 'city':
					$clause .= General::getSqlConditionClause('f.city',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

        $group = "group by a.employee_id";

		$sql = "select count(top.employee_id) from (".$sql2.$clause.$group.") top";
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

		$sql = $sql1.$clause.$group.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $staffRow = self::getStaffList($record['employee_id']);
				$this->attr[] = array(
					'id'=>$record['employee_id'],
					'employee_code'=>$staffRow["code"],
					'employee_name'=>$staffRow["name"],
					'position'=>$staffRow["position_name"],
					'city'=>$staffRow["city"],
					'city_name'=>$staffRow["city_name"],

					'title_num'=>$record['question_num'],
					'title_sum'=>$record['question_sum'],
					'success_ratio'=>($record['question_ratio']*100)."%",
				);
			}
		}
		$session = Yii::app()->session;
        $session['concludeStaff_'.$this->menu_code] = $this->getCriteria();
		return true;
	}


    public static function getStaffList($employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()
            ->select("a.id,a.code,a.name,a.position,a.city,b.name as city_name,f.name as position_name")
            ->from("hr$suffix.hr_employee a")
            ->leftJoin("security$suffix.sec_city b","a.city = b.code")
            ->leftJoin("hr$suffix.hr_dept f","a.position = f.id")
            ->where("a.id =:id",array(":id"=>$employee_id))->queryRow();
        if($rs){
            return $rs;
        }else{
            return array("id"=>"","code"=>"","name"=>"","city"=>"","city_name"=>"","position_name"=>"","position"=>"");
        }
    }
}
