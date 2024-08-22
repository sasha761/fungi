<?php
/**
 * Class RegisterSystem
 * It contains all Registrate methods
 */
class RegisterSystem {
  // private $email;
  // private $name;
  // private $password;


  public function __construct() {

  }
    
  public function registerUser($email, $gender){
    $email = $_REQUEST['email'];
    $name  = isset($_REQUEST['author']) ? $_REQUEST['author'] : [];  
    $password = isset($_REQUEST['pass']) ? wp_generate_password( 12 ) : [];
    $tel = isset($_REQUEST['tel']) ? $_REQUEST['tel'] : [];
    $site = isset($_REQUEST['url']) ? $_REQUEST['url'] : [];
    // $role = isset($_REQUEST['role']) ? $_REQUEST['role'] : []; 

    $user = get_user_by( 'email', $email );

    if ( !$user ) {
      $userdata = array(
        'user_pass'       => $password, // обязательно
        'user_login'      => $email, // обязательно
        'user_nicename'   => '',
        'user_url'        => $site,
        'user_email'      => $email,
        'display_name'    => $name,
        'nickname'        => '',
        'first_name'      => $name,
        'last_name'       => '',
        'description'     => $gender,
        'rich_editing'    => 'true', // false - выключить визуальный редактор
        'user_registered' => '', // дата регистрации (Y-m-d H:i:s) в GMT
        'role'            => 'subscriber', // (строка) роль пользователя
        'jabber'          => '',
        'aim'             => '',
        'yim'             => '',
      );
      
      $user_id = wp_insert_user( $userdata );

        if ( is_wp_error( $userdata ) ) {
            $data['success'] = false;
            wp_die();
        } else {
            $data['success'] = true;
        }
      } 
      else {  
        $data['success'] = false;
        // return;
        wp_die();
      }
    $data['success'] = true;

    header('Content-Type: application/json');
    echo(json_encode($data));
  }
}



global $RegisterSystem;
$RegisterSystem = new RegisterSystem();