<?php

namespace TSRP\Tether;

use XF\AddOn\AbstractSetup;

class Setup extends AbstractSetup
{
    public function install(array $stepParams = []): void
    {
        $this->installStep1();
    }

    public function installStep1(): void
    {
        $this->db()->insert('xf_bb_code', [
            'bb_code_tag' => 'tether',
            'bb_code_type' => 'callback',
            'bb_code_callback_class' => 'TSRP\\Tether\\BbCode\\Tether',
            'bb_code_callback_method' => 'render',
            'bb_code_example' => '[tether=arachnas-swansong]positive1,negative5[/tether]',
            'bb_code_description' => 'Render tether images with a popup that shows tether details.',
            'bb_code_active' => 1,
            'bb_code_trim' => 1,
            'bb_code_allow_signature' => 1,
            'bb_code_addon_id' => 'TSRP/Tether'
        ]);
    }

    public function uninstallStep1(): void
    {
        $this->db()->delete('xf_bb_code', 'bb_code_tag = ?', 'tether');
    }

    public function upgrade(array $stepParams = []): void
    {
        // No upgrade steps yet.
    }

    public function uninstall(array $stepParams = []): void
    {
        $this->uninstallStep1();
    }
}
