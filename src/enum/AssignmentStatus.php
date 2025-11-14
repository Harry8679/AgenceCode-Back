<?php

enum AssignmentStatus: string {
  case REQUESTED = 'REQUESTED'; // demande parent sans prof (teacher = null)
  case APPLIED   = 'APPLIED';   // candidature prof sur un enfant/matière
  case PROPOSED  = 'PROPOSED';  // proposition admin au prof
  case ACCEPTED  = 'ACCEPTED';
  case DECLINED  = 'DECLINED';
  case CANCELLED = 'CANCELLED';
}