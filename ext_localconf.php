<?php
declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

defined('TYPO3') or die();

(function($extKey)
{
    // v12 & v13 & 14
    $GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets'][$extKey] = 'EXT:'.$extKey.'/Configuration/RTE/Htmltables.yaml';

    $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
    $extensionConf = $extensionConfiguration->get('htmltables');

    // Add TypoScript Setup (useResponsiveTable, useBootstrapTableClass)
    ExtensionManagementUtility::addTypoScriptSetup(
        'plugin.'.$extKey.'.settings {
            useResponsiveTable=' . ($extensionConf['useResponsiveTable'] ?? 0) . '
            useBootstrapTableClass=' . ($extensionConf['useBootstrapTableClass'] ?? 0) .'
        }'
    );

    // backend css
    $GLOBALS['TYPO3_CONF_VARS']['BE']['stylesheets'][$extKey] = 'EXT:'.$extKey.'/Resources/Public/Backend/Css/';

})('htmltables');
