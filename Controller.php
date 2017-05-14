<?php

require_once 'config.php';
require_once 'function.php';
require_once 'Article.php';

require_once 'View.php';
require_once 'Model.php';

class Controller {

    private static $instance = null;
    private $view;
    private $model;

    public function get_instance() {
        if (!isset($instance)) {
            echo "";
            $instance = new Controller();
            $instance->view = View::get_instance();
            $instance->model = Model::get_instance();
        }
        return $instance;
    }
    
    /*
     * 記事をデータベースに登録
     */
    function create_article($title, $url, $xpath){
        if(!isset($title) || !isset($url)  || !isset($xpath)){
            return NULL;
        }
        if(!preg_match('/^(http|https)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)$/', $url)){
            return NULL;
        }
        if($title == ""){
            $title = "untitled";
        }
        $article_id = $this->model->insert_all($title, $url, $xpath);
        return $article_id;
    }
    
    /*
     * 
     */
    function delete_article($article_id){
        if(isset($article_id)){
            $this->model->delete_article($article_id);
        }
    }
    
    /*
     * 記事をviewで表示
     */
    function show_article_from_id($article_id, $is_edit_mode = FALSE){
        if(isset($article_id)){
            //指定されたIDの記事を、Articleインスタンスとして取得
            $article[] = $this->model->select_article($article_id, $is_edit_mode);
        }
        $this->view->show_articles($article);
    }
    /*
     * 記事をviewで表示
     */
    function show_article_all($is_edit_mode = FALSE){
        //全記事を、Articleインスタンスとして取得
        $articles = $this->model->select_article_all($is_edit_mode);
        $this->view->show_articles($articles);
    }
    
}
