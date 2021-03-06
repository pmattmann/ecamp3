<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\CoreTest\Data\MaterialListTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class MaterialListTest extends AbstractApiControllerTestCase {
    /** @var MaterialList */
    protected $materialList;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/material-lists';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $materialListLoader = new MaterialListTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($materialListLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->materialList = $materialListLoader->getReference(MaterialListTestData::$MATERIALLIST1);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->materialList->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->materialList->getId()}",
                "name": "MaterialList1"
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->materialList->getId()}"
                }, 
                "materialItems": {
                    "href": "http://{$this->host}/api/material-items?materialListId={$this->materialList->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = [];

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }
}
