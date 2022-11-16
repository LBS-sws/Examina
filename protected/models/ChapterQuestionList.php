<?php

class ChapterQuestionList extends CListPageModel
{
    public $menu_id;
    public $menu_code;
    public $menu_name;
    public $chapter_id;
    public $chapter_name;

    protected $code_pre="02";

    public function getCriteria() {
        return array(
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,

            'menu_id'=>$this->menu_id,
            'chapter_id'=>$this->chapter_id
        );
    }
    public function rules()
    {
        return array(
            array('menu_id,chapter_id,attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter','safe',),
        );
    }
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
			'name'=>Yii::t('study','question name'),
            'display'=>Yii::t('study','display'),
		);
	}

	public function retrieveDataByPage($chapter_id,$pageNum=1){
        $menu = Yii::app()->db->createCommand()->select("a.*,b.menu_name,b.menu_code")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id",array(":id"=>$chapter_id))->queryRow();
        if($menu){
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->chapter_id = $chapter_id;
            $this->chapter_name = $menu["chapter_name"];
            $this->getAttrForSearch();
            return true;
        }else{
            return false;
        }
	}

	private function getAttrForSearch($display=1){
        $sql1 = "select * from exa_chapter_title 
                where chapter_id = {$this->chapter_id} 
			";
        $sql2 = "select count(*) from exa_chapter_title 
                where chapter_id = {$this->chapter_id} 
			";
        $clause = "";
        if (!empty($this->searchField) && $this->searchValue!=="") {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'title_type':
                    $clause .= General::getSqlConditionClause('title_type','"'.$svalue.'"');
                    break;
                case 'title_code':
                    $clause .= General::getSqlConditionClause('title_code',$svalue);
                    break;
                case 'name':
                    $clause .= General::getSqlConditionClause('name',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by id desc ";
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
                    'title_type'=>self::choiceList($record['title_type'],true),
                    'title_code'=>$record['title_code'],
                    'name'=>$record['name'],
                    'display'=>empty($record['display'])?Yii::t("study","none"):Yii::t("study","show"),
                );
            }
        }
        $session = Yii::app()->session;
        $session['chapterQuestion_'.$this->menu_code] = $this->getCriteria();
    }

    public static function choiceList($title_type=0,$bool=false){
	    $list =array(
	        0=>Yii::t("study","Single choice"),
	        1=>Yii::t("study","Multiple choice"),
	        2=>Yii::t("study","Judgment choice"),
        );
	    if($bool){
            if(key_exists($title_type,$list)){
                return $list[$title_type];
            }else{
                return $title_type;
            }
        }else{
	        return $list;
        }
    }
}
