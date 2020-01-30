<?php

namespace IdentificationBundle\BillingFramework;

use phpDocumentor\Reflection\Types\Self_;

class ID
{
    const BRAZIL_TIM             = 2200;
    const TELENOR_PAKISTAN       = 381;
    const MOBILINK_PAKISTAN      = 338;
    const ZONG_PAKISTAN          = 340;
    const ETISALAT_EGYPT         = 136;
    const MTN_SUDAN              = 348;
    const SMARTFEN_INDONESIA     = 36;
    const AIRTEL_INDIA           = 1000;
    const DIALOG_SRILANKA        = 347;
    const JAWWAL_PALESTINE       = 1004;
    const TELKOM_KENYA           = 2201;
    const ORANGE_TUNISIA         = 364;
    const TELENOR_MYANMAR        = 384;
    const ORANGE_EGYPT           = 30;
    const TELE2_RUSSIA           = 386;
    const TELE2_RUSSIA_MEGASYST  = 390;
    const ZAIN_IRAQ              = 1020;
    const ROBI_BANGLADESH        = 388;
    const GLOBE_PHILIPPINES      = 382;
    const OOREDOO_ALGERIA        = 275;
    const CELLCARD_CAMBODIA      = 2207;
    const KCELL_KAZAKHSTAN       = 373;
    const TIGO_HONDURAS          = 2220;
    const CLARO_NICARAGUA        = 389;
    const VODAFONE_EGYPT         = 31;
    const ZAIN_KUWAIT            = 2225;
    const OOREDOO_KUWAIT         = 2059;
    const HUTCH3_INDONESIA       = 391;
    const OOREDOO_OMAN           = 2064;
    const INDOSAT_INDONESIA      = 392;
    const OOREDOO_QATAR          = 393;
    const CELCOM_MALAYSIA        = 383;
    const OOREDOO_MYANMAR        = 2233;
    const DU_UAE                 = 2008;
    const TELENOR_PAKISTAN_DOT   = 2252;
    const VODAFONE_EGYPT_TPAY    = 2253;
    const ORANGE_EGYPT_TPAY      = 2254;
    const HUTCH3_INDONESIA_DOT   = 2255;
    const ORANGE_TUNISIA_MM      = 2256;
    const ZAIN_SAUDI_ARABIA      = 2257;
    const VIVA_BAHRAIN_MM        = 2258;
    const VODAFONE_EGYPT_MM      = 2259;
    const TMOBILE_POLAND_DIMOCO  = 2260;
    const BEELINE_KAZAKHSTAN_DOT = 2327;
    const ORANGE_EG_MM           = 2328;
    const VODAFONE_DE_DIMOCO     = 2320;
    const TELEKOM_DE_DIMOCO      = 2321;
    const O2_DE_DIMOCO           = 2322;
    const DEBITEL_DE_DIMOCO      = 2323;


    const DE_DIMOCO_CARRIERS = [
        self::DEBITEL_DE_DIMOCO,
        self::O2_DE_DIMOCO,
        self::TELEKOM_DE_DIMOCO,
        self::VODAFONE_DE_DIMOCO,
    ];

    const MM_CARRIERS = [
        self::VODAFONE_EGYPT_MM,
        self::VIVA_BAHRAIN_MM,
        self::ORANGE_EG_MM,
        self::ORANGE_TUNISIA_MM
    ];
}
