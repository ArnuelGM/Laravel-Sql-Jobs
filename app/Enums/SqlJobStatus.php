<?php

namespace App\Enums;

enum SqlJobStatus {

  case PENDING      = 'PENDING';
  case IN_PROGRESS  = 'IN_PROGRESS';
  case DONE         = 'DONE';
  case CANCELLED    = 'CANCELLED';

}
