<?php


namespace App\Domain\Service\OneClickFlow;


use App\Domain\Entity\Campaign;

class OneClickFlowScheduler
{
    public function isNowInCampaignSchedule(array $schedule): bool
    {
        $today    = date('N') - 1;
        $time     = date('H:i');
        $aPeriods = $schedule[$today]['periods'];
        foreach ($aPeriods as $period) {
            if ($period['start'] <= $time && $time <= $period['end']) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $sSchedule
     *
     * @return array|null
     */
    public function getScheduleAsArray(string $sSchedule): ?array
    {
        if (empty($sSchedule)) {
            return null;
        }
        $aSchedule = json_decode("[$sSchedule]", true);
        $count     = 0;
        array_walk($aSchedule, function (&$item) use (&$count) {
            if (!empty($item['periods'])) {
                $count++;
            }
        });
        return $count == count($aSchedule) ? $aSchedule : null;
    }
}