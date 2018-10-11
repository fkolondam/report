<?php
$year = 2018;

$weeks = getIsoWeeksInYear($year);

for($x=1; $x<=$weeks; $x++){
    $dates = getStartAndEndDate($x, $year);
    echo $x . " - " . $dates['week_start'] . ' - ' . $dates['week_end'] . "<br>";
}

function getIsoWeeksInYear($year) {
    $date = new DateTime;
    $date->setISODate($year, 53);
    return ($date->format("W") === "53" ? 53 : 52);
}

function getStartAndEndDate($week, $year) {
  $dto = new DateTime();
  $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
  $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
  return $ret;
}
?>