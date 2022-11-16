<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ConcludePaperForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $title_num;
	public $title_sum;
	public $success_ratio;
	public $title_id_list;

    public $employee_id;
    public $employee_code;
    public $employee_name;

	public $markedly_id;
	public $dis_name;
	public $markedly_name;
	public $exa_num;
	public $join_must;

	public $lcd;

    public $menu_code;
    public $menu_name;
    public $menu_id;
    public $code_pre="06";

    public $paper_list=array();//試卷試題
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('study','ID'),
            'dis_name'=>Yii::t('study','test display'),
            'employee_name'=>Yii::t('study','Test Employee'),
            'markedly_name'=>Yii::t('study','test name'),
            'title_num'=>Yii::t('study','success num'),
            'title_sum'=>Yii::t('study','question sum'),
            'success_ratio'=>Yii::t('study','success ratio'),
            'join_must'=>Yii::t('study','Test Type'),
            'lcd'=>Yii::t('study','Quiz Date'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, markedly_id','safe'),
		);
	}

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()
            ->select("a.*,staff.code as employee_code,staff.name as employee_name,b.menu_code,b.menu_name,f.name as markedly_name,f.join_must,f.exa_num,f.dis_name")
            ->from("exa_take a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->leftJoin("exa_markedly f","a.markedly_id=f.id")
            ->leftJoin("hr{$suffix}.hr_employee staff","a.employee_id=staff.id")
            ->where("a.id=:id and staff.city in ({$city_allow})", array(':id'=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
            $this->employee_id = $row['employee_id'];
            $this->employee_code = $row['employee_code'];
            $this->employee_name = $row['employee_name'];
            $this->title_num = $row['title_num'];
            $this->title_sum = $row['title_sum'];
            $this->success_ratio = ($row['success_ratio']*100)."%";
            $this->title_id_list = explode(",",$row['title_id_list']);
            $this->lcd = CGeneral::toMyDate($row["lcd"]);

            $this->join_must = MarkedlyTestList::getTestType($row['join_must'],true);
            $this->markedly_id = $row['markedly_id'];
            $this->markedly_name = $row['markedly_name'];
            $this->exa_num = $row['exa_num'];
            $this->dis_name = $row['dis_name'];

            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;

            $this->paper_list = PaperMyForm::getPaPerList($this->id);
            return true;
		}
		return false;
	}
}
