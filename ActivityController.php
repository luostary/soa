<?php

namespace app\controllers;

use app\models\Activity;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class ActivityController extends Controller
{
    public $jsonResult = [
        "jsonrpc" => "2.0",
        "id" => null
    ];

    public $postData = [];

    public function beforeAction($action)
    {
        $this->postData = \Yii::$app->request->post();
        if (!$this->postData['jsonRpcToken'] || $this->postData['jsonRpcToken'] != \Yii::$app->params['jsonRpcToken']) {
            throw new ForbiddenHttpException('Forbidden');
        }
        if (!$this->postData['method']) {
            throw new NotFoundHttpException('Not found method Name');
        }
        if (!$this->postData['id']) {
            throw new NotFoundHttpException('Not found id request');
        }
        return parent::beforeAction($action);
    }

    public function actionPoint()
    {
        $result = $this->jsonResult;
        $result['id'] = $this->postData['id'];

        $action = null;

        $actionName = "action" . ucfirst($this->postData['method']);
        if (method_exists($this, $actionName)) {
            $action = $this->$actionName();

            if (!$action['error']) {
                $result['result'] = $action['result'];
            } else {
                $result['error'] = [
                    'code' => 303,
                    'message' => $action['errors']
                ];
            }
        } else {
            $result['error'] = [
                'code' => 404,
                'message' => __CLASS__ . ". В классе не существует метода {$this->postData['method']}"
            ];
        }

        return $this->asJson($result);
    }

    public function actionPost()
    {
        $activity = new Activity([
            'dt_create' => $this->postData['params']['date'],
            'sessid' => session_id(),
            'activity_id' => $this->postData['id'],
            'url' => $this->postData['params']['url'],
        ]);
        if ($activity->save()) {
            $response = [
                'error' => false,
                'result' => $activity->attributes
            ];
        } else {
            $response = [
                'error' => true,
                'errors' => $activity->errors
            ];
        }
        return $response;
    }

    public function actionGet()
    {
        return ['result' => Activity::find()
            ->select([
                'url',
                new Expression('COUNT(id) as visit_count'),
                new Expression('MAX(dt_create) as last_visit_date'),
            ])
            ->groupBy(['url'])
            ->asArray()
            ->all()];
    }

}
