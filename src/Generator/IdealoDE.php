<?php

namespace ElasticExportIdealoDE\Generator;

use ElasticExport\Helper\ElasticExportCoreHelper;
use Plenty\Modules\DataExchange\Contracts\CSVPluginGenerator;
use Plenty\Modules\Helper\Services\ArrayHelper;
use Plenty\Modules\Item\DataLayer\Models\Record;
use Plenty\Modules\Item\DataLayer\Models\RecordList;
use Plenty\Modules\DataExchange\Models\FormatSetting;
use Plenty\Modules\Helper\Models\KeyValue;
use Plenty\Modules\Order\Shipping\Models\DefaultShipping;
use Plenty\Modules\Order\Payment\Method\Models\PaymentMethod;
use Plenty\Modules\Item\Property\Contracts\PropertySelectionRepositoryContract;
use Plenty\Modules\Item\Property\Models\PropertySelection;
use Plenty\Plugin\Log\Loggable;

/**
 * Class IdealoDE
 * @package ElasticExportIdealoDE\Generator
 */
class IdealoDE extends CSVPluginGenerator
{
	use Loggable;

    const IDEALO_DE = 121.00;
    const IDEALO_CHECKOUT = 121.02;

    const DEFAULT_PAYMENT_METHOD = 'vorkasse';

    const SHIPPING_COST_TYPE_FLAT = 'flat';
    const SHIPPING_COST_TYPE_CONFIGURATION = 'configuration';
    const PROPERTY_IDEALO_DIREKTKAUF = 'CheckoutApproved';

    /**
     * @var ElasticExportCoreHelper $elasticExportHelper
     */
    private $elasticExportHelper;


    /**
     * PropertySelectionRepositoryContract $propertySelectionRepository
     */
    private $propertySelectionRepository;

    /**
     * @var array
     */
    private $itemPropertyCache = [];

    /**
     * @var ArrayHelper $arrayHelper
     */
    private $arrayHelper;

    /**
     * @var array
     */
    private $usedPaymentMethods = [];

    /**
     * @var array
     */
    private $defaultShippingList = [];

    /**
     * @var array $idlVariations
     */
    private $idlVariations = array();

    /**
     * IdealoGenerator constructor.
     *
     * @param ArrayHelper $arrayHelper
     * @param PropertySelectionRepositoryContract $propertySelectionRepository
     */
    public function __construct(
        ArrayHelper $arrayHelper,
        PropertySelectionRepositoryContract $propertySelectionRepository
    )
    {
        $this->arrayHelper = $arrayHelper;
        $this->propertySelectionRepository = $propertySelectionRepository;
    }

    /**
     * @param array $resultList
     * @param array $formatSettings
     * @param array $filter
     */
    protected function generatePluginContent($resultList, array $formatSettings = [], array $filter = [])
    {
        $this->elasticExportHelper = pluginApp(ElasticExportCoreHelper::class);

        if(is_array($resultList['documents']) && count($resultList['documents']) > 0)
        {
            $settings = $this->arrayHelper->buildMapFromObjectList($formatSettings, 'key', 'value');

            $this->setDelimiter("	"); // tab not space!

            $this->addCSVContent($this->head($settings));

            //Create a List with all VariationIds
            $variationIdList = array();

            foreach($resultList['documents'] as $key => $variation)
            {
				$attributes = $this->elasticExportHelper->getAttributeValueSetShortFrontendName($variation, $settings, '|');

				// skip main variations without attributes
				if(strlen($attributes) <= 0)
				{
					$this->getLogger(__METHOD__)
						->setReferenceType('variationId')
						->setReferenceValue($variation['data']['variation']['id'])
						->info('ElasticExportIdealoDE::item.itemMainVariationAttributeNameError');

					unset($resultList['documents'][$key]);
					continue;
				}

                $variationIdList[] = $variation['id'];
            }

            //Get the missing fields in ES from IDL(ItemDataLayer)
            if(is_array($variationIdList) && count($variationIdList) > 0)
            {
                /**
                 * @var \ElasticExportIdealoDE\IDL_ResultList\IdealoDE $idlResultList
                 */
                $idlResultList = pluginApp(\ElasticExportIdealoDE\IDL_ResultList\IdealoDE::class);
                $idlResultList = $idlResultList->getResultList($variationIdList, $settings, $filter);
            }

            //Creates an array with the variationId as key to surpass the sorting problem
            if(isset($idlResultList) && $idlResultList instanceof RecordList)
            {
            	try
				{
					$this->createIdlArray($idlResultList);
				}
				catch(\Exception $exception)
				{
					$this->getLogger(__METHOD__)->error('itemDataLayerError', $exception->getMessage());
				}
            }

            // Initiate the variables needed for grouping variations
            $currentItemId = null;
            $previousItemId = null;
            $variations = array();

            // Filter and create the grouped variations array
            foreach($resultList['documents'] as $variation)
            {
                if(!array_key_exists($variation['id'], $this->idlVariations))
                {
                    continue;
                }

                // Check if it's the first item from the resultList
                if ($currentItemId === null)
                {
                    $previousItemId = $variation['data']['item']['id'];
                }
                $currentItemId = $variation['data']['item']['id'];

                // Check if it's the same item and add it to the grouper
                if ($currentItemId == $previousItemId)
                {
                    $variations[] = $variation;
                }
                else
                {
					$this->constructData($settings, $variations);

                    // Pass the items to the CSV printer
                    $variations = array();
                    $variations[] = $variation;
                    $previousItemId = $variation['data']['item']['id'];
                }
            }

            // Write the last batch of variations
            if (is_array($variations) && count($variations) > 0)
            {
                $this->constructData($settings, $variations);
            }
        }
    }

    /**
     * Creates the Header of the CSV file.
     *
     * @param KeyValue $settings
     * @return array
     */
    private function head(KeyValue $settings):array
    {
        $data = [
            'article_id',
            'deeplink',
            'name',
            'short_description',
            'description',
            'article_no',
            'producer',
            'model',
            'availability',
            'ean',
            'isbn',
            'fedas',
            'warranty',
            'price',
            'price_old',
            'weight',
            'category1',
            'category2',
            'category3',
            'category4',
            'category5',
            'category6',
            'category_concat',
            'image_url_preview',
            'image_url',
            'base_price',
            'free_text_field',
            'checkoutApproved',
            'itemsInStock',
            'fulfillmentType',
            'twoManHandlingPrice',
            'disposalPrice'
        ];

        /**
         * If the shipping cost type is configuration, all payment methods will be taken as available payment methods from the chosen
         * default shipping configuration.
         */
        if($settings->get('shippingCostType') == self::SHIPPING_COST_TYPE_CONFIGURATION)
        {
            $paymentMethods = $this->elasticExportHelper->getPaymentMethods($settings);

            $defaultShipping = $this->elasticExportHelper->getDefaultShipping($settings);

            if($defaultShipping instanceof DefaultShipping)
            {
                foreach([$defaultShipping->paymentMethod2, $defaultShipping->paymentMethod3] as $paymentMethodId)
                {
                    if(count($this->usedPaymentMethods) == 0 && array_key_exists($paymentMethodId, $paymentMethods))
                    {
                        $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                        $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                    }
                    elseif(array_key_exists($paymentMethodId, $paymentMethods) && count($this->usedPaymentMethods) == 1
                        && ($this->usedPaymentMethods[$defaultShipping->id][0]->getAttributes()['id'] != $paymentMethodId))
                    {
                        $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                        $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                    }
                    elseif(array_key_exists($paymentMethodId, $paymentMethods) && count($this->usedPaymentMethods) == 2
                        && ($this->usedPaymentMethods[$defaultShipping->id][0]->getAttributes()['id'] != $paymentMethodId))
                    {
                        $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                        $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                    }
                }
            }
        }

        /**
         * If nothing is checked at the elastic export settings regarding the shipping cost type,
         * all payment methods within both default shipping configurations will be taken as available payment methods.
         */
        elseif($settings->get('shippingCostType') == 1)
        {
            $paymentMethods = $this->elasticExportHelper->getPaymentMethods($settings);

            $this->defaultShippingList = $this->elasticExportHelper->getDefaultShippingList();

            foreach($this->defaultShippingList as $defaultShipping)
            {
                if($defaultShipping instanceof DefaultShipping)
                {
                    foreach([$defaultShipping->paymentMethod2, $defaultShipping->paymentMethod3] as $paymentMethodId)
                    {
                        if(count($this->usedPaymentMethods) == 0 && array_key_exists($paymentMethodId, $paymentMethods))
                        {
                            $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                            $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                        }
                        elseif(array_key_exists($paymentMethodId, $paymentMethods) && count($this->usedPaymentMethods) == 1
                            && $this->usedPaymentMethods[1][0]->getAttributes()['id'] != $paymentMethodId)
                        {
                            $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                            $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                        }

                        elseif(array_key_exists($paymentMethodId, $paymentMethods) && count($this->usedPaymentMethods) == 2
                            && ($this->usedPaymentMethods[1][0]->getAttributes()['id'] != $paymentMethodId && $this->usedPaymentMethods[2][0]->getAttributes()['id'] != $paymentMethodId))
                        {
                            $data[] = $paymentMethods[$paymentMethodId]->getAttributes()['name'];
                            $this->usedPaymentMethods[$defaultShipping->id][] = $paymentMethods[$paymentMethodId];
                        }
                    }
                }
            }
        }

        if(count($this->usedPaymentMethods) <= 0 || $settings->get('shippingCostType') == self::SHIPPING_COST_TYPE_FLAT)
        {
            $data[] = self::DEFAULT_PAYMENT_METHOD;
        }

        return $data;
    }

    /**
     * Creates the item row and prints it into the CSV file.
     *
     * @param KeyValue $settings
     * @param array $variationGroup
     */
    private function constructData(KeyValue $settings, $variationGroup)
    {
        foreach($variationGroup as $variation)
        {
			try
			{
				$this->buildRow($settings, $variation);
			}
			catch(\Exception $exception)
			{
				$this->getLogger(__METHOD__)->error('ElasticExportIdealoDE::item.itemExportError', $exception->getMessage());
			}
		}
    }

	/**
	 * Creates the item row and prints it into the CSV file.
	 *
	 * @param KeyValue $settings
	 * @param array $item
	 */
    private function buildRow(KeyValue $settings, $item)
	{
		// get price and rrp
		$price = $this->idlVariations[$item['id']]['variationRetailPrice.price'];
		$rrp = $this->elasticExportHelper->getRecommendedRetailPrice($this->idlVariations[$item['id']]['variationRecommendedRetailPrice.price'], $settings);

		// compare price and rrp
		$price = $price <= 0 ? $rrp : $price;
		$rrp = $rrp <= $price ? 0 : $rrp;

		// get variation name
		$variationName = $this->elasticExportHelper->getAttributeValueSetShortFrontendName($item, $settings);

		// calculate stock
		$stock = $this->getStock($item);

		$checkoutApproved = $this->getProperty($item, 'CheckoutApproved');

		if(is_null($checkoutApproved) || strlen($checkoutApproved) <= 0)
		{
			$checkoutApproved = 'false';
		}
		else
		{
			$checkoutApproved = 'true';
		}

		$data = [
			'article_id' 		=> '',
			'deeplink' 			=> $this->elasticExportHelper->getUrl($item, $settings, true, false),
			'name' 				=> $this->elasticExportHelper->getName($item, $settings) . (strlen($variationName) ? ' ' . $variationName : ''),
			'short_description' => $this->elasticExportHelper->getPreviewText($item, $settings),
			'description' 		=> $this->elasticExportHelper->getDescription($item, $settings),
			'article_no' 		=> $this->idlVariations[$item['id']]['variationBase.customNumber'],
			'producer' 			=> $this->elasticExportHelper->getExternalManufacturerName((int)$item['data']['item']['manufacturer']['id']),
			'model' 			=> $item['data']['variation']['model'],
			'availability' 		=> $this->elasticExportHelper->getAvailability($item, $settings),
			'ean'	 			=> $this->elasticExportHelper->getBarcodeByType($item, $settings->get('barcode')),
			'isbn' 				=> $this->elasticExportHelper->getBarcodeByType($item, ElasticExportCoreHelper::BARCODE_ISBN),
			'fedas' 			=> $item['data']['item']['amazonFedas'],
			'warranty' 			=> '',
			'price' 			=> number_format((float)$price, 2, '.', ''),
			'price_old' 		=> number_format((float)$rrp, 2, '.', ''),
			'weight' 			=> $item['data']['variation']['weightG'],
			'category1' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 1),
			'category2' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 2),
			'category3' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 3),
			'category4' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 4),
			'category5' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 5),
			'category6' 		=> $this->elasticExportHelper->getCategoryBranch((int)$item['data']['defaultCategories'][0]['id'], $settings, 6),
			'category_concat' 	=> $this->elasticExportHelper->getCategory((int)$item['data']['defaultCategories'][0]['id'], $settings->get('lang'), $settings->get('plentyId')),
			'image_url_preview' => $this->elasticExportHelper->getMainImage($item, $settings, 'preview'),
			'image_url' 		=> $this->elasticExportHelper->getMainImage($item, $settings, 'normal'),
			'base_price' 		=> $this->elasticExportHelper->getBasePrice($item, $this->idlVariations[$item['id']]),
			'free_text_field'   => $this->getFreeText($this->idlVariations[$item['id']]),
			'checkoutApproved'	=> $checkoutApproved,
		];

		/**
		 * if the article is available for idealo DK further fields will be set depending on the properties of the article.
		 *
		 * Be sure to set the price in twoManHandlingPrice and disposalPrice with a dot instead of a comma for idealo DK
		 * will only except it that way.
		 *
		 * The properties twoManHandlingPrice and disposalPrice will also only be set if the property fulfillmentType is 'Spedition'
		 * otherwise these two properties will be ignored.
		 */
		if($checkoutApproved == 'true')
		{
			$data['article_id'] = $this->elasticExportHelper->generateSku($item['id'], self::IDEALO_CHECKOUT, 0, (string)$this->filterAndGetVariationSku($item));
			$data['itemsInStock'] = $stock;
			$fulfillmentType = $this->getProperty($item, 'FulfillmentType:Spedition');

			if(!is_null($fulfillmentType) || strlen($fulfillmentType) > 0)
			{
				$fulfillmentType = 'Spedition';
			}
			else
			{
				$full = $this->getProperty($item, 'FulfillmentType:Paketdienst');
				$fulfillmentType = is_null($full) || strlen($full) <= 0 ? '' : 'Paketdienst';
			}

			$data['fulfillmentType'] = $fulfillmentType;

			if($data['fulfillmentType'] == 'Spedition')
			{
				$twoManHandling = $this->getProperty($item, 'TwoManHandlingPrice');
				$twoManHandling = str_replace(",", '.', $twoManHandling);
				$twoManHandling = number_format((float)$twoManHandling, 2, ',', '');
				$disposal = $this->getProperty($item, 'DisposalPrice');
				$disposal = str_replace(",", '.', $disposal);
				$disposal = number_format((float)$disposal, 2, ',', '');

				$twoManHandling > 0 ?
					$data['twoManHandlingPrice'] = $twoManHandling : $data['twoManHandlingPrice'] = '';

				if($twoManHandling > 0)
				{
					$disposal > 0 ?
						$data['disposalPrice'] = $disposal : $data['disposalPrice'] = '';
				}
				else
				{
					$data['disposalPrice'] = '';
				}
			}
			else
			{
				$data['twoManHandlingPrice'] = '';
				$data['disposalPrice'] = '';
			}
		}
		else
		{
			$data['article_id'] = $this->elasticExportHelper->generateSku($item['id'], self::IDEALO_DE, 0, (string)$this->filterAndGetVariationSku($item));
			$data['itemsInStock'] = '';
			$data['fulfillmentType'] = '';
			$data['twoManHandlingPrice'] = '';
			$data['disposalPrice'] = '';
		}

		if(count($this->usedPaymentMethods) == 1)
		{
			foreach($this->usedPaymentMethods as $paymentMethod)
			{
				foreach($paymentMethod as $method)
				{
					$name = $method->getAttributes()['name'];
					$cost = $this->elasticExportHelper->getShippingCost($item, $settings, $method->id);
					$data[$name] = number_format((float)$cost, 2, '.', '');
				}
			}
		}
		elseif(count($this->usedPaymentMethods) > 1)
		{
			foreach($this->usedPaymentMethods as $defaultShipping => $paymentMethod)
			{
				foreach ($paymentMethod as $method)
				{
					$name = $method->getAttributes()['name'];
					$cost = $this->elasticExportHelper->calculateShippingCost(
						$item['id'],
						$this->defaultShippingList[$defaultShipping]->shippingDestinationId,
						$this->defaultShippingList[$defaultShipping]->referrerId,
						$method->id);
					$data[$name] = number_format((float)$cost, 2, '.', '');
				}
			}
		}
		elseif(count($this->usedPaymentMethods) <= 0 && $settings->get('shippingCostType') == self::SHIPPING_COST_TYPE_FLAT)
		{
			$data[self::DEFAULT_PAYMENT_METHOD] = $settings->get('shippingCostFlat');
		}
		else
		{
			$data[self::DEFAULT_PAYMENT_METHOD] = 0.00;
		}

		// Get the values and print them in the CSV file
		$this->addCSVContent(array_values($data));
	}

    /**
     * Get free text.
     *
     * @param  array $item
     * @return string
     */
    private function getFreeText($item):string
    {
        $characterMarketComponentList = $this->elasticExportHelper->getItemCharactersByComponent($this->idlVariations[$item['id']], self::IDEALO_DE, 1);

        $freeText = [];

        if(count($characterMarketComponentList))
        {
            foreach($characterMarketComponentList as $data)
            {
                if((string) $data['characterValueType'] != 'file' && (string) $data['characterValueType'] != 'empty')
                {
                    $freeText[] = (string) $data['characterValue'];
                }
            }
        }

        return implode(' ', $freeText);
    }

    /**
     * Get property.
     *
     * @param  array $item
     * @param  string $property
     * @return string|bool
     */
    private function getProperty($item, string $property)
    {
        $itemPropertyList = $this->getItemPropertyList($item, 121.00);

        if(array_key_exists($property, $itemPropertyList))
        {
            if ($property == self::PROPERTY_IDEALO_DIREKTKAUF)
            {
                return true;
            }
            else
            {
                return $itemPropertyList[$property];
            }
        }

        return '';
    }

    /**
     * Get item properties.
     *
     * @param 	array $item
     * @param   float $marketId
     * @return  array<string,string>
     */
    private function getItemPropertyList($item, float $marketId):array
    {
        if(!array_key_exists($item['id'], $this->itemPropertyCache))
        {
            $characterMarketComponentList = $this->elasticExportHelper->getItemCharactersByComponent($this->idlVariations[$item['id']], $marketId);

            $list = [];

            if(count($characterMarketComponentList))
            {
                foreach($characterMarketComponentList as $data)
                {
                    if((string) $data['characterValueType'] != 'file')
                    {
                        if((string) $data['characterValueType'] == 'selection')
                        {
                            $propertySelection = $this->propertySelectionRepository->findOne((int) $data['characterValue'], 'de');
                            if($propertySelection instanceof PropertySelection)
                            {
                                $list[(string) $data['externalComponent']] = (string) $propertySelection->name;
                            }
                        }
                        else
                        {
                            $list[(string) $data['externalComponent']] = (string) $data['characterValue'];
                        }

                    }
                }
            }

            $this->itemPropertyCache[$item['id']] = $list;
        }

        return $this->itemPropertyCache[$item['id']];
    }

    /**
     * Get the Variation Sku from the Skus array.
     *
     * @param array $item
     * @return null|string
     */
    private function filterAndGetVariationSku($item)
    {
        // get the sku from the skus array
        if (isset($item['data']['skus']) && count($item['data']['skus']) > 0)
        {
            return array_shift($item['data']['skus'])['sku'];
        }

        return null;
    }

    /**
     * Creates an array with the rest of data needed from the ItemDataLayer.
     *
     * @param RecordList $idlResultList
     */
    private function createIdlArray($idlResultList)
    {
        if($idlResultList instanceof RecordList)
        {
            foreach($idlResultList as $idlVariation)
            {
                if($idlVariation instanceof Record)
                {
                    $this->idlVariations[$idlVariation->variationBase->id] = [
                        'itemBase.id' => $idlVariation->itemBase->id,
                        'variationBase.id' => $idlVariation->variationBase->id,
                        'variationBase.customNumber' => $idlVariation->variationBase->customNumber,
                        'itemPropertyList' => $idlVariation->itemPropertyList,
                        'variationStock.stockNet' => $idlVariation->variationStock->stockNet,
                        'variationRetailPrice.price' => $idlVariation->variationRetailPrice->price,
                        'variationRetailPrice.vatValue' => $idlVariation->variationRetailPrice->vatValue,
                        'variationRecommendedRetailPrice.price' => $idlVariation->variationRecommendedRetailPrice->price,
                        'variationSpecialOfferRetailPrice.retailPrice' => $idlVariation->variationSpecialOfferRetailPrice->retailPrice
                    ];
                }
            }
        }
    }

	/**
	 * Calculates the stock based depending on different limits.
	 *
	 * @param array $item
	 * @return int
	 */
    private function getStock($item)
	{
		// get stock
		if($item['data']['variation']['stockLimitation'] == 2)
		{
			$stock = 999;
		}
		elseif($item['data']['variation']['stockLimitation'] == 1 && $this->idlVariations[$item['id']]['variationStock.stockNet'] > 0)
		{
			if($this->idlVariations[$item['id']]['variationStock.stockNet'] > 999)
			{
				$stock = 999;
			}
			else
			{
				$stock = $this->idlVariations[$item['id']]['variationStock.stockNet'];
			}
		}
		elseif($item['data']['variation']['stockLimitation'] == 0)
		{
			if($this->idlVariations[$item['id']]['variationStock.stockNet'] > 999)
			{
				$stock = 999;
			}
			else
			{
				if($this->idlVariations[$item['id']]['variationStock.stockNet'] > 0)
				{
					$stock = $this->idlVariations[$item['id']]['variationStock.stockNet'];
				}
				else
				{
					$stock = 0;
				}
			}
		}
		else
		{
			$stock = 0;
		}

		return $stock;
	}
}