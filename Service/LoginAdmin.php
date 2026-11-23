<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Service;

class LoginAdmin
{
    public function __construct(
        private \Magento\Backend\Model\Auth\Session $authStorage,
        private \Magento\Framework\Event\ManagerInterface $eventManager
    ) {
    }

    public function execute(\Magento\User\Model\User $admin): void
    {
        $this->authStorage->setUser($admin);
        $this->authStorage->processLogin();
        $this->eventManager->dispatch(
            'backend_auth_user_login_success',
            ['user' => $admin ]
        );
    }
}
