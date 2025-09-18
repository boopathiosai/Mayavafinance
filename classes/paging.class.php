<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Paging class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  SrinivasanM
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  02-Feb-2010
//**************************************************************************
// Description      :  This is the Paging class  for Mayavafinance.
//**************************************************************************

class Paging {

    /*
    ** Configuration: 
    */

    // how many entries per page
    static $per_page           = 5;

    // the currently selected page
    static $current_page       = 1;

    // how many entries does the database contain
    static $entry_count        = 0;

    // Should the page numbers get filled
    // with zeros ?
    // Example: "03" will get "003"
    // - pages_zero_spacing: -1 = disabled
    // - pages_zero_spacing: 3 = 003
    static $pages_zero_spacing = -1;


    // these variables are needed for
    // internal calculations:
    static $half_pages = 0;
    static $complete_pages = 0;
    static $pages = 0;
    static $_given_current_page = 0;
    static $current_records = 0;

    //New Variables 
    static $glbListTotalCount=0;
    static $glbPageMutiple = 5;
    static $glbListStart = 0;
    static $glbListCount = 5;
    static $glbListTotalPage = 1;
    static $glbListCurPage = 1;
    static $glbListNext = 0;
    static $glbListPrevious = 0;
    static $glbListLastIndex = 0;
    static $glbListLast = 0;

    static $glbListStartRecord = 0;
    static $glbListEndRecord = 0;
    
    static $glbPageIndexStart = 1;
    static $glbPageIndexEnd = 5;
    static $glbPageIndexCount = 5;
    

    /*
    ** New Page Setup for Better Presentation
    **
    **
    */

    public function __construct() {
    
    }

    public function setupPaging($total){
        
        self::$glbListTotalCount = $total;
        if(!isset($_GET["start"]) && isset($_SESSION["start"])){
          self::$glbListStart = $_SESSION["start"];
        }elseif(isset($_GET["start"])){
          self::$glbListStart = $_GET["start"];
        }else{
          self::$glbListStart = 0;
        }	
        
        if(self::$glbListStart >= $total)
		{
            self::$glbListStart = 0;
        }
        
        $_SESSION["start"] = self::$glbListStart;
        
        if($total < self::$glbListStart){
            $_SESSION["start"] = self::$glbListStart = 0;
        }
        
		//Page count issue fixed on 25Aug2012.
        if(!isset($_GET["pcount"]) && isset($_SESSION["pcount_page"])){
          self::$glbListCount = $_SESSION["pcount_page"];
        }elseif(isset($_GET["pcount"])){
			  self::$glbListCount = $_GET["pcount"];
			  //New session variable
			  $_SESSION["pcount_page"] = self::$glbListCount;
        }else{
				self::$glbListCount = ROWS_PER_PAGE;
			}

        self::$glbListTotalPage = ceil(self::$glbListTotalCount/self::$glbListCount);
        self::$glbListCurPage = ceil(self::$glbListStart/self::$glbListCount)+1;
        self::$glbListNext = self::$glbListStart + self::$glbListCount;
        self::$glbListPrevious = self::$glbListStart - self::$glbListCount;
        self::$glbListLastIndex = self::$glbListTotalCount-(self::$glbListTotalCount % self::$glbListCount);
        self::$glbListLast = (self::$glbListLastIndex == self::$glbListTotalCount) ? (self::$glbListTotalCount - self::$glbListCount) : self::$glbListLastIndex;
        self::$glbListStartRecord = self::$glbListStart + 1;
        self::$glbListEndRecord = (self::$glbListLastIndex == self::$glbListStart) ? (self::$glbListStart + (self::$glbListTotalCount % self::$glbListCount)) : (self::$glbListStart + self::$glbListCount);

        self::$glbPageIndexEnd = self::$glbPageIndexStart + self::$glbPageIndexCount;
        self::$glbPageIndexStart = (self::$glbListCurPage >= (self::$glbPageIndexEnd)) ? (self::$glbPageIndexEnd ) : 1;
        self::$glbPageIndexEnd = self::$glbPageIndexStart + self::$glbPageIndexCount;
                
        
    }

    /*
    ** NEW set_per_page
    ** Created by Moses C Rathna Kumar 0n 10/17/2011
    ** Sets the number of entries that are displayed on each
    ** page.
    */

    static function getPageIndexStart(){ 
        return self::$glbPageIndexStart;
    }
    static function getPageIndexEnd(){ 
        return (self::$glbPageIndexEnd >= self::$glbListTotalPage ) ? (self::$glbListTotalPage+1)  : self::$glbPageIndexEnd ;
    }
    static function getPageIndexCount(){ 
        return self::$glbPageIndexCount;
    }

    static function setPerPage($n){ 
        $_SESSION["pcount_page"] = self::$glbListCount = $n; 
    }
    static function getPerPage(){ 
        return self::$glbListCount;
    }
    
    static function getCurPage(){ 
        return self::$glbListCurPage;
    }
    
    static function getTotalPage(){ 
            return self::$glbListTotalPage;
    }

    static function getTotalRecords(){ 
            return self::$glbListTotalCount;
    }

    
    static function getListMultiCountQuery($i){
        return "start=".($i*self::$glbListCount);
    }
    
    static function getFirstLinkQuery(){
        return (self::$glbListStart == 0) ? "" : "start=0"; 
    }

    static function getPreviousLinkQuery(){
        return (self::$glbListPrevious >= 0) ? "start=".self::$glbListPrevious : ""; 
    }
    
    static function getNextLinkQuery(){
        return (self::$glbListNext <= self::$glbListLast) ? "start=".self::$glbListNext : ""; 
    }
    
    static function getLastLinkQuery(){
        return (self::$glbListStart < self::$glbListLast) ? "start=".self::$glbListLast : ""; 
    }
    
    static function getRecordsDetails(){
        return "Records (". self::$glbListStartRecord." - ".self::$glbListEndRecord." ) of ".self::$glbListTotalCount;
    }
    
    
    static function getPageDetails(){
        return "Page ".self::$glbListCurPage." / ".self::$glbListTotalPage; 
    }
    
    static function getListStart(){
        return $_SESSION["start"];
    }    
    
    /*
    ** set_per_page
    **
    ** Sets the number of entries that are displayed on each
    ** page.
    */

    static function set_per_page($n){ 
        
         self::$per_page = $n; 
    }


    /*
    ** get_per_page
    **
    ** Returns the number of entries on each page.
    */

    static function get_per_page(){ return self::$per_page; }


    /*
    ** NEW set_current_page
    ** Created By Moses C Rathina Kumar on 10/17/2011
    ** Sets the current page
    */

    static function setStartRecord($start){
        self::$glbListStart = $start;
        //echo " - inside  = ".self::$glbListStart;
    }


    /*
    ** set_current_page
    **
    ** Sets the current page
    */

    static function set_current_page($p){

        // fix number:
        $cur_page = intval(preg_replace('/[^0-9]/', '', $p));
        $cur_page = ($cur_page<=0) ? 1 : $cur_page;

         self::$current_page = $cur_page;
         self::$_given_current_page = $cur_page;
    }


    /*
    ** get_curpage
    **
    ** Returns the current page
    */

    static function get_curpage(){ return self::$current_page; }


    /*
    ** set_max
    **
    **
    */

    static function set_max($n){ 
        @self::setupPaging($n);
        self::$entry_count = intval($n); 
    }


    /*
    ** calc
    **
    ** Calculates the page count.
    */

    static function calc(){

         self::$half_pages = self::$entry_count % self::$per_page;

         self::$complete_pages = self::$entry_count - self::$half_pages;

        self::$pages = self::$complete_pages / self::$per_page;
        if (self::$half_pages != 0){ self::$pages++; }

        // Page number is too high -> does not exist,
        // replace with last number
        if (self::$current_page > self::$pages){
           self::$current_page = self::$pages;
        }

    }


    /*
    ** get_first_id
    **
    ** Returns the first id.
    */

    static function get_first_id(){
        return (self::$current_page * self::$per_page) - self::$per_page;
    }


    /*
    ** get_last_id
    **
    ** Returns the last id.
    */

    static function get_last_id(){
       $r = self::$per_page - self::$half_pages;
        if (self::$pages == self::$current_page){
            return (self::$per_page * self::$current_page) - $r;
        }
        else {
            return (self::$per_page * self::$current_page);
        }
    }



    /*
    ** NEW get_sql_limit
    ** - Moses C Rathina on 10/17/2011
    ** Returns the SQL-Limit statement.
    */

    static function getSqlLimit(){        
		return ' LIMIT '.self::$glbListStart.',' . self::$glbListCount;

    }



    /*
    ** get_sql_limit
    **
    ** Returns the SQL-Limit statement.
    */

    static function get_sql_limit(){
        self::get_first_id();
        //return 'LIMIT '.self::get_first_id().',' . self::$per_page;
        return ' LIMIT '.self::$glbListStart.',' . self::$glbListCount;
    }


    /*
    ** get_prev_page
    **
    ** Returns the previous page.
    */

    static function get_prev_page(){
        $id = self::$current_page - 1;
        return ($id <= 0) ? 1 : $id;
    }


    /*
    ** get_page_count
    **
    ** Returns the count of all pages.
    */

    static function get_page_count(){ return self::$pages; }


    /*
    ** get_next_page
    **
    **
    */

    static function get_next_page(){
        // create back page
        $id = self::$current_page + 1;
        return ($id > self::$pages) ? self::$pages : $id;
    }


    /*
    ** exists_page
    **
    ** Returns whether a page exists or not.
    */

    static function exists_page($p){
        $p = intval($p);
        return ($p > self::$pages || $p <= 0) ? false : true;
    }


    /*
    ** exists_current_page
    **
    ** Returns whether the current page exists or not.
    */

    static function exists_current_page(){
        return self::$exists_page(self::$_given_current_page);
    }


    /*
    ** get_prev_link
    **
    ** Returns the previous link.
    */

    static function get_prev_link($link_template, $no_link=''){

       // if (self::$pages <= 1){ return ''; }

        // create back page
        $back_id = self::get_prev_page();

        if (self::$current_page > 1){
            $link_template = str_replace('%pg', $back_id, $link_template);
        }
        else { $link_template = $no_link; }

        return $link_template;
    }


    /*
    ** get_next_link
    **
    ** Returns the next link.
    */

    static function get_next_link($link_template, $no_link=''){

        // create back page
        $id = self::get_next_page();
          /*echo "curr:".self::$current_page;
                    echo "page:".self::$pages;  */
        if (self::$current_page < self::$pages){
            $link_template = str_replace('%pg', $id, $link_template);
        }
        else { $link_template = $no_link; }

        return $link_template;
    }


    /*
    ** showing_from_to
    **
    **
    */

    static function showing_from_to($link_template){

        /*$link_template = str_replace('%from', self::get_first_id(), $link_template);
        $link_template = str_replace('%to', self::get_last_id(), $link_template);*/
        $from = self::get_first_id()+1;
        //echo self::get_last_id();

        $to = (self::get_last_id() == '0') ? ROWS_PER_PAGE : self::get_first_id()+$link_template;
        return $from." - ".$to;

    }


    /*
    ** get_pages
    **
    ** Returns the listing of all pages. The links can be formatted
    ** trought the parameters.
    */

    static function get_pages($link_template, $spacer=" ", $s, $e){

        $page_links = array();

        for ($x=1; $x <= self::$pages; $x++){
            $num_title = (self::$pages_zero_spacing != -1 && (self::$pages_zero_spacing - strlen($x)) > 0) ? str_repeat('0', self::$pages_zero_spacing - strlen($x)) . $x : $x;
            $link = str_replace('%pg', $x, $link_template);
            $link = str_replace('%title', $num_title, $link);
            $link = ($x == self::$current_page) ? $s.$link.$e : $link;
            $page_links[] = $link;
        }

        $page_links = implode($spacer, $page_links);

        return $page_links;
    }


    /*
    ** get_dynamic_centered_pages
    **
    ** link_template  =  Link template (Example: '<a href="?pg=%pg">%title</a>')
    ** spacer         =  Spacer (Example: ' ' or ',')
    ** start          =  Current page start Tag (Example: '<b>')
    ** end            =  Current page end Tag (Example: '</b>')
    ** per_page       =  Show X page numbers in navigation (Example: 3)
    **
    ** Returns: "�� 01 02 03 04 05 06 07 ��" (HTML)
    */

    static function get_dynamic_centered_pages($_link_template, $_spacer, $_start, $_end, $_per_page = 3){

        $page_links = array();

        $start = self::$current_page - $_per_page;
        $start = ($start < 1) ? 1 : $start;

        $end = self::$current_page + $_per_page;
        $end = ($end > self::$pages) ? self::$pages : $end;

        // first page:
        $x = 1; $num_title = '&laquo;&laquo;';
        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $link = ($x == self::$current_page) ? $_start.$link.$_end : $link;
        $page_links[] = $link;


        $dx = (($_per_page*2)-($end - $start));

        if (($end+$dx) > self::$pages)
            $start -= $dx;
        else
            $end += $dx;


        // repeat pages:
        for ($x = $start; $x <= $end; $x++){
            $num_title = (self::$pages_zero_spacing != -1 && (self::$pages_zero_spacing - strlen($x)) > 0) ? str_repeat('0', self::$pages_zero_spacing - strlen($x)) . $x : $x;
            $link = str_replace('%pg', $x, $_link_template);
            $link = str_replace('%title', $num_title, $link);
            $link = ($x == self::$current_page) ? $_start.$link.$_end : $link;
            $page_links[] = $link;
        }

        // last page:
        $x = self::$pages; $num_title = '&raquo;&raquo;';

        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $link = ($x == self::$current_page) ? $_start.$link.$_end : $link;
        $page_links[] = $link;

        // combine links:
        $page_links = implode($_spacer, $page_links);
        return $page_links;
    }


    /*
    ** get_dynamic_parent_pages
    **
    ** link_template  =  Link template (Example: '<a href="?pg=%pg">%title</a>')
    ** spacer         =  Spacer (Example: ' ' or ',')
    ** start          =  Current page start Tag (Example: '<b>')
    ** end            =  Current page end Tag (Example: '</b>')
    ** per_page       =  Show X page numbers in navigation (Example: 8)
    **
    ** Returns: "�� � 001 002 003 004 005 006 � ��" (HTML)
    */

    static function get_dynamic_parent_pages($_link_template='<a href="?pg=%pg">%title</a>', $_spacer=' ', $_start='<b>', $_end='</b>', $_per_page = 8,$mode ="default"){

        $p_pages = ceil(self::$pages / $_per_page);

        $start = floor(self::$current_page / $_per_page) * $_per_page;

        if ((self::$current_page % $_per_page) == 0)
            $start = (floor(self::$current_page / $_per_page)-1) * $_per_page;

        $end = $start + $_per_page;

        $start++;

        $page_links = array();

        // first page:
        $x = 1; $num_title = '&laquo;&laquo;';
        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $page_links[] = $link;

        // prev. page:

        $start = ($start < 1) ? 1 : $start;

        $x = $start - 1;
        $x = ($x < 1) ? 1 : $x;
        if($mode == "default"){
        $num_title = '&laquo;';
        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $page_links[] = $link;

        // remove pages on last:
        if (ceil(self::$current_page / $_per_page) == ceil(self::$pages / $_per_page))
            $end -= $_per_page - (self::$pages % $_per_page);

        for ($x = $start; $x <= $end; $x++){
            $num_title = (self::$pages_zero_spacing != -1 && (self::$pages_zero_spacing - strlen($x)) > 0) ? str_repeat('0', self::$pages_zero_spacing - strlen($x)) . $x : $x;
            $link = str_replace('%pg', $x, $_link_template);
            $link = str_replace('%title', $num_title, $link);
            $link = ($x == self::$current_page) ? $_start.$link.$_end : $link;
            $page_links[] = $link;
        }

        // next page:
        $x = $end+1;
        $x = ($x > self::$pages) ? self::$pages : $x;
        $num_title = '&raquo;';
        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $page_links[] = $link;
                 }
        // last page:
        $x = self::$pages; $num_title = '&raquo;&raquo;';
        $link = str_replace('%pg', $x, $_link_template);
        $link = str_replace('%title', $num_title, $link);
        $page_links[] = $link;

        // combine links:
        if($mode == "default"){
                $page_links = implode($_spacer, $page_links);
                }

                return $page_links;
    }


    /*
    ** get_select_box
    **
    ** Creates a select box by page numbers
    */

    static function get_select_box($name, $prefix='', $onchange=''){

        $options = array();

        for ($x=1; $x <= self::$pages; $x++){
            $sel = ($x == self::$current_page) ? ' selected="selected"' : '';
            $link = '<option value="'.$x.'"'.$sel.'>' . $prefix . $x . '</option>';
            $options[] = $link;
        }

        $onchange = ($onchange == '') ? '' : ' onchange="'.$onchange.'"';

        $options = implode("\n", $options);
        $options = "<select name=\"$name\"$onchange>\n$options\n</select>";

        return $options;
    }

    static function get_current_page(){


        return self::$current_page;
    }
    static function get_page(){


        return self::$pages;
    }

    /*
    ** page_x_of
    **
    **
    */

    static function page_x_of($link_template){

        $link_template = str_replace('%current', self::$current_page, $link_template);
        $link_template = str_replace('%cur', self::$current_page, $link_template);
        $link_template = str_replace('%all', self::$pages, $link_template);

        return $link_template;
    }

    // Follow the white rabbit.
}

?>