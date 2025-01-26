<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Test\Unit\Plugin;

use SamJUK\AutoLoginAdmin\Service\GetAdminByUsername;
use SamJUK\AutoLoginAdmin\Service\LoginAdmin;
use SamJUK\AutoLoginAdmin\Model\Config;
use SamJUK\AutoLoginAdmin\Plugin\Login;
use Magento\User\Api\Data\UserInterface;
use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    private $getAdminByUsername;
    private $loginAdmin;
    private $config;
    private $originalLogin;
    private $admin;

    public function setUp(): void
    {
        $this->admin = $this->createMock(UserInterface::class);
        $this->getAdminByUsername = $this->createMock(GetAdminByUsername::class);
        $this->loginAdmin = $this->createMock(LoginAdmin::class);
        $this->config = $this->createMock(Config::class);
        $this->originalLogin = $this->createMock(\Magento\Backend\Controller\Adminhtml\Auth\Login::class);
        $this->originalLogin->method('execute')
            ->willReturnCallback(function () {
                return true;
            });
    }

    public function testDisabledFeatureFlag()
    {
        $this->config->method('isModuleEnabled')
            ->willReturn(false);

        $this->originalLogin->expects($this->once())
            ->method('execute');

        $this->loginAdmin->expects($this->never())
            ->method('execute');

        (new Login(
            $this->getAdminByUsername,
            $this->loginAdmin,
            $this->config
        ))->aroundExecute(
            $this->originalLogin,
            [$this->originalLogin, 'execute']
        );
    }

    public function testEnabledFeatureFlag()
    {
        $this->config->method('isModuleEnabled')
            ->willReturn(true);

        $this->config->method('getTargetUserName')
            ->willReturn('admin');

        $this->admin->method('getId')
            ->willReturn(1);

        $this->originalLogin->expects($this->once())
            ->method('execute');

        $this->getAdminByUsername->expects($this->once())
            ->method('execute')
            ->willReturn($this->admin);

        $this->loginAdmin->expects($this->once())
            ->method('execute');

        (new Login(
            $this->getAdminByUsername,
            $this->loginAdmin,
            $this->config
        ))->aroundExecute(
            $this->originalLogin,
            [$this->originalLogin, 'execute']
        );
    }

    public function testSkipsLoginOnInvalidUser()
    {
        $this->config->method('isModuleEnabled')
            ->willReturn(true);

        $this->config->method('getTargetUserName')
            ->willReturn('admin');

        $this->admin->method('getId')
            ->willReturn(null);

        $this->originalLogin->expects($this->once())
            ->method('execute');

        $this->getAdminByUsername->method('execute')
            ->willReturn($this->admin);

            $this->loginAdmin->expects($this->never())
                ->method('execute');

        (new Login(
            $this->getAdminByUsername,
            $this->loginAdmin,
            $this->config
        ))->aroundExecute(
            $this->originalLogin,
            [$this->originalLogin, 'execute']
        );
    }
}
