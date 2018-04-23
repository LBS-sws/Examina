<?php

class StatisticsTitleList extends CListPageModel
{
    public $examinaName;
    public $qui_id;
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

	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select d.* from exa_examina a 
                LEFT JOIN exa_title d ON a.title_id = d.id
                where a.id > 0  
			";
        $sql2 = "select count(*) from exa_examina a 
                where id>0 
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
			    $list = $this->getCorrect($record['id']);
				$this->attr[] = array(
					'id'=>$record['id'],
					'title_code'=>$record['title_code'],
					'name'=>$record['name'],
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

	public function getCorrect($title_id){
        $rows = Yii::app()->db->createCommand()->select("b.judge")->from("exa_examina a")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.title_id=:title_id", array(':title_id'=>$title_id))->queryAll();
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
}
