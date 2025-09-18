<?php

// (c) z3n - R1V1@100802 - www.overflow.biz - rodrigo.orph@gmail.com

/*
    This class requires PEAR Spreadsheet Excel Writer Package
    http://pear.php.net/package/Spreadsheet_Excel_Writer/
*/

//require_once('Spreadsheet/Excel/Writer.php');


class XLSExport {
    private $objXLS;
    private $fileName;
    
    public $is_error;
    

    public function __construct( $memory_limit="1G", $save_dir="") {
        set_time_limit(0);
        ini_set("memory_limit",$memory_limit);

        $this->is_error=0;
        
        
        $this->fileName = $save_dir == "" ? dirname(__FILE__) : $save_dir;
        $this->fileName .= "xls_export_".md5(time().mt_rand()).".xls";

        $this->objXLS = new Spreadsheet_Excel_Writer($this->fileName);

        if (defined("lib_cache"))
            $this->objXLS->setTempDir(lib_cache);
    }
    
    public function export($worksheet_name, $data) {
            // add new sheet
            $sheet = $this->objXLS->addWorksheet($worksheet_name);
            $ri=0;
            
            foreach ($datas as $key=>$rows) {
                $ci=1;    
                foreach ($rows as $col=>$col_value) {
                    if($ri==1){
                        //$colnames[] = '"'.$col.'"';
                        $sheet->write(0,$ci,$col);
                    }
                    // add cols to sheet
                    $sheet->write($ri,$ci,$col_value);
                
                    $ci++;    
                }
               
               $ri++;
    
            }
            
            
        }

        $this->objXLS->close();
        return $this->fileName;
    }
    
    public function __destruct() {
        
    }

}

?>