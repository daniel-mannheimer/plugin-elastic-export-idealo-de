<?php

namespace ElasticExportIdealoDE;

use Plenty\Modules\DataExchange\Services\ExportPresetContainer;
use Plenty\Plugin\DataExchangeServiceProvider;

class ElasticExportIdealoDEServiceProvider extends DataExchangeServiceProvider
{
    public function register()
    {

    }

    public function exports(ExportPresetContainer $container)
    {
        $container->add(
            'IdealoDE-Plugin',
            'ElasticExportIdealoDE\ResultField\IdealoDE',
            'ElasticExportIdealoDE\Generator\IdealoDE',
            true
        );
    }
}