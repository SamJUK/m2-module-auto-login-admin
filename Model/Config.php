<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Model;

use Magento\Framework\App\State;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const XML_PATH_AUTO_LOGIN = 'samjuk_auto_login_admin/general/auto_login';
    public const XML_PATH_TARGET_USER_NAME = 'samjuk_auto_login_admin/general/username';
    public const XML_PATH_SKIP_PRODUCTION_MODE_CHECK = 'samjuk_auto_login_admin/general/skip_production_mode_check';
    
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly State $state
    ) { }

    public function isModuleEnabled()
    {
        return $this->isEnabled() && $this->isStoreInAValidMode();
    }

    public function isStoreInAValidMode()
    {
        return $this->state->getMode() !== State::MODE_PRODUCTION
            || $this->getSkipProductionModeCheck();
    }

    public function isEnabled()
    {
        return $this->getFlag(self::XML_PATH_AUTO_LOGIN);
    }

    public function getTargetUserName()
    {
        return $this->getValue(self::XML_PATH_TARGET_USER_NAME);
    }

    public function getSkipProductionModeCheck()
    {
        return $this->getFlag(self::XML_PATH_SKIP_PRODUCTION_MODE_CHECK);
    }

    private function getFlag($path, $scope = 'default', $scopeCode = null)
    {
        return (bool)$this->scopeConfig->isSetFlag(
            $path,
            $scope,
            $scopeCode
        );
    }

    private function getValue($path, $scope = 'default', $scopeCode = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            $scope,
            $scopeCode
        );
    }
}
