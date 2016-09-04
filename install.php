<?php

/** @var rex_addon $this */

if (!$this->hasConfig()) {
    $this->setConfig([
        'autoload_css' => true,
        'autoload_js' => true,
    ]);
}
