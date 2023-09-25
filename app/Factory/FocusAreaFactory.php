<?php

namespace App\Factory;

use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\Program;


class FocusAreaFactory
{

    /**
     * @return FocusArea
     */
    public function create(): FocusArea
    {
        return new FocusArea();
    }
}