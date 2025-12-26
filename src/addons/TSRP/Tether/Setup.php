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
        $bbCode = $this->app->em()->create('XF:BbCode');
        $bbCode->tag = 'tether';
        $bbCode->type = 'callback';
        $bbCode->callback_class = 'TSRP\\Tether\\BbCode\\Tether';
        $bbCode->callback_method = 'render';
        $bbCode->example = '[tether=arachnas-swansong]positive1,negative5[/tether]';
        $bbCode->description = 'Render tether images with a popup that shows tether details.';
        $bbCode->active = 1;
        $bbCode->trim = 1;
        $bbCode->allow_signature = 1;
        $bbCode->save();
    }

    public function uninstallStep1(): void
    {
        $bbCode = $this->app->finder('XF:BbCode')->where('tag', 'tether')->fetchOne();
        if ($bbCode)
        {
            $bbCode->delete();
        }
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
