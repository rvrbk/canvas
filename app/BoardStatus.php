<?php

namespace App;

enum BoardStatus: string
{
    case Open = 'open';
    case Completed = 'completed';
}
