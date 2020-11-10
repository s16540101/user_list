<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Excel
{
    function __construct()
    {
        // PHPExcel libraries have to be in your include path
        require_once(APPPATH . 'libraries/excel/Bootstrap.php');

        $cacheMethod = \PHPExcel\CachedObjectStorageFactory::CACHE_IN_MEMORY_GZIP;
        $cacheSettings = array(' memoryCacheSize ' => '8MB');
        \PHPExcel\Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        // \PHPExcel\Shared\Font::setAutoSizeMethod(\PHPExcel\Shared\Font::AUTOSIZE_METHOD_EXACT);
    }

    /*
    array to excel
    屬性 data,background,color,font-size,text-align
    $data = array(
                        array(
                            array('data'=> 1,'background' => '00ff0000','color' => 'ff0000','font-size' => 20,'width' => 15,'text-align' => 'center'),
                            array('data'=> 2,'background' => '0000ff','color' => 'ff00ff00','width' => 20,'height' => 50,'text-align' => 'right'),
                            array('data'=> 3,'background' => 'ff0000', 'rowspan' => '2', 'colspan' => '2')
                        ),
                        array(array('data'=> 4),array('data'=> 5),array('data'=> 6)),
                        array(array('data'=> 7),array('data'=> 8),array('data'=> 9))
                    );
    */
    function arrayToExcel($data, $name = 'test', $file_path = null)
    {
        $border = array(
            'allborders' => array(
                'style' => \PHPExcel\Style\Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            )
        );

        $objPHPExcel = new \PHPExcel\Spreadsheet();
        $workSheet = $objPHPExcel->getSheet(0);

        $row = 1;
        foreach ($data as $key => $value) {
            $column = 0;
            foreach ($value as $k => $v) {
                $v['data'] = $this->br2nl($v['data']);
                // 水平合併
                if (isset($v['colspan']) && (!isset($v['rowspan']))) {
                    $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . $row));
                    $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . $row))->applyFromArray(array('borders' => (isset($v['borders'])) ? $v['borders'] : $border));
                } // 垂直合併
                elseif (isset($v['rowspan']) && (!isset($v['colspan']))) {
                    $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column) . ($row + $v['rowspan'] - 1)));
                    $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column) . ($row + $v['rowspan'] - 1)))->applyFromArray(array('borders' => (isset($v['borders'])) ? $v['borders'] : $border));
                } // 垂直水平合併
                elseif (isset($v['rowspan']) && (isset($v['colspan']))) {
                    $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . ($row + $v['rowspan'] - 1)));
                    $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . ($row + $v['rowspan'] - 1)))->applyFromArray(array('borders' => (isset($v['borders'])) ? $v['borders'] : $border));
                }

                if (isset($v['is_string'])) {
                    $objPHPExcel->getActiveSheet()->getCell($this->colConvertAscii($column) . $row)->setValueExplicit($v['data'],
                        \PHPExcel\Cell\DataType::TYPE_STRING);
                } else {
                    $workSheet->setCellValueByColumnAndRow($column, $row, $v['data']);
                }

                //設寬度
                if ($row == 1) {
                    if (isset($v['width']) && $v['width'] == 'auto') {
                        $workSheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                    } elseif (isset($v['width'])) {
                        $workSheet->getColumnDimensionByColumn($column)->setAutoSize(false);
                        $workSheet->getColumnDimensionByColumn($column)->setWidth($v['width']);
                    } else {
                        $workSheet->getColumnDimensionByColumn($column)->setWidth(20);
                    }
                } elseif (isset($v['width'])) {
                    if ($v['width'] == 'auto') {
                        $v['width'] = 20;
                    }
                    $workSheet->getColumnDimensionByColumn($column)->setWidth($v['width']);
                } else {
                    $w = $workSheet->getColumnDimensionByColumn($column)->getWidth();
                    if ($w == -1) {
                        $workSheet->getColumnDimensionByColumn($column)->setWidth(20);
                    }
                }

                //設高度
                if ($column == 0 && isset($v['height'])) {
                    $workSheet->getRowDimension($row)->setRowHeight($v['height']);
                }

                $ColumnAndRow = $workSheet->getStyleByColumnAndRow($column, $row);

                // 全部垂直置中
                $ColumnAndRow->getAlignment()->setWrapText(true)->setVertical(\PHPExcel\Style\Alignment::VERTICAL_CENTER);

                // 設border
                $ColumnAndRow->getBorders()->applyFromArray((isset($v['borders'])) ? $v['borders'] : $border);

                // 字體加粗
                if (isset($v['bold'])) {
                    $ColumnAndRow->getFont()->setBold(true);
                }

                // 設置左中右 text-align
                if (isset($v['text-align'])) {
                    if ($v['text-align'] == 'right') {
                        $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_RIGHT);
                    } elseif ($v['text-align'] == 'left') {
                        $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_LEFT);
                    } else {
                        $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_CENTER);
                    }
                } else {
                    $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_CENTER);
                }

                if (isset($v['vertical-align'])) {
                    $ColumnAndRow->getAlignment()->setVertical($v['vertical-align']);
                }

                if (isset($v['color'])) {
                    $FontColor = new \PHPExcel\Style\Color();
                    if (strlen($v['color']) == 6) {
                        $FontColor->setRGB($v['color']);
                    } else {
                        $FontColor->setARGB($v['color']);
                    }
                    $ColumnAndRow->getFont()->setColor($FontColor);
                }
                //設字體大小
                if (isset($v['font-size'])) {
                    $ColumnAndRow->getFont()->setSize($v['font-size']);
                }
                //設背景
                if (isset($v['background'])) {
                    $ColumnAndRow->getFill()->setFillType(\PHPExcel\Style\Fill::FILL_SOLID);
                    if (strlen($v['background']) == 6) {
                        $ColumnAndRow->getFill()->getStartColor()->setRGB($v['background']);
                    } else {
                        $ColumnAndRow->getFill()->getStartColor()->setARGB($v['background']);
                    }
                }
                if (isset($v['colspan'])) {
                    $column += ($v['colspan']);
                } else {
                    $column++;
                }
            }
            $row++;
        }
        //return $objPHPExcel;
        if (ob_get_length()) {
            ob_end_clean();
        }
        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setPreCalculateFormulas(false); // 不做 precalculate，避免內容為 = 開頭時，匯出會有錯誤
        if (null === $file_path) {
            header("Content-type: text/html; charset=utf-8");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $name . EXCEL_EXTENTION . '"');
            header('Cache-Control: max-age=0');


            $objWriter->save('php://output');
        } else {
            $objWriter->save($file_path . $name . EXCEL_EXTENTION);
        }
    }

    // 水平欄位轉換

    /**
     * Convert BR tags to newlines and carriage returns.
     *
     * @param string The string to convert
     * @param string The string to use as line separator
     * @return string The converted string
     */
    function br2nl($string, $separator = PHP_EOL)
    {
        $separator = in_array($separator, array(
            "\n",
            "\r",
            "\r\n",
            "\n\r",
            chr(30),
            chr(155),
            PHP_EOL
        )) ? $separator : PHP_EOL;  // Checks if provided $separator is valid.
        return preg_replace('/\<br(\s*)?\/?\>/i', $separator, $string);
    }

    function colConvertAscii($column = 0)
    {
        $column = floor($column);
        $c = (int)($column / 26);
        $modc = $column % 26;
        if ($c < 1) {
            return chr($modc + 65);
        }
        return $this->colConvertAscii($c - 1) . chr($modc + 65);
    }

    function arrayToExcelForWorkTable($data, $name = 'test', $title = array(), $file_path = null)
    {
        $border = array(
            'allborders' => array(
                'style' => \PHPExcel\Style\Border::BORDER_THIN,
                'color' => array('argb' => 'FF000000'),
            )
        );

        $objPHPExcel = new \PHPExcel\Spreadsheet();

        foreach ($data as $w_key => $w_value) {
            if ($w_key != 0) {
                $objPHPExcel->createSheet();
            }

            $objPHPExcel->setActiveSheetIndex($w_key);
            if (count($title) && isset($title[$w_key]))
                $objPHPExcel->getActiveSheet()->setTitle($title[$w_key]);

            // $objPHPExcel->getActiveSheet()->setCellValue('A1', '測試' . $w_key);


            $workSheet = $objPHPExcel->getsheet($w_key);


            $row = 1;
            foreach ($w_value as $key => $value) {
                $column = 0;
                foreach ($value as $k => $v) {
                    // 水平合併
                    if (isset($v['colspan']) && (!isset($v['rowspan']))) {
                        $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . $row));
                        $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . $row))->applyFromArray(array('borders' => $border));
                    }
                    // 垂直合併
                    if (isset($v['rowspan']) && (!isset($v['colspan']))) {
                        $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column) . ($row + $v['rowspan'] - 1)));
                        $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column) . ($row + $v['rowspan'] - 1)))->applyFromArray(array('borders' => $border));
                    }

                    // 垂直水平合併
                    if (isset($v['rowspan']) && (isset($v['colspan']))) {
                        $workSheet->mergeCells(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . ($row + $v['rowspan'] - 1)));
                        $workSheet->getStyle(($this->colConvertAscii($column) . $row) . ':' . ($this->colConvertAscii($column + $v['colspan'] - 1) . ($row + $v['rowspan'] - 1)))->applyFromArray(array('borders' => $border));
                    }

                    if (isset($v['is_string'])) {
                        $objPHPExcel->getActiveSheet()->getCell($this->colConvertAscii($column) . $row)->setValueExplicit($v['data'],
                            \PHPExcel\Cell\DataType::TYPE_STRING);
                    } else {
                        $workSheet->setCellValueByColumnAndRow($column, $row, $v['data']);
                    }

                    //設寬度
                    if ($row == 1) {
                        if (isset($v['width']) && $v['width'] == 'auto') {
                            $workSheet->getColumnDimensionByColumn($column)->setAutoSize(true);
                        } elseif (isset($v['width'])) {
                            $workSheet->getColumnDimensionByColumn($column)->setAutoSize(false);
                            $workSheet->getColumnDimensionByColumn($column)->setWidth($v['width']);
                        } else {
                            $workSheet->getColumnDimensionByColumn($column)->setWidth(20);
                        }
                    } elseif (isset($v['width'])) {
                        if ($v['width'] == 'auto') {
                            $v['width'] = 20;
                        }
                        $workSheet->getColumnDimensionByColumn($column)->setWidth($v['width']);
                    } else {
                        $w = $workSheet->getColumnDimensionByColumn($column)->getWidth();
                        if ($w == -1) {
                            $workSheet->getColumnDimensionByColumn($column)->setWidth(20);
                        }
                    }

                    //設高度
                    if ($column == 0 && isset($v['height'])) {
                        $workSheet->getRowDimension($row)->setRowHeight($v['height']);
                    }

                    $ColumnAndRow = $workSheet->getStyleByColumnAndRow($column, $row);

                    // 全部垂直置中
                    $ColumnAndRow->getAlignment()->setWrapText(true)->setVertical(\PHPExcel\Style\Alignment::VERTICAL_CENTER);

                    // 設border
                    $ColumnAndRow->getBorders()->applyFromArray((isset($v['borders'])) ? $v['borders'] : $border);

                    // 字體加粗
                    if (isset($v['bold'])) {
                        $ColumnAndRow->getFont()->setBold(true);
                    }

                    // 設置左中右 text-align
                    if (isset($v['text-align'])) {
                        if ($v['text-align'] == 'right') {
                            $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_RIGHT);
                        } elseif ($v['text-align'] == 'left') {
                            $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_LEFT);
                        } else {
                            $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_CENTER);
                        }
                    } else {
                        $ColumnAndRow->getAlignment()->setHorizontal(\PHPExcel\Style\Alignment::HORIZONTAL_CENTER);
                    }

                    if (isset($v['vertical-align'])) {
                        $ColumnAndRow->getAlignment()->setVertical($v['vertical-align']);
                    }

                    if (isset($v['color'])) {
                        $FontColor = new \PHPExcel\Style\Color();
                        if (strlen($v['color']) == 6) {
                            $FontColor->setRGB($v['color']);
                        } else {
                            $FontColor->setARGB($v['color']);
                        }
                        $ColumnAndRow->getFont()->setColor($FontColor);
                    }
                    //設字體大小
                    if (isset($v['font-size'])) {
                        $ColumnAndRow->getFont()->setSize($v['font-size']);
                    }
                    //設背景
                    if (isset($v['background'])) {
                        $ColumnAndRow->getFill()->setFillType(PHPExcel\Style\Fill::FILL_SOLID);
                        if (strlen($v['background']) == 6) {
                            $ColumnAndRow->getFill()->getStartColor()->setRGB($v['background']);
                        } else {
                            $ColumnAndRow->getFill()->getStartColor()->setARGB($v['background']);
                        }
                    }
                    if (isset($v['colspan'])) {
                        $column += ($v['colspan']);
                    } else {
                        $column++;
                    }
                }
                $row++;
            }

        }
        $objPHPExcel->setActiveSheetIndex(0); // 預設顯示第一個工作表
        //return $objPHPExcel;
        if (ob_get_length()) {
            ob_end_clean();
        }

        $objWriter = \PHPExcel\IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setPreCalculateFormulas(false); // 不做 precalculate，避免內容為 = 開頭時，匯出會有錯誤
        if (null === $file_path) {
            header("Content-type: text/html; charset=utf-8");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $name . EXCEL_EXTENTION . '"');
            header('Cache-Control: max-age=0');


            $objWriter->save('php://output');
        } else {
            $objWriter->save($file_path . $name . EXCEL_EXTENTION);
        }
    }

    function excelToArray($filePath, $header = true)
    {
        //Create excel reader after determining the file type
        $inputFileName = $filePath;
        /**  Identify the type of $inputFileName  **/
        $inputFileType = \PHPExcel\IOFactory::identify($inputFileName);
        /**  Create a new Reader of the type that has been identified  **/
        $objReader = \PHPExcel\IOFactory::createReader($inputFileType);
        /** Set read type to read cell data onl **/
        $objReader->setReadDataOnly(true);
        /**  Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = $objReader->load($inputFileName);
        //Get worksheet and built array with first row as header
        $objWorksheet = $objPHPExcel->getActiveSheet();
        //excel with first row header, use header as key
        if ($header) {
            $highestRow = $objWorksheet->getHighestRow();
            $highestColumn = $objWorksheet->getHighestColumn();
            $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
            $headingsArray = $headingsArray[1];
            $r = -1;
            $namedDataArray = array();
            for ($row = 2; $row <= $highestRow; ++$row) {
                $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true,
                    true);
                if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                    ++$r;
                    foreach ($headingsArray as $columnKey => $columnHeading) {
                        $namedDataArray[$r][$columnHeading] = $dataRow[$row][$columnKey];
                    }
                }
            }
        } else {
            //excel sheet with no header
            $namedDataArray = $objWorksheet->toArray(null, true, true, false);
        }
        return $namedDataArray;
    }

    /**
     * convert excel date column to timestamp
     *
     * @param $value
     *
     * @return mixed
     */
    function excel_date_to_php($value)
    {
        return \PHPExcel\Shared\Date::ExcelToPHP($value);
    }
}
