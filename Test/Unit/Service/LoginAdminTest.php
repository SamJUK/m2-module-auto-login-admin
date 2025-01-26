<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Test\Unit\Service;

use Magento\User\Api\Data\UserInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Backend\Model\Auth\StorageInterface;
use PHPUnit\Framework\TestCase;

class LoginAdminTest extends TestCase
{

    public function testExecute()
    {
        $user = $this->createMock(UserInterface::class);
        $eventManager = $this->createMock(ManagerInterface::class);

        $storage = $this->getMockBuilder(StorageInterface::class)
            ->setMethods(array_merge(
                get_class_methods(StorageInterface::class),
                ['setUser']
            ))
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects($this->once())
            ->method('processLogin');

        $eventManager->expects($this->once())
            ->method('dispatch')
            ->with(
                'backend_auth_user_login_success',
                ['user' => $user]
            );

        (new \SamJUK\AutoLoginAdmin\Service\LoginAdmin(
            $storage,
            $eventManager
        ))->execute($user);
    }
}
