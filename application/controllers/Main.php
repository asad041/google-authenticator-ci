<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * User Authentication Class
 *
 * @package user controller
 * @subpackage user authentication
 * @author asad ullah (https://www.linkedin.com/in/asadullah041/)
 * @copyright 21 August, 2018
 * @since version 1.0.0
 * @sourcefile system/core/CI_Controller
 */
class Main extends CI_Controller
{
    var $google_auth;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Two_FactorAuth');

        $this->google_auth = new \Google\Authenticator\GoogleAuthenticator();
    }
    
    public function index()
    {
        $message                = $this->check_2fa();
        $user_2fa_secret_code   = $this->google_auth->generateSecret();

        $data = [
            'page_title'            => 'Google Authenticator',
            'message'               => $message,
            'user_2fa_secret_code'  => $user_2fa_secret_code,
            'user_2fa_qrCode'       => $this->google_auth->getURL('username', 'example.com', $user_2fa_secret_code),
        ];

        $this->load->view('2fa_method', $data);
    }

    protected function check_2fa()
    {
        if ( $this->input->method(true) === "POST" ) {            
            $code = trim($this->input->post('code'));
            $secret_code = trim($this->input->post('secret_code'));

            if ( $this->google_auth->checkCode($secret_code, $code) ) {
                $message = "Your google authenticator has been verified successfully";
            } else {
                $message = "Entered Invalid code, try again";
            }

            return $message;
        }
    }
}
