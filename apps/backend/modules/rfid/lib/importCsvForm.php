<?php
class importCsvForm extends sfForm
{

  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();
    $this->setWidget('file', new sfWidgetFormInputFile());
    $this->setValidator('file', new sfValidatorFile(
                                    array(
                                      'required'   => true,
                                      'path'       => sfConfig::get('sf_upload_dir'),
                                      'mime_types' => array('text/csv','text/plain', 'text/comma-separated-values','application/csv', 'application/excel', 'application/vnd.ms-excel', 'application/vnd.msexcel')
                                    ),
                                    array(
                                      'invalid' => 'Please select only a csv file for upload.',
                                      'required' => 'Select a file to upload.',
                                      'mime_types' => 'The file must be a csv file .',
                                    )
                                )
                        );

    $this->getWidgetSchema()->setNameFormat('import[%s]');
    $this->widgetSchema->setLabels(array(
      'file'    => 'Fichier'
    ));
  }
}