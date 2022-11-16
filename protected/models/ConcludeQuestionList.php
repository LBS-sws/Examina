<?php

class ConcludeQuestionList extends CListPageModel
{
    public $employee_id;
    public $employee_code;
    public $employee_name;

    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="09";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(	
			'id'=>Yii::t('study','ID'),
            'title_code'=>Yii::t('study','question code'),
            'title_type'=>Yii::t('study','question type'),
            'chapter_name'=>Yii::t('study','Attribution Article'),
            'name'=>Yii::t('study','question name'),
            'show_num'=>Yii::t('study','show num'),
            'success_num'=>Yii::t('study','success num'),
            'success_ratio'=>Yii::t('study','success ratio'),
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
        $sql1 = "select a.*,b.chapter_name,(a.success_num/a.show_num) as success_ratio from exa_chapter_title a
                LEFT JOIN exa_chapter_class b on a.chapter_id = b.id
                where a.display = 1 and a.show_num!=0 
			";
        $sql2 = "select count(a.id) from exa_chapter_title a 
                LEFT JOIN exa_chapter_class b on a.chapter_id = b.id
                where a.display = 1 and a.show_num!=0 
			";
        $clause = "";
        if (!empty($this->searchField) && $this->searchValue!=="") {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'title_type':
                    $clause .= General::getSqlConditionClause('a.title_type','"'.$svalue.'"');
                    break;
                case 'title_code':
                    $clause .= General::getSqlConditionClause('a.title_code',$svalue);
                    break;
                case 'name':
                    $clause .= General::getSqlConditionClause('a.name',$svalue);
                    break;
                case 'chapter_name':
                    $clause .= General::getSqlConditionClause('b.chapter_name',$svalue);
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
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'title_type_id'=>$record['title_type'],
                    'title_type'=>ChapterQuestionList::choiceList($record['title_type'],true),
                    'title_code'=>$record['title_code'],
                    'name'=>$record['name'],
                    'chapter_name'=>$record['chapter_name'],
                    'show_num'=>$record['show_num'],
                    'success_num'=>$record['success_num'],
                    'success_ratio'=>round($record['success_ratio']*100)."%",
                );
            }
        }
		$session = Yii::app()->session;
        $session['concludeQuestion_'.$this->menu_code] = $this->getCriteria();
		return true;
	}

}
