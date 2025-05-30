<?php

require_once INCLUDE_DIR . 'class.plugin.php';
require_once INCLUDE_DIR . 'class.list.php';

class AutoCloserPluginConfig extends PluginConfig {

    function pre_save(&$config, &$errors) {
        if ($config['toStatus'] == '') {
            $errors['err'] = 'You must select a status.';
            return FALSE;
        }
        if (!isset($config['waitingPeriod']) || !is_numeric($config['waitingPeriod']) || $config['waitingPeriod'] < 1) {
            $errors['waitingPeriod'] = 'Please enter a valid waiting period (minimum 1 day).';
            return FALSE;
        }
        return TRUE;
    }

    function getOptions() {
        $fromStatuses = array('' => 'Any');
        foreach (TicketStatusList::getStatuses(array('states' => array('open', 'closed'))) as $s) {
            $fromStatuses[$s->getId()] = $s->getName();
        }

        $fromDefault = '';

        $toStatuses = array();
        foreach (TicketStatusList::getStatuses(array('states' => array('open', 'closed'))) as $s) {
            $toStatuses[$s->getId()] = $s->getName();
        }

        $toDefault = '';
        if (count($toStatuses) > 0) {
            $toDefault = array_key_first($toStatuses);
        }

        return array(
            'header' => new SectionBreakField(array(
                'label' => 'Auto Closer Settings',
                'hint' => ''
            )),
            'fromStatus' => new ChoiceField(array(
                'default' => $fromDefault,
                'label' => 'Resolved status',
                'choices' => $fromStatuses
            )),
            'toStatus' => new ChoiceField(array(
                'default' => $toDefault,
                'label' => 'After waiting period change to',
                'choices' => $toStatuses
            )),
            'waitingPeriod' => new TextboxField(array(
                'default' => 7,
                'label' => 'Waiting Period (days)',
                'configuration' => array('size'=>4, 'length'=>3),
                'hint' => 'Number of days to wait after status change before auto-closing.'
            ))
        );
    }
}