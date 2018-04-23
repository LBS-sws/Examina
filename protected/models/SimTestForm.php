<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class SimTestForm extends CFormModel
{
	/* User Fields */
	public $exa_num;
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'exa_num'=>Yii::t('examina','question num'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('exa_num','safe'),
			array('exa_num','required'),
			array('exa_num','validateNumber'),
            array('exa_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
		);
	}

	public function validateNumber($attribute, $params){
	    if(is_numeric($this->exa_num)){
	        if(floatval($this->exa_num) == intval($this->exa_num)){
                $count = Yii::app()->db->createCommand()->select("count(*)")->from("exa_title")->queryScalar();
                if($this->exa_num>$count){
                    $message = Yii::t('examina','question num'). "不能大于".$count;
                    $this->addError($attribute,$message);
                }
            }
        }
    }
}
