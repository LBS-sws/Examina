<?php

class StudyClassModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $class_name;
    public $item_num;
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
            'class_name'=>Yii::t('study','class name'),
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
            array('id, menu_id, menu_code, menu_name,class_name,item_num,display,z_index','safe'),
            array('menu_id','required'),
            array('class_name','validateName','on'=>array("new")),
            array('menu_id','validateID'),
            array('id','validateDel','on'=>array("delete")),
            array('z_index', 'numerical', 'integerOnly'=>true),
        );
    }

    public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_study")
            ->where('class_id=:id',array(':id'=>$this->id))->queryRow();
        if($row){
            $message = "该分类已有文章，无法删除";
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

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_study_class")
            ->where('class_name=:name and id!=:id and menu_id=:menu_id',
                array(':name'=>$this->class_name,':id'=>$id,':menu_id'=>$this->menu_id))->queryRow();
        if($row){
            $message = "已存在相同的分类，请重新命名";
            $this->addError($attribute,$message);
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
            $rows = Yii::app()->db->createCommand()->select("id,class_name,item_num")
                ->from("exa_study_class")
                ->where("menu_id =:id and display=1",array(":id"=>$index))
                ->order("z_index asc,id desc")->queryAll();
            $this->class_list=$rows?$rows:array();
            return true;
        }
        return false;
    }

    public function retrieveClassData($menu_id){ //新增
        $menu = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where("id =:id",array(":id"=>$menu_id))->queryRow();
        if($menu){
            $this->menu_id = $menu_id;
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            return true;
        }
        return false;
    }

    public function retrieveData($index){ //修改
        $menu = Yii::app()->db->createCommand()->select("a.*,b.menu_name,b.menu_code")
            ->from("exa_study_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id",array(":id"=>$index))->queryRow();
        if($menu){
            $this->id = $index;
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->class_name = $menu["class_name"];
            $this->display = $menu["display"];
            $this->z_index = $menu["z_index"];
            $this->item_num = $menu["item_num"];
            return true;
        }
        return false;
    }

    public function echoMedia(){
        $html = "";
        if(!empty($this->class_list)){
            $i=0;
            foreach ($this->class_list as $row){
                $i++;
                $linkEdit = Yii::app()->createUrl('StudyClass/edit',array("index"=>$row["id"]));
                $linkText = Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$row["id"]));
                $html.='<div class="media">';
                $html.='<div class="media-left media-middle"><span class="num_i">'.$i.'</span></div>';
                $html.=TbHtml::link("<h4>{$row["class_name"]}<small>（共{$row["item_num"]}文章）</small></h4>",$linkText,array("class"=>"media-body media-middle"));
                $html.='<div class="media-right media-middle">';
                $html.=TbHtml::link("",$linkEdit,array("class"=>"glyphicon glyphicon-pencil"));
                $html.='</div>';
                $html.='</div>';
            }
        }else{
            $html="暂时没有文章，请与管理员联系";
        }
        return $html;
    }

    public function echoNoneDiv(){
        $html = "";
        $rows = Yii::app()->db->createCommand()->select("id,class_name,item_num")
            ->from("exa_study_class")
            ->where("menu_id =:id and display=0",array(":id"=>$this->menu_id))
            ->order("z_index asc")->queryAll();
        if($rows){
            $html = "<div class='box'><div class='box-body'>";
            $html.= "<h4>被隐藏的分类</h4>";
            $i=0;
            foreach ($rows as $row){
                $i++;
                $linkEdit = Yii::app()->createUrl('StudyClass/edit',array("index"=>$row["id"]));
                $linkText = Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$row["id"]));
                $html.='<div class="media">';
                $html.='<div class="media-left media-middle"><span class="num_i">'.$i.'</span></div>';
                $html.=TbHtml::link("<h4>{$row["class_name"]}<small>（共{$row["item_num"]}文章）</small></h4>",$linkText,array("class"=>"media-body media-middle"));
                $html.='<div class="media-right media-middle">';
                $html.=TbHtml::link("",$linkEdit,array("class"=>"glyphicon glyphicon-pencil"));
                $html.='</div>';
                $html.='</div>';
            }
            $html.= "</div></div>";
        }
        return $html;
    }

    public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                Yii::app()->db->createCommand()->insert("exa_study_class", array(
                    'menu_id'=>$this->menu_id,
                    'class_name'=>$this->class_name,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'item_num'=>0,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_study_class', array(
                    'class_name'=>$this->class_name,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_study_class', 'id=:id', array(':id'=>$this->id));
                break;
            default:
                break;
        }
    }


    //获取菜单栏的编号
    public static function getMenuCodeForStudy($id,$type){
        $code = "TE";
        switch ($type){
            case "menu_id"://菜单表id
                $row = Yii::app()->db->createCommand()->select("menu_code")
                    ->from("exa_setting")
                    ->where("id =:id",array(":id"=>$id))->queryRow();
                $code = $row?$row["menu_code"]:$code;
                break;
            case "class_id"://分类表id
                $row = Yii::app()->db->createCommand()->select("b.menu_code")
                    ->from("exa_study_class a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where("a.id=:id",array(":id"=>$id))->queryRow();
                $code = $row?$row["menu_code"]:$code;
                break;
            case "article_id"://文章表id
                $row = Yii::app()->db->createCommand()->select("b.menu_code")
                    ->from("exa_study a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where("a.id=:id",array(":id"=>$id))->queryRow();
                $code = $row?$row["menu_code"]:$code;
                break;
        }
        return $code;
    }
}
