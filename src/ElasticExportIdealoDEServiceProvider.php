<?php

namespace ElasticExportIdealoDE;

use ElasticExportIdealoDE\Helper\PriceHelper;
use ElasticExportIdealoDE\Helper\PropertyHelper;
use ElasticExportIdealoDE\Helper\StockHelper;
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
            '',
            true,
            true
        );
    }
}