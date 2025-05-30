<?php
return array(
  'id'          => 'osticket:autocloser',
  'version'     => '0.1.0',
  'name'        => 'Auto Closer',
  'author'      => 'Daniël van Ginneken',
  'description' => 'Automatically transitions tickets from "Resolved" to "Closed" after a configurable waiting period (default: 7 days), keeping your support queue clean and efficient.',
  'url'         => 'https://github.com/DanielvG-IT/osticket-auto-closer',
  'plugin'      => 'auto-closer.php:AutoCloserPlugin',
);