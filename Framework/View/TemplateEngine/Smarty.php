<?php
/**
 * @category    CleatSquad
 * @package     CleatSquad_Smarty
 * @copyright   Copyright (c) 2021 CleatSquad, Inc. (http://www.cleatsquad.com)
 */
declare(strict_types=1);

namespace CleatSquad\Smarty\Framework\View\TemplateEngine;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\State;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;
use Smarty_Internal_Template;
use SmartyException;

/**
 * Class Smarty
 * @package CleatSquad\Smarty\Framework\View\TemplateEngine
 */
class Smarty implements \Magento\Framework\View\TemplateEngineInterface
{
    /**
     * Configuration path Disable/Enable of the cache of smarty templates (cache stored in var/cache/smarty).
     */
    const XML_PATH_CACHE = 'dev/smarty/cache';

    /**
     * Configuration path Disable/Enable debug mode.
     */
    const XML_PATH_DEBUG = 'dev/smarty/debug';

    /**
     * @var \Smarty
     */
    protected $smarty;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var object[]
     */
    private $blockVariables = [];

    /**
     * Smarty constructor.
     * @param \Smarty $smarty
     * @param ScopeConfigInterface $scopeConfig
     * @param State $appState
     * @param DirectoryList $directoryList
     * @param array $blockVariables
     * @throws LocalizedException
     */
    public function __construct(
        \Smarty $smarty,
        ScopeConfigInterface $scopeConfig,
        State $appState,
        DirectoryList $directoryList,
        array $blockVariables = []
    ) {
        $this->smarty = $smarty;
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
        $this->directoryList = $directoryList;
        $this->blockVariables = $blockVariables;

        $this->init();
    }

    /**
     * Init the smarty engine
     *
     * @throws LocalizedException
     */
    private function init()
    {
        try {
            if ($this->scopeConfig->isSetFlag(self::XML_PATH_CACHE)) {
                $this->smarty->setCacheDir($this->getCacheDir());
            }
            if ($this->scopeConfig->isSetFlag(self::XML_PATH_DEBUG) && $this->appState->getMode() != State::MODE_PRODUCTION) {
                $this->smarty->setDebugging(true);
            } else {
                $this->smarty->setDebugging(false);
            }
            $this->smarty->registerPlugin("block", "translate", [$this, 'doTranslation']);
        } catch (SmartyException $e) {
            throw new LocalizedException(__('We can\'t load the smarty engine.'));
        }
    }

    /**
     * Do the translation
     *
     * @param array $params
     * @param $content
     * @param Smarty_Internal_Template $smarty
     * @param boolean $repeat
     * @return string
     */
    public function doTranslation ($params, $content, $smarty, &$repeat)
    {
        if ($content) {
            return __($content);
        }
    }

    /**
     * Render output
     *
     * @param BlockInterface $block
     * @param string $templateFile
     * @param array $dictionary
     * @return string
     * @throws LocalizedException
     */
    public function render(BlockInterface $block, $templateFile, array $dictionary = [])
    {
        try {
            $dictionary['block'] = $block;
            $dictionary['this'] = $this;
            $dictionary = array_merge($this->blockVariables, $dictionary);
            foreach ($dictionary as $key => $object) {
                $this->smarty->assignByRef($key, $object);
            }
            $result = $this->smarty->fetch($templateFile);
            return $result;
        } catch (SmartyException $e) {
            throw new LocalizedException(__('We can\'t load the template %1.', $templateFile));
        }
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function getCacheDir()
    {
        $cacheFolder = $this->directoryList->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . DirectoryList::CACHE;
        try {
            return $cacheFolder . DIRECTORY_SEPARATOR . 'smarty';
        } catch (FileSystemException $e) {
            throw new LocalizedException(__('We can\'t init the cache dir.'));
        }
        return $cacheFolder;
    }
}
