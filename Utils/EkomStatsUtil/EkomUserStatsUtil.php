<?php


namespace Module\Ekom\Utils\EkomStatsUtil;


use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;

class EkomUserStatsUtil
{


    public static function getGenderDistribution(array $options = [])
    {

        $markers = [];
        $q = "
select 
g.name,
count(u.id) as count

from ek_gender g 
inner join ek_user u on u.gender_id=g.id 

        ";


        $dateRange = $options['date_range'] ?? null;

        if ($dateRange) {
            list($dateStart, $dateEnd) = $dateRange;
            QuickPdoStmtTool::addDateRangeToQuery($q, $markers, $dateStart, $dateEnd, "u.date_creation");
        }


        $q .= "
group by g.id                
        ";

        return QuickPdo::fetchAll($q, $markers, \PDO::FETCH_COLUMN | \PDO::FETCH_UNIQUE);
    }


    public static function getAgeDistribution(array $options = [])
    {



        $markers = [];
        $dateRanges = [
            [0, 17],
            [18, 24],
            [25, 34],
            [35, 49],
            [50, 59],
            [60, null],
        ];


        $sQuery = "";
        foreach ($dateRanges as $range) {
            list($ageStart, $ageEnd) = $range;
            if (null !== $ageEnd) {
                $groupName = "$ageStart-$ageEnd ans";
                $sQuery .= 'WHEN age BETWEEN ' . $ageStart . ' AND ' . $ageEnd . ' THEN \'' . $groupName . '\'';
            } else {
                $groupName = "$ageStart ans et +";
                $sQuery .= 'WHEN age >= ' . $ageStart . ' THEN \'' . $groupName . '\'';
            }
            $sQuery .= PHP_EOL;
        }



        $qInner = "
        SELECT id,
               YEAR(CURDATE()) - 
               YEAR(date(birthday)) - 
               (RIGHT(CURDATE(), 5) < RIGHT(date(birthday), 5)) 
                 AS Age
        FROM ek_user        
        ";


        $dateRange = $options['date_range'] ?? null;
        if ($dateRange) {
            list($dateStart, $dateEnd) = $dateRange;
            QuickPdoStmtTool::addDateRangeToQuery($qInner, $markers, $dateStart, $dateEnd, "date_creation");
        }

        $q = "


SELECT
CASE 
$sQuery 
END AS agegroup, count(age) AS total

FROM (
    SELECT age
    FROM
    (
      $qInner  
    ) as Z   
) as tt
GROUP BY agegroup



        ";




        return QuickPdo::fetchAll($q, $markers);
    }


}