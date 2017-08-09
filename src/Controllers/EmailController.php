<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 25.03.2017
 * Time: 17:23
 */

namespace App\Controllers;

use phpmailerException;
use Slim\Http\Request;
use Slim\Http\Response;
use PHPMailer;

require_once __DIR__ . '/../../vendor/phpmailer/class.phpmailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/PHPMailerAutoload.php';

class EmailController extends PHPMailer
{

    var $priority = 3;
    var $to_name;
    var $to_email;
    var $From = 'noreply@hairtime.co.il'; // from (от) email адрес
    var $FromName = 'HairTime'; // from (от) имя
    var $Host = 'hairtime.co.il';
    var $Port = '465';
    var $SMTPDebug = '0';
    var $SMTPSecure = 'ssl';
    var $CharSet = "UTF-8";
    var $Username = 'noreply@hairtime.co.il';
    var $Password = '159789';
    var $Mailer = 'smtp';
    var $Sender = 'noreply@hairtime.co.il';


    function EmailController()
    {
        $this->SMTPAuth = true;
        // $this->isHTML(true);
    }


}