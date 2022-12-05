<?php

class MutualAuditList extends CListPageModel
{
    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="10";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
			'city'=>Yii::t('study','city'),
			'employee_code'=>Yii::t('study','employee code'),
			'employee_name'=>Yii::t('study','employee name'),
			'employee'=>Yii::t('study','employee name'),
			'chapter_name'=>Yii::t('study','chapter name'),

			'mutual_state'=>Yii::t('study','state'),
			'end_body'=>Yii::t('study','end body'),
			'mutual_date'=>Yii::t('study','mutual date'),
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
		$sql1 = "select a.*,b.name,b.code,b.city from exa_mutual a 
                LEFT JOIN hr{$suffix}.hr_employee b ON a.employee_id=b.id
                where a.menu_id={$this->menu_id} and a.mutual_state in (1,2)
			";
        $sql2 = "select count(a.id) from exa_mutual a 
                LEFT JOIN hr{$suffix}.hr_employee b ON a.employee_id=b.id
                where a.menu_id={$this->menu_id} and a.mutual_state in (1,2)
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'end_body':
					$clause .= General::getSqlConditionClause('a.end_body',$svalue);
					break;
				case 'employee_code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'employee_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $arr = MutualMyList::getStateForArr($record);
				$this->attr[] = array(
					'id'=>$record['id'],
                    'employee_code'=>$record['code'],
                    'employee_name'=>$record['name'],
                    'city'=>CGeneral::getCityName($record['city']),

					'end_body'=>htmlspecialchars($record['end_body']),
					'mutual_state'=>$record['mutual_state'],
					'mutual_date'=>CGeneral::toDate($record['mutual_date']),
					'color'=>$arr['color'],
					'state'=>$arr['state'],
				);
			}
		}
		$session = Yii::app()->session;
        $session['mutualMy_'.$this->menu_code] = $this->getCriteria();
		return true;
	}

}
