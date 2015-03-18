<?php

class_exists('CApi') or die();

class BlacklistPlugin extends AApiPlugin {

    public function __construct(CApiPluginManager $oPluginManager) {
        parent::__construct('0.1', $oPluginManager);
    }

    public function Init() {
        parent::Init();

        $this->AddJsFile('js/blacklist.js');
        $this->AddJsFile('js/include.js');

        $this->AddTemplate('Blacklist', 'templates/blacklist.html');
    }
}

return new BlacklistPlugin($this);