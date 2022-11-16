<?php

class MutualModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    protected $code_pre="03";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id','safe'),
            array('menu_id','required'),
            array('menu_id','validateID'),
            array('id','validateDel','on'=>array("delete")),
            //array('z_index,random_num', 'numerical', 'integerOnly'=>true),
        );
    }

    public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_chapter")
            ->where('chapter_id=:id',array(':id'=>$this->id))->queryRow();
        if($row){
            $message = "该分类已有试题，无法删除";
            $this->addError($attribute,$message);
        }
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where('id=:id',array(':id'=>$this->menu_id))->queryRow();
        if(!$row){
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }else{
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->menu_name = $row["menu_name"];
        }
    }

    public function retrieveAll($index){ //总页显示
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

}
