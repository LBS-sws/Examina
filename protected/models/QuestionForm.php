<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class QuestionForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $title_code;
	public $name;
	public $remark;
	public $city;
	public $answerList=array(
	    array("id"=>"","choose"=>""),
	    array("id"=>"","choose"=>""),
	    array("id"=>"","choose"=>""),
	    array("id"=>"","choose"=>"")
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('examina','ID'),
            'title_code'=>Yii::t('examina','question code'),
            'name'=>Yii::t('examina','question name'),
            'city'=>Yii::t('examina','City'),
            'remark'=>Yii::t('examina','question remark'),

            'answer'=>Yii::t('examina','correct answer'),
            'answer_a'=>Yii::t('examina','wrong answer A'),
            'answer_b'=>Yii::t('examina','wrong answer B'),
            'answer_c'=>Yii::t('examina','wrong answer C'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, title_code, name, remark, answerList','safe'),
			array('name','required'),
			array('answerList','required'),
			array('name','validateName'),
			array('answerList','validateAnswer'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("exa_title")
            ->where('name=:name and id!=:id',
                array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('examina','question name'). Yii::t('examina',' can not repeat');
            $this->addError($attribute,$message);
        }
    }

	public function validateAnswer($attribute, $params){
	    if(!empty($this->answerList)&&is_array($this->answerList)){
	        foreach ($this->answerList as $answer){
                if(empty($answer["choose"])){
                    $message = Yii::t('examina','choose'). Yii::t('examina',' can not empty');
                    $this->addError($attribute,$message);
                }elseif($this->getScenario() != "new"){
                    if(empty($answer["id"])){
                        $message =  Yii::t('examina','Data exception, please refresh retry.');
                        $this->addError($attribute,$message);
                    }else{
                        $row = Yii::app()->db->createCommand()->select("title_id")->from("exa_title_choose")
                            ->where('id=:id',array(':id'=>$answer["id"]))->queryRow();
                        if(!$row || $row["title_id"] != $this->id){
                            $message =  Yii::t('examina','Data exception, please refresh retry.');
                            $this->addError($attribute,$message);
                        }
                    }
                }
            }
        }else{
            $message = Yii::t('examina','correct answer'). Yii::t('examina',' can not empty');
            $this->addError($attribute,$message);
        }
    }

    //刪除验证
	public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("exa_examina")
            ->where('title_id=:title_id', array(':title_id'=>$this->id))->queryAll();
        if ($rows){
            return false;
        }
        return true;
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
				$this->id = $row['id'];
				$this->title_code = $row['title_code'];
				$this->remark = $row['remark'];
                $this->name = $row['name'];
                $this->answerList = $this->getChooseToId();
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
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
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
                $sql = "delete from exa_title where id = :id ";
				break;
			case 'new':
				$sql = "insert into exa_title(
							remark, name, city, lcu
						) values (
							:remark, :name, :city, :lcu
						)";
				break;
			case 'edit':
				$sql = "update exa_title set
							remark = :remark, 
							name = :name, 
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':name')!==false)
			$command->bindParam(':name',$this->name,PDO::PARAM_STR);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->lenStr();
            Yii::app()->db->createCommand()->update('exa_title', array(
                'title_code'=>$this->title_code
            ), 'id=:id', array(':id'=>$this->id));
        }
        $this->setAnswer();//選項錄入
        return true;
	}

    private function setAnswer(){
        if ($this->scenario=='new'){
            $key = 1;
            foreach ($this->answerList as $answer){
                Yii::app()->db->createCommand()->insert('exa_title_choose', array(
                    'title_id'=>$this->id,
                    'choose_name'=>$answer["choose"],
                    'judge'=>$key
                ));
                $key = 0;
            }
            $this->scenario = "edit";
        }else{
            foreach ($this->answerList as $answer){
                Yii::app()->db->createCommand()->update('exa_title_choose', array(
                    'choose_name'=>$answer["choose"]
                ), 'id=:id', array(':id'=>$answer["id"]));
            }
        }
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->title_code = "Q";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->title_code.="0";
        }
        $this->title_code .= $code;
    }
}
