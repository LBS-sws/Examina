<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UploadExcelForm extends CFormModel
{
	/* User Fields */
	public $file;
	public $error_list=array();
	public $start_title="";

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('file','safe'),
            array('file', 'file', 'types'=>'xlsx,xls', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
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
                    if($vaList["sqlName"] == "choose_name"){
                        $chooseList[]=array("choose_name"=>$value["data"],"judge"=>$vaList["judge"]);
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
                Yii::app()->db->createCommand()->insert("exa_title", $arrList);
                $insetId = Yii::app()->db->getLastInsertID();
                $code = $this->lenStr($insetId);
                Yii::app()->db->createCommand()->update('exa_title', array(
                    'title_code'=>$code
                ), 'id=:id', array(':id'=>$insetId));
                foreach ($chooseList as $choose){
                    $choose["title_id"] = $insetId;
                    Yii::app()->db->createCommand()->insert("exa_title_choose", $choose);
                }
                $successNum++;
            }else{
                $errNum++;
            }
        }
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Success Num：').$successNum."<br>".Yii::t('procurement','Error Num：').$errNum."<br>".$error);
    }

    private function validateStr($value,$list){
        if(empty($value)&&$list["empty"]){
            return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
        }
        if($list["name"] == "试题内容"){
            $rows = Yii::app()->db->createCommand()->select("id")->from("exa_title")
                ->where('name=:name',array(':name'=>$value))->queryRow();
            if($rows){
                return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."已存在");
            }
        }
        if($list["name"] == "类别名称"){
            $rows = Yii::app()->db->createCommand()->select("id")->from("exa_type")
                ->where('name=:name',array(':name'=>$value))->queryRow();
            if($rows){
                $value = $rows["id"];
            }else{
                $value = '';
            }
        }
        return array("status"=>1,"data"=>$value);
    }


    private function lenStr($id){
        $codeStr = strval($id);
        $code = "QI";
        for($i = 0;$i < 4-strlen($code);$i++){
            $code.="0";
        }
        $code .= $codeStr;
        return $code;
    }

    private function getList(){
        $arr = array(
            array("name"=>"试题内容","sqlName"=>"name","empty"=>true),
            array("name"=>"正确答案","sqlName"=>"choose_name","judge"=>"1","empty"=>true),
            array("name"=>"错误答案A","sqlName"=>"choose_name","judge"=>"0","empty"=>true),
            array("name"=>"错误答案B","sqlName"=>"choose_name","judge"=>"0","empty"=>true),
            array("name"=>"错误答案C","sqlName"=>"choose_name","judge"=>"0","empty"=>true),
            array("name"=>"备注","sqlName"=>"remark","empty"=>false),
            array("name"=>"类别名称","sqlName"=>"type_id","empty"=>false),
        );
        return $arr;
    }
}
