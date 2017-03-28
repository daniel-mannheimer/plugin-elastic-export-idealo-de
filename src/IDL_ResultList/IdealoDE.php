<?php

namespace ElasticExportIdealoDE\IDL_ResultList;

use Plenty\Modules\Item\DataLayer\Contracts\ItemDataLayerRepositoryContract;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Item\DataLayer\Models\RecordList;

/**
 * Class IdealoDE
 * @package ElasticExportIdealoDE\IDL_ResultList
 */
class IdealoDE
{
    const IDEALO_DE = 121.00;

    /**
     * Creates and retrieves the extra needed data from ItemDataLayer.
     *
     * @param array $variationIds
     * @param KeyValue $settings
     * @param array $filter
     * @return RecordList|string
     */
    public function getResultList($variationIds, $settings, $filter = [])
    {
        if(is_array($variationIds) && count($variationIds) > 0)
        {
            $searchFilter = array(
                'variationBase.hasId' => array(
                    'id' => $variationIds
                )
            );

            if(array_key_exists('variationStock.netPositive' ,$filter))
            {
                $searchFilter['variationStock.netPositive'] = $filter['variationStock.netPositive'];
            }
            elseif(array_key_exists('variationStock.isSalable' ,$filter))
            {
                $searchFilter['variationStock.isSalable'] = $filter['variationStock.isSalable'];
            }

            $resultFields = array(
                'itemBase' => array(
                    'id',
                ),

                'variationBase' => array(
                    'id',
                    'customNumber',
                ),

                'itemPropertyList' => array(
                    'params' => array(),
                    'fields' => array(
                        'propertyId',
                        'propertyValue',
                    )
                ),

                'variationStock' => array(
                    'params' => array(
                        'type' => 'virtual'
                    ),
                    'fields' => array(
                        'stockNet'
                    )
                ),

                'variationRetailPrice' => array(
                    'params' => array(
                        'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : self::IDEALO_DE,
                    ),
                    'fields' => array(
                        'price',
                        'vatValue',
                    ),
                ),

                'variationRecommendedRetailPrice' => array(
                    'params' => array(
                        'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : self::IDEALO_DE,
                    ),
                    'fields' => array(
                        'price',    // uvp
                    ),
                ),

                'variationSpecialOfferRetailPrice' => array(
                    'params' => array(
                        'referrerId' => $settings->get('referrerId') ? $settings->get('referrerId') : self::IDEALO_DE,
                    ),
                    'fields' => array(
                        'retailPrice',
                    ),
                ),
            );

            $itemDataLayer = pluginApp(ItemDataLayerRepositoryContract::class);

            if($itemDataLayer instanceof ItemDataLayerRepositoryContract)
			{
				return $itemDataLayer->search($resultFields, $searchFilter);
			}
        }

        return '';
    }
}