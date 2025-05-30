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
    var $logFile;

    function __construct($config) {
        $this->config = $config;
        $this->logFile = __DIR__ . '/auto_closer.log';
    }

    function log($message) {
        $maxSize = 5 * 1024 * 1024; // 5 MB
        if (file_exists($this->logFile) && filesize($this->logFile) > $maxSize) {
            file_put_contents($this->logFile, ''); // Clear log
        }
        $date = date('Y-m-d H:i:s');
        file_put_contents($this->logFile, "[$date] $message\n", FILE_APPEND);
    }    

    function processTickets() {
        $fromStatusId = $this->config->get('fromStatus');
        $toStatusId = $this->config->get('toStatus');
        $waitingDays = (int)$this->config->get('waitingPeriod');

        if (!$fromStatusId || !$toStatusId || $waitingDays < 1) {
            $this->log("Invalid configuration: fromStatus=$fromStatusId, toStatus=$toStatusId, waitingPeriod=$waitingDays");
            return;
        }

        $threshold = date('Y-m-d H:i:s', strtotime("-{$waitingDays} days"));

        $sql = "SELECT ticket_id FROM ost_ticket 
                WHERE status_id = " . db_input($fromStatusId) . "
                AND lastupdate <= " . db_input($threshold);

        $this->log("Running auto closer: fromStatus=$fromStatusId, toStatus=$toStatusId, waitingDays=$waitingDays, threshold=$threshold");

        $res = db_query($sql);
        $count = 0;
        while ($row = db_fetch_array($res)) {
            $ticket = Ticket::lookup($row['ticket_id']);
            if ($ticket && $ticket->getStatusId() == $fromStatusId) {
                $ticket->setStatus($toStatusId);
                $ticket->logNote(
                    'Auto Closer',
                    'Ticket automatically closed after ' . $waitingDays . ' days in resolved status.'
                );
                $ticket->save();
                $this->log("Closed ticket ID: " . $row['ticket_id']);
                $count++;
            }
        }
        $this->log("Auto closer finished. Total tickets closed: $count");
    }
}
