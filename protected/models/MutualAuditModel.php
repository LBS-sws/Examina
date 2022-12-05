<?php

class MutualAuditModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $employee_id;
    public $employee_code;
    public $employee_name;

    public $mutual_state=0;
    public $mutual_date;
    public $mutual_body="";
    public $end_body="";
    public $z_index;
    public $display;
    public $reject_remark;

    protected $code_pre="10";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
            'employee_name'=>Yii::t('study','employee name'),
            'mutual_state'=>Yii::t('study','state'),
            'mutual_body'=>Yii::t('study','mutual body'),
            'end_body'=>Yii::t('study','end body').Yii::t('study','(update)'),
            'mutual_date'=>Yii::t('study','mutual date'),
            'z_index'=>Yii::t('study','z_index'),
            'display'=>Yii::t('study','display'),
            'reject_remark'=>Yii::t('study','reject remark'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, end_body, mutual_date, reject_remark, display, z_index','safe'),
            array('menu_id,end_body','required'),
            array('menu_id','validateID'),
            array('z_index', 'numerical', 'integerOnly'=>true),
            array('reject_remark','required','on'=>array("reject")),
        );
    }

    public function validateID($attribute, $params){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,f.code,f.name,b.menu_name,b.menu_code")
            ->from("exa_mutual a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->leftJoin("hr{$suffix}.hr_employee f","a.employee_id=f.id")
            ->where("a.id =:id and a.mutual_state in (1,2)",
                array(":id"=>$this->id))->queryRow();
        if(!$row){
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }else{
            $this->menu_id = $row["menu_id"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->menu_name = $row["menu_name"];
            $this->employee_id = $row["employee_id"];
            $this->employee_code = $row["code"];
            $this->employee_name = $row["name"];
            $this->mutual_body = $row["mutual_body"];
        }
    }

    public function retrieveMenuData($index){ //菜单验证
        $suffix = Yii::app()->params['envSuffix'];
        //$city = Yii::app()->user->city();
        $menu = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where("id =:id",array(":id"=>$index))->queryRow();
        if($menu){
            $this->menu_id = $index;
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            return true;
        }
        return false;
    }

    public function retrieveData($index){ //查看
        $suffix = Yii::app()->params['envSuffix'];
        //$city = Yii::app()->user->city();
        $row = Yii::app()->db->createCommand()->select("a.*,f.code,f.name,b.menu_name,b.menu_code")
            ->from("exa_mutual a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->leftJoin("hr{$suffix}.hr_employee f","a.employee_id=f.id")
            ->where("a.id =:id and a.mutual_state in (1,2)",
                array(":id"=>$index))->queryRow();
        if($row){
            $this->id = $row["id"];
            $this->employee_id = $row["employee_id"];
            $this->employee_code = $row["code"];
            $this->employee_name = $row["name"];
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;

            $this->mutual_date = CGeneral::toDate($row["mutual_date"]);
            $this->mutual_body = $row["mutual_body"];
            $this->mutual_state = $row["mutual_state"];
            $this->end_body = $row["end_body"];
            $this->z_index = $row["z_index"];
            $this->display = $row["display"];
            return true;
        }
        return false;
    }

    public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "edit":
                Yii::app()->db->createCommand()->update('exa_mutual', array(
                    'mutual_date'=>$this->mutual_date,
                    'end_body'=>$this->end_body,
                    'mutual_state'=>$this->mutual_state,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "reject":
                Yii::app()->db->createCommand()->update('exa_mutual', array(
                    'reject_remark'=>$this->reject_remark,
                    'mutual_state'=>3,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            default:
                break;
        }
    }

    public function auditAll($list){
        $idList = array();
        if (!empty($list)){
            foreach ($list as $id=>$value){
                if(is_numeric($id)){
                    $idList[]=$id;
                }
            }
        }
        if(!empty($idList)){
            $uid = Yii::app()->user->id;
            $idList = implode(",",$idList);
            Yii::app()->db->createCommand()->update('exa_mutual', array(
                'mutual_state'=>2,
                'luu'=>$uid,
            ), "id in ({$idList}) and mutual_state=1 and menu_id=:menu_id",array(":menu_id"=>$this->menu_id));
        }
    }
}
