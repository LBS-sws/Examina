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
			array('list_choose','validateChoose','on'=>'new'),
		);
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
        $session = Yii::app()->session;
        $list_choose = $this->list_choose;
        $staff_id = Yii::app()->user->staff_id();
        $command = Yii::app()->db->createCommand();
        $command->insert('exa_join', array(
            'quiz_id'=>$this->quiz_id,
            'employee_id'=>$staff_id,
            'lcu'=>$uid,
        ));
        $this->join_id = Yii::app()->db->getLastInsertID();
        foreach ($session["examina_list"] as $key => $examina){
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
	}
}
