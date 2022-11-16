<?php

class MockChapterModel extends CFormModel
{
    public $id;
    public $menu_id;
    public $menu_name;
    public $menu_code;

    public $chapter_name;
    public $item_sum=0;
    public $random_num=1;
    public $display=1;
    public $z_index=0;

    public $class_list=array();
    protected $code_pre="02";

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'id'=>Yii::t('study','ID'),
            'chapter_name'=>Yii::t('study','chapter name'),
            'random_num'=>Yii::t('study','random num'),
            'item_sum'=>Yii::t('study','item sum'),
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
            array('id, menu_id, menu_code, menu_name,chapter_name,item_sum,random_num,display,z_index','safe'),
            array('menu_id,chapter_name,random_num','required'),
            array('menu_id','validateID'),
            array('chapter_name','validateName','on'=>array("new")),
            array('id','validateDel','on'=>array("delete")),
            array('z_index,random_num', 'numerical', 'integerOnly'=>true),
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

    public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_chapter_class")
            ->where('chapter_name=:name and id!=:id and menu_id=:menu_id',
                array(':name'=>$this->chapter_name,':id'=>$id,':menu_id'=>$this->menu_id))->queryRow();
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
            $rows = Yii::app()->db->createCommand()->select("id,chapter_name,item_sum,random_num")
                ->from("exa_chapter_class")
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
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id",array(":id"=>$index))->queryRow();
        if($menu){
            $this->id = $index;
            $this->menu_id = $menu["menu_id"];
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;
            $this->chapter_name = $menu["chapter_name"];
            $this->random_num = $menu["random_num"];
            $this->display = $menu["display"];
            $this->z_index = $menu["z_index"];
            $this->item_sum = $menu["item_sum"];
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
                //章节修改
                $linkEdit = Yii::app()->createUrl('MockChapter/edit',array("index"=>$row["id"]));
                //章节练习
                $linkText = Yii::app()->createUrl('ChapterArticle/test',array("chapter_id"=>$row["id"]));
                //章节的试题列表
                $linkList = Yii::app()->createUrl('ChapterQuestion/index',array("chapter_id"=>$row["id"]));
                $html.='<div class="media">';
                $html.='<div class="media-left media-middle"><span class="num_i mock">'.$i.'</span></div>';
                $label = "<h4>".$row["chapter_name"];
                if(Yii::app()->user->validRWFunction($this->menu_code)){
                    $label.="<small>（总共{$row["item_sum"]}题，随机{$row["random_num"]}题）</small>";
                }
                $label.="</h4>";
                $html.=TbHtml::link($label,$linkText,array("class"=>"media-body media-middle"));
                $html.='<div class="media-right media-middle">';
                $html.='<div class="mock-link-div">';
                $html.=TbHtml::link("",$linkEdit,array("class"=>"glyphicon glyphicon-pencil"));
                $html.=TbHtml::link("",$linkList,array("class"=>"fa fa-list"));
                $html.='</div>';
                $html.='</div>';
                $html.='</div>';
            }
        }else{
            $html="暂时没有试题，请与管理员联系";
        }
        return $html;
    }

    public function echoNoneDiv(){
        $html = "";
        $rows = Yii::app()->db->createCommand()->select("id,chapter_name,item_sum")
            ->from("exa_chapter_class")
            ->where("menu_id =:id and display=0",array(":id"=>$this->menu_id))
            ->order("z_index asc")->queryAll();
        if($rows){
            $html = "<div class='box'><div class='box-body'>";
            $html.= "<h4>被隐藏的章节</h4>";
            $i=0;
            foreach ($rows as $row){
                $i++;
                $linkEdit = Yii::app()->createUrl('MockChapter/edit',array("index"=>$row["id"]));
                $html.='<div class="media">';
                $html.='<div class="media-left media-middle"><span class="num_i mock">'.$i.'</span></div>';
                $html.=TbHtml::link("<h4>{$row["chapter_name"]}</h4>",$linkEdit,array("class"=>"media-body media-middle"));
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
                Yii::app()->db->createCommand()->insert("exa_chapter_class", array(
                    'menu_id'=>$this->menu_id,
                    'chapter_name'=>$this->chapter_name,
                    'random_num'=>$this->random_num,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'item_sum'=>0,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_chapter_class', array(
                    'chapter_name'=>$this->chapter_name,
                    'random_num'=>$this->random_num,
                    'display'=>$this->display,
                    'z_index'=>$this->z_index,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_chapter_class', 'id=:id', array(':id'=>$this->id));
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
            case "chapter_id"://分类表id
                $row = Yii::app()->db->createCommand()->select("b.menu_code")
                    ->from("exa_chapter_class a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where("a.id=:id",array(":id"=>$id))->queryRow();
                $code = $row?$row["menu_code"]:$code;
                break;
            case "article_id"://文章表id
                $row = Yii::app()->db->createCommand()->select("b.menu_code")
                    ->from("exa_chapter a")
                    ->leftJoin("exa_setting b","a.menu_id=b.id")
                    ->where("a.id=:id",array(":id"=>$id))->queryRow();
                $code = $row?$row["menu_code"]:$code;
                break;
        }
        return $code;
    }
}
