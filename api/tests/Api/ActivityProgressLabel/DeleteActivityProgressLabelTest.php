<?php

namespace App\Tests\Api\ActivityProgressLabel;

use App\Entity\ActivityProgressLabel;
use App\Tests\Api\ECampApiTestCase;

/**
 * @internal
 */
class DeleteActivityProgressLabelTest extends ECampApiTestCase {
    public function testDeleteActivityProgressLabelIsDeniedForAnonymousUser() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createBasicClient()->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId());
        $this->assertResponseStatusCodeSame(401);
        $this->assertJsonContains([
            'code' => 401,
            'message' => 'JWT Token not found',
        ]);
    }

    public function testDeleteActivityProgressLabelIsDeniedForUnrelatedUser() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user4unrelated']->getEmail()])
            ->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityProgressLabelIsDeniedForInactiveCollaborator() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user5inactive']->getEmail()])
            ->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;

        $this->assertResponseStatusCodeSame(404);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Not Found',
        ]);
    }

    public function testDeleteActivityProgressLabelIsDeniedForGuest() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user3guest']->getEmail()])
            ->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;

        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'title' => 'An error occurred',
            'detail' => 'Access Denied.',
        ]);
    }

    public function testDeleteActivityProgressLabelIsAllowedForMember() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials(['email' => static::$fixtures['user2member']->getEmail()])
            ->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId())
        ;
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ActivityProgressLabel::class)->find($activityProgressLabel->getId()));
    }

    public function testDeleteActivityProgressLabelIsAllowedForManager() {
        $activityProgressLabel = static::$fixtures['activityProgressLabel1'];
        static::createClientWithCredentials()->request('DELETE', '/activity_progress_labels/'.$activityProgressLabel->getId());
        $this->assertResponseStatusCodeSame(204);
        $this->assertNull($this->getEntityManager()->getRepository(ActivityProgressLabel::class)->find($activityProgressLabel->getId()));
    }
}
