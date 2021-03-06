<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">

        <?php
            NavBar::begin([
                'brandLabel' => 'YiiPass',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);

            // Add admin info to username in navigation bar.
            if(is_object(\Yii::$app->user->identity) && Yii::$app->user->identity->is_admin == 1) {
                $displayed_username = Yii::$app->user->identity->username . ' - admin';
            } elseif (is_object(\Yii::$app->user->identity)) {
                $displayed_username = Yii::$app->user->identity->username;
            }

            $aNavWidgetUserActions[] = ['label' => 'Home', 'url' => ['/yiipass/password/index']];

            if (\Yii::$app->params['single_user_mode'] === FALSE) {
                if (Yii::$app->user->isGuest) {
                    $aNavWidgetUserActions = [
                        'label' => 'Login', 'url' => ['/site/login']
                    ];
                } else {
                    if (Yii::$app->user->identity->is_admin == 1) {
                        $aNavWidgetUserActions[] = [
                            'label' => 'Users', 'url' => ['/users'],
                          ]
                        ;
                    }

                    $aNavWidgetUserActions[] = [
                      'label' => 'Logout (' . $displayed_username . ')',
                      'url' => ['/site/logout'],
                      'linkOptions' => ['data-method' => 'post']
                    ];

                }
            }

            $aNavbar = [
              'options' => ['class' => 'navbar-nav navbar-right'],
              'items' =>
                  /**
                   * Display user actions if applications is not in
                   * single user mode.
                   */
                empty($aNavWidgetUserActions) ? '' :
                  $aNavWidgetUserActions
              ,
            ];

            echo Nav::widget($aNavbar);
            NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= $content ?>
        </div>
    </div>

<?php $this->registerJsFile(Yii::$app->request->baseUrl.'/js/script.js') ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
