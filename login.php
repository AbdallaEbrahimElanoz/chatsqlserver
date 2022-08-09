<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__.'/classes/Database.php';
require __DIR__.'/classes/JwtHandler.php';

function msg($success,$status,$message,$extra = []){
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ],$extra);
}

$db_connection = new Database();
$conn = $db_connection->dbConnection();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// IF REQUEST METHOD IS NOT EQUAL TO POST
if($_SERVER["REQUEST_METHOD"] != "POST"):
    $returnData = msg(0,404,'Page Not Found!');

// CHECKING EMPTY FIELDS
elseif(!isset($data->user_email) 
    || !isset($data->user_password)
    || empty(trim($data->user_email))
    || empty(trim($data->user_password))
    ):

    $fields = ['fields' => ['user_email','user_password']];
    $returnData = msg(0,422,'Please Fill in all Required Fields!',$fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else:
    $email = trim($data->user_email);
    $password = trim($data->user_password);

    // CHECKING THE EMAIL FORMAT (IF INVALID FORMAT)
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)):
        $returnData = msg(0,422,'Invalid Email Address!');
    
    // IF PASSWORD IS LESS THAN 8 THE SHOW THE ERROR
    elseif(strlen($password) < 8):
        $returnData = msg(0,422,'Your password must be at least 8 characters long!');

    // THE USER IS ABLE TO PERFORM THE LOGIN ACTION
    else:
        try{
            
            $fetch_user_by_email = "SELECT * FROM chat_user_table WHERE user_email=:email";
            $query_stmt = $conn->prepare($fetch_user_by_email);
            $query_stmt->bindValue(':email', $email,PDO::PARAM_STR);
            $query_stmt->execute();
            $status='Enable';
           // $user_token='';
            $user_login_status='Login';
            $insert_query = "UPDATE chat_user_table SET user_status =:user_status ,user_login_status =:user_login_status WHERE user_email=:email";
            $insert_stmt = $conn->prepare($insert_query);

             // DATA BINDING
            $insert_stmt->bindValue(':user_status', $status, PDO::PARAM_STR);
            $insert_stmt->bindValue(':user_login_status', $user_login_status, PDO::PARAM_STR);
            $insert_stmt->bindValue(':email', htmlspecialchars(strip_tags($email)), PDO::PARAM_STR);
         //   $insert_stmt->bindValue(':user_token', $user_token, PDO::PARAM_STR);

             $insert_stmt->execute();
            // IF THE USER IS FOUNDED BY EMAIL
            if($query_stmt->rowCount()):
                $row = $query_stmt->fetch(PDO::FETCH_ASSOC);
                $check_password = password_verify($password, $row['user_password']);

                // VERIFYING THE PASSWORD (IS CORRECT OR NOT?)
                // IF PASSWORD IS CORRECT THEN SEND THE LOGIN TOKEN
                if($check_password):

                    $jwt = new JwtHandler();
                    $token = $jwt->jwtEncodeData(
                        'http://localhost/chatapisqlser/',
                        array("user_id"=> $row['user_id'])
                    );

    
                    $returnData = [
                        'status'=>'ok',
                        'success' => 1,
                        'message' => 'You have successfully logged in.',
                        'token' => $token,
                        // 'type-users'=>$row['type-users']
                    ];
                    $insert_query = "UPDATE chat_user_table SET user_token =:user_token WHERE user_email=:email";
                    $insert_stmt = $conn->prepare($insert_query);
                    $insert_stmt->bindValue(':user_token', $token, PDO::PARAM_STR);
                    $insert_stmt->bindValue(':email', htmlspecialchars(strip_tags($email)), PDO::PARAM_STR);
                    $insert_stmt->execute();


                   
                // IF INVALID PASSWORD
                else:
                    $returnData = msg(0,422,'Invalid Password!');
                endif;

            // IF THE USER IS NOT FOUNDED BY EMAIL THEN SHOW THE FOLLOWING ERROR
            else:
                $returnData = msg(0,422,'Invalid Email Address!');
            endif;
        }
        catch(PDOException $e){
            $returnData = msg(0,500,$e->getMessage());
        }

    endif;

endif;

echo json_encode($returnData);