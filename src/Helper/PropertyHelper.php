<?php

namespace ElasticExportIdealoDE\Helper;

use Plenty\Modules\Item\Property\Contracts\PropertyMarketReferenceRepositoryContract;
use Plenty\Modules\Item\Property\Contracts\PropertyNameRepositoryContract;
use Plenty\Modules\Item\Property\Models\PropertyName;
use Plenty\Plugin\Log\Loggable;

class PropertyHelper
{
    use Loggable;

    const IDEALO_DE = 121.00;

    const PROPERTY_IDEALO_DIREKTKAUF    = 'CheckoutApproved';
    const PROPERTY_IDEALO_SPEDITION     = 'FulfillmentType:Spedition';
    const PROPERTY_IDEALO_PAKETDIENST   = 'FulfillmentType:Paketdienst';

    /**
     * @var array
     */
    private $itemFreeTextCache = [];

    /**
     * @var array
     */
    private $itemPropertyCache = [];

    /**
     * @var PropertyNameRepositoryContract
     */
    private $propertyNameRepository;

    /**
     * @var PropertyMarketReferenceRepositoryContract
     */
    private $propertyMarketReferenceRepository;


    /**
     * PropertyHelper constructor.
     *
     * @param PropertyNameRepositoryContract $propertyNameRepository
     * @param PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository
     */
    public function __construct(
        PropertyNameRepositoryContract $propertyNameRepository,
        PropertyMarketReferenceRepositoryContract $propertyMarketReferenceRepository)
    {
        $this->propertyNameRepository = $propertyNameRepository;
        $this->propertyMarketReferenceRepository = $propertyMarketReferenceRepository;
    }

    /**
     * Get free text.
     *
     * @param  array $variation
     * @return string
     */
    public function getFreeText($variation):string
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemFreeTextCache))
        {
            $freeText = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file' &&
                    $property['property']['valueType'] != 'empty')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], 'de');
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::IDEALO_DE);

                    // Skip properties which do not have the Component Id set
                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->componentId != 1)
                    {
                        continue;
                    }

                    if($property['property']['valueType'] == 'text')
                    {
                        if(is_array($property['texts']))
                        {
                            $freeText[] = $property['texts'][0]['value'];
                        }
                    }

                    if($property['property']['valueType'] == 'selection')
                    {
                        if(is_array($property['selection']))
                        {
                            $freeText[] = $property['selection'][0]['name'];
                        }
                    }
                }
            }

            $this->itemFreeTextCache[$variation['data']['item']['id']] = implode(' ', $freeText);
        }

        return $this->itemFreeTextCache[$variation['data']['item']['id']];
    }

    /**
     * Get property.
     *
     * @param  array $variation
     * @param  string $property
     * @return string|bool
     */
    public function getProperty($variation, string $property)
    {
        $itemPropertyList = $this->getItemPropertyList($variation);

        if(array_key_exists($property, $itemPropertyList))
        {
            if ($property == self::PROPERTY_IDEALO_DIREKTKAUF   ||
                $property == self::PROPERTY_IDEALO_SPEDITION    ||
                $property == self::PROPERTY_IDEALO_PAKETDIENST)
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
     * Get item properties for a given variation.
     *
     * @param  array $variation
     * @return array
     */
    private function getItemPropertyList($variation):array
    {
        if(!array_key_exists($variation['data']['item']['id'], $this->itemPropertyCache))
        {
            $list = array();

            foreach($variation['data']['properties'] as $property)
            {
                if(!is_null($property['property']['id']) &&
                    $property['property']['valueType'] != 'file')
                {
                    $propertyName = $this->propertyNameRepository->findOne($property['property']['id'], 'de');
                    $propertyMarketReference = $this->propertyMarketReferenceRepository->findOne($property['property']['id'], self::IDEALO_DE);

                    // Skip properties which do not have the External Component set up
                    if(!($propertyName instanceof PropertyName) ||
                        is_null($propertyName) ||
                        is_null($propertyMarketReference) ||
                        $propertyMarketReference->externalComponent == '0')
                    {
                        continue;
                    }

                    if($property['property']['valueType'] == 'text')
                    {
                        if(is_array($property['texts']))
                        {
                            $list[(string)$propertyMarketReference->externalComponent] = $property['texts'][0]['value'];
                        }
                    }

                    if($property['property']['valueType'] == 'selection')
                    {
                        if(is_array($property['selection']))
                        {
                            $list[$propertyMarketReference->externalComponent] = $property['selection'][0]['name'];
                        }
                    }

                    if($property['property']['valueType'] == 'empty')
                    {
                        $list[$propertyMarketReference->externalComponent] = $propertyMarketReference->externalComponent;
                    }
                }
            }

            $this->itemPropertyCache[$variation['data']['item']['id']] = $list;
        }

        return $this->itemPropertyCache[$variation['data']['item']['id']];
    }
}