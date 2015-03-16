<?php

/**
 * ExpositionVisiteurNeeds form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpositionVisiteurNeedsForm extends BaseExpositionVisiteurNeedsForm
{
  public function configure()
  {
  	unset($this['updated_at'], $this['created_at']);
  	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    $this->getWidget('exposition_id')->setOption('order_by', array('libelle', 'asc'));

    $this->getWidget('langue_list')->setOption('order_by', array('libelle', 'asc'));
    $this->getWidget('langue_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('langue_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));
    $this->getWidget('preference_media_list')->setOption('order_by', array('libelle', 'asc'));
    $this->getWidget('preference_media_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
  	$this->getWidget('preference_media_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));
  }

    public function savePreferenceMediaList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['preference_media_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->PreferenceMedia->getPrimaryKeys();
        $values = $this->getValue('preference_media_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('PreferenceMedia', array_values($unlink));

            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('exposition_visiteurneeds_id: "'. $this->getObject()->getGuid() . '"|preference_media_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('PreferenceMediaExpositionVisiteurNeeds');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('PreferenceMedia', array_values($link));
        }
    }

    public function saveLangueList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['langue_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->Langue->getPrimaryKeys();
        $values = $this->getValue('langue_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('Langue', array_values($unlink));

            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('exposition_visiteurneeds_id: "'. $this->getObject()->getGuid() . '"|langue_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('LangueExpositionVisiteurNeeds');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('Langue', array_values($link));
        }
    }
}
