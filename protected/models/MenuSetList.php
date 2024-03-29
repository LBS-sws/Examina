<?php

class MenuSetList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
			'menu_code'=>Yii::t('study','menu code'),
			'menu_name'=>Yii::t('study','menu name'),
			'display'=>Yii::t('study','display'),
			'z_index'=>Yii::t('study','z_index'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select * from exa_setting 
                where id>0 
			";
        $sql2 = "select count(*) from exa_setting 
                where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'menu_code':
				    if(is_numeric($svalue)){
                        $clause .= ' and menu_code = "'.$svalue.'"';
                    }
					break;
				case 'menu_name':
				    if(is_numeric($svalue)){
                        $clause .= ' and menu_name = "'.$svalue.'"';
                    }
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
					'menu_code'=>$record['menu_code'],
					'menu_name'=>$record['menu_name'],
					'display'=>empty($record['display'])?Yii::t("study","none"):Yii::t("study","show"),
					'z_index'=>$record['z_index'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['menuSet_01'] = $this->getCriteria();
		return true;
	}
}
