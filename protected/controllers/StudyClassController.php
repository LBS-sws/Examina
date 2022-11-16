<?php

class StudyClassController extends Controller
{
	public $function_id='TE01';

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
				'actions'=>array('edit','add','save','delete'),
				'expression'=>array('StudyClassController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('StudyClassController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($index=0)
	{
		$model = new StudyClassModel();
        if (!$model->retrieveAll($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('index',array('model'=>$model));
        }
	}


	public function actionSave()
	{
		if (isset($_POST['StudyClassModel'])) {
			$model = new StudyClassModel($_POST['StudyClassModel']['scenario']);
			$model->attributes = $_POST['StudyClassModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('StudyClass/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
	
	public function actionAdd($index)
	{
		$model = new StudyClassModel('new');
        if (!$model->retrieveClassData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
	}
	
	public function actionEdit($index)
	{
		$model = new StudyClassModel('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new StudyClassModel('delete');
		if (isset($_POST['StudyClassModel'])) {
			$model->attributes = $_POST['StudyClassModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('StudyClass/index',array("index"=>$model->menu_id)));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
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
}
