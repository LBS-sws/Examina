<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class PaperMyForm extends CFormModel
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
    public $code_pre="05";

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
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()
            ->select("a.*,b.menu_code,b.menu_name,f.name as markedly_name,f.join_must,f.exa_num,f.dis_name")
            ->from("exa_take a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->leftJoin("exa_markedly f","a.markedly_id=f.id")
            ->where("a.id=:id and a.employee_id={$this->employee_id}", array(':id'=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
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


    //獲取所有試題
    public static function getPaPerList($take_id){
        $list = array();
        $rows = Yii::app()->db->createCommand()
            ->select("f.title_id,f.choose_id,f.list_choose,f.is_correct,a.id,a.title_type,a.title_code,a.name,a.remark,a.chapter_id")
            ->from("exa_take_title f")
            ->leftJoin("exa_chapter_title a","a.id = f.title_id")
            ->where("f.take_id=:id",array(':id'=>$take_id))
            ->order("f.id asc")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $orderId = explode(",",$row['list_choose']);
                $i=1;
                $orderSql = "ORDER BY case id ";
                foreach ($orderId as $id){
                    $orderSql.=" when {$id} then {$i} ";
                    $i++;
                }
                $orderSql.="end asc";
                $list[$row["id"]] = $row;
                $chooseList = Yii::app()->db->createCommand()
                    ->select("id,choose_name,judge")
                    ->from("exa_chapter_title_choose")
                    ->where("id in ({$row['list_choose']}) {$orderSql}")
                    ->queryAll();
                if($chooseList){
                    foreach ($chooseList as $choose){
                        $list["{$row["id"]}"]["chooseList"]["{$choose["id"]}"] = $choose;
                    }
                }
            }
        }
        return $list;
    }

    public static function showSelect($row,$chooseList){
        $html = "";
        $chooseStr = array("A","B","C","D");
        if(!empty($chooseList)){
            $error_id = explode(",",$row["choose_id"]);
            $error_id = is_array($error_id)?$error_id:array($error_id);
            $i=0;
            foreach ($chooseList as $choose){
                $html.= "<div class='checkbox'>";
                //choose_id,is_correct
                $class = "";
                if($choose["judge"]==1){
                    $class.=" text-primary";
                }elseif (in_array($choose["id"],$error_id)){
                    $class.=" text-danger";
                }
                $html.="<label class='{$class}'>";
                $html.=$chooseStr[$i]."、".$choose["choose_name"];
                $html.="</label>";
                $html.="</div>";
                $i++;
            }
        }
        return $html;
    }

    public static function showPaperTitle($model){
        $className = get_class($model);
        $html = "";
        if(!empty($model->paper_list)){
            $activeId = key_exists("title_id",$_GET)?$_GET["title_id"]:current($model->paper_list)["id"];
            $i = 0;
            $count = count($model->paper_list);
            foreach ($model->paper_list as $row){
                $i++;
                $active=$activeId==$row["id"]?"active":"";
                $html.="<div class='resultDiv {$active}' data-id='{$row["id"]}'>";
                //试题id
                $html.=TbHtml::hiddenField("{$className}[title_id_list][]",$row["id"]);
                //保存选项的固定顺序（choose_id）
                $html.=TbHtml::hiddenField("{$className}[choose_id][{$row["id"]}]",$row["list_choose"]);
                $html.="<div class='resultBody'>";
                $html.="<h4 class='resultBody_t'><b>{$i}/{$count}、{$row['name']}</b></h4>";
                $html.="<div class='resultBody_b'>";
                $html.=self::showSelect($row,$row["chooseList"]);
                $html.="</div>";
                $html.="</div>";
                $html.="<div class='resultRemark show' data-success='{$row['is_correct']}'>";
                $html.="<b>".Yii::t("study","Interpretation")."：</b>";
                $html.="<span>".$row['remark']."</span>";
                $html.="</div>";
                $html.="</div>";
            }

        }
        return $html;
    }

    //答題卡
    public static function showAnswerSheet($model){
        $html = "<p class='text-center'>".Yii::t("study","answer sheet")."</p>";
        $html.= "<ul class='list-inline answer-sheet-ul' id='answerSheet'>";
        if(!empty($model->paper_list)){
            $i=0;
            foreach ($model->paper_list as $row){
                $i++;
                $active=$row["is_correct"]==1?"success":"error";
                $html.="<li data-id='{$row['id']}' class='{$active}'>{$i}<span></span>";
            }
        }
        $html.= "</ul>";
        $html.= "<div>错误：<span id='span_error'>".($model->title_sum-$model->title_num)."</span>题</div>";
        $html.= "<div>正确：<span id='span_success'>{$model->title_num}</span>题</div>";
        $html.= "<div>正确率：<span id='span_ratio'>{$model->success_ratio}</span></div>";
        return $html;
    }

}
