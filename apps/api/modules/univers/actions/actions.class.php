<?php

/**
 * univers actions.
 *
 * @package    sf_sandbox
 * @subpackage univers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autouniversActions
 */
class universActions extends autouniversActions
{
    public function getIndexValidators()
    {
        $validators = parent::getIndexValidators();
        $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));

        return $validators;
    }

    public function query($params)
    {
        $q = parent::query($params);
        $q->leftJoin('UniversStatus.Gain Gain');
        $q->orderBy('UniversStatus.level desc');

        return $q;
    }

    protected function setFieldVisibility()
    {
        parent::setFieldVisibility();
        // remove gain fields
        foreach ($this->objects as $i => $object) {
            if (isset($this->objects[$i]['image']) && $this->objects[$i]['image'] != '') {
                $this->objects[$i]['image'] = 'http://' . sfConfig::get(
                        'app_front_url'
                    ) . '/univers/' . $this->objects[$i]['image'];
            }

            if (isset($this->objects[$i]['logo']) && $this->objects[$i]['logo'] != '') {
                $this->objects[$i]['logo'] = 'http://' . sfConfig::get(
                        'app_front_url'
                    ) . '/univers/' . $this->objects[$i]['logo'];
            }


            // only filter objects, not additional fields
            if (is_int($i) && isset($object['UniversStatus'])) {
                foreach ($object['UniversStatus'] as $j => $related_object) {

                    if (isset($this->objects[$i]['UniversStatus'][$j]['image1']) && $this->objects[$i]['UniversStatus'][$j]['image1'] != '') {
                        $this->objects[$i]['UniversStatus'][$j]['image1'] = 'http://' . sfConfig::get(
                                'app_front_url'
                            ) . '/univers_status/' . $this->objects[$i]['UniversStatus'][$j]['image1'];
                    }
                    if (isset($this->objects[$i]['UniversStatus'][$j]['image1']) && $this->objects[$i]['UniversStatus'][$j]['image1'] != '') {
                        $this->objects[$i]['UniversStatus'][$j]['image2'] = 'http://' . sfConfig::get(
                                'app_front_url'
                            ) . '/univers_status/' . $this->objects[$i]['UniversStatus'][$j]['image2'];
                    }
                    if (isset($this->objects[$i]['UniversStatus'][$j]['image1']) && $this->objects[$i]['UniversStatus'][$j]['image1'] != '') {
                        $this->objects[$i]['UniversStatus'][$j]['image3'] = 'http://' . sfConfig::get(
                                'app_front_url'
                            ) . '/univers_status/' . $this->objects[$i]['UniversStatus'][$j]['image3'];
                    }
                    if (is_int($j) && isset($related_object['Gain'])) {
                        if (isset($this->objects[$i]['UniversStatus'][$j]['Gain']['image']) && $this->objects[$i]['UniversStatus'][$j]['Gain']['image'] != '') {
                            $this->objects[$i]['UniversStatus'][$j]['Gain']['image'] = 'http://' . sfConfig::get(
                                    'app_front_url'
                                ) . '/univers_status/' . $this->objects[$i]['UniversStatus'][$j]['Gain']['image'];
                        }
                        unset(
                        $this->objects[$i]['UniversStatus'][$j]['gain_id'],
                        $this->objects[$i]['UniversStatus'][$j]['Gain']['created_at'],
                        $this->objects[$i]['UniversStatus'][$j]['Gain']['updated_at'],
                        $this->objects[$i]['UniversStatus'][$j]['Gain']['is_tosync']
                        );

                    }
                }
            }
        }
    }

}