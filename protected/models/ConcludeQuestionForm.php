<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ConcludeQuestionForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
    public $title_id;
    public $choose_list=array();
    public $success_str="";
    public $title_code;
    public $title_type;
    public $name;
    public $chapter_id;
    public $chapter_name;
    public $remark;
    public $success_ratio;
    public $success_num;
    public $show_num;

    public $menu_code;
    public $menu_name;
    public $menu_id;
    public $code_pre="09";

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
            'chapter_name'=>Yii::t('study','Attribution Article'),
            'title_code'=>Yii::t('study','question code'),
            'title_type'=>Yii::t('study','question type'),
            'name'=>Yii::t('study','question name'),
            'display'=>Yii::t('study','display'),
            'remark'=>Yii::t('study','Interpretation'),
            'show_num'=>Yii::t('study','show num'),
            'success_num'=>Yii::t('study','success num'),
            'success_ratio'=>Yii::t('study','success ratio'),
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
		);
	}

	public function retrieveData($index)
	{
        $row = Yii::app()->db->createCommand()
            ->select("f.*,a.chapter_name,a.item_sum,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_title f")
            ->leftJoin("exa_chapter_class a","a.id=f.chapter_id")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('f.id=:id',array(':id'=>$index))->queryRow();
        if ($row){
            $this->id = $row['id'];
            $this->title_id = $row['id'];
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_id = $row["chapter_id"];
            $this->chapter_name = $row["chapter_name"];

            $this->title_code = $row['title_code'];
            $this->title_type = $row['title_type'];
            $this->remark = $row['remark'];
            $this->name = $row['name'];
            $this->show_num = $row['show_num'];
            $this->success_num = $row['success_num'];
            $this->success_ratio = empty($row['show_num'])?0:round(($row['success_num']/$row['show_num'])*100)."%";

            $this->paper_list = $this->setPaPerList();
            return true;
        }
	}

    //獲取試題
    private function setPaPerList(){
        $list[$this->id] = array(
            "id"=>$this->id,
            "title_type"=>$this->title_type,
            "title_code"=>$this->title_code,
            "name"=>$this->name,
            "remark"=>$this->remark,
            "chapter_id"=>$this->chapter_id,
            "is_correct"=>1,
            "choose_id"=>"",
            "list_choose"=>"",
        );
        $chooseList = Yii::app()->db->createCommand()
            ->select("id,choose_name,judge")
            ->from("exa_chapter_title_choose")
            ->where("title_id={$this->id}")
            ->order("id asc")
            ->queryAll();
        if($chooseList){
            $i=0;
            $strList = array("A","B","C","D");
            $list_choose=array();
            $choose_id=array();
            $success_str=array();
            foreach ($chooseList as $choose){
                $list_choose[] = $choose["id"];
                $this->choose_list[$choose["id"]]=$strList[$i];
                if($choose["judge"]==1){
                    $success_str[]=$strList[$i];
                    $choose_id[] = $choose["id"];
                }
                $list["{$this->id}"]["chooseList"]["{$choose["id"]}"] = $choose;
                $i++;
            }
            $this->success_str.=implode("、",$success_str);
            $list["{$this->id}"]["list_choose"] = implode(",",$list_choose);
            $list["{$this->id}"]["choose_id"] = implode(",",$choose_id);
        }
        return $list;
    }

    //参与某个试题的所有员工
    public static function showJoinStaffTableForTitleId($model){
        $title_id = $model->title_id;
        $suffix = Yii::app()->params['envSuffix'];
        $html = "<table class='table table-striped table-bordered table-hover'>";
        $html.="<thead>";
        $html.="<tr>";
        $html.="<td>".Yii::t("study","Attribution Staff")."</td>";
        $html.="<td>".Yii::t("study","employee code")."</td>";
        $html.="<td>".Yii::t("study","Attribution Test")."</td>";
        $html.="<td>".Yii::t("study","success choose")."</td>";
        $html.="<td>".Yii::t("study","staff choose")."</td>";
        $html.="</tr>";
        $html.="</thead>";
        $html.="<tbody>";
        $rows = Yii::app()->db->createCommand()
            ->select("b.name as employee_name,b.code as employee_code,g.name as markedly_name,a.is_correct,a.choose_id")
            ->from("exa_take_title a")
            ->leftJoin("hr{$suffix}.hr_employee b","b.id=a.employee_id")
            ->leftJoin("exa_take f","f.id=a.take_id")
            ->leftJoin("exa_markedly g","g.id=f.markedly_id")
            ->where('a.title_id=:id',array(':id'=>$title_id))
            ->order("b.id desc")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $choose_id = explode(",",$row["choose_id"]);
                shuffle($choose_id);
                $choose_list = array();
                if(is_array($choose_id)){
                    foreach ($choose_id as $choose){
                        $choose_list[]=key_exists($choose,$model->choose_list)?$model->choose_list[$choose]:$choose;
                    }
                }
                if($row["is_correct"]==1){
                    $html.="<tr>";
                }else{
                    $html.="<tr class='danger'>";
                }
                $html.="<td>".$row["employee_name"]."</td>";
                $html.="<td>".$row["employee_code"]."</td>";
                $html.="<td>".$row["markedly_name"]."</td>";
                $html.="<td>".$model->success_str."</td>";
                $html.="<td>".implode("、",$choose_list)."</td>";
                $html.="</tr>";
            }
        }
        $html.="</tbody>";
        $html.= "</table>";

        return $html;
    }
}
