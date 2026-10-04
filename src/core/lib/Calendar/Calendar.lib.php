<?php
/**
 * $Id: Calendar.lib.php 168 2014-04-26 07:28:51Z radu $
 *
 * Calendar library.
 * 
 * Credits: http://cool-php-tutorials.blogspot.hu/2012/01/better-php-calendar-class.html
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebCalendar
 * @version    $Revision: 168 $
 * @since      Version 5
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (!defined('_WEB'))
{
	/**
	 * Stop script execution.
	 */
	exit();
}


/**
 * Calendar
 * 
 * Calendar library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @package    WEb
 * @subpackage WebCalendar
 *
 */
class Calendar extends Library
{
	/**
	 * Library properties.
	 *
	 * @var array
	 */
	private $_mProp = array();

	/**
	 * Implement GetInstance() method.
	 *
	 * This is required for PHP < 5.3 compatibility (no late static binding)
	 *
	 * @param string $className class name to instantiate
	 *
	 * @return object
	 */
	public static function GetInstance($className = __CLASS__)
	{
		/**
		 * Call parent method with current class name as parameter.
		 */
		return parent::_GetInstance($className);
	}
	
	public function GetWeekCount($month, $year) {
		
		// every month has at least 4 weeks
        $num_weeks=4;
    
        // finding where it starts
        $first_day = $this->GetFirstDay($month, $year);  
        
        // if the first week doesn't start on monday 
        // we are sure that the month has at minimum 5 weeks
        if($first_day!=1) $num_weeks++;
        
        // find the "widow" days (i.e. empty cells in the 1st week)
        $widows=$first_day-1;  
        $fw_days=7-$widows;
        if($fw_days==7) $fw_days=0;       
        
        // number of days in the month
        $numdays=date("t",mktime(2, 0, 0, $month, 1, $year));
        
        if( ($numdays - $fw_days) > 28 ) $num_weeks++;
         // that's it!
        return $num_weeks;
	}
	
	public function GetFirstDay($month, $year) {
		
		$first_day= date("w", mktime(2, 0, 0, $month, 1, $year));
        
        if($first_day==0) $first_day=7; # convert Sunday
        
        return $first_day;
	}
	
	public function GetDays($month, $year, $week, $num_weeks = 0) {
		
		$days=array();

        // this is just to avoid calling num_weeks every time 
        // when you loop through the weeks        
        if(empty($num_weeks)) $num_weeks=$this->GetWeekCount($month, $year);
        
        // find which day of the week is 1st of the given month        
        $first_day = $this->GetFirstDay($month, $year);
                
        // find widow days (first week)
        $widows=$first_day-1;
        
        // first week days
        $fw_days=7-$widows;
        
        // if $week==1 don't do further calculations
        if($week==1)
        {
            for($i=0;$i<$widows;$i++) $days[]=0;            
            for($i=1;$i<=$fw_days;$i++) $days[]=$i;            
            return $days;
        }
        
        // any other week
        if($week!=$num_weeks)
        {
            $first=$fw_days+(($week-2)*7);
            for($i=$first+1;$i<=$first+7;$i++) $days[]=$i;            
            return $days;
        }
        
        
        # only last week calculations below
        
        // number of days in the month
        $numdays=date("t",mktime(2, 0, 0, $month, 1, $year));
                
        // find orphan days (last week)  
        $orphans=$numdays-$fw_days-(($num_weeks-2)*7);                     
        $empty=7-$orphans;
        for($i=($numdays-$orphans)+1;$i<=$numdays;$i++) $days[]=$i;
        for($i=0;$i<$empty;$i++) $days[]=0;
        return $days;
	}
	
	public function GetNextMonth($month, $year) {
		
		$date = mktime(0, 0, 0, $month+1, 1,  $year);
		
		return array(date('m', $date), date('Y', $date));
	}
	
	public function GetPreviousMonth($month, $year) {
		
		$date = mktime(0, 0, 0, $month-1, 1,  $year);
		
		return array(date('m', $date), date('Y', $date));
	}
	
	public function GetCalendar($month, $year) {
		
		$calendar = array();
		
		$weekCount = $this->GetWeekCount($month, $year);
		
		for ($i = 1; $i <= $weekCount; $i++) {
			$calendar[$i] = $this->GetDays($month, $year, $i, $weekCount);
		}
		
		return $calendar;
	}
}
?>