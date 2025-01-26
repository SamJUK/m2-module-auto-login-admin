<?php //phpcs:disable Generic.PHP.NoSilencedErrors

declare(strict_types=1);

namespace SamJUK\AutoLoginAdmin\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use SamJUK\AutoLoginAdmin\Model\Config;

class ConfigTest extends TestCase
{
    private $config;
    private $scopeConfig;
    private $state;

    public function setUp(): void
    {
        $this->scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $this->state = $this->createMock(\Magento\Framework\App\State::class);
        $this->config = new Config(
            $this->scopeConfig,
            $this->state
        );
    }

    public function testProductionModeIsInvalidWithoutSkipFlag()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK)->willReturn(false);
        $this->assertFalse($this->config->isStoreInAValidMode());
    }

    public function testProductionModelIsValidWithSkipFlag()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK)->willReturn(true);
        $this->assertTrue($this->config->isStoreInAValidMode());
    }

    public function testModuleFeatureFlagEnabledInDevelopment()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_DEVELOPER);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_AUTO_LOGIN)->willReturn(true);
        $this->assertTrue($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagDisabledInDevelopment()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_DEVELOPER);
        $this->scopeConfig->method('isSetFlag')->with(Config::XML_PATH_AUTO_LOGIN)->willReturn(false);
        $this->assertFalse($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagEnabledInProduction()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);
        $this->scopeConfig->method('isSetFlag')->willReturnCallback(fn($path) => @[
            Config::XML_PATH_AUTO_LOGIN => true,
            Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK => false
        ][$path] ?? null);

        $this->assertFalse($this->config->isModuleEnabled());
    }

    public function testModuleFeatureFlagEnabledInProductionWithSkipFlag()
    {
        $this->state->method('getMode')->willReturn(\Magento\Framework\App\State::MODE_PRODUCTION);

        $this->scopeConfig->method('isSetFlag')->willReturnCallback(fn($path) => @[
            Config::XML_PATH_AUTO_LOGIN => true,
            Config::XML_PATH_SKIP_PRODUCTION_MODE_CHECK => true
        ][$path] ?? null);

        $this->assertTrue($this->config->isModuleEnabled());
    }
}
