<?php
class ReportController extends Controller
{
	protected static $actions = array(
						'salessummary'=>'YB02',
					);
	
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
		);
	}

	public function accessRules() {
		$act = array();
		foreach ($this->action as $key=>$value) { $act[] = $key; }
		return array(
			array('allow', 
				'actions'=>$act,
				'expression'=>array('ReportController','allowExecute'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionSalessummary() {
		$model = new ReportY01Form;
		if (isset($_POST['ReportY01Form'])) {
			$model->attributes = $_POST['ReportY01Form'];
			if ($model->validate()) {
				$model->addQueueItem();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Report submitted. Please go to Report Manager to retrieve the output.'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
			}
		}
		$this->render('form_y01',array('model'=>$model));
	}

	public static function allowExecute() {
		return Yii::app()->user->validFunction(self::$actions[Yii::app()->controller->action->id]);
	}
}
?>
