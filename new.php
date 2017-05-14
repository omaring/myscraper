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
                        <li><a href="index.php">ホーム</a></li>
                        <li class="active"><a href="new.php">記事の投稿</a></li>
                        <li><a href="edit.php">編集</a></li>
                    </ul>
                    <form class="navbar-form navbar-right" role="form">

                    </form>
                </div>
            </div>
        </nav>

        <!--ヘッダ-->
        <div class='page-header'>
            <div class="container">
                <h2>新しい記事</h2>
            </div>
        </div>

        <!--本文-->
        <div class="container">
            <form class="form-horizontal" method="post" action="viewer.php" enctype="multipart/form-data" accept-charset="utf-8">
                <div class="form-group">
                    <label class="col-sm-3 control-label">登録タイトル</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="input_title" placeholder="任意">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">URL</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control" name="input_url" placeholder="http://">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">xpath</label>
                    <div class="col-sm-8">
                        <textarea class="form-control" name="input_xpath" rows="5" placeholder="改行で複数のxpathを選択"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3 pull-right">
                        <button type="submit" class="btn btn-danger btn-lg">記事を投稿する</button>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
