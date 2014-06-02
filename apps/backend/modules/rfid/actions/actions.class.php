<?php

require_once dirname(__FILE__).'/../lib/rfidGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/rfidGeneratorHelper.class.php';

/**
 * rfid actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rfidActions extends autoRfidActions
{
    protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
        $rfid = $form->save();
      } catch (Doctrine_Validator_Exception $e) {

        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }
      catch (Exception $e) {
        $message = sprintf("Impossible d'enregistrer : un badge rfid existe déjà %s", $form->getObject()->getUid());
        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }


      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $rfid)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@rfid_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $route = 'rfid';

        $action = $form->getObject()->isNew() ? 'new' : 'edit';

        $redirection = strtolower($this->configuration->getValue($action . '.redirection'));

        if (isset($redirection) && 'list' !== $redirection)
        {
          $route .= '_' . $redirection;
        }

        $url = array('sf_route' => $route);
        if (isset($redirection) && 'list' !== $redirection)
        {
          $url['sf_subject'] = $rfid;
        }

        $this->redirect($url);
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

	public function executeBatchImport(sfWebRequest $request)
	{
		$this->form = new importCsvForm();

		if($request->isMethod("POST")) {
    		$this->processImportForm($request, $this->form);
		}
	}

  public function executeAssignGroup(sfWebRequest $request)
  {
    $this->form = new assignGroupeForm();

    if($request->isMethod("POST")) {
        $this->processAssignGroupForm($request, $this->form);
    }

    $this->redirect('@rfid');
  }

  protected function processAssignGroupForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid())
    {
      try
      {
        $notice = "Assignement réussi.";
        $collection = new Doctrine_Collection('Rfid');
        $rfid_ids = explode(",",$form->getValue('rfid_ids'));

        foreach($rfid_ids as $rfid_id)
        {
           $rfid = Doctrine_Core::getTable('Rfid')->findOneByUid($rfid_id);
           $rfid->setGroupeId($form->getValue('rfid_groupe_id'));
           $collection->add($rfid);
        }
        $collection->save();
      }
      catch (Exception $e)
      {
        $message = "Erreur Assignement : ".$e->getMessage();
        $this->getUser()->setFlash('error', sprintf("error : %s", $message));
        return sfView::SUCCESS;
      }

      $this->getUser()->setFlash('notice', $notice);

    }
    else
    {
      $this->getUser()->setFlash('error', 'The assignement has not been saved due to some errors.', false);
    }
  }


  public function executeBatch(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    if (!$ids = $request->getParameter('ids'))
    {
      $this->getUser()->setFlash('error', 'You must at least select one item.');

      $this->redirect('@rfid');
    }

    if (!$action = $request->getParameter('batch_action'))
    {
      $this->getUser()->setFlash('error', 'You must select an action to execute on the selected items.');

      $this->redirect('@rfid');
    }

    if (!method_exists($this, $method = 'execute'.ucfirst($action)))
    {
      throw new InvalidArgumentException(sprintf('You must create a "%s" method for action "%s"', $method, $action));
    }

    if (!$this->getUser()->hasCredential($this->configuration->getCredentials($action)))
    {
      $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
    }

    $validator = new sfValidatorDoctrineChoice(array('multiple' => true, 'model' => 'Rfid'));

    try
    {
      // validate ids
      $ids = $validator->clean($ids);

      // execute batch
      $this->$method($request);
      if($method == "executeBatchAssign_badges")
      {
        $this->setTemplate("BatchAssignBadges");
        $this->form = new assignGroupeForm();
        $this->form->setDefault('rfid_ids', implode(",",$ids));
        return sfView::SUCCESS;
      }
    }
    catch (sfValidatorError $e)
    {
      $this->getUser()->setFlash('error', 'A problem occurs when deleting the selected items as some items do not exist anymore.');
    }

    $this->redirect('@rfid');
  }


  protected function executeBatchAssign_badges(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }


  protected function processImportForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));

    if ($form->isValid())
    {
      $notice = 'Import réussi';
      try
      {
			$file = $form->getValue('file');
			$filename=$file->getTempName();
      ini_set('auto_detect_line_endings',TRUE);
			$filestream = fopen($filename, "r");
			$collection = new Doctrine_Collection('Rfid');

			while (($data = fgetcsv($filestream, 1000, ";")) !== FALSE)
			{
				$rfid = new Rfid();
				$rfid->setUid($data[0]);

                if(isset($data[1]) && !empty($data[1]))
                {
                  $rfid->setGroupeId($data[1]);
                }
                if(isset($data[2]) && !empty($data[2]))
                {
                  $rfid->setType($data[2]);
                }else{
                    $rfid->setType('visiteur');
                }
                if(isset($data[3]))
                {
                    $rfid->setValeur1($data[3]);
                }
                if(isset($data[4]))
                {
                    $rfid->setValeur2($data[4]);
                }
                if(isset($data[5]))
                {
                    $rfid->setValeur3($data[5]);
                }

                if(isset($data[6]))
                {
                  $rfid->setIsActive($data[6]);
                }
                if(isset($data[7]))
                {
                  $rfid->setIsResettable($data[7]);
                }

				$collection->add($rfid);
    		}
        ini_set('auto_detect_line_endings',FALSE);
    		fclose($filestream);
			   $collection->save();
      }
      catch (Exception $e)
      {
      	$message = "Erreur import : ".$e->getMessage();
        $this->getUser()->setFlash('error', sprintf("error : %s", $message));
        return sfView::SUCCESS;
      }

	  $this->getUser()->setFlash('notice', $notice);

    }
    else
    {
      $this->getUser()->setFlash('error', 'The import has not been saved due to some errors.', false);
    }
  }


    public function executeIndex(sfWebRequest $request)
    {
        // searching
        if ($request->hasParameter('search'))
        {

            $this->setSearch($request->getParameter('search'));
            $request->setParameter('page', 1);
        }

        // filtering
        if ($request->getParameter('filters'))
        {

            $this->setFilters($request->getParameter('filters'));
        }

        // sorting
        if ($request->getParameter('sort'))
        {
            $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
        }

        //maxPerPage
        if ($request->getParameter('maxPerPage'))
        {
            $this->setMaxPerPage($request->getParameter('maxPerPage'));
            $this->setPage(1);
        }


        // pager
        if ($request->getParameter('page'))
        {
            $this->setPage($request->getParameter('page'));
        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        if ($request->isXmlHttpRequest())
        {

            $partialFilter = null;
            sfConfig::set('sf_web_debug', false);
            $this->setLayout(false);
            sfProjectConfiguration::getActive()->loadHelpers(array('I18N', 'Date'));

            if ($request->hasParameter('search'))
            {
                $partialSearch = $this->getPartial('rfid/search', array('configuration' => $this->configuration));
            }

            if ($request->hasParameter('_reset'))
            {
                $partialFilter = $this->getPartial('rfid/filters', array('form' => $this->filters, 'configuration' => $this->configuration));
            }


            $partialList = $this->getPartial('rfid/list', array('pager' => $this->pager, 'sort' => $this->sort, 'helper' => $this->helper));

            if (isset($partialSearch))
            {
                $partialList .= '#__filter__#'.$partialSearch;
            }
            if (isset($partialFilter))
            {
                $partialList .= '#__filter__#'.$partialFilter;
            }
            return $this->renderText($partialList);
        }
    }


    protected function buildQuery()
    {
        $tableMethod = $this->configuration->getTableMethod();
        if (null === $this->filters)
        {
            $this->filters = $this->configuration->getFilterForm($this->getFilters());
        }

        $this->filters->setTableMethod($tableMethod);

        $query = $this->filters->buildQuery($this->getFilters());

        $this->addSearchQuery($query);

        $this->addSortQuery($query);

        $filter = $this->getFilters();

        // patch search type
        if(array_key_exists('type', $filter)){
            $query->andWhereIn('type', $filter['type']);
        }

        $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_query'), $query);
        $query = $event->getReturnValue();

        return $query;
    }



    public function executeFilter(sfWebRequest $request)
    {
        $this->setPage(1);

        if ($request->hasParameter('_reset'))
        {
            $this->setFilters($this->configuration->getFilterDefaults());

            if ($request->isXmlHttpRequest())
            {

                return $this->executeIndex($request);
            }
            else
            {

                $this->redirect('@rfid');
            }
        }

        $this->filters = $this->configuration->getFilterForm($this->getFilters());


        $this->filters->bind($request->getParameter($this->filters->getName()));
        if ($this->filters->isValid())
        {
            $this->setFilters($this->filters->getValues());

            if ($request->isXmlHttpRequest())
            {
                return $this->executeIndex($request);
            }
            else
            {
                $this->redirect('@rfid');
            }


        }

        $this->pager = $this->getPager();
        $this->sort = $this->getSort();

        $this->setTemplate('index');
    }
}
