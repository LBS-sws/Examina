<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/7 0007
 * Time: 上午 11:30
 */
class MyTestController extends Controller
{
	public $function_id='EM01';

    public function filters()
    {
        return array(
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
                'actions'=>array('again'),
                'expression'=>array('MyTestController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('MyTestController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('EM01');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('EM01')||Yii::app()->user->validFunction('EM02');
    }
    public function actionIndex($pageNum=0){
        $model = new MyTestList;
        if (isset($_POST['MyTestList'])) {
            $model->attributes = $_POST['MyTestList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['myTest_01']) && !empty($session['myTest_01'])) {
                $criteria = $session['myTest_01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        $model->retrieveDataByPage($model->pageNum);
        $this->render('index',array('model'=>$model));
    }


    //再次答題（不計入數據庫）
    public function actionAgain($index)
    {
/*        $model = new MyTestForm('new');
        $this->render('form',array('model'=>$model,));*/
        $quiz_id = SimTestForm::getQuizIdToJoinID($index);
        $quizModel = new Examina($quiz_id,false,$index);
        if(!$quizModel->getErrorBool()){
            $this->render('new_test',array('model'=>$quizModel,'index'=>$index,));
            //var_dump($quizModel->getResultList());
        }else{
            throw new CHttpException(403,'沒有找到試題');
        }
    }

    //測試單詳情
    public function actionView($index)
    {
        $model = new TestTopForm('view');
        $model->join_id = $index;
        $quiz_id = SimTestForm::getQuizIdToJoinID($index);
        if (!$model->retrieveData($quiz_id)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }


    /*
      //保存測試結果
      public function actionSave()
      {
          //var_dump($_POST);die();
          if (isset($_POST['examina'])) {
              $model = new MyTestForm('new');
              $model->attributes = $_POST['examina'];
              if ($model->validate()) {
                  $model->saveData();
                  Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                  $this->redirect(Yii::app()->createUrl('myTest/view',array('index'=>$model->quiz_id)));
              } else {
                  $message = CHtml::errorSummary($model);
                  Dialog::message(Yii::t('dialog','Validation Message'), $message);
                  $this->redirect(Yii::app()->createUrl('myTest/new',array('index'=>$model->quiz_id)));
              }
          }else{
              throw new CHttpException(404,'The requested page does not exist.');
          }
      }

      public function actionDelete(){
          $model = new MyTestForm('delete');
          if (isset($_POST['MyTestForm'])) {
              $model->attributes = $_POST['MyTestForm'];
              if($model->validateDelete()){
                  $model->saveData();
                  Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
              }else{
                  Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','This record is used by some user records'));
                  $this->redirect(Yii::app()->createUrl('myTest/edit',array('index'=>$model->id)));
              }
          }
          $this->redirect(Yii::app()->createUrl('myTest/index'));
      }*/
}