<?php
require_once 'config.php';
require_once 'function.php';
require_once 'Controller.php';

$controller = Controller::get_instance();

$delete_article_id = filter_input(INPUT_POST, "delete_article");
$controller->delete_article($delete_article_id);
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
                        <li><a href="index.php">ホーム</a></li>
                        <li><a href="new.php">記事の投稿</a></li>
                        <li class="active"><a href="edit.php">編集</a></li>
                    </ul>
                    <form class="navbar-form navbar-right" role="form">
                        <!--モーダルウィンドウを表示-->
                        
                    </form>
                </div>
            </div>
        </nav>

        <!--ヘッダ-->
        <div class='page-header'>
            <div class="container">
                <h2>記事の編集</h2>
            </div>
        </div>

        <!--本文-->
        <div class="container">
            <?php $controller->show_article_all(TRUE); ?>
        </div>
<!--        <div class="container">
            <?php $url = "http://omrg.php.xdomain.jp/blog/"; ?>
            <?php $xpath = "//div[@class='main']/h3[@class='title']"; ?>
            <?php $controller->show_content($url, $xpath); ?>
        </div>-->
        
    </body>
</html>
