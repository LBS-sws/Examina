<?php

class StatisticsTitleList extends CListPageModel
{
    public $searchTitle;//測驗單id
    public $searchCity;//城市
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'title_code'=>Yii::t('examina','question code'),
            'name'=>Yii::t('examina','question name'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'city'=>Yii::t('examina','city all'),
            'city_name'=>Yii::t('examina','City'),
		);
	}

    public function rules()
    {
        return array(
            array('attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, searchTitle, searchCity','safe',),
        );
    }

    public function getThisUserCityList(){
	    $arr=array();
        $city_allow = Yii::app()->user->city_allow();
        $city_list = explode(",",str_replace("'","",$city_allow));
        foreach ($city_list as $city){
            $arr[$city] = CGeneral::getCityName($city);
        }
        return $arr;
    }

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $city_list = explode(",",str_replace("'","",$city_allow));
        $city = $this->searchCity;
		if(empty($city)||!in_array($city,$city_list)){
            $city = Yii::app()->user->city();
        }
        $this->searchCity = $city;
		$sql1 = "select d.*,e.city from exa_examina a 
                LEFT JOIN exa_join b ON b.id = a.join_id
                LEFT JOIN hr$suffix.hr_employee e ON e.id = a.employee_id
                LEFT JOIN exa_title d ON a.title_id = d.id
                where e.city = '$city' 
			";
        $sql2 = "select count(*) from exa_examina a 
                where e.city = '$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'title_code':
					$clause .= General::getSqlConditionClause('d.title_code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('d.name',$svalue);
					break;
			}
		}
        if (!empty($this->searchTitle) && !empty($this->searchTitle)) {
            $svalue = str_replace("'","\'",$this->searchTitle);
            $clause .= " and b.quiz_id ='$svalue' ";
        }
		$group = " group by a.title_id";

		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		}

		$sql = $sql1.$clause.$group;
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if($rows){
            $this->totalRow = count($rows);
        }else{
            $this->totalRow = 0;
        }

		$sql = $sql1.$clause.$group.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
			    $list = $this->getCorrect($record['id'],$record['city']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'title_code'=>$record['title_code'],
					'name'=>$record['name'],
					'city'=>CGeneral::getCityName($record['city']),
                    'correct'=>$list["correct"],
                    'correctNum'=>$list["correctNum"],
                    'occurrences'=>$list["occurrences"],
				);
			}
		}
		$session = Yii::app()->session;
		$session['statisticsTitle_01'] = $this->getCriteria();
		return true;
	}

	public function getCorrect($title_id,$city){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("b.judge")->from("exa_examina a")
            ->leftJoin("hr$suffix.hr_employee e","e.id = a.employee_id")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.title_id=:title_id and e.city=:city", array(':title_id'=>$title_id,':city'=>$city))->queryAll();
        if($rows){
            $num = 0;
            foreach ($rows as $row){
                if($row["judge"] == 1){
                    $num++;
                }
            }
            return array(
                'occurrences'=>count($rows),//出現次數
                'correctNum'=>$num,//正確數量
                'correct'=>sprintf("%.2f",($num/count($rows)*100))."%",//正確率
            );
        }else{
            return array(
                'correctNum'=>0,//正確數量
                'correct'=>0,//正確率
                'occurrences'=>0,//出現次數
            );
        }
    }

    //獲取測驗單列表
    public function getAllTestTopList(){
	    $arr = array(""=>Yii::t("examina","Selection test sheet"));
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("exa_quiz")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }
}
