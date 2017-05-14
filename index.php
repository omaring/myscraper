<?php
require_once 'config.php';
require_once 'function.php';
require_once 'Controller.php';

$controller = Controller::get_instance();
?>

<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= h(TITLE); ?>　</title>
        <!-- 自分で設定したCSSの読み込み -->
        <link rel="stylesheet" href="mystyle.css">
        <!-- BootstrapのCSS読み込み -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- jQuery読み込み -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- BootstrapのJS読み込み -->
        <script src="js/bootstrap.min.js"></script>
    </head>

    <body>
        <!--ナビゲーションバー-->
        <nav class="navbar navbar-default navbar-static-top" role="navigation">
            <div class="container">
                <div class="navbar_header">
                    <button type="button" class="navbar-toggle collapsed">
                        <span class="sr-only">Toggle</span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href=""><?= h(TITLE) ?></a>
                </div>
                <div class="navbar-collapse collapse">                
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="index.php">ホーム</a></li>
                        <li><a href="new.php">記事の投稿</a></li>
                        <li><a href="edit.php">編集</a></li>
                    </ul>
                    <form class="navbar-form navbar-right" role="form">
                        <!--ナビゲーションバーの右側-->
                    </form>
                </div>
            </div>
        </nav>

        <!--本文ヘッダ-->
        <div class='page-header'>
            <div class="container">
                <h2>記事一覧（<?=h(date(FORMAT_DATETIME)); ?>取得）</h2>
                
            </div>
        </div>

        <!--本文-->
        <div class="container">
            <?php $controller->show_article_all(); ?>
        </div>
        
    </body>
</html>
