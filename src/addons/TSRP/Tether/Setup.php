<?php

namespace TSRP\Tether;

use XF\AddOn\SetupBase;
class Setup extends SetupBase
{
    public function installStep1(): void
    {
        $bbCode = $this->app->em()->create('XF:BbCode');
        $bbCode->bb_code_tag = 'tether';
        $bbCode->bb_code_type = 'callback';
        $bbCode->bb_code_callback_class = 'TSRP\\Tether\\BbCode\\Tether';
        $bbCode->bb_code_callback_method = 'render';
        $bbCode->bb_code_example = '[tether=arachnas-swansong]positive1,negative5[/tether]';
        $bbCode->bb_code_description = 'Render tether images with a popup that shows tether details.';
        $bbCode->bb_code_mode = 'simple';
        $bbCode->bb_code_active = 1;
        $bbCode->bb_code_trim = 1;
        $bbCode->bb_code_allow_signature = 1;
        $bbCode->save();
    }

    public function uninstallStep1(): void
    {
        $bbCode = $this->app->finder('XF:BbCode')->where('bb_code_tag', 'tether')->fetchOne();
        if ($bbCode)
        {
            $bbCode->delete();
        }
    }
}
