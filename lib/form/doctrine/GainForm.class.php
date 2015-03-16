<?php

/**
 * Gain form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class GainForm extends BaseGainForm
{
  public function configure()
  {
      $this->validatorSchema->setOption('allow_extra_fields', true);
      $this->validatorSchema->setOption('filter_extra_fields', false);

      unset($this['updated_at'], $this['created_at'], $this['is_tosync'], $this['visiteur_list']);
      $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
      $this->setDefault('guid', Guid::generate());

      $this->setWidget('start_at',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true)));
      $this->setWidget('end_at',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true)));

      $this->validatorSchema->setPostValidator( new sfValidatorOr( array (
              new sfValidatorAnd( array (
                  new sfValidatorSchemaCompare ('start_at', sfValidatorSchemaCompare::NOT_EQUAL, null ),
                  new sfValidatorSchemaCompare ('end_at', sfValidatorSchemaCompare::EQUAL, null )
              )) ,
              new sfValidatorSchemaCompare('start_at', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'end_at',array('throw_global_error' => false), array('invalid' => 'The start date ("%left_field%") must be before the end date ("%right_field%")' ))
          )));

      if(!$this->isNew())
      {
          $this->widgetSchema['image'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Image',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/gain/'.$this->getObject()->getImage(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['image'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/gain/",
                  'mime_types' => array('image/jpeg','image/png', 'image/jpg')
              ),
              array(
                  'invalid' => 'Please select only a jpeg or png file for upload.',
                  'required' => 'Select a file to upload.',
                  'mime_types' => 'The file must be a png / jpeg file .',
              )
          );

          //$this->widgetSchema['image_delete'] = new sfWidgetFormInputCheckbox(array('value_attribute_value'=> 'on') );
          //$this->validatorSchema['image_delete'] = new sfValidatorString();

      }
      else
      {
          unset( $this->widgetSchema['image']);
      }


      $this->setWidget('send_email_visiteur_template_id', new sfWidgetFormDoctrineChoice(array('model' => 'TemplateMail', 'order_by' => array('key_search', 'asc'), 'add_empty' => true)));
      $this->setWidget('send_email_admin_template_id', new sfWidgetFormDoctrineChoice(array('model' => 'TemplateMail', 'order_by' => array('key_search', 'asc'), 'add_empty' => true)));

      $this->setValidator('send_email_visiteur_template_id', new sfValidatorDoctrineChoice(array('model' => 'TemplateMail', 'column' => 'guid', 'required' => false)));
      $this->setValidator('send_email_admin_template_id', new sfValidatorDoctrineChoice(array('model' => 'TemplateMail', 'column' => 'guid', 'required' => false)));
  }
}
