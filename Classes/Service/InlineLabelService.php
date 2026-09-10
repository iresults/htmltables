<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

namespace Zarth\Htmltables\Service;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Resource\Exception\InvalidUidException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Text\TextCropper;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * inline label service
 *
 */
class InlineLabelService
{
    /**
     * Get the user function label for the file_reference table
     *
     * @param array $params
     */
    public function getInlineLabel(array &$params): void
    {
        $row = $params['row'];

        if (!empty($row)) {
            $contentElement = $this->getContentElement($row);
        }

        $autoCols = 0;
        if (!empty($contentElement['cols'])) {
            $autoCols = $contentElement['cols'] - 1;
        }

        // set row title
        $params['title'] = $this->setRowTitle($row);

        // get cell amount & content
        $cells = is_int($row['uid']) ? $this->getCellData($row['uid']) : [];
        $amountOfCellsRow = $this->getAmountOfCells($cells);
        $cellContentsRow = $this->getCellContents($cells, $autoCols);

        // get configuration of cell information display
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class);
        $displayCells = $extensionConfiguration->get('htmltables', 'showCellInformation');

        // set cell data after row title
        switch ($displayCells) {
            case '1':
                $params['title'] .= $amountOfCellsRow;
                break;

            case '2':
                $params['title'] .= $cellContentsRow;
                break;

            case '3':
                $params['title'] .= $amountOfCellsRow . $cellContentsRow;
                break;

            default:
                break;
        }
    }

    /**
     * receive cell content as html-wrapped piece
     *
     * @param array $cells
     * @param int $autoCols
     * @return string
     */
    protected function getCellContents(array $cells, int $autoCols): string
    {
        $contentArray = array_column($cells, 'bodytext');
        $recordsArray = array_column($cells, 'records');
        $lastKey = array_key_last($contentArray);

        // fill up empty cols with generic setting from "cols" field
        if ($lastKey < $autoCols) {
            for ($i = $lastKey; $i < $autoCols; $i++) {
                $contentArray[] = '';
            }
            $lastKey = $autoCols;
        }

        // strip tags
        array_walk($contentArray, function(&$value, $key) use ($lastKey, $recordsArray): void {
            $class = "htmltables-preview-cell text-truncate";
            if (!empty($value)) {
                $value = strip_tags($value);
            } else {
                if (empty($recordsArray[$key])) {
                    $value = ' ⸺ ';
                } else {
                    $value = '< ' . $recordsArray[$key] .' >';
                }
                $class .= ' cell-empty';
            }
            $class .= ($key === $lastKey) ? ' border border-0' : ' border border-0 border-end';
            $value = '<span class="badge rounded-0 bg-transparent '.$class.'">'.$value.'</span>';
        });

        $cellContents = implode('', $contentArray);
        $cellContentsRow = $cellContents ? ' &nbsp; <span class="mb-0 float-end" style="line-height:1.75">' . $cellContents . '</span>' : '';

        return $cellContentsRow;
    }

    /**
     * get the number of cells in the row
     *
     * @param array $cells
     * @return string
     */
    protected function getAmountOfCells(array $cells): string
    {
        $amountOfCells = is_array($cells) ? count($cells) : 0;
        $amountOfCellsRow = '<span class="ms-2 badge border float-end">' . $amountOfCells . '</span>';
        return $amountOfCellsRow;
    }

    /**
     * @param array $row
     * @return array
     */
    protected function getContentElement(array $row): array
    {
        if (!empty($row['parenttable']) && !empty($row['parentid'])) {
            return BackendUtility::getRecord($row['parenttable'], $row['parentid']) ?: [];
        }
        return [];
    }

    /**
     * returns the row title
     *
     * @param array $row
     * @return string
     */
    protected function setRowTitle(array $row): string
    {
        if (!empty($row)) {
            $contentElement = $this->getContentElement($row);
        }

        $isFirstHeaderRow = !empty($contentElement['table_header_position']) && $contentElement['table_header_position'] === 1;
        $isLastFooterRow = !empty($contentElement['table_tfoot']) && $contentElement['table_tfoot'] === 1;

        // set title with preceding nr. (1. row)
        if (!empty($row['title'])) {
            $title = $row['title'];
        } elseif (!empty($row['sorting'])) {
            $title = $row['sorting'].'. row';
        } else {
            $title = '<i>NEW row</i>';
        }

        // set [Header] or [Footer]
        $rowIndex = $this->getRowIndices($row['parentid']);
        if (is_array($rowIndex)) {
            if ($isFirstHeaderRow && $rowIndex['isFirst'] === $row['uid']) {
                $title .= ' [Header]';
            }

            if ($isLastFooterRow && $rowIndex['isLast'] === $row['uid'] && $rowIndex['total'] > 2) {
                $title .= ' [Footer]';
            }
        }

        return $title;
    }

    /**
     * return row indices
     *
     * @param int $contentUid
     * @return array|false
     */
    protected function getRowIndices(int $contentUid): array|false
    {
        $table = 'tx_htmltables_table_row';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $result = $queryBuilder
            ->select('uid', 'sorting')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('parentid', $queryBuilder->createNamedParameter($contentUid, Connection::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        if (!empty($result)) {
            return [
                'isFirst' => current($result)['uid'],
                'total' => count($result),
                'isLast' => end($result)['uid']
            ];
        }

        return false;
    }

    /**
     * return cell data
     *
     * @param int $rowUid
     * @return array
     */
    public function getCellData(int $rowUid): array
    {
        $table = 'tx_htmltables_table_cell';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        return $queryBuilder
            ->select('uid', 'headercell', 'bodytext', 'records', 'colspan', 'rowspan')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('parentid', $queryBuilder->createNamedParameter($rowUid, Connection::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /**
     * @param int $contentUid
     * @return array|false
     */
    public function getRows(int $contentUid): array|false
    {
        $table = 'tx_htmltables_table_row';
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);
        $result = $queryBuilder
            ->select('uid', 'sorting')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('parentid', $queryBuilder->createNamedParameter($contentUid, Connection::PARAM_INT))
            )
            ->orderBy('sorting')
            ->executeQuery()
            ->fetchAllAssociative();

        return !empty($result) ? $result : false;
    }
}
