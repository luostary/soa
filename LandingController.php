<?php

namespace app\controllers;

use yii\base\ErrorException;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\web\Controller;

class LandingController extends Controller
{
    public static $params = [];

    public function actionPage1()
    {
        self::$params = [
            'method' => 'Post',
            'id' => uniqid(),
            'params' => [
                'url' => $this->request->url,
                'date' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->curlRequest();
    }

    public function actionPage2()
    {
        self::$params = [
            'method' => 'Post',
            'id' => uniqid(),
            'params' => [
                'url' => $this->request->url,
                'date' => date('Y-m-d H:i:s'),
            ],
        ];
        $this->curlRequest();
    }

    public function actionPage100500()
    {
        self::$params = [
            'method' => 'Post',
            'id' => uniqid(),
            'params' => [
                'url' => $this->request->url,
                'date' => date('Y-m-d H:i:s'),
            ],
        ];
        $result = json_decode($this->curlRequest());
        echo '<pre>';
        \yii\helpers\VarDumper::dump($result, 7,false);
        echo '</pre>';
    }

    public function actionActivity()
    {
        self::$params = [
            'method' => 'Get',
            'id' => uniqid(),
        ];
        $data = json_decode($this->curlRequest());

        echo GridView::widget([
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $data->result,
                'pagination' => [
                    'pageSize' => 2,
                ],
            ]),
        ]);
    }

    private function curlRequest()
    {

        $curl = curl_init();
        $params = [
            CURLOPT_URL => 'http://localhost/activity/point',
            // CURLOPT_PORT => 8080,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: ' . \Yii::$app->request->headers->get('cookie'),
            ],
            CURLOPT_POSTFIELDS => http_build_query(array_merge(
                ['jsonRpcToken' => \Yii::$app->params['jsonRpcToken']],
                self::$params)),
        ];

        curl_setopt_array($curl, $params);
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new ErrorException(curl_error($curl));
        }
        curl_close($curl);

        return $response;
    }


}
