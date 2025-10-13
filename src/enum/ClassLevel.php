<?php
namespace App\Enum;

enum ClassLevel: string
{
    case SIXIEME = '6e';
    case CINQUIEME = '5e';
    case QUATRIEME = '4e';
    case TROISIEME = '3e';
    case SECONDE = '2nde';
    case PREMIERE = '1ère';
    case TERMINALE = 'Terminale';
    case BACPLUS1 = 'Bac+1';
    case BACPLUS2 = 'Bac+2';
    case BACPLUS3 = 'Bac+3';
    case BACPLUS4 = 'Bac+4';
    case BACPLUS5 = 'Bac+5';
}