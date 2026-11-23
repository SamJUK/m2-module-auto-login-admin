<?php

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Test\Unit\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use SamJUK\AutoLoginAdmin\Model\Config;

class ConfigTest extends TestCase
{
    private Config $config;
    private ScopeConfigInterface&MockObject $scopeConfig;
    private State&MockObject $state;

    public function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->state = $this->createMock(State::class);
        $this->config = new Config(
            $this->scopeConfig,
            $this->state
        );
    }

    public function testProductionModeIsInvalidWithoutSkipFlag(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK)->willReturn(false);
        $this->assertFalse($this->config->isStoreInAValidMode());
    }

    public function testProductionModelIsValidWithSkipFlag(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK)->willReturn(true);
        $this->assertTrue($this->config->isStoreInAValidMode());
    }

    public function testModuleFeatureFlagEnabledInDevelopment(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_DEVELOPER);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_AUTO_LOGIN)->willReturn(true);
        $this->assertTrue($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagDisabledInDevelopment(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_DEVELOPER);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_AUTO_LOGIN)->willReturn(false);
        $this->assertFalse($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagEnabledInProduction(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->willReturnCallback(
            fn (string $path): bool => $path === Config::XML_PATH_AUTO_LOGIN
        );

        $this->assertFalse($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagEnabledInProductionWithSkipFlag(): void
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);

        $this->scopeConfig->method('isSetFlag')->willReturnCallback(
            fn (string $path): bool => in_array(
                $path,
                [Config::XML_PATH_AUTO_LOGIN, Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK],
                true
            )
        );

        $this->assertTrue($this->config->isModuleEnabled());
    }
}
