<?php

/**
 * Interactif form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InteractifForm extends BaseInteractifForm
{

protected function doUpdateObject($values)
{
  if (isset($values['interactif_score']))
  {

    /*$carList = is_array($values['cat_list']) ? $values['cat_list'] : array($values['cat_list']);
    array_push($catList, 99);
    $this->getObject()->setCatList($catList)*/
  }
	
  parent::doUpdateObject($values);

}
  public function configure()
  {

 //interactif_image1_delete

    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema->setOption('filter_extra_fields', false);

    $this->setDefault('guid', Guid::generate());
		unset($this['is_tosync']);
		
    $typologies = Doctrine_Core::getTable('Typologie')->findAll();
    $score = $this->getObject()->getScore();
    $decode_score = json_decode($score);
    foreach ($typologies as $typologie)
    {
      $id  = 'score_'.$typologie->getGuid();
      $this->widgetSchema[$id] = new sfWidgetFormInputText();
      $this->widgetSchema[$id]->setLabel("Score ".$typologie->getLibelle());
        if($typologie->getGuid() && is_object($decode_score)){
            $this->setDefault($id, $decode_score->{$typologie->getGuid()});
        }

    }
    
    $this->widgetSchema['file'] = new sfWidgetFormInputFile();
    $this->validatorSchema['file'] =  new sfValidatorFile(
                                    array(
                                      'required'   => false,
                                      'path'       => sfConfig::get('sf_web_dir')."/interactif/",
                                      'mime_types' => array('application/zip','application/x-zip','application/octet-stream','application/x-zip-compressed')
                                    ),
                                    array(
                                      'invalid' => 'Please select only a zip file for upload.',
                                      'required' => 'Select a file to upload.',
                                      'mime_types' => 'The file must be a zip file .',
                                    )
                                );
									$this->getWidgetSchema()->moveField('refresh_deploiement', sfWidgetFormSchema::AFTER, 'file');    
									$this->getWidgetSchema()->moveField('url_fichier_interactif', sfWidgetFormSchema::AFTER, 'refresh_deploiement');                               
if(!$this->isNew())
{
    $this->widgetSchema['image1'] =  new sfWidgetFormInputFileEditable(array(
       'label' => 'Package Image 1',
       'delete_label' => 'Supprimer le fichier ?',
       'file_src' => '/interactif/'.$this->getObject()->getGuid().'/'.$this->getObject()->getImage1(),
       'is_image' => true,
       'edit_mode' => !$this->isNew(),
       'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
    );

    $this->validatorSchema['image1'] =  new sfValidatorFile(
                                    array(
                                      'required'   => false,
                                      'path'       => sfConfig::get('sf_web_dir')."/interactif/".$this->getObject()->getGuid(),
                                      'mime_types' => array('image/jpeg','image/png', 'image/jpg')
                                    ),
                                    array(
                                      'invalid' => 'Please select only a jpeg or png file for upload.',
                                      'required' => 'Select a file to upload.',
                                      'mime_types' => 'The file must be a png / jpeg file .',
                                    )
                                );

    $this->widgetSchema['image2'] =  new sfWidgetFormInputFileEditable(array(
       'label' => 'Package Image 2',
       'delete_label' => 'Supprimer le fichier ?',
       'file_src' => '/interactif/'.$this->getObject()->getGuid().'/'.$this->getObject()->getImage2(),
       'is_image' => true,
       'edit_mode' => !$this->isNew(),
       'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
    );

    $this->validatorSchema['image2'] =  new sfValidatorFile(
                                    array(
                                      'required'   => false,
                                      'path'       => sfConfig::get('sf_web_dir')."/interactif/".$this->getObject()->getGuid(),
                                      'mime_types' => array('image/jpeg','image/png')
                                    ),
                                    array(
                                      'invalid' => 'Please select only a jpeg or png file for upload.',
                                      'required' => 'Select a file to upload.',
                                      'mime_types' => 'The file must be a png / jpeg file .',
                                    )
                                );

    $this->widgetSchema['image3'] =  new sfWidgetFormInputFileEditable(array(
       'label' => 'Package Image 3',
       'delete_label' => 'Supprimer le fichier ?',
       'file_src' => '/interactif/'.$this->getObject()->getGuid().'/'.$this->getObject()->getImage3(),
       'is_image' => true,
       'edit_mode' => !$this->isNew(),
       'template' => '<div>%file%<br />%input%<br />%delete_label% %delete%</div>')
    );

    $this->validatorSchema['image3'] =  new sfValidatorFile(
                                    array(
                                      'required'   => false,
                                      'path'       => sfConfig::get('sf_web_dir')."/interactif/".$this->getObject()->getGuid(),
                                      'mime_types' => array('image/jpeg','image/png')
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
    $this->setWidget('date_diff',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true)));

  	$this->setDefault('is_visiteur_needed', null);
  	$this->setDefault('is_logvisite_needed', null);
  	$this->setDefault('is_logvisite_verbose_needed', false);
  	$this->setDefault('is_parcours_needed', null);

  	$this->setValidator('libelle', new sfValidatorString(array('max_length' => 255, 'required' => true)));

    $choices = array(0 => 'Choix multiple', 1 => 'Courant', 2 => 'Tous');
    $this->setWidget('url_interactif_type', new sfWidgetFormSelectRadio(array('choices' => $choices)));
    $this->setDefault('url_interactif_type', 1);
    $this->setValidator('url_interactif_type', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));

    $interactifs = Doctrine_Core::getTable('Interactif')->findAll();
    $choices_interactifs = array();
    foreach ($interactifs as $interactif)
    {
      $choices_interactifs[$interactif->getGuid()] = $interactif->getLibelle();
    }
      asort($choices_interactifs);

    $this->setWidget('url_interactif_choice', new sfWidgetFormChoice(array('multiple' => true, 'choices' => $choices_interactifs, 'renderer_class' => 'sfWidgetFormSelectDoubleList')));


    $this->getWidget('url_interactif_choice')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    $this->setValidator('url_interactif_choice', new sfValidatorChoice(array('multiple' => true, 'required' => false,  'choices' => array_keys($choices_interactifs))));
    
    $choices = array(0 => 'Courant', 1 => 'Tous');
    $this->setWidget('url_visiteur_type', new sfWidgetFormSelectRadio(array('choices' => $choices)));
    $this->setDefault('url_visiteur_type', 1);
    $this->setValidator('url_visiteur_type', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));

    $choices = array('m' => 'Minutes', 'h' => 'Heures', 'j' => 'Jours', 's' => 'Semaines', 'm' => 'Mois');
    $this->setWidget('url_start_at_type', new sfWidgetFormSelect(array('choices' => $choices)));
    $this->setValidator('url_start_at_type', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));
    
    $this->setWidget('url_end_at_type', new sfWidgetFormSelect(array('choices' => $choices)));
    $this->setValidator('url_end_at_type', new sfValidatorChoice(array('choices' => array_keys($choices), 'required' => false)));
    
    unset($this['updated_at'], $this['created_at'], $this['parcours_list']);
    $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    
    $this->validatorSchema->setPostValidator(
      new sfValidatorCallback(array('callback' => array($this, 'checkValidator')))
    );
    if(!sfContext::getInstance()->getUser()->hasPermission('admin'))
    {
      $this->widgetSchema->setHelp('libelle','<b>Attention :</b> l\'interactif ne sera visible dans le listing que pendant 8h après l\'enregistrement. Il sera définitivement visible une fois attaché à une exposition.');
    }

    $this->getWidgetSchema()->moveField('is_logvisite_needed', sfWidgetFormSchema::BEFORE, 'url_interactif_type');
    $this->getWidgetSchema()->moveField('is_logvisite_verbose_needed', sfWidgetFormSchema::BEFORE, 'url_interactif_type');



    //if(!$this->isNew()){
      //$this->validatorSchema->setPostValidator(
      //   new sfValidatorSchemaCompare('date_diff', sfValidatorSchemaCompare::GREATER_THAN_EQUAL, 'created_at',array('throw_global_error' => false), array('invalid' => 'The start date ("%left_field%") must be before the end date ("%right_field%")' ))
      //);
    //}

  }

  public function checkValidator($validator, $values)
  {
    if(!$values['is_logvisite_verbose_needed'] && !$values['is_logvisite_needed'])
    {
      unset($values['url_interactif_choice'], $values['url_interactif_type'], $values['url_visiteur_type'], $values['url_visiteur_choice'], $values['url_start_at'], $values['url_end_at'], $values['url_start_at_type'], $values['url_end_at_type']);
    }
    else
    {
      if($values['url_interactif_type'] != 0)
      {
        $values['url_interactif_choice'] = '';
      }
      elseif(count($values['url_interactif_choice']) && $values['url_interactif_type'] == 0)
      {
        $values['url_interactif_choice'] = implode(',', $values['url_interactif_choice']);
      }
      else 
      {
        throw new sfValidatorError($validator, 'Choix des Interactifs requis.');
      }
    }
    return $values;
  }

  public function setDefaults($defaults) {
    if (isset($defaults['url_interactif_choice']) && $defaults['url_interactif_type'] == 0)
    {
      $defaults['url_interactif_choice'] = explode(',', $defaults['url_interactif_choice']);
    }
    else
    {
    	$defaults['url_interactif_choice'] = array();
    }
    parent::setDefaults($defaults);
  }

}
