<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class SimTestForm extends CFormModel
{
	/* User Fields */
	public $quiz_id;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            //'exa_num'=>Yii::t('examina','question num'),
            'quiz_id'=>Yii::t('examina','test name'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('quiz_id','safe'),
			array('quiz_id','required'),
		);
	}

	public function getQuizIdToJoinID($index){
        $rows = Yii::app()->db->createCommand()->select("quiz_id")->from("exa_join")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if($rows){
            return $rows["quiz_id"];
        }else{
            return "";
        }
    }

	public function getJoinList($index){
        $rows = Yii::app()->db->createCommand()->select()->from("exa_join")
            ->where("id=:id", array(':id'=>$index))->queryRow();
        if($rows){
            return $rows;
        }else{
            return false;
        }
    }
}
