<?php

namespace App\Helpers\Statistics;

class StatisticsHelper
{
    public function getData()
    {
        // Check member is PRO +
        //$isProMember = auth()->user()->isProMember;
        $isProMember = true;
        if ($isProMember) {
            return $this->proAnalysis();
        } else {
            return $this->basicAnalysis();
        }
    }

    private function basicAnalysis()
    {

    }

    private function proAnalysis()
    {

    }
}
