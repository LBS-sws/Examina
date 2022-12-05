<?php

class MutualModel extends CFormModel
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

    public $maxPage;
    public $mutual_list=array();

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
            'employee_name'=>Yii::t('study','employee name'),
            'mutual_state'=>Yii::t('study','state'),
            'mutual_body'=>Yii::t('study','mutual body'),
            'end_body'=>Yii::t('study','end body').Yii::t('study','(update)'),
            'mutual_date'=>Yii::t('study','mutual date'),
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
            array('id, menu_id, mutual_body, mutual_date, reject_remark','safe'),
            array('menu_id,mutual_body','required'),
            array('menu_id','validateID'),
            array('id','validateDel','on'=>array("delete")),
            //array('z_index,random_num', 'numerical', 'integerOnly'=>true),
        );
    }

    public function validateDel($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("exa_mutual")
            ->where('id=:id and mutual_state in (0,3) and employee_id=:employee_id',
                array(':id'=>$this->id,':employee_id'=>$this->employee_id))->queryRow();
        if(!$row){//没找到
            $message = "学习互助不存在，无法删除";
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

    public function retrieveMenuData($index){ //新增
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
        $row = Yii::app()->db->createCommand()->select("a.*,b.menu_name,b.menu_code")
            ->from("exa_mutual a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where("a.id =:id and a.employee_id=:employee_id",
                array(":id"=>$index,":employee_id"=>$this->employee_id))->queryRow();
        if($row){
            $this->id = $row["id"];
            $this->menu_id = $row["menu_id"];
            $this->menu_name = $row["menu_name"];
            $this->menu_code = $row["menu_code"].$this->code_pre;

            $this->mutual_date = $row["mutual_date"];
            $this->mutual_body = $row["mutual_body"];
            $this->mutual_state = $row["mutual_state"];
            $this->end_body = $row["end_body"];
            $this->z_index = $row["z_index"];
            $this->display = $row["display"];
            $this->reject_remark = $row["reject_remark"];
            return true;
        }
        return false;
    }

    public function retrieveAll($index){ //总页显示
        //$city = Yii::app()->user->city();
        $menu = Yii::app()->db->createCommand()->select("menu_name,menu_code")
            ->from("exa_setting")
            ->where("id =:id",array(":id"=>$index))->queryRow();
        if($menu){
            $this->menu_id = $index;
            $this->menu_name = $menu["menu_name"];
            $this->menu_code = $menu["menu_code"].$this->code_pre;

            $count = Yii::app()->db->createCommand()
                ->select("count(a.id)")
                ->from("exa_mutual a")
                ->where("a.menu_id=:menu_id and a.mutual_state=2 and a.display=1",array(":menu_id"=>$this->menu_id))
                ->queryScalar();
            $this->maxPage = $count?ceil($count/25):1;
            $this->mutual_list=array();
            return true;
        }
        return false;
    }

    public function getMutualHtml($pageNum=1){
        $suffix = Yii::app()->params['envSuffix'];

        $noOfItem = 25;
        $pageNum = is_numeric($pageNum) ? intval($pageNum):0;
        $pageNum = $pageNum<1 ? 1:$pageNum;
        $offset = ($pageNum-1) * $noOfItem;
        $rows = Yii::app()->db->createCommand()
            ->select("a.id,a.end_body,a.mutual_date,b.code,b.name")
            ->from("exa_mutual a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->where("a.menu_id=:menu_id and a.mutual_state=2 and a.display=1",array(":menu_id"=>$this->menu_id))
            ->order("a.z_index asc,a.mutual_date desc")->limit($noOfItem,$offset)->queryAll();


        $this->mutual_list=$rows?$rows:array();
        return $this->echoMedia();
    }


    public function echoMedia(){
        $html = "";
        if(!empty($this->mutual_list)){
            $i=0;
            foreach ($this->mutual_list as $row){
                $i++;
                $conText = htmlspecialchars($row["end_body"]);
                $conText = str_replace("\r\n","<br/>",$conText);
                $linkEdit = Yii::app()->createUrl('mutualAudit/edit',array("index"=>$row["id"]));
                $html.="<div class=\"fall-div\" data-href=\"{$linkEdit}\">";
                $html.='<blockquote>';
                //$html.='<p>'.htmlentities($row["end_body"]).'</p>';
                //$html.='<pre>'.htmlspecialchars($row["end_body"]).'</pre>';
                //$html.='<div>'.htmlspecialchars($row["end_body"]).'</div>';
                $html.='<p class="text-break">'.$conText.'</p>';
                $html.='<footer>'.$row["name"]." (".$row["code"].")";
                $html.='<cite class="pull-right">'.CGeneral::toDate($row["mutual_date"]).'</cite>';
                $html.='</footer>';
                $html.='</blockquote>';
                $html.='</div>';
            }
        }
        return $html;
    }

    public function saveData(){
        $uid = Yii::app()->user->id;
        switch ($this->getScenario()){
            case "new":
                Yii::app()->db->createCommand()->insert("exa_mutual", array(
                    'menu_id'=>$this->menu_id,
                    'employee_id'=>$this->employee_id,
                    'mutual_date'=>date("Y-m-d"),
                    'mutual_body'=>$this->mutual_body,
                    'end_body'=>$this->mutual_body,
                    'mutual_state'=>$this->mutual_state,
                    'display'=>1,
                    'lcu'=>$uid,
                ));
                $this->id = Yii::app()->db->getLastInsertID();
                break;
            case "edit":
                Yii::app()->db->createCommand()->update('exa_mutual', array(
                    'mutual_date'=>date("Y-m-d"),
                    'mutual_body'=>$this->mutual_body,
                    'end_body'=>$this->mutual_body,
                    'mutual_state'=>$this->mutual_state,
                    'reject_remark'=>"",
                    'display'=>1,
                    'luu'=>$uid,
                ), "id={$this->id}");
                break;
            case "delete":
                Yii::app()->db->createCommand()->delete('exa_mutual', 'id=:id', array(':id'=>$this->id));
                break;
            default:
                break;
        }
    }
}
