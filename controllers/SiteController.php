<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\LinkLog;
use app\models\ShortLink;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use yii\helpers\Url;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGenerate()
    {
        $originalUrl = Yii::$app->request->post('url');

        // Валидация URL
        if (!filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            return $this->asJson(['error' => 'Неверный формат URL']);
        }

        // Проверка доступности URL
        $headers = @get_headers($originalUrl);
        if (!$headers || strpos($headers[0], '200') === false) {
            return $this->asJson(['error' => 'Данный URL не доступен']);
        }

        // Генерация короткой ссылки
        $shortCode = substr(md5(uniqid(rand(), true)), 0, 6);
        $shortUrl = Url::to(['site/redirect', 'code' => $shortCode], true);

        // Сохранение в базу
        $model = new ShortLink();
        $model->original_url = $originalUrl;
        $model->short_code = $shortCode;
        $model->created_at = time();
        $model->save();

        // Генерация QR-кода
        $qrCode = new QrCode($shortUrl);
        $writer = new PngWriter();
        $qrCodePath = Yii::getAlias('@webroot/qrcodes/' . $shortCode . '.png');
        $writer->write($qrCode)->saveToFile($qrCodePath);

        return $this->asJson([
            'short_url' => $shortUrl,
            'qr_code' => '/qrcodes/' . $shortCode . '.png',
        ]);
    }

    public function actionRedirect($code)
    {
        $link = ShortLink::findOne(['short_code' => $code]);
        if (!$link) {
            throw new \yii\web\NotFoundHttpException('Ссылка не найдена');
        }

        // Логирование перехода
        $log = new LinkLog();
        $log->link_id = $link->id;
        $log->ip_address = Yii::$app->request->userIP;
        $log->accessed_at = time();
        $log->save();

        // Обновление счетчика кликов
        $link->clicks_count++;
        $link->save();

        // Редирект
        return $this->redirect($link->original_url);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
