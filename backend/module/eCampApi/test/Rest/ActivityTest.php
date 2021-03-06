<?php

namespace eCamp\ApiTest\Rest;

use Doctrine\Common\DataFixtures\Loader;
use eCamp\Core\Entity\Activity;
use eCamp\Core\Entity\ActivityCategory;
use eCamp\Core\Entity\User;
use eCamp\CoreTest\Data\ActivityCategoryTestData;
use eCamp\CoreTest\Data\ActivityTestData;
use eCamp\CoreTest\Data\PeriodTestData;
use eCamp\CoreTest\Data\ScheduleEntryTestData;
use eCamp\CoreTest\Data\UserTestData;
use eCamp\LibTest\PHPUnit\AbstractApiControllerTestCase;

/**
 * @internal
 */
class ActivityTest extends AbstractApiControllerTestCase {
    /** @var Activity */
    protected $activity;

    /** @var ActivityCategory */
    protected $activityCategory;

    /** @var User */
    protected $user;

    private $apiEndpoint = '/api/activities';

    public function setUp(): void {
        parent::setUp();

        $userLoader = new UserTestData();
        $activityLoader = new ActivityTestData();
        $periodLoader = new PeriodTestData();
        $scheduleEntryLoader = new ScheduleEntryTestData();

        $loader = new Loader();
        $loader->addFixture($userLoader);
        $loader->addFixture($activityLoader);
        $loader->addFixture($periodLoader);
        $loader->addFixture($scheduleEntryLoader);
        $this->loadFixtures($loader);

        $this->user = $userLoader->getReference(UserTestData::$USER1);
        $this->activity = $activityLoader->getReference(ActivityTestData::$ACTIVITY1);
        $this->activityCategory = $activityLoader->getReference(ActivityCategoryTestData::$CATEGORY2);

        $this->authenticateUser($this->user);
    }

    public function testFetch() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activity->getId()}", 'GET');

        $this->assertResponseStatusCode(200);

        $expectedBody = <<<JSON
            {
                "id": "{$this->activity->getId()}",
                "title": "Activity1",
                "location" : ""
            }
JSON;

        $expectedLinks = <<<JSON
            {
                "self": {
                    "href": "http://{$this->host}{$this->apiEndpoint}/{$this->activity->getId()}"
                }
            }
JSON;
        $expectedEmbeddedObjects = ['camp', 'activityCategory', 'scheduleEntries', 'activityContents', 'campCollaborations']; // TODO discuss: wouldn't 'activityResponsibles' be more intuitive than 'campCollaborations'

        $this->verifyHalResourceResponse($expectedBody, $expectedLinks, $expectedEmbeddedObjects);
    }

    public function testFetchAll() {
        $campId = $this->activity->getCamp()->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&campId={$campId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&campId={$campId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activity->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    public function testFetchAllByPeriod() {
        $periodId = $this->activity->getCamp()->getPeriods()->get(0)->getId();
        $this->dispatch("{$this->apiEndpoint}?page_size=10&periodId={$periodId}", 'GET');

        $this->assertResponseStatusCode(200);

        $this->assertEquals(1, $this->getResponseContent()->total_items);
        $this->assertEquals(10, $this->getResponseContent()->page_size);
        $this->assertEquals("http://{$this->host}{$this->apiEndpoint}?page_size=10&periodId={$periodId}&page=1", $this->getResponseContent()->_links->self->href);
        $this->assertEquals($this->activity->getId(), $this->getResponseContent()->_embedded->items[0]->id);
    }

    /*
    TODO: add validator for title

    public function testCreateWithoutTitle() {
        $this->setRequestContent([
            'title' => '', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('isEmpty', $this->getResponseContent()->validation_messages->title);
    }
    */

    public function testCreateWithoutCategory() {
        $this->setRequestContent([
            'title' => 'Activity2',
            'activityCategoryId' => 'xxx', ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(422);
        $this->assertObjectHasAttribute('notFound', $this->getResponseContent()->validation_messages->activityCategoryId);
    }

    public function testCreateSuccess() {
        $this->setRequestContent([
            'title' => 'Activity2',
            'activityCategoryId' => $this->activity->getActivityCategory()->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}", 'POST');

        $this->assertResponseStatusCode(201);
        $this->assertEquals('Activity2', $this->getResponseContent()->title);
    }

    public function testUpdateSuccess() {
        $this->setRequestContent([
            'title' => 'Activity3',
            'activityCategoryId' => $this->activityCategory->getId(), ]);

        $this->dispatch("{$this->apiEndpoint}/{$this->activity->getId()}", 'PATCH');

        $this->assertResponseStatusCode(200);

        $this->assertEquals('Activity3', $this->getResponseContent()->title);
        $this->assertEquals($this->activityCategory->getId(), $this->getResponseContent()->_embedded->activityCategory->id);
    }

    public function testDelete() {
        $this->dispatch("{$this->apiEndpoint}/{$this->activity->getId()}", 'DELETE');

        $this->assertResponseStatusCode(204);

        $result = $this->getEntityManager()->find(Activity::class, $this->activity->getId());
        $this->assertNull($result);
    }
}
