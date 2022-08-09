<?php
require __DIR__ . '/classes/JwtHandler.php';

class Auth extends JwtHandler
{
    protected $db;
    protected $headers;
    protected $token;

    public function __construct($db, $headers)
    {
        parent::__construct();
        $this->db = $db;
        $this->headers = $headers;
    }

    public function isValid()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchUser($data['data']->user_id)
            ) :
                return [
                    "status" =>'ok',
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function getchatroom()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchchatroom($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function getprivatechat()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchprivatechat($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function inserUserID()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchPostsINsert($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function fetchuploadchatroom()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchupload($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function insertprivchat()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchPostsINsert($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function DElEtEUserID()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchPostsDelete($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }
    public function updateUserID()
    {

        if (array_key_exists('Authorization', $this->headers) && preg_match('/Bearer\s(\S+)/', $this->headers['Authorization'], $matches)) {

            $data = $this->jwtDecodeData($matches[1]);

            if (
                isset($data['data']->user_id) &&
                $user = $this->fetchPostsUpdate($data['data']->user_id)
            ) :
                return [
                    "success" => 1,
                    "user" => $user
                ];
            else :
                return [
                    "success" => 0,
                    "message" => $data['message'],
                ];
            endif;
        } else {
            return [
                "success" => 0,
                "message" => "Token not found in request"
            ];
        }
    }




    protected function fetchUser($user_id)
    {
        try {
            $fetch_user_by_id = "SELECT * FROM chat_user_table WHERE user_id =:id";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            $query_stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
            $query_stmt->execute();

            if ($query_stmt->rowCount()) :
                return $query_stmt->fetch(PDO::FETCH_ASSOC);
            else :
                return false;
            endif;
        } catch (PDOException $e) {
            return null;
        }
    }
    protected function fetchchatroom($user_id)
    {
        $post_id = null;
        if (isset($_GET['id'])) {
                $post_id = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
                    'options' => [
                        'default' => 'all_posts',
                        'min_range' => 1
                    ]
                ]);
            }

        try {
            $fetch_user_by_id =is_numeric($post_id) ? "SELECT * FROM chatrooms  WHERE userid=:user_id AND id=$post_id"  :"SELECT * FROM chatrooms WHERE userid=:user_id";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            $query_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $query_stmt->execute();
            
            if ($query_stmt->rowCount()) :
                return $query_stmt->fetchAll(PDO::FETCH_ASSOC);
            else :
                return false;
            endif;
        } catch (PDOException $e) {
            return null;
        }
    }
    protected function fetchprivatechat($user_id)
    {
        $post_id = null;
        if (isset($_GET['id'])) {
                $post_id = filter_var($_GET['id'], FILTER_VALIDATE_INT, [
                    'options' => [
                        'default' => 'all_posts',
                        'min_range' => 1
                    ]
                ]);
            }

        try {
            $fetch_user_by_id =is_numeric($post_id) ? "SELECT * FROM chat_message  WHERE from_user_id=:user_id AND id=$post_id"  :"SELECT * FROM chat_message WHERE from_user_id=:user_id";
            $query_stmt = $this->db->prepare($fetch_user_by_id);
            $query_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $query_stmt->execute();
            
            if ($query_stmt->rowCount()) :
                return $query_stmt->fetchAll(PDO::FETCH_ASSOC);
            else :
                return false;
            endif;
        } catch (PDOException $e) {
            return null;
        }
    }
    protected function insertprivatechat($user_id)
    {
        $db_connection = new Database();
        $conn = $db_connection->dbConnection();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->chat_message) ) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Please fill all the fields | title, body, author.',
            ]);
            exit;
        
        elseif (empty(trim($data->chat_message)) ) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Oops! empty field detected. Please fill all the fields.',
            ]);
            exit;
        
        endif;

        try {

            $chat_message = htmlspecialchars(trim($data->chat_message));
            // $body = htmlspecialchars(trim($data->body));
            // $author = htmlspecialchars(trim($data->author));

            $query = "INSERT INTO chat_message(chat_message) VALUES(:chat_message)";

            $stmt = $conn->prepare($query);

            $stmt->bindValue(':chat_message', $chat_message, PDO::PARAM_STR);

            if ($stmt->execute()) {
                  
                http_response_code(201);
                echo json_encode([
                    'success' => 1,
                    'message' => 'Data Inserted Successfully.'
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => 0,
                'message' => 'Data not Inserted.'
            ]);
            exit;
        
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    protected function fetchupload($user_id)
    {
        $db_connection = new Database();
        $conn = $db_connection->dbConnection();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($_FILES["file"]) ) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Please fill all the fields | msg.',
            ]);
            exit;
        
        elseif (empty(($_FILES["file"]))) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Oops! empty field detected. Please fill all the fields.',
            ]);
            exit;
        
        endif;

        try {
            $supported_video_extensions = array(".mp4", ".mov", ".wmv", ".flv", ".avi", ".avchd", ".webm", "mkv");
            $supported_image_extensions = array(".png", ".jpg", ".jpeg", ".gif");

            
        if(!empty($_FILES)) {
            foreach($_FILES as $file) {
                $fileName = $file["name"];
                $ext = strtolower(get_extension($fileName));

                if(in_array($ext, $supported_video_extensions)) {
                    $validator->check($_FILES, array(
                        $file["name"]=>array(
                            "name"=>"Video",
                            "video"=>"video"
                        )
                    ));
                } else if(in_array($ext, $supported_image_extensions)) {
                    $validator->check($_FILES, array(
                        $file["name"]=>array(
                            "name"=>"Picture",
                            "image"=>"image"
                        )
                    ));
                }
            }
        }


                            $query = "INSERT INTO chatrooms(msg) VALUES(:msg)";

                           $stmt = $conn->prepare($query);

                         $stmt->bindValue(':msg', $fileName , PDO::PARAM_STR);
                        
                
                
           
      
            // $msg = htmlspecialchars(trim($data->msg));
           

            

            if ($stmt->execute()) {

                http_response_code(201);
                echo json_encode([
                    'success' => 1,
                    'message' => 'Data Inserted Successfully.'
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => 0,
                'message' => 'Data not Inserted.'
            ]);
            exit;
        
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    

    protected function fetchPostsINsert($user_id)
    {
        $db_connection = new Database();
        $conn = $db_connection->dbConnection();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || !isset($data->body) || !isset($data->author)) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Please fill all the fields | title, body, author.',
            ]);
            exit;
        
        elseif (empty(trim($data->title)) || empty(trim($data->body)) || empty(trim($data->author))) :
        
            echo json_encode([
                'success' => 0,
                'message' => 'Oops! empty field detected. Please fill all the fields.',
            ]);
            exit;
        
        endif;

        try {

            $title = htmlspecialchars(trim($data->title));
            $body = htmlspecialchars(trim($data->body));
            $author = htmlspecialchars(trim($data->author));

            $query = "INSERT INTO posts(title,body,author) VALUES(:title,:body,:author)";

            $stmt = $conn->prepare($query);

            $stmt->bindValue(':title', $title, PDO::PARAM_STR);
            $stmt->bindValue(':body', $body, PDO::PARAM_STR);
            $stmt->bindValue(':author', $author, PDO::PARAM_STR);

            if ($stmt->execute()) {

                http_response_code(201);
                echo json_encode([
                    'success' => 1,
                    'message' => 'Data Inserted Successfully.'
                ]);
                exit;
            }
            
            echo json_encode([
                'success' => 0,
                'message' => 'Data not Inserted.'
            ]);
            exit;
        
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    protected function fetchPostsDelete($user_id)
    {
        $database = new Database();
        $conn = $database->dbConnection();

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id)) {
            echo json_encode(['success' => 0, 'message' => 'Please provide the post ID.']);
            exit;
        }

        try {
            $fetch_post = "SELECT * FROM posts WHERE id=:post_id";
            $fetch_stmt = $conn->prepare($fetch_post);
            $fetch_stmt->bindValue(':post_id', $data->id, PDO::PARAM_INT);
            $fetch_stmt->execute();
            // error >0
            if ($fetch_stmt->rowCount()) :

                $delete_post = "DELETE FROM posts WHERE id=:post_id";
                $delete_post_stmt = $conn->prepare($delete_post);
                $delete_post_stmt->bindValue(':post_id', $data->id,PDO::PARAM_INT);

                if ($delete_post_stmt->execute()) {

                    echo json_encode([
                        'success' => 1,
                        'message' => 'Post Deleted successfully'
                    ]);
                    exit;
                }

                echo json_encode([
                    'success' => 0,
                    'message' => 'Post Not Deleted. Something is going wrong.'
                ]);
                exit;

            else :
                echo json_encode(['success' => 0, 'message' => 'Invalid ID. No posts found by the ID.']);
                exit;
            endif;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
    protected function fetchPostsUpdate($user_id) 
    {
        $database = new Database();
        $conn = $database->dbConnection();

        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->id)) {
            echo json_encode(['success' => 0, 'message' => 'Please provide the post ID.']);
            exit;
        }

        try {

            $fetch_post = "SELECT * FROM posts WHERE id=:post_id";
            $fetch_stmt = $conn->prepare($fetch_post);
            $fetch_stmt->bindValue(':post_id', $data->id, PDO::PARAM_INT);
            $fetch_stmt->execute();

            //error >0

            if ($fetch_stmt->rowCount()) :

                $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);
                $post_title = isset($data->title) ? $data->title : $row['title'];
                $post_body = isset($data->body) ? $data->body : $row['body'];
                $post_author = isset($data->author) ? $data->author : $row['author'];

                $update_query = "UPDATE posts SET title = :title, body = :body, author = :author 
                WHERE id = :id";

                $update_stmt = $conn->prepare($update_query);

                $update_stmt->bindValue(':title', htmlspecialchars(strip_tags($post_title)), PDO::PARAM_STR);
                $update_stmt->bindValue(':body', htmlspecialchars(strip_tags($post_body)), PDO::PARAM_STR);
                $update_stmt->bindValue(':author', htmlspecialchars(strip_tags($post_author)), PDO::PARAM_STR);
                $update_stmt->bindValue(':id', $data->id, PDO::PARAM_INT);


                if ($update_stmt->execute()) {

                    echo json_encode([
                        'success' => 1,
                        'message' => 'Post updated successfully'
                    ]);
                    exit;
                }

                echo json_encode([
                    'success' => 0,
                    'message' => 'Post Not updated. Something is going wrong.'
                ]);
                exit;

            else :
                echo json_encode(['success' => 0, 'message' => 'Invalid ID. No posts found by the ID.']);
                exit;
            endif;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => 0,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }
}