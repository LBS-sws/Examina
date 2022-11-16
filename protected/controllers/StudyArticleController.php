<?php

class StudyArticleController extends Controller
{
	public $function_id='TE09';

	public function init(){
        $session = Yii::app()->session;
        if(key_exists("menu_code",$_GET)){
            $code = $_GET["menu_code"];
            $session["menu_code"]=$code;
        }elseif (isset($session['menu_code']) && !empty($session['menu_code'])) {
            $code = $session['menu_code'];
        }else{
            $code = "无";
        }
        $this->function_id = "{$code}01";
        parent::init();
    }

    public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('edit','add','save','delete','UploadImg','uploadImgArea'),
				'expression'=>array('StudyArticleController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('StudyArticleController','allowReadOnly'),
			),
            array('allow',
                'actions'=>array('PrintImage'),
                'expression'=>array('StudyArticleController','allowAll'),
            ),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($class_id,$pageNum=0)
	{
		$model = new StudyArticleList();
        if (isset($_POST['StudyArticleList'])) {
            $model->attributes = $_POST['StudyArticleList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['studyArticle_'.$this->function_id]) && !empty($session['studyArticle_'.$this->function_id])) {
                $criteria = $session['studyArticle_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        if (!$model->retrieveDataByPage($class_id,$pageNum)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('index',array('model'=>$model));
        }
	}


	public function actionSave()
	{
		if (isset($_POST['StudyArticleModel'])) {
			$model = new StudyArticleModel($_POST['StudyArticleModel']['scenario']);
			$model->attributes = $_POST['StudyArticleModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('StudyArticle/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
	
	public function actionAdd($index)
	{
		$model = new StudyArticleModel('new');
        if (!$model->retrieveClassData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
	}
	
	public function actionEdit($index)
	{
		$model = new StudyArticleModel('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionView($index)
	{
		$model = new StudyArticleModel('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('view',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new StudyArticleModel('delete');
		if (isset($_POST['StudyArticleModel'])) {
			$model->attributes = $_POST['StudyArticleModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('StudyArticle/index',array("class_id"=>$model->class_id)));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}

    //上傳圖片
    public function actionUploadImg(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new UploadImgForm();
            $img = CUploadedFile::getInstance($model,'file');
            $class_id = key_exists("class_id",$_POST)?$_POST["class_id"]:0;
            $id = key_exists("id",$_POST)?$_POST["id"]:0;
            $class_id = is_numeric($class_id)?$class_id:0;
            $id = is_numeric($id)?$id:0;
            $id = $id==0?"user_".Yii::app()->user->id."_0":$id;
            $path =Yii::app()->basePath."/../upload/images/";
            if (!file_exists($path)){
                mkdir($path);
                $myfile = fopen($path."index.php", "w");
                fclose($myfile);
            }
            $path.="study_{$class_id}/";
            if (!file_exists($path)){
                mkdir($path);
                $myfile = fopen($path."index.php", "w");
                fclose($myfile);
            }
            $url = "upload/images/study_".$class_id."/article_{$id}.".$img->getExtensionName();
            $model->file = $img->getName();
            if ($model->file && $model->validate()) {
                $img->saveAs($url);
                //$url = "/".Yii::app()->params['systemId']."/".$url;
                $url = "../../".$url;
                echo CJSON::encode(array('status'=>1,'data'=>$url,'imgType'=>$img->getExtensionName()));
            }else{
                echo CJSON::encode(array('status'=>0,'error'=>$model->getErrors()));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('site/index'));
        }
    }

    //上傳圖片(富文本)
    public function actionUploadImgArea(){
        $img = CUploadedFile::getInstanceByName("upload");
        $path =Yii::app()->basePath."/../images/uploadArea/";
        $city = Yii::app()->user->city();
        if (!file_exists($path)){
            mkdir($path);
            $myfile = fopen($path."index.php", "w");
            fclose($myfile);
        }
        $url = "images/uploadArea/{$city}_".date("YmdHis").".".$img->getExtensionName();
        if ($img->getError()==0) {
            $img->saveAs($url);
            $url = Yii::app()->getBaseUrl(true)."/".$url;
            echo CJSON::encode(array('uploaded'=>1,'url'=>$url,'fileName'=>$img->getName()));
        }else{
            echo CJSON::encode(array('uploaded'=>1,'url'=>"",'fileName'=>$img->getName(),'error'=>array("message"=>"图片大小不能超过2M")));
        }
        die();
    }

    public function actionPrintImage($id = 0) {
        $row = Yii::app()->db->createCommand()->select("study_img,class_id")
            ->from("exa_study")->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            if(empty($row["study_img"])){
                echo "圖片不存在";
                return false;
            }else{
                $n = new imgdata;
                $path =Yii::app()->basePath."/../upload/images/";
                $path.= "study_".$row["class_id"]."/article_{$id}.".$row["study_img"];
                if (file_exists($path)) {
                    $n -> getdir($path);
                    $n -> img2data();
                    $n -> data2img();
                } else {
                    echo "地址不存在";
                    return false;
                }
            }
        }else{
            echo "沒找到圖片";
            return false;
        }
    }
	
	public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
		return Yii::app()->user->validRWFunction("{$code}01");
	}
	
	public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
		return Yii::app()->user->validFunction("{$code}01");
	}

    public static function allowAll() {
        return true;
    }
}
