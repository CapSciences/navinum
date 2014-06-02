<?php

class caNotificationsTools
{
    private static $instance;

    private $uri;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->uri = sfConfig::get('app_servervip2_notif_uri');
    }

    /**
     * Send notification through websocket server
     *
     * @param $type string
     * @param $dest array array of notification's recipients (visiteur:<guid> ,visite:<guid>, exposition:<guid>, parcours:<guid>)
     * @param $options array array of notification's parameters
     * @param $model array -> array('model' => '', 'model_id' => '')
     */
    public function sendNotification($type, $dest, $options, $model = null)
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'ServerVip pusher');
        $socket->connect($this->uri);

        $vars = array();
        $explode1 = explode(",", $dest);
        foreach($explode1 as $values){
            $data = explode(":", $values);
            $key = trim($data[0]);
            $value = trim($data[1]);
            $vars[$key] = $value;
        }

        if(isset($vars['visiteur']) && trim($vars['visiteur']) != ''){
            $visiteur_id = $vars['visiteur'];
        }else{
	        throw new sfException('You must specify visiteur.');
            $visiteur_id = 'NULL';
        }

        if(isset($vars['visite']) ){
            $visite_id = $vars['visite'];
        }else{
            $visite_id = 'NULL';
        }
	

        if($visiteur_id && strpos($option, 'stdClass') === false){
            $notif = new Notification();
            $notif->setGuid(Guid::generate());
            $notif->setLibelle($options['title']);
            $notif->setVisiteurId($visiteur_id);
	    $notif->setVisiteId($visite_id);
            if(isset($model['model']))$notif->setFromModel($model['model']);
            if(isset($model['model_id']))$notif->setFromModelId($model['model_id']);
            $notif->setParameter(json_encode((array)$options));
            $notif->save();
        }
        
        $socket->send(json_encode(array(
            'command' => 'notification.send',
            'data' => array(
                'dest' => $dest,
                'type' => $type,
                'options' => $options,
                'model' => $model
            )
        )));
    }

    /**
     * Notify websocket server that a model has new data (ex: when user authenticate)
     * @param $model
     * @param $id
     */
    public function notifyUpdate($model, $object)
    {
    return;
        // This is our new stuff
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'ServerVip pusher');
        $socket->connect($this->uri);

        $socket->send(json_encode(array(
            'command' => 'core.update',
            'data' => array(
                'model' => $model,
                'object' => $object
            )
        )));
    }

    /**
     * Notify websocket server that a visite has been registred on a peripherique
     * @param $model
     * @param $id
     */
    public function notifyAuth($visiteId, $connectionId)
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'ServerVip pusher');
        $socket->connect($this->uri);

        $socket->send(json_encode(array(
            'command' => 'core.auth',
            'data' => array(
                'visite_id' => $visiteId,
                'connection_id' => $connectionId
            )
        )));
    }
    /**
     * Notify websocket server that a visite has been registred on a peripherique
     * @param $model
     * @param $id
     */
    public function notifyReset($visiteId)
    {
        // This is our new stuff
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'ServerVip pusher');
        $socket->connect($this->uri);

        $socket->send(json_encode(array(
            'command' => 'core.reset',
            'data' => array(
                'visite_id' => $visiteId
            )
        )));
    }
}
