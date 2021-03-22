<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class FlowTitleForm extends CFormModel
{
	/* User Fields */
	public $id = 0;
	public $flow_code;
	public $flow_title;
	static private $title=array(
	    'code1_1'=>array(
	        'function_id'=>'TP01',
	        'urlBack'=>'/enterprise/index',
	        'title'=>'第一步：初步了解企业情况',
            'contract'=>"1、入职前, 必须先看清洁丶灭虫服务视频，观察新人是否能够接受工作的模式<br><br>（尤其是男的学清洁怕无面子, 女的学灭虫惧怕老鼠蟑螂）",
        ),'code1_2'=>array(
            'function_id'=>'TP01',
            'urlBack'=>'enterprise/index',
	        'title'=>'第一步：初步了解企业情况',
            'contract'=>"2、观看迎新PPT之史伟莎迎新会 (第1章) –公司简介"
        ),
	    'code2_1'=>array(
            'function_id'=>'TP02',
            'urlBack'=>'/practice/index',
	        'title'=>'第二步：服务实际操作学习',
            'contract'=>"(建议第2天，具体时间地方视情况决定)<br>1、安排导师带新人到客户处实地操作，学习第一天建议以观察和听为主，新同事需记录服务的步骤、工作中注意事项、特殊情况的处理方法，第二天亲自动手做服务（或上午以观察和听为主，下午亲自做）具体安排地区决定<br>2、导师必须观察学员的学习能力和态度，尽快向公司回馈是否继续安排培训。"
        ),
	    'code3_1'=>array(
            'function_id'=>'TP03',
            'urlBack'=>'/Theory/index',
	        'title'=>'第三步：理论知识学习',
            'contract'=>"(建议第3 - 4天，具体时间地方视情况决定)<br>1、根据员工服务属性（IA、IB、综合）认识清洁或灭虫的物料和工具（物料名称、用途、配比、应注意事项，建议系统观看图片后，再拿出实物进行讲解）",

        ),
        'code3_2'=>array(
            'function_id'=>'TP03',
            'urlBack'=>'/Theory/index',
            'title'=>'第三步：理论知识学习',
            'contract'=>"2、认识各种机器名称,零件,需掌握使用和调较方法, 懂得安装及维修的方法（建议系统上观看视频后，再拿出实物进行讲解）",
        ),
        'code4_1'=>array(
            'function_id'=>'TP04',
            'urlBack'=>'/answer/index',
            'title'=>'第四步：沟通对答学习',
            'contract'=>"(建议第5 - 6天，具体时间地方视情况决定)<br>当客户看到新人到店服务时，可能会出现各种提问，因此新同事需要掌握各种沟通对答知识。例：当客户看到服务同事换人了会提出的问题、工作前应该怎么咨询客户近況、顶单时客户提问、客户投诉时应如何回复等情况观看图片的形式，新同事自行点击左右滑动按键进行阅读",
        ),
        'code5_1'=>array(
            'function_id'=>'TP05',
            'urlBack'=>'/examTheory/index',
            'title'=>'考试（理论+实操）',
            'contract'=>"(建议第7 - 8天，具体时间地方视情况决定)<br>1ˎ经过一段时间的培训，导师需要回馈学员学习进度，同事安排考核。\r\n当天师徒两人一起回公司考核清洁服务及服务前、后的沟通话术。<br>2ˎ 队长/组长负责现场考核灭虫服务实操及评分表，于公司完成考核理论试卷",
        ),
        'code6_1'=>array(
            'function_id'=>'TP06',
            'urlBack'=>'/examPrevious/index',
            'title'=>'系统操作培训（独立做单前）',
            'contract'=>"1、LBS系统及新U系统的操作:手机做单流程及工作报告的填写等<br>2、工作对接内容、会接触到哪些文件、表格、单据、需怎么填写、怎么交回公司、特殊客户特殊要求、注意事项要记录",
        ),
        'info3_1'=>array(
            'function_id'=>'TP03',
            'urlBack'=>'/Theory/index',
            'title'=>'第三步：理论知识学习 - 清洁类',
            'contract'=>"",
        ),
        'info3_2'=>array(
            'function_id'=>'TP03',
            'urlBack'=>'/Theory/index',
            'title'=>'第三步：理论知识学习 - 灭虫类',
            'contract'=>"",
        )
    );

	public $flow_photo;//圖片
	public $flow_name;//圖片說明
	public $z_index;//圖片層級



    public $no_of_attm = array(
        'flowth'=>0
    );
    public $docType = 'FLOWTH';
    public $docMasterId = array(
        'flowth'=>0
    );
    public $files;
    public $removeFileId = array(
        'flowth'=>0
    );
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'id'=>Yii::t('examina','ID'),
            'flow_code'=>Yii::t('examina','flow code'),
            'flow_title'=>Yii::t('examina','flow name'),
            'flow_photo'=>Yii::t('examina','Images'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            //array('id, position, leave_reason, remarks, email, staff_type, leader','safe'),
            array('id, flow_code, flow_title, flow_photo, flow_name, z_index','safe'),
            array('flow_code','required'),
            array('flow_title','required','on'=>array('edit')),
            array('flow_code','validateName','on'=>array('edit')),
            array('flow_photo', 'file', 'types'=>'png,jpg,jpe,jpeg,gif', 'allowEmpty'=>false, 'maxFiles'=>1,'on'=>array('photo')),

            array('files, removeFileId, docMasterId, no_of_attm','safe')
        );
	}

	public function validateName($attribute, $params){
	    if(!empty($this->flow_code)){
            if(key_exists($this->flow_code,self::$title)){
                $row = Yii::app()->db->createCommand()->select("id")->from("exa_flow_title")
                    ->where('flow_code=:flow_code',array(':flow_code'=>$this->flow_code))->queryRow();
                if($row){
                    $this->id = $row["id"];
                }else{
                    $this->setScenario("new");
                }
            }else{
                $message = "異常參數，請與管理員聯繫";
                $this->addError($attribute,$message);
            }
        }
    }

    //刪除验证
	public function validateDelete(){
        return false;
    }

    public function setFlowCode($flow_code){
        if(key_exists($flow_code,self::$title)){
            $this->flow_code = $flow_code;
        }else{
            $this->flow_code = "code1_1";
        }
    }

	public function getFlowBackUrl($flow_code=""){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        if(key_exists($flow_code,self::$title)){
            return self::$title[$flow_code]["urlBack"];
        }else{
            return $flow_code;
        }
	}

	public function getFlowName($flow_code=""){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        if(key_exists($flow_code,self::$title)){
            return self::$title[$flow_code]["title"];
        }else{
            return $flow_code;
        }
	}

	public function getFunctionId($flow_code=""){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        if(key_exists($flow_code,self::$title)){
            return self::$title[$flow_code]["function_id"];
        }else{
            return $flow_code;
        }
	}

    public function getFlowTitle($flow_code="",$bool=true){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        $flow_title = "";
        if(key_exists($flow_code,self::$title)){
            $row = Yii::app()->db->createCommand()->select("flow_title")->from("exa_flow_title")
                ->where("flow_code=:flow_code", array(':flow_code'=>$flow_code))->queryRow();
            if($row&&!empty($row["flow_title"])){
                $flow_title = $row["flow_title"];
            }else{
                $flow_title = self::$title[$flow_code]["contract"];
            }
        }
        if($bool){
            return $flow_title;
            //return str_replace("\r\n","<br/>",$flow_title);
        }else{
            return $flow_title;
        }
    }

    public function getFlowPhoto($flow_code=""){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        $html = "";
        if(key_exists($flow_code,self::$title)){
            $rows = Yii::app()->db->createCommand()->select("id,flow_name,flow_photo")->from("exa_flow_photo")
                ->where("flow_code=:flow_code", array(':flow_code'=>$flow_code))->order("z_index asc,id asc")->queryAll();
            if($rows){
                $html="<ul class='viewerUl hide' data-code='$flow_code'>";
                foreach ($rows as $row){
                    $imgSrc = Yii::app()->createUrl('flowTitle/printImage',array('id'=>$row["id"]));
                    $html.="<li>";
                    $html.=TbHtml::image($imgSrc,$row["flow_name"],array("data-original"=>$imgSrc));
                    $html.="</li>";
                }
                $html.="</ul>";
            }
        }
        return $html;
    }

	public function getUpdateLink($flow_code="",$bool=false){
        $flow_code = empty($flow_code)?$this->flow_code:$flow_code;
        $html = "";
	    if($bool&&key_exists($flow_code,self::$title)){
            $html = TbHtml::link(Yii::t('misc',"Edit"),Yii::app()->createUrl('flowTitle/edit',array('code'=>$flow_code)),array('class'=>'link-red'));
        }
        return $html;
	}

	public function getTableBody(){
        $flow_code = $this->flow_code;
        $html = '<tr><td colspan="4">无内容</td></tr>';
        if(key_exists($flow_code,self::$title)){
            $rows = Yii::app()->db->createCommand()->select("id,flow_code,flow_name,flow_photo,z_index")->from("exa_flow_photo")
                ->where("flow_code=:flow_code", array(':flow_code'=>$flow_code))->order("z_index asc,id asc")->queryAll();
            if($rows){
                $html = "";
                foreach ($rows as $row){
                    $id = $row["id"];
                    $html.="<tr>";
                    $html.="<td class='text-center'>".TbHtml::image(Yii::app()->createUrl('flowTitle/printImage',array('id'=>$row["id"])),'',array('height'=>"100px"))."</td>";
                    $html.="<td>".TbHtml::textArea("test[$id][textarea]",$row["flow_name"],array('class'=>'textarea','row'=>2))."</td>";
                    $html.="<td>".TbHtml::textField("test[$id][number]",$row["z_index"],array('class'=>'number'))."</td>";
                    $html.="<td>";
                    $html.=TbHtml::button(Yii::t("misc","Amend"),array('class'=>'amend','submit'=>Yii::app()->createUrl('flowTitle/photoEdit',array('id'=>$row["id"]))));
                    $html.=TbHtml::button(Yii::t("misc","Delete"),array('class'=>'delete','submit'=>Yii::app()->createUrl('flowTitle/photoDel',array('id'=>$row["id"]))));
                    $html.="</td>";
                    $html.="</tr>";
                }
            }
        }

        return $html;
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveStaff($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

    //获取文档数量
    public function getFilesNumber(){
        //docman$suffix.countdoc('CYRAL',a.id) as cyraldoc
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("docman$suffix.countdoc('FLOWTH',1) as cyraldoc")
            ->queryScalar();
        if($row){
            $this->no_of_attm['flowth'] = $row;
        }
    }

	public function savePhoto()
	{
	    $this->z_index = empty($this->z_index)?0:$this->z_index;
        $url = Yii::app()->basePath."/../upload/".$this->flow_code;
        if(!is_dir($url)){
            mkdir($url);
        }
        $img = CUploadedFile::getInstance($this,'flow_photo');
        $flow_photo = "upload/".$this->flow_code."/".date("YmdHsi").".".$img->getExtensionName();
        $img->saveAs($flow_photo);
        Yii::app()->db->createCommand()->insert("exa_flow_photo",array(
            "flow_code"=>$this->flow_code,
            "flow_name"=>$this->flow_name,
            "flow_photo"=>$flow_photo,
            "z_index"=>$this->z_index,
            "lcu"=>Yii::app()->user->id,
        ));
	}

    public function photoEdit($data,$id)
    {
        if(key_exists($id,$data)){
            $data = $data[$id];
            Yii::app()->db->createCommand()->update("exa_flow_photo",array(
                "flow_name"=>$data["textarea"],
                "z_index"=>$data["number"],
                "luu"=>Yii::app()->user->id,
            ),"id=:id",array(":id"=>$id));
        }
    }

    public function photoDel($id){
        $row = Yii::app()->db->createCommand()->select("flow_photo")->from("exa_flow_photo")
            ->where("id=:id", array(':id'=>$id))->queryRow();
        if($row){
            $url = Yii::app()->basePath."/../".$row["flow_photo"];
            if(file_exists($url)){
                unlink($url);
            }
            Yii::app()->db->createCommand()->delete("exa_flow_photo","id=:id",array(":id"=>$id));
        }
    }

	protected function saveStaff(&$connection)
	{
		$sql = '';
        $uid = Yii::app()->user->id;
		switch ($this->scenario) {
			case 'delete':
                $sql = "delete from exa_flow_title where id = :id ";
				break;
			case 'new':
				$sql = "insert into exa_flow_title(
							flow_code, flow_title, lcu
						) values (
							:flow_code, :flow_title, :lcu
						)";
				break;
			case 'edit':
				$sql = "update exa_flow_title set
							flow_code = :flow_code, 
							flow_title = :flow_title, 
							luu = :luu
						where id = :id
						";
				break;
		}

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':flow_code')!==false)
			$command->bindParam(':flow_code',$this->flow_code,PDO::PARAM_STR);
		if (strpos($sql,':flow_title')!==false)
			$command->bindParam(':flow_title',$this->flow_title,PDO::PARAM_STR);

		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);

		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
        }
        return true;
	}

}
