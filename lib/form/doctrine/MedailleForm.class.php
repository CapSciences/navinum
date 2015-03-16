<?php

/**
 * Medaille form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MedailleForm extends BaseMedailleForm
{
  public function configure()
  {
	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    $this->setWidget('interactif_id', new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Interactif'), 'add_empty' => false, 'order_by' => array('libelle', 'asc'))));

      $this->getWidget('univers_list')->setOption('order_by', array('libelle', 'asc'));
      $this->getWidget('univers_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
      $this->getWidget('univers_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));


      unset($this['updated_at'], $this['created_at']);
  }

    protected function doSave($con = null)
    {
        $this->saveUniversList($con);

        parent::doSave($con);
    }

    public function saveUniversList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['univers_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->Univers->getPrimaryKeys();
        $values = $this->getValue('univers_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('Univers', array_values($unlink));

            // trace for synchro
            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('medaille_id: "'. $this->getObject()->getGuid() . '"|univers_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('UniversMedaille');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('Univers', array_values($link));
        }
    }
}
