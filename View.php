<?php

require_once 'config.php';
require_once 'function.php';
require_once 'Article.php';

class View {

    private static $instance = null;

    public function get_instance() {
        if (!isset($instance)) {
            $instance = new View();
        }
        return $instance;
    }

    /*
     * $entityを文字列としてhtmlコードに変換
     */

    function get_html_as_text($entity) {
        $html = "";
        
        if (isset($entity["href"])) {
            $html .= "<p><a href='" . h($entity["href"]) . "' target='_blank'>" . hbr($entity) . "</a></p>";
        } else {
            $html .= "<p>" . hbr($entity) . "</p>";
        }
        return $html;
    }

    /*
     * $entityが画像のパスのとき、画像としてhtmlコードに変換
     */

    function get_html_as_image($entity) {
        $html = "";
        
        //data-srcまたはsrc
        if(isset($entity["data-src"])){
            
            //画像にリンクが貼られている場合はリンクを貼る
            if(isset($entity["href"])){
                $url = $entity["href"];
            } else {
                $url = $entity["data-src"];
            }  
            $html = <<< HTML
<p><a href="{$url}" target="_blank"><image src="{$entity["data-src"]}"></a></p>
HTML;

        } else if(isset($entity["src"])){

            //画像にリンクが貼られている場合はリンクを貼る
            if(isset($entity["href"])){
                $url = $entity["href"];
            } else {
                $url = $entity["src"];
            }
            $html = <<< HTML
<p><a href="{$url}" target="_blank"><image src="{$entity["src"]}"></a></p>
HTML;
        }
        
        return $html;
    }

    /*
     * $entityを画像または文字を表示するhtmlコードとして出力
     */

    function convert_entity_to_html($entity) {
        if(isset($entity)){
            if (isset($entity["src"]) || isset($entity["data-src"])) {
                return $this->get_html_as_image($entity);
            } else {
                return $this->get_html_as_text($entity);
            }
        }
    }

    /*
     * 記事の編集オプション部のhtmlコードを返す
     */

    function get_html_edit($article_id) {
        $html = <<< HTML
<form action="edit.php" method="post">
    <button type="submit" class="btn btn-danger btn-sm" name="delete_article" value="{$article_id}">記事を削除</button>
</form>
HTML;
        return $html;
    }

    /*
     * Articleクラスをhtmlコードとして出力
     */

    function show_articles($articles) {
        $html = "<div class='container'><div class='row'>";
        foreach ($articles as $article) {
            $article_id = $article->get_article_id();
            $url = $article->get_url();
            $entities = $article->get_entities();
            $title = $article->get_title();
            $created = $article->get_created();
            $viewed = $article->get_viewed();
            $is_edit_mode = $article->get_is_edit_mode();

            $entity_html = "";
            foreach ($entities as $entity) {
                foreach ($entity as $e){
                    $entity_html .= $this->convert_entity_to_html($e);
                }
            }
            $edit_html = $is_edit_mode ?
                    $this->get_html_edit($article_id) : "";

            $html .= <<< HTML
<div class="article">
    <div class="col col-md-6">
        <div class="thumbnail">      
            
            <div class="row">
                <div class="col col-md-12">
                    <div class="title">
                        <h3><a href="viewer.php?id={$article_id}">{$title}</a></h3>
                        <p>from <a href="{$url}" target="_blank">{$url}</a></p>
                    </div>
                </div>
            </div>
                        
            <div class="row">
                <div class="col col-md-12">
                    <div class="entity">
                        {$entity_html}
                    </div>  
                </div>
            </div>
                         
            <div class="row">
                <div class="col col-md-12">
                    <div class="form-group">
                        {$edit_html}
                    </div>
                </div>
            </div>
                        
        </div>
    </div>
</div>
HTML;
        }
        $html .= "</div></div>";
        echo $html;
    }

}
