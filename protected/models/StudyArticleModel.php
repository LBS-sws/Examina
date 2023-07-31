<?php

class StudyArticleModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $class_id;
    public $class_name;
    public $item_num;


    public $study_title;
    public $study_img;
    public $study_subtitle;
    public $study_body;
    public $study_body_min;
    public $study_date;
    public $display=1;
    public $z_index=0;

    public $class_list=array();
    protected $code_pre="01";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
            'study_title'=>Yii::t('study','Article Name'),
            'study_img'=>Yii::t('study','Article Image'),
            'study_subtitle'=>Yii::t('study','Article Subtitle'),
            'study_body'=>Yii::t('study','Article Body (pc)'),
            'study_body_min'=>Yii::t('study','Article Body (min)'),
            'study_date'=>Yii::t('study','Article Date'),
            'display'=>Yii::t('study','display'),
            'z_index'=>Yii::t('study','z_index'),
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, menu_id, class_id, study_body_min,study_title,study_subtitle,study_body,study_date,study_img,display,z_index','safe'),
            array('class_id,study_title,study_subtitle,study_body,study_date','required'),
            array('class_id','validateID'),
            array('study_title','validateName','on'=>array("edit","new")),
            array('z_index', 'numerical', 'integerOnly'=>true),
        );
    }

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("a.class_name,a.item_num,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_study_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$this->class_id))->queryRow();
        if(!$row){
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }else{
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->class_name = $row["class_name"];
            $this->item_num = $row["item_num"];
        }
    }

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_study")
            ->where('study_title=:name and id!=:id and class_id=:class_id',
                array(':name'=>$this->study_title,':id'=>$id,':class_id'=>$this->class_id))->queryRow();
        if($row){
            $message = "已存在相同的文章标题，请重新命名";
            $this->addError($attribute,$message);
        }
    }

    public function retrieveClassData($class_id){ //新增
        $menu = Yii::app()->db->createCommand()->select("a.*,b.menu_name,b.menu_code")
            ->from("exa_study_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id",array(":id"=>$class_id))->queryRow();
        if($menu){
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->class_id = $class_id;
            $this->class_name = $menu["class_name"];
            return true;
        }
        return false;
    }

    public function retrieveData($index){ //修改
        $menu = Yii::app()->db->createCommand()->select("f.*,a.item_num,a.class_name,b.menu_name,b.menu_code")
            ->from("exa_study f")
            ->leftJoin("exa_study_class a","a.id=f.class_id")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("f.id =:id",array(":id"=>$index))->queryRow();
        if($menu){
            $this->id = $index;
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->class_id = $menu["class_id"];
            $this->class_name = $menu["class_name"];
            $this->study_title = $menu["study_title"];
            $this->study_img = $menu["study_img"];
            $this->study_subtitle = $menu["study_subtitle"];
            $this->study_body = $menu["study_body"];
            $this->study_body_min = $menu["study_body_min"];
            $this->study_date = empty($menu["study_date"])?"":CGeneral::toDate($menu["study_date"]);
            $this->display = $menu["display"];
            $this->z_index = $menu["z_index"];
            $this->item_num = $menu["item_num"];
            return true;
        }
        return false;
    }

    public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                Yii::app()->db->createCommand()->insert("exa_study", array(
                    'menu_id'=>$this->menu_id,
                    'class_id'=>$this->class_id,
                    'study_title'=>$this->study_title,
                    'study_img'=>$this->study_img,
                    'study_subtitle'=>$this->study_subtitle,
                    'study_body'=>$this->study_body,
                    'study_body_min'=>$this->study_body_min,
                    'study_date'=>empty($this->study_date)?null:$this->study_date,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                $this->resetImageFileName();//修改新增时的图片名称
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_study', array(
                    'study_title'=>$this->study_title,
                    'study_img'=>$this->study_img,
                    'study_subtitle'=>$this->study_subtitle,
                    'study_body'=>$this->study_body,
                    'study_body_min'=>$this->study_body_min,
                    'study_date'=>empty($this->study_date)?null:$this->study_date,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_study', 'id=:id', array(':id'=>$this->id));
                break;
            default:
                break;
        }
        $this->changeClassItemNum();
    }

    protected function resetImageFileName(){
        $path ="upload/images/study_".$this->class_id."/article_user_".Yii::app()->user->id."_0.".$this->study_img;
        $newName ="upload/images/study_".$this->class_id."/article_".$this->id.".".$this->study_img;
        if (file_exists($newName)){
            unlink($newName);
        }
        if (file_exists($path)){
            rename($path,$newName);
        }
    }

    protected function changeClassItemNum(){
        $sum = Yii::app()->db->createCommand()->select("count(id)")->from("exa_study")
            ->where("class_id =:id and display=1",array(":id"=>$this->class_id))->queryScalar();
        if($sum!=$this->item_num){
            Yii::app()->db->createCommand()->update('exa_study_class', array(
                'item_num'=>$sum?$sum:0
            ), "id={$this->class_id}");
        }
    }

    public function saveLinkHits(){
        $uid = Yii::app()->user->id;
        $menu_id = key_exists("menu_id",$_POST)?$_POST["menu_id"]:0;
        $study_id = key_exists("study_id",$_POST)?$_POST["study_id"]:0;
        $link_url = key_exists("link_url",$_POST)?$_POST["link_url"]:'';
        $employee_id = self::getEmployeeId();
        if($employee_id){
            Yii::app()->db->createCommand()->insert("exa_link_hits", array(
                'menu_id'=>$menu_id,
                'study_id'=>$study_id,
                'link_url'=>$link_url,
                'employee_id'=>$employee_id,
                'hit_type'=>1,
                'hit_date'=>date("Y/m/d H:i:s"),
                'lcu'=>$uid,
            ));
        }
    }

    public static function getEmployeeId(){
        $uid = Yii::app()->user->id;
        $suffix = Yii::app()->params['envSuffix'];
        $rs = Yii::app()->db->createCommand()->select("b.id,b.code,b.name")
            ->from("hr$suffix.hr_binding a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where("a.user_id ='$uid'")->queryRow();
        if($rs){
            return $rs["id"];
        }else{
            return false;
        }
    }
}
