<?php
/**
 * @category    CleatSquad
 * @package     CleatSquad_Smarty
 * @copyright   Copyright (c) 2021 CleatSquad, Inc. (https://www.cleatsquad.com)
 */
declare(strict_types=1);

namespace CleatSquad\Smarty\Test\Unit\Framework\View;

use CleatSquad\Smarty\Framework\View\TemplateEngine\Smarty as SmartyTemplateEngine;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\State;
use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Smarty;

/**
 * Class SmartyTest
 * @package CleatSquad\Smarty\Test\Unit\Framework\View
 */
class SmartyTest extends TestCase
{
    /**
     * Constant define test prop value
     */
    const TEST_PROP_VALUE = 'TEST_PROP_VALUE';

    /**
     * @var Smarty|MockObject
     */
    private $smartyEngineMock;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var State|MockObject
     */
    private $stateMock;

    /**
     * @var DirectoryList|MockObject
     */
    private $directoryListMock;

    /**
     * @var SmartyTemplateEngine
     */
    private $smartyEngine;

    /**
     * Create a Smarty template engine to test.
     */
    protected function setUp(): void
    {
        $this->smartyEngineMock = $this->getMockForAbstractClass(Smarty::class);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->stateMock = $this->createMock(\Magento\Framework\App\State::class);
        $this->directoryListMock = $this->createMock(DirectoryList::class);

        $this->smartyEngine = (new ObjectManager($this))->getObject(
            SmartyTemplateEngine::class,
            [
                'smarty' => $this->smartyEngineMock,
                'scopeConfig' => $this->scopeConfigMock,
                'appState' => $this->stateMock,
                'directoryList' => $this->directoryListMock
            ]
        );

    }

    /**
     * Test the render() function with a very simple .tpl file.
     *
     * Note: the call() function will be covered because simple.tpl has a call to the block.
     */
    public function testRender()
    {
        $blockMock = $this->getMockBuilder(
            Template::class
        )->setMethods(
            ['testMethod']
        )->disableOriginalConstructor()
            ->getMock();

        $blockMock->expects($this->once())->method('testMethod');
        $blockMock->property = self::TEST_PROP_VALUE;

        $filename = __DIR__ . '/_files/simple.tpl';

        $actualOutput = $this->smartyEngine->render($blockMock, $filename);

        $expectedOutput = '<html>'. self::TEST_PROP_VALUE . '</html>' . PHP_EOL;
        $this->assertSame($expectedOutput, $actualOutput, 'tpl file did not render correctly');
    }

    /**
     * Test the render() function with a nonexistent filename.
     *
     * Expect an exception if the specified file does not exist.
     */
    public function testRenderException()
    {
        $message = 'We can\'t load the template This_is_not_a_file.';
        $exception = new \Exception($message);

        $blockMock = $this->getMockBuilder(
            Template::class
        )->setMethods(
            ['testMethod']
        )->disableOriginalConstructor()
            ->getMock();

        $filename = 'This_is_not_a_file';
        $this->expectException('Exception');
        $this->expectExceptionMessage($message);
        $this->smartyEngine->render($blockMock, $filename);
    }
}
