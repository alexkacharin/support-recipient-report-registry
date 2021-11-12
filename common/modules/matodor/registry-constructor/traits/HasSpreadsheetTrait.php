<?php

namespace Matodor\RegistryConstructor\traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

/**
 * @property-read Spreadsheet $spreadsheet
 */
trait HasSpreadsheetTrait
{
    protected function setAlignment(array $pStyles, $col1, $row1, $col2 = null, $row2 = null)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow($col1, $row1, $col2, $row2)
            ->getAlignment()
            ->applyFromArray($pStyles);
    }

    protected function setBackground(string $color, $col1, $row1, $col2 = null, $row2 = null)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow($col1, $row1, $col2, $row2)
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setRGB($color);
    }

    protected function setAllBorder($borderStyle, $col1, $row1, $col2 = null, $row2 = null)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow($col1, $row1, $col2, $row2)
            ->getBorders()
            ->getAllBorders()
            ->applyFromArray([
                'borderStyle' => $borderStyle,
                'color' => ['rgb' => '000000'],
            ]);
    }

    protected function setOutlineBorder($borderStyle, $col1, $row1, $col2 = null, $row2 = null)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow($col1, $row1, $col2, $row2)
            ->getBorders()
            ->getOutline()
            ->applyFromArray([
                'borderStyle' => $borderStyle,
                'color' => ['rgb' => '000000'],
            ]);
    }

    protected function setFontWeightBold($col1, $row1, $col2 = null, $row2 = null)
    {
        $this->spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow($col1, $row1, $col2, $row2)
            ->getFont()
            ->setBold(true);
    }
}
