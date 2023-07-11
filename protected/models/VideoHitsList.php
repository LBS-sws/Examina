<?php

class VideoHitsList extends CListPageModel
{
    public $menu_id;
    public $menu_name;
    public $menu_code;
    public $code_pre="11";
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'entry_time'=>Yii::t('examina','entry time'),
            'employee_name'=>Yii::t('study','employee name'),
            'city_name'=>Yii::t('examina','City'),
            'hit_date'=>Yii::t('study','hit date'),
            'study_title'=>Yii::t('study','Article Name'),
            'city'=>Yii::t('report','City'),
        );
    }

    public function rules()
    {
        return array(
            array('attr, menu_id, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType','safe',),
        );
    }

    public function retrieveAll($menu_id,$pageNum=1,$test=false){
        $menu = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where("id =:id",array(":id"=>$menu_id))->queryRow();
        if($menu){
            $this->menu_id = $menu_id;
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->retrieveDataByPage($pageNum,$test);
            return true;
        }
        return false;
    }

    public function retrieveDataByPage($pageNum=1,$test=false)
    {
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $sql1 = "select a.id,a.hit_date,a.link_url,f.code,f.name as employee_name,g.study_title,
                b.name as city_name,f.entry_time
                from exa_link_hits a 
                LEFT JOIN hr{$suffix}.hr_employee f ON a.employee_id=f.id
                LEFT JOIN exa_study g ON a.study_id=g.id
                LEFT JOIN security$suffix.sec_city b ON f.city=b.code 
                where a.menu_id={$this->menu_id} and a.hit_type=1 
			";
        $sql2 = "select count(a.id)
                from exa_link_hits a 
                LEFT JOIN hr{$suffix}.hr_employee f ON a.employee_id=f.id
                LEFT JOIN exa_study g ON a.study_id=g.id
                LEFT JOIN security$suffix.sec_city b ON f.city=b.code 
                where a.menu_id={$this->menu_id} and a.hit_type=1
			";
        $clause = "";
        if (!empty($this->searchField) && !empty($this->searchValue)) {
            $svalue = str_replace("'","\'",$this->searchValue);
            switch ($this->searchField) {
                case 'study_title':
                    $clause .= General::getSqlConditionClause('g.study_title',$svalue);
                    break;
                case 'employee_name':
                    $clause .= General::getSqlConditionClause('f.name',$svalue);
                    break;
                case 'city_name':
                    $clause .= General::getSqlConditionClause('b.name',$svalue);
                    break;
            }
        }

        $order = "";
        if (!empty($this->orderField)) {
            $order .= " order by ".$this->orderField." ";
            if ($this->orderType=='D') $order .= "desc ";
        }else{
            $order .= " order by a.hit_date desc ";
        }
        $sql = $sql2.$clause;
        $this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();

        $sql = $sql1.$clause.$order;
        $sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
        $records = Yii::app()->db->createCommand($sql)->queryAll();
        $this->attr = array();
        if($test){//添加虛擬數據
            $this->totalRow+=$this->preTestList($records);
        }
        if (count($records) > 0) {
            foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'hit_date'=>$record['hit_date'],

                    'entry_time'=>General::toMyDate($record['entry_time']),
                    'study_title'=>$record['study_title'],
                    'code'=>$record['code'],
                    'employee_name'=>$record['employee_name'],
                    'city_name'=>$record['city_name'],
                    'style'=>"",
                );
            }
        }
        $session = Yii::app()->session;
        $session['videoHits_'.$this->menu_code] = $this->getCriteria();
        return true;
    }

    private function preTestList(&$records){
        $records=$records?$records:array();
        $model = new VideoHitsModel($this->menu_id);
        $list = $model->getRoundList();
        $records = array_merge($records,$list);
        return count($list);
    }
}
