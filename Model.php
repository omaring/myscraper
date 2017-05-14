<?php
require_once 'config.php';
require_once 'function.php';
require_once 'Article.php';

class Model {

    private static $instance = null;
    private $pdo;
    private $html;  // 同じHTMLファイルを2回以上読み込まないために保存

    public function get_instance() {
        if (!isset($instance)) {
            $instance = new Model();
            $instance->pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
            $instance->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $instance;
    }
    
    function insert_articles($url, $title){
        $stmt = $this->pdo->prepare("insert into articles (url, title) values (:url, :title)");
        $stmt->bindValue(":url", $url, PDO::PARAM_STR);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    function insert_entities($xpath){
        $stmt = $this->pdo->prepare("insert into entities (xpath) values (:xpath)");
        $stmt->bindValue(":xpath", $xpath, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    function insert_articles_entities($article_id, $entity_id){
        $stmt = $this->pdo->prepare("insert into articles_entities (article_id, entity_id) values (:article_id, :entity_id)");
        $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->bindValue(":entity_id", $entity_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    function insert_all($title, $url, $xpath_text){
        $this->pdo->beginTransaction();
        try{
            $this->insert_articles($url, $title);
            $article_id = $this->pdo->lastInsertId();
            
            $xpath_text = str_replace(array("\r\n", "\r", "\n"), "\n", $xpath_text);
            $xpath_array = ($xpath_text == "") ? [] : explode("\n", $xpath_text);
            $xpath_array = array_map('trim', $xpath_array);
            $xpath_array = array_filter($xpath_array, 'strlen');
            $xpath_array = array_values($xpath_array);

            foreach ($xpath_array as $xpath){
                $this->insert_entities($xpath);
                $entity_id = $this->pdo->lastInsertId();

                $this->insert_articles_entities($article_id, $entity_id);
            }
            
            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            exit($e->getMessage());
        }
        return $article_id;
    }
    
    function delete_article($article_id){
        $stmt = $this->pdo->prepare("delete from articles_entities where article_id = :article_id");
        $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $stmt = $this->pdo->prepare("delete from articles where article_id = :article_id");
        $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    
    function select_xpaths_from_article_id($article_id){
        $sql = <<< SQL
select 
    xpath 
from 
    entities as e 
inner join 
    articles_entities as ae 
on 
    e.entity_id = ae.entity_id
where 
    ae.article_id = :article_id
order by
    e.entity_id asc
SQL;
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $xpaths = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $xpaths;
    }
    
    function select_article_all($is_edit_mode = NULL){
        $stmt = $this->pdo->prepare("select * from articles order by created desc");
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $article_instances = [];
        foreach ($articles as $article){
            $xpaths = $this->select_xpaths_from_article_id($article["article_id"]);
            $entities = [];
            foreach ($xpaths as $xpath){
                $entity = $this->get_entities_from_url($article["url"], $xpath["xpath"]);
                
                // xpathによって取得したentityが空だった場合は追加しない
                if(isset($entity) && !empty($entity)){
                    $entities[] = $entity;
                }
            }
            $article_instances[] = $this->convert_to_article_instance($article, $entities, $is_edit_mode);
        }
        return $article_instances;
    }
    
    function select_article($article_id, $is_edit_mode = FALSE){
        $stmt = $this->pdo->prepare("select * from articles where article_id = :article_id limit 1");
        $stmt->bindValue(":article_id", $article_id, PDO::PARAM_INT);
        $stmt->execute();
        $article = $stmt->fetch();
        
        $xpaths = $this->select_xpaths_from_article_id($article["article_id"]);
        $entities = [];
        foreach ($xpaths as $xpath){
            $entity = $this->get_entities_from_url($article["url"], $xpath["xpath"]);
            // xpathによって取得したentityが空だった場合は追加しない
            if(isset($entity) && !empty($entity)){
                $entities[] = $entity;
            }
        }
        $article_instance = $this->convert_to_article_instance($article, $entities, $is_edit_mode);
        return $article_instance;
    }
    
    function convert_to_article_instance($article, $entities, $is_edit_mode = FALSE){
        $article_id = $article["article_id"];
        $url = $article["url"];
        $title = $article["title"];
        $created = $article["created"];
        $viewed = $article["viewed"];
        
        return new Article($article_id, $url, $entities, $title, $created, $viewed, $is_edit_mode);
    }
    
    function convert(){
        
    }
    
    function get_html($url){
        if(!isset($this->html[$url])){
            if($this->html[$url] = @file_get_contents($url)){
            } else {
                //urlが存在するかどうかをチェック
                if(count($http_response_header) > 0){
                    $status = explode(' ', $http_response_header[0]);
                    switch ($status[1]){
                        case 404:
                            echo "指定したページが存在しません。";
                            break;
                        case 500:
                            echo "サーバからの応答がありません。";
                            break;
                        default:
                            echo "ページ取得エラー";
                    }
                } else {
                    echo "URLが間違っています。";
                }
                return NULL;
            }
        }
        return $this->html[$url];
    }
    
    /*
     * 指定したURLのHTMLコードから、xpathで指定した要素を抜き出す
     * $xpath1つに対して、この関数が配列を返す場合もある
     */
    function get_entities_from_url($url, $xpath) {
        $html = $this->get_html($url);
        if($html == false){
            return false;
        }
        
        $html = mb_convert_encoding($html, "HTML-ENTITIES", "auto");
        
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xml = simplexml_import_dom($dom);
        $entities = $xml->xpath($xpath);
        
        //複数の要素が抽出された場合は1個ずつ
        foreach ($entities as $entity){
            //$entityの文字列が画像であれば、相対urlを絶対urlに変換
//            if(preg_match("/\.png$|\.jpg$|\.jpeg$|\.bmp$/i", $entity)) {
            if(isset($entity["src"])){
                $entity["src"] = $this->createUri($url, $entity["src"]);
            }
            //$entityがhref属性を持っていれば、相対urlを絶対urlに変換
            if(isset($entity["href"])){
                $entity["href"] = $this->createUri($url, $entity["href"]);
            }
        }
        
        return $entities;
    }
    
    /**
     * 相対パスから絶対URLを返します
     * 
     * @param string $base ベースURL（絶対URL）
     * @param string $relational_path 相対パス
     * @return string 相対パスの絶対URL
     * @link http://blog.anoncom.net/2010/01/08/295.html
     * @link http://logic.stepserver.jp/data/archives/501.html
     */
    function createUri( $base = '', $relational_path = '' ) {

        $parse = array (
            'scheme' => null,
            'user' => null,
            'pass' => null,
            'host' => null,
            'port' => null,
            'path' => null,
            'query' => null,
            'fragment' => null,
        );
        $parse = parse_url ( $base );

        // パス末尾が / で終わるパターン
        if ( strpos( $parse['path'], '/', ( strlen( $parse['path'] ) - 1 ) ) !== FALSE ) {
            $parse['path'] .= '.';	// ダミー挿入
        }
        if ( preg_match ( '#^https?\://#', $relational_path ) ) {
            // 相対パスがURLで指定された場合
            return $relational_path;
        } elseif ( preg_match ( '#^/.*$#', $relational_path ) ) {
            // ドキュメントルート指定
            return $parse['scheme'] . '://' . $parse ['host'] . $relational_path;
        } else {
            // 相対パス処理
            $basePath = explode ( '/', dirname ( $parse ['path'] ) );
            $relPath = explode ( '/', $relational_path );
            foreach ( $relPath as $relDirName ) {
                if ($relDirName == '.') {
                    array_shift ( $basePath );
                    array_unshift ( $basePath, '' );
                } elseif ($relDirName == '..') {
                    array_pop ( $basePath );
                    if ( count ( $basePath ) == 0 ) {
                        $basePath = array( '' );
                    }
                } else {
                    array_push ( $basePath, $relDirName );
                }
            }
            $path = implode ( '/', $basePath );
            return $parse ['scheme'] . '://' . $parse ['host'] . $path;
        }
    }

}
