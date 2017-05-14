<?php

class Article{
    private $article_id;
    private $url;
    private $entities = [];
    private $title;
    private $created;
    private $viewed = 0;
    private $is_edit_mode = FALSE;
    
    function __construct($article_id, $url, $entities, $title, $created, $viewed, $is_edit_mode = FALSE) {
        $this->article_id = h($article_id);
        $this->url = h($url);
        $this->entities = $entities;
        $this->title = h($title);
        $this->created = h($created);
        $this->viewed = h($viewed);
        $this->is_edit_mode = $is_edit_mode;
    }
    function get_article_id(){
        return $this->article_id;
    }
    function get_url(){
        return $this->url;
    }
    function get_entities(){
        return $this->entities;
    }
    function get_title(){
        return $this->title;
    }
    function get_created(){
        return $this->created;
    }
    function get_viewed(){
        return $this->viewed;
    }
    function get_is_edit_mode(){
        return $this->is_edit_mode;
    }
}
