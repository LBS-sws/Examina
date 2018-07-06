<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class StatisticsViewForm extends CFormModel
{
    /* User Fields */
    public $id = 0;
    public $employee_id = 0;
    public $employee_name = '';
    public $dis_name;
    public $name;
    public $start_time;
    public $end_time;
    public $exa_num;
    public $staff_all;
    public $staffList;
    public $city='';
    public $correctList=0;
    public $wrongList=0;
    public $lcd;
    public $quiz_id;
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
            'city'=>Yii::t('examina','city all'),
            'staff_all'=>Yii::t('examina','staff all'),
            'staffList'=>Yii::t('examina','staff select'),
            'correct_num'=>Yii::t('examina','correct num'),
            'wrong_num'=>Yii::t('examina','wrong num'),
            'lcd'=>Yii::t('examina','Participate in time'),
            'quiz_id'=>Yii::t('examina','category name'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, name, dis_name, start_time, end_time, quiz_id, exa_num, city, staff_all, staffList','safe'),
            array('quiz_id','required'),
            array('name','required'),
            array('start_time','required'),
            array('end_time','required'),
            array('exa_num','required'),
            array('name','validateName'),
            array('staffList','validateStaff'),
            array('exa_num','validateNumber'),
            array('exa_num', 'numerical', 'min'=>1, 'integerOnly'=>true),
        );
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

    public function validateStaff($attribute, $params){
        if($this->staff_all == 0){
            if(empty($this->staffList)){
                $message = Yii::t('examina','staff select'). Yii::t('examina',' can not empty');
                $this->addError($attribute,$message);
            }
        }
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


    //刪除验证
    public function validateDelete(){
        $rows = Yii::app()->db->createCommand()->select()->from("exa_examina")
            ->where('quiz_id=:quiz_id', array(':quiz_id'=>$this->id))->queryAll();
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

    //獲取所有城市列表
    public function getEmployeeNameToId($employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("name")->from("hr$suffix.hr_employee")->where("id=:id",array(":id"=>$employee_id))->queryRow();
        if($rows){
            return $rows["name"];
        }
        return $employee_id;
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

    //獲取正確數量
    public function getCorrectNum(){
        $staff_id = Yii::app()->user->staff_id();
        $rows = Yii::app()->db->createCommand()->select("a.*,b.judge,c.name as title_name,c.remark")->from("exa_examina a")
            ->leftJoin("exa_title c","a.title_id = c.id")
            ->leftJoin("exa_title_choose b","a.choose_id = b.id")
            ->where("a.quiz_id=:quiz_id and a.employee_id=:employee_id", array(':quiz_id'=>$this->id,':employee_id'=>$this->employee_id))->queryAll();
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

    public function retrieveData($index,$staff)
    {
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $rows = Yii::app()->db->createCommand()->select()->from("exa_quiz")
            ->where("id=:id", array(':id'=>$index))->queryAll();
        if (count($rows) > 0)
        {
            $this->employee_id = $staff;
            foreach ($rows as $row)
            {
                $this->id = $row['id'];
                $this->dis_name = $row['dis_name'];
                $this->name = $row['name'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->exa_num = $row['exa_num'];
                $this->city = $row['city'];
                $this->staff_all = $row['staff_all'];
                $this->staffList = $this->getStaffListToTestId();
                break;
            }
        }
        return true;
    }
}
