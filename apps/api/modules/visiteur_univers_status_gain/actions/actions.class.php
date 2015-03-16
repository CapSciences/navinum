<?php

/**
 * visiteur_univers_status_gain actions.
 *
 * @package    sf_sandbox
 * @subpackage visiteur_univers_status_gain
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autovisiteur_univers_status_gainActions
 */
class visiteur_univers_status_gainActions extends autovisiteur_univers_status_gainActions
{
    public function getIndexValidators()
    {
        //$validators = parent::getIndexValidators();
        $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['univers_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['univers_status_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['gain_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['status_gain'] = new sfValidatorChoice(array('choices' => array('waiting' => 'waiting', 'removed' => 'removed', 'canceled' => 'canceled', 'expired' => 'expired'), 'required' => false));
        $validators['expiration_gain_date'] = new sfValidatorDate(array('required' => false));

        $validators['exposition_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['contexte_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['sort_by'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
        $validators['sort_order'] = new sfValidatorChoice(array('choices' => array('asc', 'desc'), 'required' => false));
        $validators['limit'] = new sfValidatorInteger(array('min' => 1, 'required' => false));
        return $validators;
    }

    public function getCreateValidators()
    {
        return array(

              'guid' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
              'visiteur_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurUniversStatusGain')->getRelation('Visiteur')->getAlias(), 'required' => true)),
              'univers_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurUniversStatusGain')->getRelation('Univers')->getAlias(), 'required' => true)),
              'univers_status_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurUniversStatusGain')->getRelation('UniversStatus')->getAlias(), 'required' => false)),
              'gain_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurUniversStatusGain')->getRelation('Gain')->getAlias(), 'required' => false)),
              'status_gain' => new sfValidatorChoice(array('choices' => array('waiting' => 'waiting', 'removed' => 'removed', 'canceled' => 'canceled', 'expired' => 'expired'), 'required' => false)),
              'expiration_gain_date' => new sfValidatorDate(array('required' => false)),
              'contexte_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurUniversStatusGain')->getRelation('Contexte')->getAlias(), 'required' => false))
        );
    }


    protected function doSave()
    {
        $this->object->save();

        $this->getRequest()->setParameter('guid', $this->object->get('guid'));
        $this->getRequest()->setMethod("GET");


         $this->forward("visiteur_univers_status_gain", "index");
    }


    protected function createObject()
    {
        $new_vusg = new $this->model();
        $new_vusg->setGuid(Guid::generate());
        return $new_vusg;
    }

    public function executeDelete(sfWebRequest $request)
    {
        parent::executeDelete($request);

        $serializer = $this->getSerializer();
        $result = array(array('message' => 'ok'));
        $this->output = $serializer->serialize($result);

        $this->setTemplate('index');
        return sfView::SUCCESS;
    }

    public function query($params){
        $q = Doctrine_Query::create()

            ->from($this->model.' '.$this->model)

            ->leftJoin($this->model.'.Gain Gain')
            ->leftJoin($this->model.'.UniversStatus UniversStatus')
            ->leftJoin($this->model.'.Contexte Contexte');

        if (isset($params['limit']))
        {
            $q->limit($params['limit']);
            unset($params['limit']);
        }

        if (isset($params['sort_by']))
        {
            $sort = $params['sort_by'];
            unset($params['sort_by']);

            if (isset($params['sort_order']))
            {
                $sort .= ' '.$params['sort_order'];
                unset($params['sort_order']);
            }
        }

        if (isset($sort))
        {
            $q->orderBy($sort);
        }

        if (isset($params['guid']))
        {
            $values = explode(',', $params['guid']);

            if (count($values) == 1)
            {
                $q->andWhere($this->model.'.guid = ?', $values[0]);
            }
            else
            {
                $q->whereIn($this->model.'.guid', $values);
            }

            unset($params['guid']);
        }

        foreach ($params as $name => $value)
        {
            $q->andWhere($this->model.'.'.$name.' = ?', $value);
        }

        return $q;
    }

}
