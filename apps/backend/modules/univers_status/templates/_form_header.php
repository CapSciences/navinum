<?php


if(!$form->isNew()){
    $univers_id = $form->getDefault('univers_id');

}else{
?>
    Choisir l'univers :
<?php
    $url = '/'.sfContext::getInstance()->getConfiguration()->getApplication().'.php/'. preg_replace('/\?.*/', '', sfContext::getInstance()->getRouting()->getCurrentInternalUri());
    $form->getWidget('univers_id')->setAttribute('onchange', 'document.location.href=\''.$url.'?univers_id=\' + this.value;');
    $univers_id = sfContext::getInstance()->getRequest()->getParameter('univers_id', null);
    if(!$univers_id){
        $univers =  Doctrine_Core::getTable('Univers')->createQuery('u')->orderBy('libelle asc')->limit(1)->fetchOne();
        if($univers){
            $univers_id = $univers->getGuid();
        }
    }
    echo $form->getWidget('univers_id')->render('univers_id', $univers_id);
}


if($univers_id){
    $form->getWidget('univers_id')->setDefault($univers_id);
    $univers = Doctrine_Core::getTable('Univers')->find($univers_id);
    if($univers){
        $count_medaille = $univers->getMedaille()->count();
        $univers_status = $univers->getUniversStatusByOrderLevel();
        $univers_name = '';
        $str = '<table><tr><th>Libellé</th><th>Niveau</th><th>Statut</th><th>Nombre de médaille pour passer un prochain status</th><th>Gain</th></tr>';
        foreach($univers_status as $status){
            $univers_name = $status->getUnivers();
            $str .= '<tr><td>'.$status->getLibelle().'</td>'.
            '<td>'.$status->getLevel().'</td>'.
            '<td>'.$status->getLevelName().'</td>'.
            '<td>'.$status->getNbMedaille().'</td>'.
            '<td>'.$status->getGain().'</td></tr>';
        }
        $str .= '</table>';
        $str = '<span><i>'.$count_medaille.' médaille(s) enregistrée(s) dans l\'univers '.$univers_name.'</i></span>'.$str;
        echo $str;
    }

}
