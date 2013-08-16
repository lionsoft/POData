<?php

namespace UnitTests\POData\Facets\NorthWind2;

use POData\Configuration\EntitySetRights;
use POData\IDataService;
use POData\IRequestHandler;
use POData\Configuration\DataServiceProtocolVersion;
use POData\Configuration\DataServiceConfiguration;
use POData\IServiceProvider;
use POData\DataService;


class NorthWindDataService1 extends DataService1 implements IServiceProvider
{
    private $_northWindMetadata = null;
    private $_northWindQueryProvider = null;
    
    /**
     * This method is called only once to initialize service-wide policies
     * 
     * @param DataServiceConfiguration $config
     */
    public function initializeService(DataServiceConfiguration &$config)
    {
        $config->setEntitySetPageSize('*', 5);
        $config->setEntitySetAccessRule('*', EntitySetRights::ALL);
        $config->setAcceptCountRequests(true);
        $config->setAcceptProjectionRequests(true);
        $config->setMaxDataServiceVersion(DataServiceProtocolVersion::V3);
    }

    /**
     * 
     * @see library/POData.IServiceProvider::getService()
     * 
     * @return object
     */
    public function getService($serviceType)
    {
        if ($serviceType === 'IDataServiceMetadataProvider') {
            if (is_null($this->_northWindMetadata)) {
                $this->_northWindMetadata = NorthWindMetadata::Create();
            }

            return $this->_northWindMetadata;
        } else if ($serviceType === 'IDataServiceQueryProvider') {
            if (is_null($this->_northWindQueryProvider)) {
                $this->_northWindQueryProvider = new NorthWindQueryProvider1();
            }

            return $this->_northWindQueryProvider;
        } else if ($serviceType === 'IDataServiceStreamProvider') {
            return new NorthWindStreamProvider2();
        }

        return null;
    }

    /**
     * This method will be called to verify that DSExpressionProvider is 
     * implemented by the end-developer or not
     * 
     * @return object
     */
    public function &getExpressionProvider()
    {
    	return null;
    }
}