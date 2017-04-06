<?php

namespace ElasticExportIdealoDE\Helper;

use Plenty\Modules\StockManagement\Stock\Contracts\StockRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Repositories\Models\PaginatedResult;

class StockHelper
{
    use Loggable;

    /**
     * Calculates the stock based depending on different limits.
     *
     * @param  array $variation
     * @return int
     */
    public function getStock($variation):int
    {
        $stock = $stockNet = 0;
        $stockRepositoryContract = pluginApp(StockRepositoryContract::class);

        if($stockRepositoryContract instanceof StockRepositoryContract)
        {
            $stockRepositoryContract->setFilters(['variationId' => $variation['id']]);
            $stockResult = $stockRepositoryContract->listStockByWarehouseType('sales', ['stockNet'], 1, 1);

            if($stockResult instanceof PaginatedResult)
            {
                $stockNet = $stockResult->getResult()->first()->stockNet;
            }
        }

        // get stock
        if($variation['data']['variation']['stockLimitation'] == 2)
        {
            $stock = 999;
        }
        elseif($variation['data']['variation']['stockLimitation'] == 1 && $stockNet > 0)
        {
            if($stockNet > 999)
            {
                $stock = 999;
            }
            else
            {
                $stock = $stockNet;
            }
        }
        elseif($variation['data']['variation']['stockLimitation'] == 0)
        {
            if($stockNet > 999)
            {
                $stock = 999;
            }
            else
            {
                if($stockNet > 0)
                {
                    $stock = $stockNet;
                }
                else
                {
                    $stock = 999;
                }
            }
        }

        return $stock;
    }
}