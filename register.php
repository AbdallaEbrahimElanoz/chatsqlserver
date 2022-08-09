<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$error = '';

$success_message = '';

require __DIR__ . '/classes/Database.php';
$db_connection = new Database();
$conn = $db_connection->dbConnection();

function msg($success, $status, $message, $extra = [])
{
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

// DATA FORM REQUEST
$data = json_decode(file_get_contents("php://input"));
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST") :

    $returnData = msg(0, 404, 'Page Not Found!');

elseif (
    !isset($data->user_name)
    || !isset($data->user_email)
    || !isset($data->user_password)
    // || !isset($data->user_profile)
    // || !isset($data->user_created_on)
    // || !isset($data->user_verification_code)
    || empty(trim($data->user_name))
    || empty(trim($data->user_email))
    || empty(trim($data->user_password))
    // || empty(trim($data->user_profile))
    // || empty(trim($data->user_created_on))

) :

    $fields = ['fields' => ['user_name', 'user_email', 'user_password']];
    $returnData = msg(0, 422, 'Please Fill in all Required Fields!', $fields);

// IF THERE ARE NO EMPTY FIELDS THEN-
else :
    function make_avatar($character)
	{
	    $path = "images/". time() . ".png";
		$image = imagecreate(200,200);
		$red = rand(0, 255);
		$green = rand(0, 255);
		$blue = rand(0, 255);
	    imagecolorallocate($image, $red, $green, $blue);  
	    $textcolor = imagecolorallocate($image, 255,255,255);

	    $font = dirname(__FILE__) . '/font/arial.ttf';

	    imagettftext($image, 100, 0, 55, 150, $textcolor, $font, $character);
	    imagepng($image, $path);
	    imagedestroy($image);
	    return $path;
	}

    $name = trim($data->user_name);
    $email = trim($data->user_email);
    $password = trim($data->user_password);
    $profile = make_avatar(strtoupper($data->user_name[0]));
    $status='Disabled';
    $created_on = date('Y-m-d H:i:s');
    $verification= md5(uniqid());

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) :
        $returnData = msg(0, 422, 'Invalid Email Address!');

    elseif (strlen($password) < 8) :
        $returnData = msg(0, 422, 'Your password must be at least 8 characters long!');

    elseif (strlen($name) < 3) :
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');

    else :
        try {

            $check_email = "SELECT user_email FROM chat_user_table WHERE user_email=:email";
            $check_email_stmt = $conn->prepare($check_email);
            $check_email_stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $check_email_stmt->execute();

            if ($check_email_stmt->rowCount()) :
                $returnData = msg(0, 422, 'This E-mail already in use!');

            else :
                $insert_query = "INSERT INTO chat_user_table (user_name,user_email,user_password,user_profile,user_status,user_created_on,user_verification_code) VALUES(:name,:email,:password,:user_profile,:user_status,:user_created_on,:user_verification_code)";

                $insert_stmt = $conn->prepare($insert_query);

                // DATA BINDING
                $insert_stmt->bindValue(':name', htmlspecialchars(strip_tags($name)), PDO::PARAM_STR);
                $insert_stmt->bindValue(':email', $email, PDO::PARAM_STR);
                $insert_stmt->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $insert_stmt->bindValue(':user_profile',$profile, PDO::PARAM_STR);
                $insert_stmt->bindValue(':user_status',$status, PDO::PARAM_STR);
                $insert_stmt->bindValue(':user_created_on',$created_on, PDO::PARAM_STR);
                $insert_stmt->bindValue(':user_verification_code',$verification, PDO::PARAM_STR);
              //  $insert_stmt->bindValue(':code', $password, PDO::PARAM_STR);

                $insert_stmt->execute();

                $returnData = msg(1, 201, 'You have successfully registered.');

                //  server stmp 


            //     $mail = new PHPMailer(true);

            // $mail->isSMTP();

            // $mail->Host ='smtp.sendgrid.net';

            // $mail->SMTPAuth = true;

            // $mail->Username   = 'apikey';                     // SMTP username
            // $mail->Password   = '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';

            // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // $mail->Port = 587;

            // $mail->setFrom('anaabdoa319@gmail.com','chat');

            // $mail->addAddress($email);

            // $mail->isHTML(true);

            // $mail->Subject = 'Registration Verification for Chat Application Demo';

            // $mail->Body = '
            // <p>Thank you for registering for Chat Application Demo.</p>
            //     <p>This is a verification email, please click the link to verify your email address.</p>
            //     <p><a href="http://localhost/ratchet-chat-application/verify.php?code='.$verification.'">Click to Verify</a></p>
            //     <p>Thank you...</p>
            // ';

            // $mail->send();


            // $success_message = 'Verification Email sent to ' . $email . ', so before login first verify your email';
        

            endif;
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    endif;
endif;

echo json_encode($returnData);