  <?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    if(isset($_POST['submit'])){
        

        try{
            $mail = new PHPMailer(true);
            $mail -> isSMTP();
            $mail -> Host =  'smtp.gmail.com';
            $mail -> SMTPAuth = true;
            $mail -> Username = 'wezimosiuoa@gmail.com';
            $mail -> Password = 'lkjcjwukldudvpho'; 
            $mail -> SMTPSecure = 'ssl';
            $mail -> Port = 465;
    
            $mail -> setFrom('wezimosiuoa@gmail.com');
            $mail -> addAddress($_POST['email']);
    
            $mail -> isHTML(true);
            $mail -> Subject = $_POST['subject'];
            $mail -> Body = $_POST['message'];
    
            $mail -> send();
    
            echo "
                <script>
                    alert('Email is sent ');
                    document.location.href= 'index.php';
                </script>
            ";
        }catch(Exception $err){
            echo "
            
                <h1> Your internet connection is poor, try to connect to internet and retry </h>
            ";
        }
    }
     
  ?>