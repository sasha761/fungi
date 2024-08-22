<?php
/**
 * Class NotificationSystem
 * It contains all Notification methods
 */

class NotificationSystem {
    private $to_admin;
    private $headers; 
    private $subject; 

    public function __construct() {
			$this->RegisterSystem = new RegisterSystem;
      $this->to_admin       = get_option('admin_email');
      $this->headers        = 'Content-Type:text/html;charset=UTF-8';
      $this->subject        = get_option('home');

      add_action( 'wp_ajax_subscribe', array( $this, 'subscribe' ) );
      add_action( 'wp_ajax_nopriv_subscribe', array( $this, 'subscribe' ) );
    }

    public function subscribe(){
      $email  = $_POST['email'];
      $gender = $_POST['gender'];

    	$registerRespons = $this->RegisterSystem->registerUser($email, $gender);

      $user_mail_body = 'Спасибо за подписку! Ваш скидочный купон  <b> kapd7bvu </b>';
      wp_mail( $email, $this->subject, $user_mail_body, $this->headers );
    }
}

$NotificationSystem = new NotificationSystem();