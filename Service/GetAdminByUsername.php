<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Service;

class GetAdminByUsername
{
    public function __construct(
        private \Magento\User\Model\UserFactory $userFactory
    ) { }

    public function execute(string $username): \Magento\User\Api\Data\UserInterface
    {
        return $this->userFactory->create()->loadByUsername($username);
    }
}
