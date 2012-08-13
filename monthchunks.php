<?php
/*
Plugin Name: monthchunks
Version: 2.0
Plugin URI: http://justinsomnia.org/2005/04/monthchunks-howto/
Description: Display your monthly archives by year with individual links to each month. Replacement for <code>wp_get_archives('type=monthly')</code>
Author: Justin Watt
Author URI: http://justinsomnia.org/

INSTRUCTIONS

1) Save this file as monthchunks.php in /path/to/wordpress/wp-content/plugins/ 
2) Activate "monthchunks" from the Wordpress control panel
3) In your sidebar.php template file, replace wp_get_archives('type=monthly'); with monthchunks();


CHANGELOG

2.0
removed <ul></ul> output to make monthchunks more of a drop-in replacement for wp_get_archives()
added logic to de-link from current month
sort years in chronlogical order
don't print separator space after last month of year

1.2
used $wpdb->posts instead of wp_posts as table name

1.1
used wordpress's get_month_link() function to output link to monthly archive (thanks rapha�le)

1.0
inital version
turned custom_archive function into monthchunks plugin (thanks jackson)


LICENSE

monthchunks.php
Copyright (C) 2005 Justin Watt
justincwatt@gmail.com
http://justinsomnia.org/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

function monthchunks()
{
    // get access to wordpress' database object
    global $wpdb;
    $current_month = "";
    $current_year  = "";
    
    // get current year/month if current page is monthly archive
    if (is_month())
    {
        $current_month = get_the_time('n');
        $current_year  = get_the_time('Y');
    }
    
    // get an array of the years in which there are posts
    $wpdb->query("SELECT DATE_FORMAT(post_date, '%Y') as post_year
                  FROM $wpdb->posts
                  GROUP BY post_year
                  ORDER BY post_year");
    $years = $wpdb->get_col();
    
    // each list item will be the year and the months which have blog posts
    foreach($years as $year)
    {
        // get an array of months for the current year without leading zero
        // sort by month with leading zero
        $wpdb->query("SELECT DATE_FORMAT(post_date, '%c') as post_month
                      FROM $wpdb->posts
                      WHERE DATE_FORMAT(post_date, '%Y') = $year
                      GROUP BY post_month
                      ORDER BY DATE_FORMAT(post_date, '%m')");
        $months = $wpdb->get_col();

        // start the list item displaying the year
        print "<li><strong>$year</strong><br />\n";
        
        // loop through each month, creating a link
        // followed by a single space
        $month_count = count($months);
        foreach($months as $month_index => $month)
        {
            if ($year == $current_year && $month == $current_month)
            {
                print "<strong>$month</strong>";
            }
            else
            {
                print "<a href='" . get_month_link($year, $month) . "'>" . $month . "</a>";
            }

            if ($month_index < $month_count-1)
            {
                print " ";
            }
        }

        //end the year list item
        print "</li>\n";
    }
}

?>