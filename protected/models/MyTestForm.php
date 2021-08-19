<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class MyTestForm extends CFormModel
{
	/* User Fields */
	public $join_id;
	public $id = 0;
	public $quiz_id;
	public $employee_id;
	public $title_id;
	public $choose_id;
	public $list_choose;
	public $lcd;
	private $staffList;

	public $title_sum=1;
	public $title_num=0;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('examina','ID'),
            'quiz_id'=>Yii::t('examina','question code'),
            'employee_id'=>Yii::t('examina','Employee Name'),
            'title_id'=>Yii::t('examina','test name'),
            'choose_id'=>Yii::t('examina','choose'),

            'list_choose'=>Yii::t('examina','choose'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, quiz_id, employee_id, title_id, choose_id, list_choose','safe'),
			array('quiz_id','required'),
			array('quiz_id','validateName','on'=>'new'),
			array('list_choose','required'),
			array('employee_id','validateStaff'),
			array('list_choose','validateChoose','on'=>'new'),
		);
	}

	public function validateStaff($attribute, $params){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        //position:職位  department：部門
        $rs = Yii::app()->db->createCommand()->select("b.id,b.name,b.code,b.department,b.position,b.entry_time,f.technician")->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("hr$suffix.hr_dept f","b.position=f.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            $this->staffList = $rs;
        }else{
            $message = "该账号未绑定员工，请与管理员联系";
            $this->addError($attribute,$message);
        }
    }

	public function validateChoose($attribute, $params){
        $session = Yii::app()->session;
        $list_choose = $this->list_choose;
        if (isset($session['examina_list']) && !empty($session['examina_list'])) {
            foreach ($session["examina_list"] as $key => $examina){
                if(!is_array($list_choose)){
                    $message = Yii::t('examina','Data exception, please refresh retry.');
                    $this->addError($attribute,$message);
                    return false;
                }
                if(!array_key_exists($key,$list_choose)){
                    $message = Yii::t('examina','Data exception, please refresh retry.');
                    $this->addError($attribute,$message);
                    return false;
                }
                $idList = array_column($examina,"id");
                if(!in_array($list_choose[$key],$idList)){
                    $message = Yii::t('examina','Data exception, please refresh retry.');
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }else{
            $message = Yii::t('examina','Data exception, please refresh retry.');
            $this->addError($attribute,$message);
        }
    }

	public function validateName($attribute, $params){
        $bumen = Yii::app()->user->bumen();
        $staff_id = Yii::app()->user->staff_id();
        if(empty($staff_id)){
            $message = Yii::t('examina','Employee Name'). Yii::t('examina',' Did not find');
            $this->addError($attribute,$message);
            return false;
        }
        $rows = Yii::app()->db->createCommand()->select("*")->from("exa_quiz")
            ->where("id=:id and (bumen=''||bumen LIKE '%,$bumen,%')",array(':id'=>$this->quiz_id))->queryRow();
        if(!$rows){
            $message = Yii::t('examina','test name'). Yii::t('examina',' Did not find');
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index)
	{
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("exa_title")
            ->where("id=:id", array(':id'=>$index))->queryAll();
		if (count($rows) > 0)
		{
			foreach ($rows as $row)
			{
/*				$this->id = $row['id'];
				$this->title_code = $row['title_code'];
				$this->remark = $row['remark'];
                $this->name = $row['name'];
                $this->answerList = $this->getChooseToId();*/
				break;
			}
		}
		return true;
	}
    //獲取問題的選項
    public function getChooseToId($id = 0){
        if(empty($id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id,title_id,choose_name as choose,judge")->from("exa_title_choose")
            ->where("title_id=:title_id", array(':title_id'=>$id))->order('id ASC')->queryAll();
        if($rows){
            return $rows;
        }else{
            return array();
        }
    }
	
	public function saveData()
	{
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $session = Yii::app()->session;
        $list_choose = $this->list_choose;
        $staff_id = $this->staffList["id"];
        $command = Yii::app()->db->createCommand();
        $command->insert('exa_join', array(
            'quiz_id'=>$this->quiz_id,
            'employee_id'=>$staff_id,
            'lcu'=>$uid,
        ));
        $title_sum=count($session["examina_list"]);
        $title_sum = $title_sum<1?1:$title_sum;
        $title_num=0;
        $this->join_id = Yii::app()->db->getLastInsertID();
        foreach ($session["examina_list"] as $key => $examina){
            $bool = Yii::app()->db->createCommand()->select("count(id)")->from("exa_title_choose")
                ->where("judge=1 and id=:id and title_id=:title_id",
                    array(":title_id"=>$examina[0]["title_id"],":id"=>$list_choose[$key]))->queryScalar();
            if($bool == 1){
                $title_num++;
            }
            $command->reset();
            $command->insert('exa_examina', array(
                'join_id'=>$this->join_id,
                'employee_id'=>$staff_id,
                'title_id'=>$examina[0]["title_id"],
                'choose_id'=>$list_choose[$key],
                'list_choose'=>implode(",",array_column($examina,"id")),
                'lcu'=>$uid,
            ));
        }
        $command->reset();
        $command->update('exa_join', array(
            "title_sum"=>$title_sum,
            "title_num"=>$title_num
        ),"id='$this->join_id'");
        $this->title_sum=$title_sum;
        $this->title_num=$title_num;
        $mustJoinID = General::getQuizIdForMust();
        if($mustJoinID==$this->join_id&&($title_num/$title_sum<0.85)&&$this->staffList["technician"]==1){
            $startDate = date("Y/m/d");
            $date = date("Y/m/01");
            $endDate = date("Y-m",strtotime("$date -3 month"));
            $date = date("Y/m/d",strtotime($this->staffList["entry_time"]));
            if($date<=$startDate&&$date>=$endDate){
                Dialog::message(Yii::t('dialog','Warning'), Yii::t('block','validateNewStaff'));
            }else{
                Dialog::message(Yii::t('dialog','Warning'), Yii::t('block','validateExamination'));
            }
        }else{
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
        }
	}

	//重置以前的測驗單
    public function resetOldTest(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("exa_join")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list = Yii::app()->db->createCommand()->select("title_id,choose_id")->from("exa_examina")->where("join_id='".$row['id']."'")->queryAll();
                $title_sum=count($list);
                $title_num=0;
                foreach ($list as $item){
                    $bool = Yii::app()->db->createCommand()->select("count(id)")->from("exa_title_choose")
                        ->where("judge=1 and id=:id and title_id=:title_id",
                            array(":title_id"=>$item["title_id"],":id"=>$item["choose_id"]))->queryScalar();
                    if($bool == 1){
                        $title_num++;
                    }
                }
                Yii::app()->db->createCommand()->update('exa_join', array(
                    "title_sum"=>$title_sum,
                    "title_num"=>$title_num
                ),"id='".$row["id"]."'");
            }
        }
    }
}
