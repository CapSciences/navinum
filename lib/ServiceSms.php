<?php
require_once sfConfig::get('sf_lib_dir')."/vendor/vendor/autoload.php";
use Guzzle\Http\Client;

/**
 * Description of dhSmsSendclass
 *
 * @author dasher
 */
class ServiceSms {

    protected $options =
        array(
            'api_id'    => null,
            'user'      => null,
            'password'  => null
            );

    protected $session =
        array(
            'session_id'    => null,
            'last_accessed' => null,
            'use_sessions'  => true,
        );

    protected $allowedOptions = array('from', 'callback', 'deliv_ack', 'msg_type');

    /**
     *
     * @var sfContext
     */
    protected $context = null;

    public function  __construct($apiID = null, $username=null, $password=null) {
        $this->options['user'] = (!null===$username?$username:sfConfig::get('app_service_sms_username', null));
        $this->options['password'] = (!null===$password?$password:sfConfig::get('app_service_sms_password', null));
        $this->options['api_id'] = (!null===$apiID?$password:sfConfig::get('app_service_sms_api', null));

        if (null === $this->options['user'] || null === $this->options['password'] || null=== $this->options['api_id']) {
            throw new sfException('Required Options not set.  User, Password and api_id must all be defined');
        }

        $this->context = sfContext::getInstance();
    }


    private function updateLastRequest() {
        $this->session['last_accessed'] = time();
    }

    private function parseResponse($response) {
        $result =  explode(":", $response);
        return $result[1];
    }

    private function validResponse($response) {
        $result =  explode(":", $response);
        return strtolower($result[0]) == "ok";
    }

    private function setupSession() {
        $result = $this->doCall("/http/auth", $this->options);
        $this->session['session_id'] = $result;
        return true;
    }

    private function hasSession() {
        if ($this->session['use_sessions']) {
            // so we should be using sessions
            if ($this->session['session_id']) {
                // We have an existing session_id
                if ((time() - $this->session['last_accessed']) > (15 * 60) ) {
                    // Session expired
                    $this->session['use_sessions'] = false;
                    $this->setupSession();
                    $this->session['use_sessions'] = true;
                }
            } else {
                $this->session['use_sessions'] = false;
                $this->setupSession();
                $this->session['use_sessions'] = true;
            }
            return null !== $this->session['session_id'];
        }
        return false;
    }


    private function buildRequest($params = array()) {

        $result = "?";
        foreach($params as $key => $value) {
            $result .= sprintf("%s=%s&",$key,$value);
        }
        return $result;
    }

    public function doCall($action, $params = array()) {

        if ($this->hasSession()) {
            $params['session_id'] = $this->session['session_id'];
        }

        $client = new Client('http://api.clickatell.com');

        $request = $action . $this->buildRequest($params);

        $this->log(sprintf("Executing Request: %s",$request));

        $request = $client->get($request);
        $response = $request->send();

        $rawResponse = (string)$response->getBody();

        $response = $this->parseResponse($rawResponse);
        return trim($response);

    }

    public function accountBalance() {
        return $this->doCall("/http/getbalance");
    }

    public function cleanPhone($phone)
    {
        if(substr($phone,0, 1) == "0")
            $phone = "33".substr($phone,1, strlen($phone) - 1);
        if(substr($phone,0, 1) == "+")
            $phone = substr($phone,1, strlen($phone) - 1);

        return $phone;
    }

    public function sendMessage($to, $content, $options = null) {

        $messageOptions = array('to' => $this->cleanPhone($to), 'text' => urlencode($content));
        foreach($this->allowedOptions as $optionName) {
            if (isset ($options[$optionName])) {
                $messageOptions[$optionName] = $options[$optionName];
            }
        }

        return
            $this->doCall(
                "/http/sendmsg",
                $messageOptions
                );
    }

    public function queryMessage($messageID) {
        return $this->doCall("http://api.clickatell.com/http/querymsg", array('apimsgid' => $messageID));
    }

    private function log($message) {
        if (sfConfig::get('sf_debug', true)) {
            $this->context->getLogger()->log($message);
        }
    }

}
?>
