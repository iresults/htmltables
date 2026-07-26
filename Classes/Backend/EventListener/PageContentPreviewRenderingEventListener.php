<?php

declare(strict_types=1);

namespace Zarth\Htmltables\Backend\EventListener;

use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use Zarth\Htmltables\Service\InlineLabelService;
use TYPO3\CMS\Core\Information\Typo3Version;

#[AsEventListener(
    identifier: 'htmltables/preview-rendering-htmltables',
)]
final readonly class PageContentPreviewRenderingEventListener
{
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        if ($event->getTable() !== 'tt_content')
            return;

        $versionInformation = GeneralUtility::makeInstance(Typo3Version::class);
        if ($versionInformation->getMajorVersion() >= 14)
            $record = $event->getRecord()->toArray();
        else
            $record = $event->getRecord();

        if ($record['CType'] === 'htmltables_htmltable') {
            $inlineLabelService = GeneralUtility::makeInstance(InlineLabelService::class);
            $rows = $inlineLabelService->getRows($record['uid']);
            $headerPosition = $record['table_header_position'];

            $previewRows = '';
            if (!empty($rows)) {
                foreach ($rows as $key => $row) {
                    $cells = $inlineLabelService->getCellData($row['uid']);
                    if ($headerPosition === 1 && $key > 0) $headerPosition = 0;
                    $previewRows .=  '<tr>' . $this->getCellContents($cells, $headerPosition) . '</tr>';
                }
            }

            $caption = $record['table_caption']?'<caption>'.$record['table_caption'].'</caption>':'';
            $table =    '<table class="table table-sm table-responsive">' .
                            $caption .
                            $previewRows .
                        '</table>';

            $event->setPreviewContent($table);
        }
    }

    /**
     * receive cell content as html-wrapped piece
     *
     * @param array $cells
     *
     * @return string
     */
    protected function getCellContents($cells, $headerPos)
    {
        $contentArray   = array_column($cells, 'bodytext');
        $headerArray    = array_column($cells, 'headercell');
        $recordsArray   = array_column($cells, 'records');
        $colspanArray   = array_column($cells, 'colspan');
        $rowspanArray   = array_column($cells, 'rowspan');

        // strip tags
        array_walk($contentArray, function(&$value, $key) use ($recordsArray, $headerArray, $headerPos, $colspanArray)
        {
            if (!empty($value)) {
                $value = strip_tags($value);
            }
            else {
                if (empty($recordsArray[$key]))
                    $value = ' ⸺ ';
                else
                    $value = '< ' . $recordsArray[$key] .' >';
            }

            $colspan = '';
            if ($colspanArray[$key] > 0) $colspan = ' colspan="'.$colspanArray[$key].'"';

            if ($headerArray[$key] === 1 || $headerPos === 1 || ($headerPos === 2 && $key === 0) )
                $value = '<th'.$colspan.'>'.$value.'</th>';
            else
                $value = '<td'.$colspan.'>'.$value.'</td>';

        });
        $cellContents = implode(' ', $contentArray);
        $cellContentsRow = $cellContents ? $cellContents : '';

        return $cellContentsRow;
    }
}
