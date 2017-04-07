<?php

namespace ElasticExportIdealoDE\Helper;

use Plenty\Modules\StockManagement\Stock\Contracts\StockRepositoryContract;
use Plenty\Plugin\Log\Loggable;
use Plenty\Repositories\Models\PaginatedResult;

class StockHelper
{
    use Loggable;

    /**
     * Checks if variation is filtered by stock.
     *
     * @param array $variation
     * @param array $filter
     * @return bool
     */
    public function isFilteredByStock($variation, $filter)
    {
        /**
         * If the stock filter is set, this will sort out all variations
         * not matching the filter.
         */
        if(array_key_exists('variationStock.netPositive' ,$filter))
        {
            return $this->isStockNegative($variation);
        }
        elseif(array_key_exists('variationStock.isSalable' ,$filter))
        {
            if(count($filter['variationStock.isSalable']['stockLimitation']) == 2)
            {
                if($variation['data']['variation']['stockLimitation'] != 0 && $variation['data']['variation']['stockLimitation'] != 2)
                {
                    return $this->isStockNegative($variation);
                }
            }
            else
            {
                if($variation['data']['variation']['stockLimitation'] != $filter['variationStock.isSalable']['stockLimitation'][0])
                {
                    return $this->isStockNegative($variation);
                }
            }
        }

        return false;
    }

    /**
     * Checks if variation stock is negative.
     *
     * @param $variation
     * @return bool
     */
    private function isStockNegative($variation):bool
    {
        $stock = 0;
        $stockRepositoryContract = pluginApp(StockRepositoryContract::class);

        if($stockRepositoryContract instanceof StockRepositoryContract)
        {
            $stockRepositoryContract->setFilters(['variationId' => $variation['id']]);
            $stockResult = $stockRepositoryContract->listStockByWarehouseType('sales', ['stockNet'], 1, 1);

            if($stockResult instanceof PaginatedResult)
            {
                $stock = $stockResult->getResult()->first()->stockNet;
            }
        }

        if($stock <= 0)
        {
            return true;
        }

        return false;
    }

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