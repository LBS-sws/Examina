<?php

class PaperWrongList extends CListPageModel
{
    public $employee_id;
    public $employee_code;
    public $employee_name;

    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="04";
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
			'employee'=>Yii::t('study','employee name'),
			'chapter_name'=>Yii::t('study','chapter name'),

			'name'=>Yii::t('study','question name'),
			'title_type'=>Yii::t('study','question type'),
			'wrong_date'=>Yii::t('study','wrong date'),
            'wrong_type'=>Yii::t('study','wrong root'),
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
		$sql1 = "select f.chapter_name,a.id,a.title_id,a.wrong_date,a.wrong_type,a.take_id,b.title_type,b.name from exa_wrong_title a 
                LEFT JOIN exa_chapter_title b ON a.title_id=b.id
                LEFT JOIN exa_chapter_class f ON b.chapter_id=f.id
                where a.menu_id={$this->menu_id} and a.employee_id={$this->employee_id} and a.display=1 
			";
        $sql2 = "select count(a.id) from exa_wrong_title a 
                LEFT JOIN exa_chapter_title b ON a.title_id=b.id
                LEFT JOIN exa_chapter_class f ON b.chapter_id=f.id
                where a.menu_id={$this->menu_id} and a.employee_id={$this->employee_id} and a.display=1 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'chapter_name':
					$clause .= General::getSqlConditionClause('f.chapter_name',$svalue);
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

					'name'=>$record['name'],
					'title_id'=>$record['title_id'],
					'title_type_id'=>$record['title_type'],
					'title_type'=>ChapterQuestionList::choiceList($record['title_type'],true),
					'wrong_date'=>$record['wrong_date'],
					'wrong_type'=>$record['wrong_type'],
					'take_id'=>$record['take_id'],
					'chapter_name'=>$record['chapter_name'],
				);
			}
		}
		$session = Yii::app()->session;
        $session['paperWrong_'.$this->menu_code] = $this->getCriteria();
		return true;
	}
}
