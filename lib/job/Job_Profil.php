<?php

class Job_Profil
{
    /**
     * @var Visiteur
     */
    private $visiteur;

    public function perform()
    {
        $this->visiteur = Doctrine_Core::getTable('Visiteur')->findOneByGuid($this->args["visiteur_id"]);
        $this->notifyAnniversaire();
        $this->notifyAnniversaireInscription();
    }

    private function notifyAnniversaire()
    {
        if ($this->visiteur->getDateNaissance() == date('Y-m-d')) {
            caNotificationsTools::getInstance()->sendNotification('general:notif:anniversaire', 'visiteur:' . $this->visiteur->getGuid(), array(
                'title' => sprintf('Pshit !!!! champagne !!!'),
                'message' => 'Joyeux Anniversaire !'
            ), array(
                'model' => 'visiteur',
                'model_id' => $this->visiteur->getGuid()
            ));
        }
    }

    private function notifyAnniversaireInscription()
    {
        $createdAt = $this->visiteur->getDateTimeObject('created_at');

        if ($createdAt->format('Y-m-d') == date('Y-m-d')) {
            caNotificationsTools::getInstance()->sendNotification('general:notif:anniversaire-inscription', 'visiteur:' . $this->visiteur->getGuid(), array(
                'title' => sprintf('Pshit !!!! champagne !!!'),
                'message' => 'Tu viens de passer une nouvelle année parmis nous !'
            ), array(
                'model' => 'visiteur',
                'model_id' => $this->visiteur->getGuid()
            ));
        }
    }

}