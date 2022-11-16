<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ChapterQuestionForm extends CFormModel
{
    public $menu_id;
    public $menu_code;
    public $menu_name;
    public $code_pre="02";
	/* User Fields */
	public $id = 0;
	public $title_code;
	public $title_type;
	public $name;
	public $chapter_id;
    public $chapter_name;
	public $remark;
    public $city;
	public $display=1;
	public $item_sum=0;
	public $show_num=0;
	public $answerList=array(
	    array("id"=>"","choose"=>"","judge"=>1,"display"=>1),
	    array("id"=>"","choose"=>"","judge"=>0,"display"=>1),
	    array("id"=>"","choose"=>"","judge"=>0,"display"=>1),
	    array("id"=>"","choose"=>"","judge"=>0,"display"=>1)
    );
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
            'title_code'=>Yii::t('study','question code'),
            'title_type'=>Yii::t('study','question type'),
            'name'=>Yii::t('study','question name'),
            'display'=>Yii::t('study','display'),
            'remark'=>Yii::t('study','Interpretation'),

            'answer'=>Yii::t('study','correct answer'),
            'answer_a'=>Yii::t('study','wrong answer A'),
            'answer_b'=>Yii::t('study','wrong answer B'),
            'answer_c'=>Yii::t('study','wrong answer C'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			//array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, title_code, title_type, name, remark, chapter_id, answerList,display','safe'),
			array('name','required'),
			array('chapter_id','required'),
            array('chapter_id', 'numerical', 'integerOnly'=>true),
			array('answerList','required'),
            array('name','validateName',"on"=>array("edit")),
            array('chapter_id','validateChapter'),
			array('answerList','validateAnswer'),
		);
	}

    public function validateChapter($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("a.chapter_name,a.item_sum,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$this->chapter_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_name = $row["chapter_name"];
            $this->item_sum = $row["item_sum"];
        }else{
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }
    }

	public function validateName($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("*")->from("exa_chapter_title")
            ->where('id=:id',array(':id'=>$this->id))->queryRow();
        if($row){
            $this->show_num = $row["show_num"];
            if(!empty($row["show_num"])){
                $this->title_type = $row["title_type"];
            }
        }else{
            $message = "数据异常，请刷新重试";
            $this->addError($attribute,$message);
        }
    }

	public function validateAnswer($attribute, $params){
	    if(!empty($this->answerList)&&is_array($this->answerList)){
	        $success=0;//正确数量
            $list = array();
            $i=0;
	        foreach ($this->answerList as $row){
                if($this->title_type==2&&$i>1){//单选题只有两个选项
	                $row["display"]=0;
	                $row["judge"]=0;
	                $row["choose"]=null;
                }else{
                    $row["display"]=1;
                }
                if($row["judge"]==1){
                    $success++;
                }
                $list[]=$row;
	            $i++;
            }
            if($success==0){
                $message = "请至少选择一个正确答案";
                $this->addError($attribute,$message);
            }elseif ($success>1&&in_array($this->title_type,array(0,2))){
                $message = "单选题和判断题只能有一个正确答案";
                $this->addError($attribute,$message);
            }
            $this->answerList = $list;
        }else{
            $message = "数据异常，请刷新重试";
            $this->addError($attribute,$message);
        }
    }

	public function retrieveChapterData($chapter_id){
        $row = Yii::app()->db->createCommand()
            ->select("a.chapter_name,a.item_sum,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$chapter_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_id = $chapter_id;
            $this->chapter_name = $row["chapter_name"];
            $this->item_sum = $row["item_sum"];
            return true;
        }
        return false;
    }

	public function retrieveData($index){
        $row = Yii::app()->db->createCommand()
            ->select("f.*,a.chapter_name,a.item_sum,a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_title f")
            ->leftJoin("exa_chapter_class a","a.id=f.chapter_id")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('f.id=:id',array(':id'=>$index))->queryRow();
		if ($row){
            $this->id = $row['id'];
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->chapter_id = $row["chapter_id"];
            $this->chapter_name = $row["chapter_name"];
            $this->item_sum = $row["item_sum"];
            $this->show_num = $row["show_num"];

            $this->title_code = $row['title_code'];
            $this->title_type = $row['title_type'];
            $this->remark = $row['remark'];
            $this->name = $row['name'];
            $this->display = $row['display'];
            $this->answerList = $this->getChooseToId();
            return true;
		}
		return false;
	}
    //獲取問題的選項
    public function getChooseToId(){
        $rows = Yii::app()->db->createCommand()
            ->select("id,title_id,choose_name as choose,judge,display")
            ->from("exa_chapter_title_choose")
            ->where("title_id=:title_id", array(':title_id'=>$this->id))
            ->order('id ASC')->queryAll();
        if($rows){
            return $rows;
        }
    }
	
	public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                Yii::app()->db->createCommand()->insert("exa_chapter_title", array(
                    'menu_id'=>$this->menu_id,
                    'chapter_id'=>$this->chapter_id,
                    'title_type'=>$this->title_type,
                    'name'=>$this->name,
                    'remark'=>$this->remark,
                    'display'=>$this->display,
                    'city'=>Yii::app()->user->city(),
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                $this->lenStr();
                Yii::app()->db->createCommand()->update('exa_chapter_title', array(
                    'title_code'=>$this->title_code
                ), "id={$this->id}");
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_chapter_title', array(
                    'title_type'=>$this->title_type,
                    'name'=>$this->name,
                    'remark'=>$this->remark,
                    'display'=>$this->display,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_chapter_title', 'id=:id', array(':id'=>$this->id));
                Yii::app()->db->createCommand()->delete('exa_chapter_title_choose', 'title_id=:id', array(':id'=>$this->id));
                break;
            default:
                break;
        }
        $this->setAnswer();
        $this->changeClassItemNum();
	}

    private function setAnswer(){
        if ($this->scenario=='new'){
            foreach ($this->answerList as $answer){
                Yii::app()->db->createCommand()->insert('exa_chapter_title_choose', array(
                    'menu_id'=>$this->menu_id,
                    'chapter_id'=>$this->chapter_id,
                    'title_id'=>$this->id,
                    'choose_name'=>$answer["choose"],
                    'display'=>$answer["display"],
                    'judge'=>$answer["judge"]
                ));
            }
            $this->scenario = "edit";
        }else{
            foreach ($this->answerList as $answer){
                $updateArr["choose_name"]=$answer["choose"];
                if(empty($this->show_num)){ //如果试题没出现在测试单，允许修改
                    $updateArr["display"]=$answer["display"];
                    $updateArr["judge"]=$answer["judge"];
                }
                Yii::app()->db->createCommand()->update('exa_chapter_title_choose', $updateArr,
                    'id=:id and title_id=:title_id', array(':id'=>$answer["id"],':title_id'=>$this->id));
            }
        }
    }

    private function lenStr(){
        $code = strval($this->id);
        $this->title_code = "CQ";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->title_code.="0";
        }
        $this->title_code .= $code;
    }

    protected function changeClassItemNum(){
        $sum = Yii::app()->db->createCommand()->select("count(id)")->from("exa_chapter_title")
            ->where("chapter_id =:id and display=1",array(":id"=>$this->chapter_id))->queryScalar();
        if($sum!=$this->item_sum){
            Yii::app()->db->createCommand()->update('exa_chapter_class', array(
                'item_sum'=>$sum?$sum:0
            ), "id={$this->chapter_id}");
        }
    }
}
