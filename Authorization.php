<?php
require __DIR__ . '/vendor/autoload.php';
use \Firebase\JWT\JWT;
class Authorization {
    private $db;

    public function __construct() {
        $this->db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    }

    public function reg() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['login']) || !isset($input['password'])) {
            echo json_encode(["message" => "Пожалуйста, укажите login и password."]);
            exit;
        }
        $login=$input['login'];
        $stmt = $this->db->prepare("SELECT login FROM users WHERE login='$login'");
        $stmt->execute();
        $res=$stmt->fetch(PDO::FETCH_ASSOC);
        if($res!=false){
            echo json_encode(["message" => "Пользователь с таким именем уже существует."]);
            exit;
        }
        $stmt = $this->db->prepare("INSERT INTO users (login, password) VALUES (?, ?)");
        $stmt->execute([$input['login'], $input['password']]);
        $stmt = $this->db->prepare("SELECT MAX(`id`) FROM `users`");
        $stmt->execute();
        $res=$stmt->fetch(PDO::FETCH_ASSOC);
        $payload = [
            "iss" => "http://projectapi.local",         
            "aud" => "http://projectapi.local",         
            "iat" => time(),                    
            "exp" => time() + TOKEN_EXPIRATION,  
            "data" => [
                "userid" => $res['MAX(`id`)'],
                "userlogin" => $input['login']
            ]
        ];   
        
        $jwt = JWT::encode($payload, SECRET_KEY,'HS256');

        echo json_encode(["message" => "Регистрация прошла успешно", "jwt" => $jwt]);
        exit;
    }

    public function log() {
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['login']) || !isset($input['password'])) {
            echo json_encode(["message" => "Пожалуйста, укажите login и password."]);
            exit;
        }
        $login=$input['login'];
        $password=$input['password'];
        $stmt = $this->db->prepare("SELECT id, login, password FROM users WHERE login='$login' AND  password='$password'");
        $stmt->execute();
        $res=$stmt->fetch(PDO::FETCH_ASSOC);
        if($res==false){
            echo json_encode(["message" => "Неверное имя пользователя или пароль."]);
            exit;
        }
        $payload = [
            "iss" => "http://projectapi.local",
            "aud" => "http://projectapi.local",
            "iat" => time(),
            "exp" => time() + TOKEN_EXPIRATION,
            "data" => [
                "userId" => $res['id'],
                "userlogin" => $res['login']
            ]
        ];
        $jwt = JWT::encode($payload, SECRET_KEY,'HS256');
    
        echo json_encode(["message" => "Успешный вход", "jwt" => $jwt]);
        exit;
    }

}
?>
