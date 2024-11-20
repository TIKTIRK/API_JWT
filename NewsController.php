<?php
class NewsController {
    private $newsModel;

    public function __construct() {
        $this->newsModel = new News();
    }

    public function showall() {
        $res=$this->newsModel->getAll();
        $json_news['result'] = array();
        foreach($res as $row){
            $json_news['result'][] = array(
            'id'=>$row['id'], 
            'title'=>preg_replace("/[\r\n]{2,}/i", "", $row['title']),
            'description'=>$row['description'], 
            'text'=>$row['text'], 
            'date'=>$row['date'], 
            'tags'=>$row['tags'], 
            'author'=>$row['author']);
        }
        echo json_encode($json_news);
    }

    public function show($id) {
        $res=$this->newsModel->getOne($id);
        $res['title']=preg_replace("/[\r\n]{2,}/i", "", $res['title']);
        echo json_encode($res);
    }

    public function create() {
        $input = json_decode(file_get_contents('php://input'), true);

        if (
            isset($input['title']) && 
            isset($input['description']) && 
            isset($input['text']) && 
            isset($input['date']) && 
            isset($input['tags']) && 
            isset($input['author'])
            ) {
            $title = $input['title'];
            $description = $input['description'];
            $text = $input['text'];
            $date = $input['date'];
            $tags = $input['tags'];
            $author = $input['author'];

            echo json_encode(['message' => 'News created']);
            echo json_encode($this->newsModel->addNews($title, $description, $text,  $date, $tags, $author));

        } else {
            echo json_encode(['error' => 'Invalid input']);
        }
    }

    public function update() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (
            isset($input['id']) && 
            ( 
            isset($input['title']) ||
            isset($input['description']) || 
            isset($input['text']) || 
            isset($input['date']) || 
            isset($input['tags']) || 
            isset($input['author'])
            )
            ) {
            $id = $input['id'];   
            $title = $input['title'];
            $description = $input['description'];
            $text = $input['text'];
            $date = $input['date'];
            $tags = $input['tags'];
            $author = $input['author'];

            if ($this->newsModel->updateNews($id,$title, $description, $text,  $date, $tags, $author)) {
                echo json_encode(['message' => 'News update']);
                $this->show($id);
            } else {
                echo json_encode(['error' => 'Failed update']);
            }
        } else {
            echo json_encode(['error' => 'Invalid input']);
        }
    }

    public function delete() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['id']) ) {
            $id = $input['id'];

            if ($this->newsModel->deleteNews($id)) {
                echo json_encode(['message' => 'News delete']);
            } else {
                echo json_encode(['error' => 'Failed delete']);
            }
        } else {
            echo json_encode(['error' => 'Invalid input']);
        }
    }
}
?>