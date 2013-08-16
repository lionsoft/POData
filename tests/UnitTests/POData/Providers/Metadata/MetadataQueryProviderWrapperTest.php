<?php

namespace UnitTests\POData\Providers\Metadata;

use POData\Providers\Metadata\ResourceSet;
use POData\Providers\Metadata\ResourceType;
use POData\Providers\Metadata\ResourceProperty;
use POData\Providers\Metadata\ResourceTypeKind;
use POData\Providers\MetadataQueryProviderWrapper;
use POData\Configuration\DataServiceConfiguration;
use POData\Configuration\EntitySetRights;
use POData\Providers\Metadata\IDataServiceMetadataProvider;
use POData\Common\ODataException;

use UnitTests\POData\Facets\NorthWind1\NorthWindMetadata;

class MetadataQueryProviderWrapperTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testContainerNameAndNameSpace1()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuration
                                        false
                                        );

            $this->assertEquals($metaQueryProverWrapper->getContainerName(), 'NorthWindEntities');
            $this->assertEquals($metaQueryProverWrapper->getContainerNamespace(), 'NorthWind');
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());
        }
    }

    public function testResolveResourceSet1()
    {
        try {
            //Try to resolve invisible resource set
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );        
            $customerResourceSet = $metaQueryProverWrapper->resolveResourceSet('Customers');
            $this->assertNull($customerResourceSet);

            //Try to resolve visible resource set
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );                                    
            $configuration->setEntitySetAccessRule('Customers', EntitySetRights::ALL);
            $customerResourceSet = $metaQueryProverWrapper->resolveResourceSet('Customers');
            $this->assertNotNull($customerResourceSet);
            $this->assertEquals($customerResourceSet->getName(), 'Customers');
            $this->assertEquals($customerResourceSet->getResourceType()->getName(), 'Customer');
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testGetResourceSets1()
    {
        try {
            //Try to get all resource sets with non of the resouce sets are visible
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $resourceSets = $metaQueryProverWrapper->getResourceSets();
            $this->assertTrue(empty($resourceSets));

            //Try to get all resource sets after setting all resouce sets as visible
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $configuration->setEntitySetAccessRule('*', EntitySetRights::ALL);
            $resourceSets = $metaQueryProverWrapper->getResourceSets();
            $this->assertEquals(count($resourceSets), 5);
            //Try to resolve 'Customers' entity set, we should the resource set for it from cache as the above getResourceSets call caches all resource sets 
            $customerResourceSet = $metaQueryProverWrapper->resolveResourceSet('Customers');
            $this->assertNotNull($customerResourceSet);
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testResolveResourceType1()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            //Try to resolve non-existing type
            $type = $metaQueryProverWrapper->resolveResourceType('Customer1');
            $this->assertNull($type);
            $customerEntityType = $metaQueryProverWrapper->resolveResourceType('Customer');
            $this->assertNotNull($customerEntityType);
            $this->assertEquals($customerEntityType->getName(), 'Customer');
            $this->assertEquals($customerEntityType->getResourceTypeKind(), ResourceTypeKind::ENTITY);

            $addressCoomplexType = $metaQueryProverWrapper->resolveResourceType('Address');
            $this->assertNotNull($addressCoomplexType);
            $this->assertEquals($addressCoomplexType->getName(), 'Address');
            $this->assertEquals($addressCoomplexType->getResourceTypeKind(), ResourceTypeKind::COMPLEX);
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testGetTypes1()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $resourceTypes = $metaQueryProverWrapper->getTypes();
            $this->assertEquals(count($resourceTypes), 7);
            $orderEntityType = $metaQueryProverWrapper->resolveResourceType('Order');
            $this->assertNotNull($orderEntityType);
            $this->assertEquals($orderEntityType->getName(), 'Order');
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testGetDerivedTypes1()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $customerEntityType = $metaQueryProverWrapper->resolveResourceType('Customer');
            $this->assertNotNull($customerEntityType);
            $derivedTypes = $metaQueryProverWrapper->getDerivedTypes($customerEntityType);        
            $this->assertNull($derivedTypes);
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testHasDerivedTypes1()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $orderEntityType = $metaQueryProverWrapper->resolveResourceType('Order');
            $this->assertNotNull($orderEntityType);
            $hasDerivedTypes = $metaQueryProverWrapper->hasDerivedTypes($orderEntityType);        
            $this->assertFalse($hasDerivedTypes);
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testGetResourceAssociationSet1()
    {
        try {
            //Get the association set where resource set in both ends are visible
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $configuration->setEntitySetAccessRule('Customers', EntitySetRights::ALL);
            $configuration->setEntitySetAccessRule('Orders', EntitySetRights::ALL);
            $customersEntitySetWrapper = $metaQueryProverWrapper->resolveResourceSet('Customers');
            $this->assertNotNull($customersEntitySetWrapper);
            $customerEntityType = $metaQueryProverWrapper->resolveResourceType('Customer');
            $this->assertNotNull($customerEntityType);
            $ordersProperty = $customerEntityType->tryResolvePropertyTypeByName('Orders');
            $this->assertNotNull($ordersProperty);
            $associationSet = $metaQueryProverWrapper->getResourceAssociationSet($customersEntitySetWrapper, $customerEntityType, $ordersProperty);
            $this->assertNotNull($associationSet);
            $associationSetEnd1 = $associationSet->getEnd1();
            $this->assertNotNull($associationSetEnd1);
            $associationSetEnd2 = $associationSet->getEnd2();
            $this->assertNotNull($associationSetEnd2);
            $this->assertEquals($associationSetEnd1->getResourceSet()->getName(), 'Customers');
            $this->assertEquals($associationSetEnd2->getResourceSet()->getName(), 'Orders');
            $this->assertEquals($associationSetEnd1->getResourceType()->getName(), 'Customer');
            $this->assertEquals($associationSetEnd2->getResourceType()->getName(), 'Order');

            //Try to get the association set where resource set in one end is invisible
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration1($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $configuration->setEntitySetAccessRule('Customers', EntitySetRights::ALL);
            //Set orders entity set as invisible
            $configuration->setEntitySetAccessRule('Orders', EntitySetRights::NONE);
            $customersEntitySetWrapper = $metaQueryProverWrapper->resolveResourceSet('Customers');
            $this->assertNotNull($customersEntitySetWrapper);
            $customerEntityType = $metaQueryProverWrapper->resolveResourceType('Customer');
            $this->assertNotNull($customerEntityType);
            $ordersProperty = $customerEntityType->tryResolvePropertyTypeByName('Orders');
            $this->assertNotNull($ordersProperty);
            $associationSet = $metaQueryProverWrapper->getResourceAssociationSet($customersEntitySetWrapper, $customerEntityType, $ordersProperty);
            $this->assertNull($associationSet);
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testContainerNameAndNameSpace2()
    {
        try {
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration2($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );

            $exceptionThrown = false;
            try {
                $metaQueryProverWrapper->getContainerName();
            } catch (ODataException $exception) {
                $exceptionThrown = true;
                $this->assertEquals($exception->getMessage(), 'The value returned by IDataServiceMetadataProvider::getContainerName method must not be null or empty');
            }

            if (!$exceptionThrown) {
                $this->fail('An expected ODataException null container name has not been thrown');
            }

            $exceptionThrown = false;
            try {
                $metaQueryProverWrapper->getContainerNamespace();
            } catch (ODataException $exception) {
                $exceptionThrown = true;
                $this->assertEquals($exception->getMessage(), 'The value returned by IDataServiceMetadataProvider::getContainerNamespace method must not be null or empty');
            }

            if (!$exceptionThrown) {
                $this->fail('An expected ODataException null container namespace has not been thrown');
            }
            
        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }

    public function testGetResourceSets2()
    {

        //Try to get all resource sets with non of the resouce sets are visible
        $configuration = null;
        $metadataProvider = $this->_createMetadataAndConfiguration2($configuration);

	    $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
            $metadataProvider, //IDataServiceMetadataProvider implementation
            null, //IDataServiceQueryProvider implementation (set to null)
            $configuration, //Service configuuration
            false
        );

        $exceptionThrown = false;
        try {
            $metaQueryProverWrapper->getResourceSets();
        } catch(ODataException $exception) {
            $exceptionThrown = true;
            $this->assertEquals($exception->getMessage(), 'More than one entity set with the name \'Customers\' was found. Entity set names must be unique');
        }

        if (!$exceptionThrown) {
            $this->fail('An expected ODataException for entity set repetition has not been thrown');
        }


    }

    public function testGetTypes2()
    {
        try {
            //Try to get all resource sets with non of the resouce sets are visible
            $configuration = null;
            $metadataProvider = $this->_createMetadataAndConfiguration2($configuration);
            $metaQueryProverWrapper = new MetadataQueryProviderWrapper(
                                        $metadataProvider, //IDataServiceMetadataProvider implementation 
                                        null, //IDataServiceQueryProvider implementation (set to null)
                                        $configuration, //Service configuuration
                                        false
                                        );
            $exceptionThrown = false;
            try {
                $metaQueryProverWrapper->getTypes();
            } catch(ODataException $exception) {
                $exceptionThrown = true;                
                $this->assertEquals($exception->getMessage(), 'More than one entity type with the name \'Order\' was found. Entity type names must be unique.');
            }

            if (!$exceptionThrown) {
                $this->fail('An expected ODataException for entity type name repetition has not been thrown');
            }

        } catch (\Exception $exception) {
            $this->fail('An unexpected Exception has been raised' . $exception->getMessage());    
        }
    }
    
    protected function tearDown()
    {
    }

    /**
     * Creates a valid IDataServiceMetadataProvider implementation, and associated configuration
     * Note: This metadata is for positive testing
     * 
     * @param DataServiceConfiguration $configuration On return, this will hold reference to configuration object
     * 
     * @return IDataServiceMetadataProvider
     */
    private function _createMetadataAndConfiguration1(&$configuration)
    {
        $northWindMetadata = NorthWindMetadata::Create();
        $configuration = new DataServiceConfiguration($northWindMetadata);
        return $northWindMetadata;
    }

    /**
     * Creates a valid IDataServiceMetadataProvider implementation, and associated configuration
     * Note: This metadata is for negative testing
     * 
     * @param DataServiceConfiguration $configuration On return, this will hold reference to configuration object
     * 
     * @return IDataServiceMetadataProvider
     */
    private function _createMetadataAndConfiguration2(&$configuration)
    {
        $northWindMetadata = new NorthWindMetadata2();
        $configuration = new DataServiceConfiguration($northWindMetadata);
        return $northWindMetadata;
    }
}

class NorthWindMetadata2 implements IDataServiceMetadataProvider
{
    protected $_resourceSets = array();
    protected $_resourceTypes = array();
	
    public function __construct()
    {
        $customerEntityType = new ResourceType(new \ReflectionClass('UnitTests\POData\Facets\NorthWind1\Customer2'), ResourceTypeKind::ENTITY, 'Customer');
    	$this->_resourceTypes[] = $customerEntityType;

	    //Add the Order resource type twice
	    $orderEntityType = new ResourceType(new \ReflectionClass('UnitTests\POData\Facets\NorthWind1\Order2'), ResourceTypeKind::ENTITY, 'Order');
    	$this->_resourceTypes[] = $orderEntityType;
    	$this->_resourceTypes[] = $orderEntityType;
    	
    	$customersResourceSet = new ResourceSet('Customers', $customerEntityType);
    	//Add the customers resource set twice to the collection
    	$this->_resourceSets[] = $customersResourceSet;
    	$this->_resourceSets[] = $customersResourceSet;

	    $ordersResourceSet = new ResourceSet('Orders', $orderEntityType);
    	$this->_resourceSets[] = $ordersResourceSet;
    }

	//Begin Implementation of IDataServiceMetadataProvider
    public function getContainerName()
    {
    	return null;
    }

    public function getContainerNamespace()
    {
    	return null;
    }

    public function getResourceSets()
    {
    	return $this->_resourceSets;
    }

    public function getTypes()
    {
    	return $this->_resourceTypes;
    }

    public function resolveResourceSet($name)
    {
    }

    public function resolveResourceType($name)
    {
    }

    public function getDerivedTypes(ResourceType $resourceType)
    {
        return null;     
    }

    public function hasDerivedTypes(ResourceType $resourceType)
    {     
    }

    public function getResourceAssociationSet(ResourceSet $sourceResourceSet, ResourceType $sourceResourceType, ResourceProperty $targetResourceProperty)
    {
    }
}