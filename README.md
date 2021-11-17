# Magento 2 Smarty

The module is an integration of the [Smarty](https://www.smarty.net/) template engine in Magento2.

To use this package you need to write your Magento2 module, or you can write all your template files in Smarty. 

This template engine is intended to be used in addition to the `.tpl` files. 

## Badges

[![Total Downloads](https://poser.pugx.org/cleatsquad/magento-smarty/d/total.svg)](https://packagist.org/packages/cleatsquad/magento-smarty)
[![Latest Stable Version](https://poser.pugx.org/cleatsquad/magento-smarty/v/stable.svg)](https://packagist.org/packages/cleatsquad/magento-smarty)
[![Latest Unstable Version](https://poser.pugx.org/cleatsquad/magento-smarty/v/unstable.svg)](https://packagist.org/packages/cleatsquad/magento-smarty)
[![License](https://poser.pugx.org/cleatsquad/magento-smarty/license.svg)](https://packagist.org/packages/cleatsquad/magento-smarty)

## Getting Started

### Prerequisites

What things you need to install the software and how to install them

```
Magento 2.2 - 2.3
PHP 7.2 - 7.4
```

### Installing

Add dependency
```
composer require cleatsquad/magento-smarty
```

Enable the module
```
bin/magento module:enable CleatSquad_Smarty
```

Update the database entries
```
bin/magento setup:upgrade
```

## Magento 2 

### Components

A new engine has been added to this module which will allow you to use smarty. and this the injection of the new engine in the class Magento\Framework\View\TemplateEngineFactory.

### Frontend Integration

Your template files must have the file extension `.tpl` to get automatically recognized.

In your layout xml files or blocks please specify the new template

```xml
<referenceBlock name="page.main.title" template="Magento_Theme::html/title.tpl"/>
```

#### Example header.phtml converted to header.tpl

```php
<?php
/**
 * @var $block \Magento\Theme\Block\Html\Title
 */
$cssClass = $block->getCssClass() ? ' ' . $block->getCssClass() : '';
$titleHtml = '';
if (trim($block->getPageHeading())) {
    $titleHtml = '<span class="base" data-ui-id="page-title-wrapper" '
        . $block->getAddBaseAttribute()
        . '>'
        . $block->escapeHtml($block->getPageHeading())
        . '</span>';
}
?>
<?php if ($titleHtml) : ?>
<div class="page-title-wrapper<?= $block->escapeHtmlAttr($cssClass) ?>">
    <h1 class="page-title"
        <?php if ($block->getId()) : ?> id="<?= $block->escapeHtmlAttr($block->getId()) ?>" <?php endif; ?>
        <?php if ($block->getAddBaseAttributeAria()) : ?>
            aria-labelledby="<?= $block->escapeHtmlAttr($block->getAddBaseAttributeAria()) ?>"
        <?php endif; ?>>
        <?= /* @noEscape */ $titleHtml ?>
    </h1>
    <?= $block->getChildHtml() ?>
</div>
<?php endif; ?>

```

```tpl
{if ($block->getCssClass())}
    {assign var = "cssClass" value = {$block->getCssClass()}}}
{else}
    {assign var = "cssClass" value = ''}}
{/if}


{if (trim($block->getPageHeading()))}
    {assign var = "titleHtml" value = '<span class="base" data-ui-id="page-title-wrapper" {$block->getAddBaseAttribute()}>{$block->escapeHtml($block->getPageHeading())}</span>'}
{else}
    {assign var = "titleHtml" value = ''}}
{/if}
{if ($titleHtml)}
<div class="page-title-wrapper{$block->escapeHtmlAttr($cssClass)}">
    <h1 class="page-title"
        {if ($block->getId()) : ?> id={$block->escapeHtmlAttr($block->getId())}"{/if}
        {if ($block->getAddBaseAttributeAria()) : ?>
            aria-labelledby="{$block->escapeHtmlAttr($block->getAddBaseAttributeAria())}"
        {/if}>
        {$titleHtml}
    </h1>
    {$block->getChildHtml()}
</div>
{/if}
```

```xml
<referenceBlock name="breadcrumbs" template="Magento_Theme::html/breadcrumbs.tpl"/>
```

#### Example breadcrumbs.phtml converted to breadcrumbs.tpl

```php
<?php if ($crumbs && is_array($crumbs)) : ?>
<div class="breadcrumbs">
    <ul class="items">
        <?php foreach ($crumbs as $crumbName => $crumbInfo) : ?>
            <li class="item <?php echo $crumbName ?>">
            <?php if ($crumbInfo['link']) : ?>
                <a href="<?php echo $crumbInfo['link'] ?>" title="<?php echo $this->escapeHtml($crumbInfo['title']) ?>">
                    <?php echo $this->escapeHtml($crumbInfo['label']) ?>
                </a>
            <?php elseif ($crumbInfo['last']) : ?>
                <strong><?php echo $this->escapeHtml($crumbInfo['label']) ?></strong>
            <?php else: ?>
                <?php echo $this->escapeHtml($crumbInfo['label']) ?>
            <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>
```

```tpl
{if $crumbs}
    <div class="breadcrumbs">
        <ul class="items">
            {foreach from = $crumbs key = crumbName item = crumbInfo}
            <li class="item {$crumbName}">
                {if $crumbInfo.link}
                    <a href="{$crumbInfo.link}" title="{$crumbInfo.title}">
                        {$crumbInfo.label}
                    </a>
                {elseif $crumbInfo.last}
                    <strong>{$crumbInfo.label}</strong>
                {else}
                    {$crumbInfo.label}
                {/if}
            </li>
            {/foreach}
        </ul>
    </div>
{/if}
```

#### Access helper methods

Write in your `.tpl` file:

```tpl
{assign var = "directoryHelper" value = $this->helper("Magento\Directory\Helper\Data")}
{$directoryHelper->getDefaultCountry()}
```

### Configurations

Configuration options can be found Stores -> Settings -> Configuration -> Advanced -> Developer -> Smarty.

| Section | Group | Field | Description | 
| ------ | ----- | ----- | ----------- |
| dev | smarty | debug | Disable/Enable debug mode. |
| dev | smarty| cache | Disable/Enable of the cache of smarty templates (cache stored in var/cache/smarty) |

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/cleatsquad/magento-smarty/tags). 

## Authors

* **Mohamed El Mrabet** - *Initial work* - [mimou78](https://github.com/mimou78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
