<?php

namespace ElasticExportIdealoDE\Helper;

use Plenty\Legacy\Repositories\Item\SalesPrice\SalesPriceSearchRepository;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\SalesPrice\Contracts\SalesPriceSearchRepositoryContract;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchRequest;
use Plenty\Modules\Item\SalesPrice\Models\SalesPriceSearchResponse;
use Plenty\Plugin\Log\Loggable;

class PriceHelper
{
    use Loggable;

    const TRANSFER_RRP_YES = 1;

    /**
     * @var SalesPriceSearchRepositoryContract
     */
    private $salesPriceSearchRepository;

    /**
     * PriceHelper constructor.
     *
     * @param SalesPriceSearchRepositoryContract $salesPriceSearchRepository
     */
    public function __construct(SalesPriceSearchRepositoryContract $salesPriceSearchRepository)
    {
        $this->salesPriceSearchRepository = $salesPriceSearchRepository;
    }

    /**
     * Get a list with price and recommended retail price.
     *
     * @param  array $variation
     * @param  KeyValue $settings
     * @return array
     */
    public function getPriceList($variation, KeyValue $settings):array
    {
        $variationPrice = $variationRrp = 0.00;

        /**
         * SalesPriceSearchRequest $salesPriceSearchRequest
         */
        $salesPriceSearchRequest = pluginApp(SalesPriceSearchRequest::class);
        if($salesPriceSearchRequest instanceof SalesPriceSearchRequest)
        {
            $salesPriceSearchRequest->variationId = $variation['id'];
            $salesPriceSearchRequest->referrerId = $settings->get('referrerId');
        }

        // getting the retail price
        $salesPriceSearch = $this->salesPriceSearchRepository->search($salesPriceSearchRequest);
        if($salesPriceSearch instanceof SalesPriceSearchResponse)
        {
            $variationPrice = (float)$salesPriceSearch->price;
        }

        // getting the recommended retail price
        if($settings->get('transferRrp') == self::TRANSFER_RRP_YES)
        {
            $salesPriceSearchRequest->type = 'rrp';
            $rrpPriceSearch = $this->salesPriceSearchRepository->search($salesPriceSearchRequest);

            if($rrpPriceSearch instanceof SalesPriceSearchResponse)
            {
                $variationRrp = (float)$rrpPriceSearch->price;
            }
        }

        // set the initial price and recommended retail price
        $price = $variationPrice;
        $rrp = $variationRrp;

        // compare price and recommended retail price
        if ($variationPrice != '' || $variationPrice != 0.00)
        {
            // if recommended retail price is set and less than retail price...
            if ($variationRrp > 0 && $variationPrice > $variationRrp)
            {
                //set recommended retail price as selling price
                $price = $variationRrp;
                //set retail price as recommended retail price price
                $rrp = $variationPrice;
            }
        }

        return array(
            'variationRetailPrice.price'            =>  $price,
            'variationRecommendedRetailPrice.price' =>  $rrp
        );
    }
}