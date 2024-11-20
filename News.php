<?php
class News {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM news_list");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $stmt = $this->db->prepare("SELECT * FROM news_list WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addNews($title, $description, $text,  $date, $tags, $author) {
        $stmt = $this->db->prepare("INSERT INTO news_list (title, description, text, date, tags, author) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $text,  $date, $tags, $author]);
        $query= $this->db->prepare("SELECT * FROM `news_list` ORDER BY id DESC LIMIT 1;");
        $query->execute();
        return  $query->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteNews($id) {
        $stmt = $this->db->prepare("DELETE FROM news_list WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function updateNews($id, $title, $description, $text,  $date, $tags, $author) {
        $update="";
        if($title!=NULL){$update.="title='$title'";}
        if($description!=NULL && $update!=""){$update.=", description='$description'";}elseif($description!=NULL){$update.="description='$description'";}
        if($text!=NULL && $update!=""){$update.=", text='$text'";}elseif($text!=NULL){$update.="text='$text'";}
        if($date!=NULL && $update!=""){$update.=", date='$date'";}elseif($date!=NULL){$update.="date='$date'";}
        if($tags!=NULL && $update!=""){$update.=", tags='$tags'";}elseif($tags!=NULL){$update.="tags='$tags'";}
        if($author!=NULL && $update!=""){$update.=", author='$author'";}elseif($author!=NULL){$update.="author='$author'";}
        $stmt = $this->db->prepare("UPDATE news_list SET $update WHERE id=? ");
        return $stmt->execute([$id]);
    }
}
?>