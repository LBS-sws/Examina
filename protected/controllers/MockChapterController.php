<?php

class MockChapterController extends Controller
{
	public $function_id='TE02';

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
        $this->function_id = "{$code}02";
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
				'expression'=>array('MockChapterController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index'),
				'expression'=>array('MockChapterController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($index=0)
	{
		$model = new MockChapterModel();
        if (!$model->retrieveAll($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('index',array('model'=>$model));
        }
	}


	public function actionSave()
	{
		if (isset($_POST['MockChapterModel'])) {
			$model = new MockChapterModel($_POST['MockChapterModel']['scenario']);
			$model->attributes = $_POST['MockChapterModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('MockChapter/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
	
	public function actionAdd($index)
	{
		$model = new MockChapterModel('new');
        if (!$model->retrieveClassData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
	}
	
	public function actionEdit($index)
	{
		$model = new MockChapterModel('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new MockChapterModel('delete');
		if (isset($_POST['MockChapterModel'])) {
			$model->attributes = $_POST['MockChapterModel'];
			if ($model->validate()&&$this->function_id==$model->menu_code) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('MockChapter/index',array("index"=>$model->menu_id)));
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
        return Yii::app()->user->validRWFunction("{$code}02");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}02");
    }
}
