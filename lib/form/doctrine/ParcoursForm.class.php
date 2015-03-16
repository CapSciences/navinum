<?php

/**
 * Parcours form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ParcoursForm extends BaseParcoursForm
{
  public function configure()
  {
  	unset($this['updated_at'], $this['created_at'], $this['exposition_list']);

    $this->getWidget('interactif_list')->setOption('order_by', array('ordre', 'asc'));
    $this->getWidget('interactif_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('interactif_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    if(!sfContext::getInstance()->getUser()->hasPermission('admin'))
    {
      $this->widgetSchema->setHelp('libelle','<b>Attention :</b> le parcours ne sera visible dans le listing que pendant 8h après l\'enregistrement. Il sera définitivement visible une fois attaché à une exposition.');
    }
  }


    public function saveInteractifList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['interactif_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->Interactif->getPrimaryKeys();
        $values = $this->getValue('interactif_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('Interactif', array_values($unlink));

            // trace for synchro
            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('parcours_id: "'. $this->getObject()->getGuid() . '"|interactif_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('ParcoursInteractif');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('Interactif', array_values($link));
        }
    }
}
