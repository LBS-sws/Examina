<?php

class PaperMyList extends CListPageModel
{

    public $employee_id;
    public $employee_code;
    public $employee_name;

    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="05";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
			'employee'=>Yii::t('study','Test Employee'),
			'markedly_name'=>Yii::t('study','test name'),
			'title_num'=>Yii::t('study','success num'),
			'title_sum'=>Yii::t('study','question sum'),
			'success_ratio'=>Yii::t('study','success ratio'),
            'join_must'=>Yii::t('study','Test Type'),
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
		$sql1 = "select a.*,b.name as markedly_name,b.join_must,b.exa_num from exa_take a 
                LEFT JOIN exa_markedly b ON a.markedly_id=b.id
                where a.menu_id={$this->menu_id} and a.employee_id ={$this->employee_id} 
			";
        $sql2 = "select count(a.id) from exa_take a 
                LEFT JOIN exa_markedly b ON a.markedly_id=b.id
                where a.menu_id={$this->menu_id} and a.employee_id ={$this->employee_id} 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'markedly_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
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
				$this->attr[] = array(
					'id'=>$record['id'],
					'employee'=>$this->employee_name." ({$this->employee_code})",

					'title_num'=>$record['title_num'],
					'title_sum'=>$record['title_sum'],
					'success_ratio'=>($record['success_ratio']*100)."%",
					'markedly_name'=>$record['markedly_name'],
					'lcd'=>CGeneral::toMyDate($record["lcd"]),
					'join_must'=>MarkedlyTestList::getTestType($record['join_must'],true),
				);
			}
		}
		$session = Yii::app()->session;
        $session['paperMy_'.$this->menu_code] = $this->getCriteria();
		return true;
	}

}
