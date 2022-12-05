<?php

class MutualAuditController extends Controller
{
	public $function_id='CCCC';

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
        $this->function_id = "{$code}10";
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
				'actions'=>array('edit','reject','audit','auditAll'),
				'expression'=>array('MutualAuditController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('MutualAuditController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionIndex($index,$pageNum=0){
        $model = new MutualAuditList();
        if (isset($_POST['MutualAuditList'])) {
            $model->attributes = $_POST['MutualAuditList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['mutualAudit_'.$this->function_id]) && !empty($session['mutualAudit_'.$this->function_id])) {
                $criteria = $session['mutualAudit_'.$this->function_id];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveAll($index,$model->pageNum);
        $this->render('index',array('model'=>$model));
    }

    public function actionReject()
    { //草稿
        if (isset($_POST['MutualAuditModel'])) {
            $model = new MutualAuditModel("reject");
            $model->attributes = $_POST['MutualAuditModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->mutual_state=3;
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('mutualAudit/index',array('index'=>$model->menu_id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->render('form',array('model'=>$model,));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
            }
        }
    }

    public function actionAudit()
    { //审核
        if (isset($_POST['MutualAuditModel'])) {
            $model = new MutualAuditModel($_POST['MutualAuditModel']['scenario']);
            $model->attributes = $_POST['MutualAuditModel'];
            if(MarkedlyTakeModel::validateEmployee($model)){
                if ($model->validate()&&$this->function_id==$model->menu_code) {
                    $model->mutual_state=2;
                    $model->saveData();
                    Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                    $this->redirect(Yii::app()->createUrl('mutualAudit/edit',array('index'=>$model->id)));
                } else {
                    $message = CHtml::errorSummary($model);
                    Dialog::message(Yii::t('dialog','Validation Message'), $message);
                    $this->render('form',array('model'=>$model,));
                }
            }else{
                throw new CHttpException(404,'该账号未绑定员工，请与管理员联系');
            }
        }
    }

    public function actionAuditAll($index)
    { //审核
        if (isset($_POST['checkId'])) {
            $model = new MutualAuditModel();
            if($model->retrieveMenuData($index)){
                $model->auditAll($_POST['checkId']);
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('mutualAudit/index',array('index'=>$index)));
            }else{
                throw new CHttpException(404,'数据异常');
            }
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "请选择需要通过的学习互助");
            $this->redirect(Yii::app()->createUrl('mutualAudit/index',array('index'=>$index)));
        }
    }

    public function actionEdit($index)
    {
        $model = new MutualAuditModel('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionView($index)
    {
        $model = new MutualAuditModel('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public static function allowReadWrite() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validRWFunction("{$code}10");
    }

    public static function allowReadOnly() {
        $session = Yii::app()->session;
        $code = isset($session['menu_code'])?$session['menu_code']:"dd";
        return Yii::app()->user->validFunction("{$code}10");
    }
}
