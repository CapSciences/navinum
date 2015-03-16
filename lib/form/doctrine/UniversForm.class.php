<?php

/**
 * Univers form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UniversForm extends BaseUniversForm
{
  public function configure()
  {
      $this->validatorSchema->setOption('allow_extra_fields', true);
      $this->validatorSchema->setOption('filter_extra_fields', false);

      unset($this['updated_at'], $this['created_at'], $this['is_tosync']);
      $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
      $this->setDefault('guid', Guid::generate());

      $this->getWidget('medaille_list')->setOption('order_by', array('libelle', 'asc'));
      $this->getWidget('medaille_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
      $this->getWidget('medaille_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));

      if(!$this->isNew())
      {
          $this->widgetSchema['image'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Image',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/univers/'.$this->getObject()->getImage(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['image'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/univers/",
                  'mime_types' => array('image/jpeg','image/png', 'image/jpg')
              ),
              array(
                  'invalid' => 'Please select only a jpeg or png file for upload.',
                  'required' => 'Select a file to upload.',
                  'mime_types' => 'The file must be a png / jpeg file .',
              )
          );

          $this->widgetSchema['logo'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Logo',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/univers/'.$this->getObject()->getLogo(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['logo'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/univers/",
                  'mime_types' => array('image/jpeg','image/png', 'image/jpg')
              ),
              array(
                  'invalid' => 'Please select only a jpeg or png file for upload.',
                  'required' => 'Select a file to upload.',
                  'mime_types' => 'The file must be a png / jpeg file .',
              )
          );

      }
      else
      {
          unset( $this->widgetSchema['image']);
          unset( $this->widgetSchema['logo']);
      }

  }

    public function saveMedailleList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['medaille_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->Medaille->getPrimaryKeys();
        $values = $this->getValue('medaille_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('Medaille', array_values($unlink));

            // trace for synchro
            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('univers_id: "'. $this->getObject()->getGuid() . '"|medaille_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('UniversMedaille');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('Medaille', array_values($link));
        }
    }
}
