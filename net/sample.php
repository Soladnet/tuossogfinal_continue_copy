<?php
ini_set('max_execution_time', 7200);
require_once('class.phpmailer.php');
require_once('class.pop3.php'); // required for POP before SMTP

$pop = new POP3();
$pop->Authorise('pop.secureserver.net', 995, 30, 'soladnet', 'Mydomain1223', 1);

$mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

$mail->IsSMTP();

try {
  $mail->SMTPDebug = 2;
  $mail->Host     = 'pop.secureserver.net';
//  $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->AddAddress('soladnet@gmail.com', 'John Doe');
  $mail->SetFrom('ola@gossout.com', 'Abdulrasheed Soladoye');
//  $mail->AddReplyTo('name@yourdomain.com', 'First Last');
  $mail->Subject = 'PHPMailer Test Subject via mail(), advanced';
  $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
  $mail->MsgHTML(file_get_contents('testHtml5.php'));
//  $mail->AddAttachment('images/phpmailer.gif');      // attachment
//  $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
  $mail->Send();
  echo "Message Sent OK\n";
} catch (phpmailerException $e) {
  echo $e->errorMessage(); //Pretty error messages from PHPMailer
} catch (Exception $e) {
  echo $e->getMessage(); //Boring error messages from anything else!
}
?>