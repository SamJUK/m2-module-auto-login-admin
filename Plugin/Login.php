<?php

namespace SamJUK\AutoLoginAdmin\Plugin;

use Magento\Backend\Controller\Adminhtml\Auth\Login as BaseLogin;

class Login
{
    public function __construct(
        private \SamJUK\AutoLoginAdmin\Service\GetAdminByUsername $getAdminByUsername,
        private \SamJUK\AutoLoginAdmin\Service\LoginAdmin $loginAdmin,
        private \SamJUK\AutoLoginAdmin\Model\Config $config
    ) { }

    public function aroundExecute(BaseLogin $subject, callable $proceed)
    {
        if (!$this->config->isModuleEnabled()) {
            return $proceed();
        }

        $user = $this->getAdminByUsername->execute(
            $this->config->getTargetUserName()
        );

        if ($user->getId()) {
            $this->loginAdmin->execute($user);
        }

        return $proceed();
    }
}
