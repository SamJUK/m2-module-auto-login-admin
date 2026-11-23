<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Test\Unit\Service;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Event\ManagerInterface;
use Magento\User\Model\User;
use PHPUnit\Framework\TestCase;

class LoginAdminTest extends TestCase
{
    public function testExecute(): void
    {
        $user = $this->createMock(User::class);
        $eventManager = $this->createMock(ManagerInterface::class);

        $storage = $this->createMock(Session::class);

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
