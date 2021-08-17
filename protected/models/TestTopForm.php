<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class TestTopForm extends CFormModel
{
	/* User Fields */
	public $join_id = 0;//我的測試id
	public $id = 0;
	public $dis_name;
	public $name;
	public $start_time;
	public $end_time;
	public $exa_num;
	public $correctList=0;
	public $wrongList=0;
	public $lcd;
	public $bumen;
    public $bumen_ex="全部";
    public $join_must="全部";
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('examina','ID'),
            'name'=>Yii::t('examina','test name'),
            'dis_name'=>Yii::t('examina','test display'),
            'start_time'=>Yii::t('examina','start time'),
            'end_time'=>Yii::t('examina','end time'),
            'exa_num'=>Yii::t('examina','question num'),
            'correct_num'=>Yii::t('examina','correct num'),
            'wrong_num'=>Yii::t('examina','wrong num'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'bumen_ex'=>Yii::t('examina','department'),
            'bumen'=>Yii::t('examina','department'),
            'join_must'=>Yii::t('examina','Test Type'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, dis_name, start_time, end_time, exa_num, bumen, bumen_ex, join_must','safe'),
			array('name','required'),
			array('start_time','required'),
			array('end_time','required'),
			array('exa_num','required'),
			array('name','validateName'),
			array('join_must','validateMust'),
            array('exa_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

	public function validateMust($attribute, $params){
	    if($this->join_must == 1){
            Yii::app()->db->createCommand()->update("exa_quiz",array("join_must"=>0),"id!=:id",array("id"=>$this->id));
        }
    }

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("exa_quiz")
            ->where('name=:name and id!=:id',
                array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('examina','test name'). Yii::t('examina',' can not repeat');
            $this->addError($attribute,$message);
        }
    }


    //刪除验证
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("exa_examina a")
            ->leftJoin("exa_join b","a.join_id = b.id")
            ->where('b.quiz_id=:quiz_id', array(':quiz_id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }
        return true;
    }

	public function getStaffListToTestId(){
        $rows = Yii::app()->db->createCommand()->select("employee_id")->from("exa_quiz_staff")
            ->where('quiz_id=:quiz_id', array(':quiz_id'=>$this->id))->queryAll();
        if ($rows){
            return array_column($rows,"employee_id");
        }
        return array();
    }

    //獲取所有城市列表
    public function getAllCityList(){
        $suffix = Yii::app()->params['envSuffix'];
        $cityList=array(""=>Yii::t("examina","all city"));
        $rows = Yii::app()->db->createCommand()->select()->from("security$suffix.sec_city")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $cityList[$row["code"]] = $row["name"];
            }
        }
        return $cityList;
    }

    //獲取所有員工列表
    public function getAllStaffList($city=""){
        $suffix = Yii::app()->params['envSuffix'];
        $staffList=array();
        if(!empty($city)){
            $rows = Yii::app()->db->createCommand()->select("id,name")->from("hr$suffix.hr_employee")
                ->where("city =:city and staff_status=0",array(":city"=>$city))->queryAll();
            if($rows){
                foreach ($rows as $row){
                    $staffList[$row["id"]] = $row["name"];
                }
            }
        }
        return $staffList;
    }

    //獲取測驗單列表（僅能使用的）
    public function getAllTestListOnly(){
        $date = date("Y-m-d");
        $bumen = Yii::app()->user->bumen();
        $arr= array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("id,name")->from("exa_quiz")
            ->where("(bumen LIKE '%,$bumen,%' or bumen='') and date_format(start_time,'%Y-%m-%d')<='$date' and date_format(end_time,'%Y-%m-%d')>='$date'")
            ->order("join_must desc,id desc")->queryAll();
        if($rows){
            foreach ($rows as $key =>$row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //獲取正確數量
    public function getCorrectNum(){
        //$staff_id = Yii::app()->user->staff_id();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.judge,c.name as title_name,c.remark")->from("exa_join d")
            ->leftJoin("exa_examina a","a.join_id = d.id")
            ->leftJoin("exa_title c","a.title_id = c.id")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("d.id=:join_id", array(':join_id'=>$this->join_id))->queryAll();
        if($rows){
            $this->lcd = $rows[0]["lcd"];
            $this->correctList = array();
            $this->wrongList = array();
            foreach ($rows as $row){
                $row["chooseList"] = $this->getChooseList($row["list_choose"]);
                if($row["judge"] == 1){
                    $this->correctList[] = $row;
                }else{
                    $this->wrongList[] = $row;
                }
            }
            return true;
        }else{
            return false;
        }
    }

    //獲取正確數量
    public function getChooseList($str){
        $arr = array();
        $list = explode(",",$str);
        foreach ($list as $item){
            $row = Yii::app()->db->createCommand()->select("*")->from("exa_title_choose")
                ->where("id=:id", array(':id'=>$item))->queryRow();
            $arr[] = $row;
        }
        return $arr;
    }

    //部門查詢
    public function searchDepartment($department){
        $suffix = Yii::app()->params['envSuffix'];
        $arr = array();
        $sql = "";
        if(!empty($department)){
            $sql.="and (a.name like '%$department%' or b.name like '%$department%') ";
        }
        $rows = Yii::app()->db->createCommand()->select("a.*,b.name as city_name")->from("hr$suffix.hr_dept a")
            ->leftjoin("security$suffix.sec_city b","b.code = a.city")
            ->where("type = 0 $sql")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $arr[$row["id"]] = $row["name"]."（".$row["city_name"]."）";
            }
        }
        return $arr;
    }

	public function getQuizTitleName($quiz_id)
	{
        $rows = Yii::app()->db->createCommand()->select("name")->from("exa_quiz")
            ->where("id=:id", array(':id'=>$quiz_id))->queryRow();
        if($rows){
            return $rows["name"];
        }else{
            return $quiz_id;
        }
	}

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("exa_quiz")
            ->where("id=:id", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
				$this->id = $row['id'];
				$this->dis_name = $row['dis_name'];
                $this->name = $row['name'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->exa_num = $row['exa_num'];
                $this->bumen = $row['bumen'];
                $this->bumen_ex = $row['bumen_ex'];
                $this->join_must = $row['join_must'];
				break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			var_dump($e);
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from exa_quiz where id = :id ";
				break;
			case 'new':
				$sql = "insert into exa_quiz(
							dis_name,join_must, name, start_time, end_time, exa_num, bumen_ex, bumen, lcu
						) values (
							:dis_name,:join_must, :name, :start_time, :end_time, :exa_num, :bumen_ex, :bumen, :lcu
						)";
				break;
			case 'edit':
				$sql = "update exa_quiz set
							dis_name = :dis_name, 
							join_must = :join_must, 
							name = :name, 
							start_time = :start_time, 
							end_time = :end_time, 
							exa_num = :exa_num, 
							bumen_ex = :bumen_ex, 
							bumen = :bumen,  
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':join_must')!==false)
			$command->bindParam(':join_must',$this->join_must,PDO::PARAM_INT);
		if (strpos($sql,':dis_name')!==false)
			$command->bindParam(':dis_name',$this->dis_name,PDO::PARAM_STR);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);
		if (strpos($sql,':start_time')!==false)
			$command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
		if (strpos($sql,':end_time')!==false)
			$command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
		if (strpos($sql,':exa_num')!==false)
			$command->bindParam(':exa_num',$this->exa_num,PDO::PARAM_INT);
        if (strpos($sql,':bumen_ex')!==false)
            $command->bindParam(':bumen_ex',$this->bumen_ex,PDO::PARAM_STR);
        if (strpos($sql,':bumen')!==false)
            $command->bindParam(':bumen',$this->bumen,PDO::PARAM_STR);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->setScenario("edit");
        }
        return true;
	}

}
