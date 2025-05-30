<?php

require_once(INCLUDE_DIR . 'class.signal.php');
require_once(INCLUDE_DIR . 'class.plugin.php');
require_once(INCLUDE_DIR . 'class.ticket.php');
require_once(INCLUDE_DIR . 'class.osticket.php');
require_once(INCLUDE_DIR . 'class.config.php');
require_once('config.php');

class AutoCloserPlugin extends Plugin {
    var $config_class = 'AutoCloserPluginConfig';

    function bootstrap() {
        Signal::connect('cron', [$this, 'runAutoCloser']);
    }

    function runAutoCloser() {
        $backend = new AutoCloserBackend($this->getConfig());
        $backend->processTickets();
    }
}

class AutoCloserBackend {
    var $config;

    function __construct($config) {
        $this->config = $config;
    }

    function processTickets() {
        $fromStatusId = $this->config->get('fromStatus');
        $toStatusId = $this->config->get('toStatus');
        $waitingDays = (int)$this->config->get('waitingPeriod');

        if (!$fromStatusId || !$toStatusId || $waitingDays < 1)
            return;

        $threshold = date('Y-m-d H:i:s', strtotime("-{$waitingDays} days"));

        $sql = "SELECT ticket_id FROM ost_ticket 
                WHERE status_id = " . db_input($fromStatusId) . "
                AND lastupdate <= " . db_input($threshold);

        $res = db_query($sql);
        while ($row = db_fetch_array($res)) {
            $ticket = Ticket::lookup($row['ticket_id']);
            if ($ticket && $ticket->getStatusId() == $fromStatusId) {
                $ticket->setStatus($toStatusId);
                $ticket->logNote(
                    'Auto Closer',
                    'Ticket automatically closed after ' . $waitingDays . ' days in resolved status.'
                );
                $ticket->save();
            }
        }
    }
}
