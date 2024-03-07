<?php

namespace App\Enums;

enum AppointmentsStatus: int{
    case SCHEDULED = 1;
    case CONFIRMED  = 2;
    case CANCELLED = 3;

    public function color(): string {
        return match($this){
            AppointmentsStatus::SCHEDULED => 'primary',
            AppointmentsStatus::CONFIRMED => 'success',
            AppointmentsStatus::CANCELLED => 'danger',
        };
    }

}