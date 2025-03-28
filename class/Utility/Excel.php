<?
namespace Utility;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style;

class Excel {
   public $xlsObj;
   public $wsObj;
   public $row;

   public function __construct($operation = "w", $filename = "", $readOnly=true) {
      if ($operation == "r") {
         if (!empty($filename)) {
            $this->readToFile($filename, $readOnly);
         }
      } else {
         $this->xlsObj = new Spreadsheet();
         $this->wsObj = $this->xlsObj->getActiveSheet();
         $this->row = 1;
      }
      return $this;
   }
   
   public function readToFile($filename, $readOnly=true) {
      $reader = IOFactory::createReaderForFile($filename);
      $reader->setReadDataOnly($readOnly);
      $this->xlsObj = $reader->load($filename);
      $this->wsObj = $this->xlsObj->getActiveSheet();
      $this->row = 1;
      return $this;
   }

   public function setSheetHeader($headerArr, $width=0, $styleArr="") {
      foreach ($headerArr as $col => $value) {
         $this->wsObj->setCellValueByColumnAndRow($col+1, $this->row, $value);
         if (!$width)
            $this->wsObj->getColumnDimensionByColumn($col+1)->setAutoSize(true);
         else if ($width > 0)
            $this->wsObj->getColumnDimensionByColumn($col+1)->setWidth($width);
         if (!empty($styleArr) && is_array($styleArr))
            $this->wsObj->getStyleByColumnAndRow($col+1, $this->row)->applyFromArray($styleArr);
      }
      $this->row++;
      return $this;
   }

   public function writeRowData($dataArr) {
      foreach ($dataArr as $col => $data) {
         $type = Cell\DataType::TYPE_STRING;
         if (isset($data['type'])) {
            switch(strtolower($data['type'])) {
               case 'n' : $type = Cell\DataType::TYPE_NUMERIC; break;
               case 's' :
               default: $type = Cell\DataType::TYPE_STRING; break;
            }
         }
         $this->wsObj->setCellValueExplicitByColumnAndRow($col+1, $this->row, $data['value'], $type);
      }
      $this->row++;
      return $this;
   }
   
   public function readColData($col, $row='', $nullVal=false) {
      if ($row == '') $row = $this->row;
      $cellObj = $this->wsObj->getCellByColumnAndRow($col, $row, false);
      if (!is_null($cellObj)) {
         return $cellObj->getValue();
      }
      return ($nullVal)?null:"";
   }

   public function saveFile($filepath) {
      $writerObj = IOFactory::createWriter($this->xlsObj, 'Xlsx');
      $writerObj->save($filepath);

      return $this;
   }

   public function downloadFile($filename) {
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
      header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
      header('Cache-Control: max-age=0');

      $writerObj = IOFactory::createWriter($this->xlsObj, 'Xlsx');
      $writerObj->save('php://output');

      return $this;
   }

}