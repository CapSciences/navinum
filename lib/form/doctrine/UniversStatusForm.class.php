<?php

/**
 * UniversStatus form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UniversStatusForm extends BaseUniversStatusForm
{
  public function configure()
  {
      unset($this['updated_at'], $this['created_at'], $this['visiteur_list'], $this['is_tosync']);
      $this->validatorSchema->setOption('allow_extra_fields', true);
      $this->validatorSchema->setOption('filter_extra_fields', false);
      
      $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
      $this->setDefault('guid', Guid::generate());

      $this->getValidator('libelle')->setOption('required', true);
      $count = '';
      if($this->isNew()){
          $univers = Doctrine_Query::create()->from('Univers')->limit(1)->orderBy('libelle asc')->fetchOne();
          if($univers){
              $count = $univers->getMedaille()->count();
          }
      }else if($this->getObject()->getUniversId()){
          $count = $this->getObject()->getUnivers()->getMedaille()->count();
      }

      $this->setWidget('gain_id', new sfWidgetFormDoctrineChoice(array('model' => 'Gain', 'order_by' => array('libelle', 'asc'), 'add_empty' => true)));
      $this->getWidgetSchema()->moveField('gain_id', sfWidgetFormSchema::AFTER, 'univers_id');
      $this->setValidator('gain_id', new sfValidatorDoctrineChoice(array('model' => 'Gain', 'column' => 'guid', 'required' => false)));

      $this->getWidget('univers_id')->setOption('order_by', array('libelle', 'asc'));

      $this->getWidget('level')->setDefault('1');
      $this->getWidget('level')->setAttribute('style', 'width:40px');
      $this->getWidget('nb_medaille')->setAttribute('style', 'width:40px');


      if(!$this->isNew())
      {
          $this->widgetSchema['image1'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Image 1',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/univers_status/'.$this->getObject()->getImage1(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['image1'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/univers_status/",
                  'mime_types' => array('image/jpeg','image/png', 'image/jpg')
              ),
              array(
                  'invalid' => 'Please select only a jpeg or png file for upload.',
                  'required' => 'Select a file to upload.',
                  'mime_types' => 'The file must be a png / jpeg file .',
              )
          );

          $this->widgetSchema['image2'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Image 2',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/univers_status/'.$this->getObject()->getImage2(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['image2'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/univers_status/",
                  'mime_types' => array('image/jpeg','image/png', 'image/jpg')
              ),
              array(
                  'invalid' => 'Please select only a jpeg or png file for upload.',
                  'required' => 'Select a file to upload.',
                  'mime_types' => 'The file must be a png / jpeg file .',
              )
          );

          $this->widgetSchema['image3'] =  new sfWidgetFormInputFileEditable(array(
                  'label' => 'Image 3',
                  'delete_label' => 'Supprimer le fichier ?',
                  'file_src' => '/univers_status/'.$this->getObject()->getImage3(),
                  'is_image' => true,
                  'edit_mode' => !$this->isNew(),
                  'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
          );

          $this->validatorSchema['image3'] =  new sfValidatorFile(
              array(
                  'required'   => false,
                  'path'       => sfConfig::get('sf_web_dir')."/univers_status/",
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
          unset( $this->widgetSchema['image1']);
          unset( $this->widgetSchema['image2']);
          unset( $this->widgetSchema['image3']);
      }

  }

    protected function doSave($con = null)
    {
        if (null === $con)
        {
            $con = $this->getConnection();
        }
       $this->updateObject();
       $this->getObject()->save($con);
       $this->saveEmbeddedForms($con);
    }
}
