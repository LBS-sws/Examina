<?php

class MarkedlyTakeController extends Controller
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
        $this->function_id = "{$code}07";
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
				'expression'=>array('MarkedlyTakeController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('test'),
				'expression'=>array('MarkedlyTakeController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionTest($markedly_id)
    {
        $model = new MarkedlyTakeModel('test');
        if(MarkedlyTakeModel::validateEmployee($model)){
            if (!$model->retrieveMarkedlyData($markedly_id)) {
                throw new CHttpException(404,'The requested page does not exist.');
            } else {
                $this->render('form',array('model'=>$model));
            }
        }else{
            throw new CHttpException(404,'该账号未绑定员工，无法进行测试');
        }
    }

    public function actionSave()
    {
        if (isset($_POST['MarkedlyTakeModel'])) {
            $model = new MarkedlyTakeModel($_POST['MarkedlyTakeModel']['scenario']);
            $model->attributes = $_POST['MarkedlyTakeModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('paperMy/edit',array('index'=>$model->id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->redirect(Yii::app()->createUrl('MarkedlyTake/test',array('markedly_id'=>$model->markedly_id)));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，无法进行测试');
            }
        }
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}07");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}07");
    }
}
