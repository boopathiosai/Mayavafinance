<?php

     /*
     * Class is used for save the data into microsoft excel format.
     * It takes data into array or you can write data column vise.
     */


    Class ExcelWriter
    {

        var $fp=null;
        var $error;
        var $state="CLOSED";
        var $newRow=false;
        public $headerTitle="XLS Exported Report";

        /*
        * @Params : $file  : file name of excel file to be created.
        * @Return : On Success Valid File Pointer to file
        *           On Failure return false
        */

        function ExcelWriter($file="", $title="XLS Exported Report", $other='')
        {
            $this->headerTitle = $title;

            if(is_array($other)){
                $this->headerSpan = $other['column_count'];
            }

            return $this->open($file);
        }

        /*
        * @Params : $file  : file name of excel file to be created.
        *           if you are using file name with directory i.e. test/myFile.xls
        *           then the directory must be existed on the system and have permissioned properly
        *           to write the file.
        * @Return : On Success Valid File Pointer to file
        *           On Failure return false
        */
        function open($file)
        {
            if($this->state!="CLOSED")
            {
                $this->error="Error : Another file is opend .Close it to save the file";
                return false;
            }

            if(!empty($file))
            {
                $this->fp=@fopen($file,"w+");
            }
            else
            {
                $this->error="Usage : New ExcelWriter('fileName')";
                return false;
            }
            if($this->fp==false)
            {
                $this->error="Error: Unable to open/create File.You may not have permmsion to write the file.";
                return false;
            }
            $this->state="OPENED";
            fwrite($this->fp,$this->GetHeader());
            return $this->fp;
        }

        function close()
        {
            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            if($this->newRow)
            {
                fwrite($this->fp,"</tr>");
                $this->newRow=false;
            }

            fwrite($this->fp,$this->GetFooter());
            fclose($this->fp);
            $this->state="CLOSED";
            return ;
        }
        /* @Params : Void
        *  @return : Void
        * This function write the header of Excel file.
        */

        function GetHeader()
        {
            $header = <<<EOH
                <html xmlns:o="urn:schemas-microsoft-com:office:office"
                xmlns:x="urn:schemas-microsoft-com:office:excel"
                xmlns="http://www.w3.org/TR/REC-html40">

                <head>
                <meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
                <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
                <meta name=ProgId content=Excel.Sheet>
                <!--[if gte mso 9]><xml>
                 <o:DocumentProperties>
                  <o:LastAuthor>Sriram</o:LastAuthor>
                  <o:LastSaved>2005-01-02T07:46:23Z</o:LastSaved>
                  <o:Version>10.2625</o:Version>
                 </o:DocumentProperties>
                 <o:OfficeDocumentSettings>
                  <o:DownloadComponents/>
                 </o:OfficeDocumentSettings>
                </xml><![endif]-->
                <style>
                <!--table
                    {mso-displayed-decimal-separator:"\.";
                    mso-displayed-thousand-separator:"\,";}
                @page
                    {margin:1.0in .75in 1.0in .75in;
                    mso-header-margin:.5in;
                    mso-footer-margin:.5in;}
                tr
                    {mso-height-source:auto;}
                col
                    {mso-width-source:auto;}
                br
                    {mso-data-placement:same-cell;}
                .style0
                    {mso-number-format:General;
                    text-align:general;
                    vertical-align:bottom;
                    white-space:nowrap;
                    mso-rotate:0;
                    mso-background-source:auto;
                    mso-pattern:auto;
                    color:windowtext;
                    font-size:10.0pt;
                    font-weight:bold;
                    font-style:normal;
                    text-decoration:none;
                    font-family:Arial;
                    mso-generic-font-family:auto;
                    mso-font-charset:0;
                    border:none;
                    mso-protection:locked visible;
                    mso-style-name:Normal;
                    mso-style-id:0;}
                td
                    {mso-style-parent:style0;
                    padding-top:1px;
                    padding-right:1px;
                    padding-left:1px;
                    mso-ignore:padding;
                    color:windowtext;
                    font-size:10.0pt;
                    font-weight:400;
                    font-style:normal;
                    text-decoration:none;
                    font-family:Arial;
                    mso-generic-font-family:auto;
                    mso-font-charset:0;
                    mso-number-format:General;
                    text-align:general;
                    vertical-align:bottom;
                    border:none;
                    mso-background-source:auto;
                    mso-pattern:auto;
                    mso-protection:locked visible;
                    white-space:nowrap;
                    mso-rotate:0;}
                .xl24
                    {mso-style-parent:style0;
                    white-space:normal;}
                -->
                </style>
                <!--[if gte mso 9]><xml>
                 <x:ExcelWorkbook>
                  <x:ExcelWorksheets>
                   <x:ExcelWorksheet>
                    <x:Name>Mayava Finance</x:Name>
                    <x:WorksheetOptions>
                     <x:Selected/>
                     <x:ProtectContents>False</x:ProtectContents>
                     <x:ProtectObjects>False</x:ProtectObjects>
                     <x:ProtectScenarios>False</x:ProtectScenarios>
                    </x:WorksheetOptions>
                   </x:ExcelWorksheet>
                  </x:ExcelWorksheets>
                  <x:WindowHeight>10005</x:WindowHeight>
                  <x:WindowWidth>10005</x:WindowWidth>
                  <x:WindowTopX>120</x:WindowTopX>
                  <x:WindowTopY>135</x:WindowTopY>
                  <x:ProtectStructure>False</x:ProtectStructure>
                  <x:ProtectWindows>False</x:ProtectWindows>
                 </x:ExcelWorkbook>
                </xml><![endif]-->
                </head>

                <body link=blue vlink=purple>
                <table x:str border=0 cellpadding=0 cellspacing=0 style='border-collapse: collapse;table-layout:fixed;'>
EOH;
                $title = '<tr><th colspan="15" align=left><h3>'.$this->headerTitle.'</h3></th></tr>';
            return $header.$title;
        }

        function GetFooter()
        {
            return "</table></body></html>";
        }

        /*
        * @Params : $line_arr: An valid array
        * @Return : Void
        */

        function writeLine($line_arr)
        {

            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            if(!is_array($line_arr))
            {
                $this->error="Error : Argument is not valid. Supply an valid Array.";
                return false;
            }
            fwrite($this->fp,"<tr>");
            foreach($line_arr as $col)
                fwrite($this->fp,"<td class=xl24 width=64 valign=top >".nl2br($col)."</td>");
            fwrite($this->fp,"</tr>");
        }

        /*
        * @Params : Void
        * @Return : Void
        */
        function writeRow()
        {
            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            if($this->newRow==false)
                fwrite($this->fp,"<tr>");
            else
                fwrite($this->fp,"</tr><tr>");
            $this->newRow=true;
        }

        /*
        * @Params : $value : Coloumn Value
        * @Return : Void
        */
        function writeCol($value)
        {
            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            fwrite($this->fp,"<td width=64 ><h4>".$value."</h4></td>");
        }

        /*
        * @Params : $value : Coloumn Value with bolded result
        * @Return : Void
        */
        function writeColBold($value)
        {
            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            fwrite($this->fp,"<td width=64 ><h4><b>".$value."</b></h4></td>");
        }


        function writeTitle($value)
        {
            if($this->state!="OPENED")
            {
                $this->error="Error : Please open the file.";
                return false;
            }
            fwrite($this->fp,"<tr height=50><td colspan=15 align=center><h4>".$value."</h4></td></tr>");
        }

        function exportData($data, $file_name)
        {

            //Delete the existing old exported report files.
            deleteFilesByModifyDate("tmp/");

            if(count($data) > 0){
              //echo "<pre>";
              $ri = 1;
              foreach($data as $row){
                $ci = 0;
                if(count($row) > 0 && $ri==1){
                    foreach($row as $key => $val){
                        $colArr[] = ucwords(preg_replace("/_/"," ",$key));
                        $ci++;
                    }
                    //print_r($colArr);
                    $this->writeLine($colArr);
                }
                $rowArr[] = $row;
               // print_r($colArr);exit;
                $this->writeLine($row);
                $ri++;
              }
            }
            else{

            $this->writeTitle("No Record Found");

            }

            $this->download($file_name);

        }

        function exportDataDetailedStockReport($data, $file_name) {
            //Delete the existing old exported report files.
            deleteFilesByModifyDate("tmp/");

            if(count($data) > 0){
                $colArr = array('Loan Code', 'Loan Amount', 'Loan Code',
                        'Loan Amount', 'Loan Code', 'Loan Amount',
                        'Loan Code', 'Loan Amount');
                foreach($colArr as $col){
                        $this->writeColBold($col);
                }
                $this->writeRow();
                $i = 1;
                foreach($data as $rKey => $dRow){
                    foreach($dRow as $cKey => $dColumn){
                        foreach($dColumn as $dColumnval){
                            if(count($data) == $i) {
                                    $this->writeColBold($dColumnval);
                            } else {
                                    $this->writeCol($dColumnval);
                            }
                        }
                    }
                    $i++;
                    $this->writeRow();
                }
            }
            else{
                $this->writeTitle("No Record Found");
            }
            $this->download($file_name);
        }

        function load($file_name){
            header("location:".$file_name);
        }

function download($file_name){

    // make sure it's a file before doing anything!
    if(is_file($file_name))
    {
        //echo "here";exit;
      /*
        Do any processing you'd like here:
        1.  Increment a counter
        2.  Do something with the DB
        3.  Check user permissions
        4.  Anything you want!
      */

      // required for IE
      if(ini_get('zlib.output_compression')) { ini_set('zlib.output_compression', 'Off');  }

      // get the file mime type using the file extension
      /*switch(strtolower(substr(strrchr($file_name,'.'),1)))
      {
        case 'pdf': $mime = 'application/pdf'; break;
        case 'zip': $mime = 'application/zip'; break;
        case 'jpeg':
        case 'jpg': $mime = 'image/jpg'; break;
        default: $mime = 'application/force-download';
      }*/

      $mime = 'application/force-download';

      header('Pragma: public');   // required
      header('Expires: 0');    // no cache
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_name)).' GMT');
      header('Cache-Control: private',false);
      header('Content-Type: '.$mime);
      //header("Content-type:   application/x-msexcel; charset=utf-8");

      header('Content-type: application/x-msdownload; charset=utf-8');
      header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
      header('Content-Transfer-Encoding: binary');
      header('Content-Length: '.filesize($file_name));  // provide file size
      header('Connection: close');
      readfile($file_name);    // push it out
      exit();

    }


}

    }
