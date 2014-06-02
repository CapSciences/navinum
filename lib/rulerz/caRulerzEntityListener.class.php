<?php

class caRulerzEntityListener extends Doctrine_Record_Listener
{
    public function postInsert(Doctrine_Event $event)
    {
        $name = sprintf('%s::CREATE', get_class($event->getInvoker()));
        caRulerzTools::getInstance()->dispatchRulez($name, $event->getInvoker());
        caNotificationsTools::getInstance()->notifyUpdate(get_class($event->getInvoker()), $event->getInvoker());
    }

    public function preUpdate(Doctrine_Event $event)
    {
        $name = sprintf('%s::UPDATE', get_class($event->getInvoker()));
        caRulerzTools::getInstance()->dispatchRulez($name, $event->getInvoker());
        caNotificationsTools::getInstance()->notifyUpdate(get_class($event->getInvoker()), $event->getInvoker());
    }
}