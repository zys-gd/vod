<?php


namespace App\OneClickFlow;


class OneClickFlowParameters
{
    const IS_CONFIRMATION_CLICK = 1;
    const IS_CONFIRMATION_POP_UP = 2;
    const IS_LP_OFF = 3;

    const AVAILABLE_PARAMETERS = [
        'IS_CONFIRMATION_CLICK' => self::IS_CONFIRMATION_CLICK,
        'IS_CONFIRMATION_POP_UP'=> self::IS_CONFIRMATION_POP_UP,
        'IS_LP_OFF' => self::IS_LP_OFF
    ];
}