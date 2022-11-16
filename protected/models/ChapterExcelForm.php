<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class ChapterExcelForm extends CFormModel
{
	/* User Fields */
	public $file;
	public $chapter_id;
	public $menu_id;
	public $menu_code;
	public $code_pre="02";
	public $error_list=array();
	public $start_title="";

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('file,chapter_id','safe'),
            array('chapter_id','required'),
            array('chapter_id', 'validateID'),
            array('file', 'file', 'types'=>'xlsx,xls', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
	}

    public function validateID($attribute, $params){
        $row = Yii::app()->db->createCommand()
            ->select("a.menu_id,b.menu_code,b.menu_name")
            ->from("exa_chapter_class a")
            ->leftJoin("exa_setting b","a.menu_id=b.id")
            ->where('a.id=:id',array(':id'=>$this->chapter_id))->queryRow();
        if ($row){
            $this->menu_id = $row["menu_id"];
            $this->menu_code = $row["menu_code"].$this->code_pre;
            $this->savePath();//生成文件夾（保存地址）
        }else{
            $message = "数据异常请刷新重试";
            $this->addError($attribute,$message);
        }
    }

	//批量導入物品
    public function loadGoods($arr){
	    $errNum = 0;//失敗條數
	    $successNum = 0;//成功條數
        $validateArr = $this->getList();
        foreach ($validateArr as $vaList){
            if(!in_array($vaList["name"],$arr["listHeader"])){
                Dialog::message(Yii::t('dialog','Validation Message'), $vaList["name"].Yii::t("examina"," Did not find"));
                return false;
            }
        }
        foreach ($arr["listBody"] as $list){
            $arrList = array();
            $chooseList = array();
            $continue = true;
            $this->start_title = current($list);//每行的第一個文本
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList);
                if($value['status'] == 1){
                    if($vaList["sqlName"] == "choose_name"||$vaList["sqlName"] == "judge"){
                        $chooseList[$vaList["key"]][$vaList["sqlName"]]=$value["data"];
                    }else{
                        $arrList[$vaList["sqlName"]] = $value["data"];
                    }
                }else{
                    $continue = false;
                    array_push($this->error_list,$value["error"]);
                    break;
                }
            }
            if($continue){
                $city = Yii::app()->user->city();
                $uid = Yii::app()->user->id;
                //新增
                $arrList["lcu"] = $uid;
                $arrList["city"] = $city;
                $arrList["chapter_id"] = $this->chapter_id;
                $arrList["menu_id"] = $this->menu_id;
                $arrList["title_type"] = self::titleTypeForInt($arrList["title_type"]);
                Yii::app()->db->createCommand()->insert("exa_chapter_title", $arrList);
                $insetId = Yii::app()->db->getLastInsertID();
                $code = $this->lenStr($insetId);
                Yii::app()->db->createCommand()->update('exa_chapter_title', array(
                    'title_code'=>$code
                ), 'id=:id', array(':id'=>$insetId));
                foreach ($chooseList as $key =>$choose){
                    $choose["menu_id"] = $this->menu_id;
                    $choose["chapter_id"] = $this->chapter_id;
                    $choose["title_id"] = $insetId;
                    $choose["judge"] = $choose["judge"]=="正确"?1:0;
                    if($arrList["title_type"]==2&&$key>2){//判斷題只有兩個選項
                        $choose["judge"]=0;
                        $choose["display"]=0;
                    }
                    Yii::app()->db->createCommand()->insert("exa_chapter_title_choose", $choose);
                }
                $successNum++;
            }else{
                $errNum++;
            }
        }

        $this->changeClassItemNum();
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), Yii::t('examina','Success Num：').$successNum."<br>".Yii::t('examina','Error Num：').$errNum."<br>".$error);
    }


    protected function changeClassItemNum(){
        $sum = Yii::app()->db->createCommand()->select("count(id)")->from("exa_chapter_title")
            ->where("chapter_id =:id and display=1",array(":id"=>$this->chapter_id))->queryScalar();
        Yii::app()->db->createCommand()->update('exa_chapter_class', array(
            'item_sum'=>$sum?$sum:0
        ), "id={$this->chapter_id}");
    }

    private function validateStr($value,$list){
        if(empty($value)&&$list["empty"]){
            return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
        }
        return array("status"=>1,"data"=>$value);
    }


    private function lenStr($id){
        $codeStr = strval($id);
        $code = "QI";
        for($i = 0;$i < 7-strlen($code);$i++){
            $code.="0";
        }
        $code .= $codeStr;
        return $code;
    }

    private function getList(){
        $arr = array(
            array("name"=>"试题类型","sqlName"=>"title_type","empty"=>true),
            array("name"=>"试题内容","sqlName"=>"name","empty"=>true),
            array("name"=>"讲解","sqlName"=>"remark","empty"=>false),

            array("name"=>"选项1","sqlName"=>"choose_name","key"=>1,"empty"=>true),
            array("name"=>"选项1是否正确","sqlName"=>"judge","key"=>1,"empty"=>true),
            array("name"=>"选项2","sqlName"=>"choose_name","key"=>2,"empty"=>true),
            array("name"=>"选项2是否正确","sqlName"=>"judge","key"=>2,"empty"=>true),
            array("name"=>"选项3","sqlName"=>"choose_name","key"=>3,"empty"=>false),
            array("name"=>"选项3是否正确","sqlName"=>"judge","key"=>3,"empty"=>false),
            array("name"=>"选项4","sqlName"=>"choose_name","key"=>4,"empty"=>false),
            array("name"=>"选项4是否正确","sqlName"=>"judge","key"=>4,"empty"=>false),
        );
        return $arr;
    }


    //導入的地址驗證
    private function savePath(){
        $city = Yii::app()->user->city();
        $path =Yii::app()->basePath."/../upload/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path =Yii::app()->basePath."/../upload/excel/";
        if (!file_exists($path)){
            mkdir($path);
        }
        $path.=$city."/";
        if (!file_exists($path)){
            mkdir($path);
        }
    }

    public static function titleTypeForInt($str){
        switch ($str){
            case "单选题":
                return 0;
            case "多选题":
                return 1;
            case "判断题":
                return 2;
            default:
                return 0;
        }
    }
}
