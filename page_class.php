<?php
error_reporting(0); // disable the annoying error report
class page_class
{
    // Properties
    var $current_page;
    var $amount_of_data;
    var $page_total;
    var $row_per_page;

    // Constructor
    function page_class($rows_per_page)
    {
        $this->row_per_page = $rows_per_page;

        $this->current_page = $_GET['page'];
        if (empty($this->current_page))
            $this->current_page = 1;
    }

    function specify_row_counts($amount)
    {
        $this->amount_of_data = $amount;
        $this->page_total=
            ceil($amount / $this->row_per_page);
    }

    function get_starting_record()
    {
        $starting_record = ($this->current_page - 1) *
            $this->row_per_page;
        return $starting_record;
    }

    function show_pages_link()
    {
        if ($this->page_total > 1)
        {
            print("<center><div class=\"notice\"><span class=\"note\">Page: ");
            for ($hal = 1; $hal <= $this->page_total; $hal++)
            {
                if ($hal == $this->current_page)
                    echo "$hal | ";
                else
                {
                    $script_name = $_SERVER['PHP_SELF'];
                    echo "<a href=\"$script_name?page=$hal\">$hal</a> |\n";
                }
            }
        }
    }
}