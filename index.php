<?php
define("SESSION_SAVE_PATH", dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR . "sessions");

class SessionManager extends \SessionHandler
{
    private $sessionName = 'MYAPPSESSION';
    private $sessionMaxLifetime = 0;        // this means session will end when you close your browser
    private $sessionSSL = false;
    private $sessionHTTPOnly = true;        // this means the cooke cannot be accessd through the client-side-script (javascript)
    private $sessionPath = "/";
    private $sessionDomain = ".mvcapp2.test";   // this mean for any subdomain
    private $sessionSavePath = SESSION_SAVE_PATH;

    private $sessionCipherAlgo = "AES-128-ECB";
    private $sessionCipherKey = "WQAS201VXZP@221D";

    /*
When use_only_cookies is disable, php will pass the sessionID via URL
this makes the aplication more vulnerable to session hijacking attackes
so we make it true to make php send session IDS in cookies
*/
    public function __construct()
    {
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.use_trans_sid', 0);
        ini_set('session.save_handler', "files");

        session_name($this->sessionName); // this function used to get or set session name
        session_save_path($this->sessionSavePath);
        session_set_cookie_params(
            $this->sessionMaxLifetime,
            $this->sessionPath,
            $this->sessionDomain,
            $this->sessionSSL,
            $this->sessionHTTPOnly
        );
        session_set_save_handler($this, true);
    }

    public function read($id)
    {
        return openssl_decrypt(parent::read($id), $this->sessionCipherAlgo, $this->sessionCipherKey);
    }

    public function write($id, $data)
    {
        return parent::write($id, openssl_encrypt($data, $this->sessionCipherAlgo, $this->sessionCipherKey));
    }

    public function start()
    {
        if ("" === session_id()) {
            return session_start();
        }
    }
}

$sessions = new SessionManager();
$sessions->start();
$_SESSION["name"] = "Mostafa";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";
