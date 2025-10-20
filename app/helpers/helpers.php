<?php

if (!function_exists('formatGeneralDate')) {
    function formatGeneralDate($date) {
        if (!$date) {
            return ''; // Return empty string for null or invalid dates
        }

        // Parse the date if it's not already a Carbon instance
        $carbonDate = $date instanceof \Carbon\Carbon
            ? $date
            : \Carbon\Carbon::parse($date);

        // Return formatted date based on year
        return $carbonDate->year === now()->year
            ? $carbonDate->format('d.m')
            : $carbonDate->format('d.m.Y');
    }
}

if (! function_exists('myP')) {
    function myP($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

if (! function_exists('myDate')) {
    function myDate($date, $format)
    {
        $myDate = date($format, strtotime($date));

        return $myDate;
    }
}

if (! function_exists('formatDecimal')) {
    function formatDecimal($number)
    {
        if ($number < 10) {

            return number_format($number, 2);
        } elseif ($number < 100) {

            return number_format($number, 1);
        } else {

            return number_format($number, 0);
        }
    }
}

if (! function_exists('Buffer')) {
    function Buffer($MSQ, $PP_RMB, $IsPUItem, $IsMeterItem)
    {
        // Buffer for standard items
        $buffer = 1 + intval($MSQ / 25);

        // $buffer = 5 + intval($MSQ / 10); // Replace 5 and 10 with actual values for debugging

        // If Buffer below limit then set to 0
        if ($MSQ <= 3) {
            $buffer = 0;
        }

        // Buffer for special items (expensive / VPE / Meterware items) =14
        if ($PP_RMB > 14) {
            $buffer = intval($MSQ / 25);
        }

        if ($IsPUItem === 1) {
            $buffer = 0;
        }

        if ($IsMeterItem === 1) {
            $buffer = 5 + intval($MSQ / 25);
        }

        return $buffer;
    }
}

if (! function_exists('SupplierMOQ')) {
    function SupplierMOQ($MSQ, $PP_RMB, $IsPUItem, $IsMeterItem)
    {
        // SUPPLIERMOQ for standard items
        $SUPPLIERMOQ = round(0.5 * $MSQ, -1);

        // Buffer for special items (expensive / VPE / Meterware items)
        if ($PP_RMB > 14) {
            $SUPPLIERMOQ = intval(0.3 * $MSQ);
        }

        if ($IsPUItem === 1) {
            $SUPPLIERMOQ = 1;
        }

        return $SUPPLIERMOQ;
    }
}
if (! function_exists('SUPPLIERINTERVAL')) {
    function SUPPLIERINTERVAL($SUPPLIERMOQ, $PP_RMB, $IsPUItem, $IsMeterItem)
    {
        // SUPPLIERINTERVAL for standard items
        $SUPPLIERINTERVAL = intval(0.5 * $SUPPLIERMOQ);

        if ($SUPPLIERMOQ == 10) {
            if ($PP_RMB < 1) {
                $SUPPLIERINTERVAL = 10;
            } elseif ($PP_RMB >= 1) {
                $SUPPLIERINTERVAL = 5;
            }
        }

        // Buffer for special items (expensive / VPE / Meterware items)
        if ($PP_RMB > 14) {
            $SUPPLIERINTERVAL = 1;
        }

        if ($IsPUItem === 1) {
            $SUPPLIERINTERVAL = 1;
        }

        if ($IsMeterItem === 1) {
            $SUPPLIERINTERVAL = 10;
        }

        return $SUPPLIERINTERVAL;
    }
}
if (! function_exists('ShippingClass')) {
    function ShippingClass($Weight, $Length, $Width, $Height)
    {

        if ($Weight == 0 or $Height == 0 or $Width == 0 or $Length == 0) {
            $ShippingClass = 'Na';
        } elseif ($Weight <= 0.036 && $Height <= 0.9 && $Width <= 8 && $Length <= 18.5) {
            $ShippingClass = '1';
        } elseif ($Weight <= 0.47 && $Height <= 1.9 && $Width <= 20 && $Length <= 35.5) {
            $ShippingClass = '2';
        } elseif ($Weight <= 0.96 && $Height <= 4.8 && $Width <= 20 && $Length <= 35.5) {
            $ShippingClass = '3';
        } elseif ($Length >= 95 && $Length < 115) {
            $ShippingClass = '5';
        } elseif ($Length >= 115) {
            $ShippingClass = '6';
        } else {
            $ShippingClass = '4';
        }

        return $ShippingClass;
    }
}
if (! function_exists('EK_net')) {
    function EK_net($PP_RMB, $cat_id) // Euro price
    {// dd($PP_RMB, $cat_id);
        $Exchange_RMBtoEUR = 7.8;
        $BankCharges_RMB = 0.008;
        $F = 0;

        // STD; && $cat_id !=3 && $cat_id !=4 && $cat_id !=5
        switch ($cat_id != 2 && $cat_id != 3 && $cat_id != 4 && $cat_id != 5) {
            case $PP_RMB >= 0 && $PP_RMB <= 0.25:
                $F = 2.5;
                break;
            case $PP_RMB > 0.25 && $PP_RMB <= 0.5:
                $F = 2.25;
                break;
            case $PP_RMB > 0.5 && $PP_RMB <= 1:
                $F = 2;
                break;
            case $PP_RMB > 1 && $PP_RMB <= 2:
                $F = 1.8;
                break;
            case $PP_RMB > 2 && $PP_RMB <= 4:
                $F = 1.7;
                break;
            case $PP_RMB > 4 && $PP_RMB <= 8:
                $F = 1.6;
                break;
            case $PP_RMB > 8 && $PP_RMB <= 15:
                $F = 1.55;
                break;
            case $PP_RMB > 15 && $PP_RMB <= 30:
                $F = 1.5;
                break;
            case $PP_RMB > 30 && $PP_RMB <= 100:
                $F = 1.45;
                break;
            case $PP_RMB > 100 && $PP_RMB <= 500:
                $F = 1.4;
                break;
            default:
                $F = 1.35;
                break;
        }
        // GBL;
        switch ($cat_id === 2) {
            case $PP_RMB >= 0:
                $F = 1.7;
                break;
        }

        // GTR;
        switch ($cat_id === 3) {
            case $PP_RMB >= 0:
                $F = 1.6;
                break;
        }

        // PRO;
        switch ($cat_id === 4) {
            case $PP_RMB >= 0:
                $F = 1.3;
                break;
        }

        // ERS;
        switch ($cat_id === 5) {
            case $PP_RMB >= 0:
                $F = 1.3;
                break;
        }
        // Calculate EK_net
        // dd($F, $PP_RMB, $cat_id);
        $EK_net = round($F * $PP_RMB / $Exchange_RMBtoEUR * (1 + $BankCharges_RMB), 2);

        return $EK_net;
    }
}
if (! function_exists('FreightCostsVolume')) {
    function FreightCostsVolume($Weight, $ItemVolumeDM3)
    {
        $FreightPerCBM = 250;
        $FreightKGperCBM = 500;
        $PackageAddon = 0.1;

        // Calculate Freight Cost Per Volume in EUR
        $FreightCostsVolume = $ItemVolumeDM3 * (1 + $PackageAddon) * ($FreightPerCBM / 1000);

        return $FreightCostsVolume;
    }
}
if (! function_exists('FreightCostsWeight')) {
    function FreightCostsWeight($Weight, $ItemVolumeDM3)
    {
        $FreightPerCBM = 250;
        $FreightKGperCBM = 500;
        $PackageAddon = 0.1;

        // Calculate Freight Cost Per Weight in EUR
        $FreightCostsWeight = $Weight / $FreightKGperCBM * $FreightPerCBM;

        return $FreightCostsWeight;
    }
}
if (! function_exists('SPdeNET')) {
    function SPdeNET($PurchasePriceEUR, $OrderQty, $ItemFreightCostEUR, $ImportDutyChargeEUR, $ManyComponentsItemRating, $ItemEffortRating)
    {
        // echo $ManyComponentsItemRating;
        $CostEUR = $PurchasePriceEUR + $ItemFreightCostEUR + $ImportDutyChargeEUR; // 1.24

        switch ($OrderQty) {
            case 1:
                $MarginFactor = [4, 3.75, 3.5, 3.25, 3, 2.75, 2.5, 2.25, 2, 1.8, 1.6, 1.4, 1.3, 1.2, 1.1, 1.05, 1, 0.95, 0.9, 0.85, 0.8, 0.75];
                break;
            case 2:
                $MarginFactor = [3.75, 3.518, 3.286, 3.054, 2.822, 2.59, 2.358, 2.126, 1.894, 1.707, 1.52, 1.333, 1.236, 1.139, 1.042, 0.99, 0.938, 0.886, 0.834, 0.782, 0.73, 0.678];
                break;
            case 5:
                $MarginFactor = [3.5, 3.286, 3.072, 2.858, 2.644, 2.43, 2.216, 2.002, 1.788, 1.614, 1.44, 1.266, 1.172, 1.078, 0.984, 0.93, 0.876, 0.822, 0.768, 0.714, 0.66, 0.606];
                break;
            case 10:
                $MarginFactor = [3.25, 3.054, 2.858, 2.662, 2.466, 2.27, 2.074, 1.878, 1.682, 1.521, 1.36, 1.199, 1.108, 1.017, 0.926, 0.87, 0.814, 0.758, 0.702, 0.646, 0.59, 0.534];
                break;
            case 25:
                $MarginFactor = [3, 2.822, 2.644, 2.466, 2.288, 2.11, 1.932, 1.754, 1.576, 1.428, 1.28, 1.132, 1.044, 0.956, 0.868, 0.81, 0.752, 0.694, 0.636, 0.578, 0.52, 0.462];
                break;
            case 50:
                $MarginFactor = [2.75, 2.59, 2.43, 2.27, 2.11, 1.95, 1.79, 1.63, 1.47, 1.335, 1.2, 1.065, 0.98, 0.895, 0.81, 0.75, 0.69, 0.63, 0.57, 0.51, 0.45, 0.39];
                break;
            case 100:
                $MarginFactor = [2.5, 2.358, 2.216, 2.074, 1.932, 1.79, 1.648, 1.506, 1.364, 1.242, 1.12, 0.998, 0.916, 0.834, 0.752, 0.69, 0.628, 0.566, 0.504, 0.442, 0.38, 0.318];
                break;
            case 200:
                $MarginFactor = [2.25, 2.126, 2.002, 1.878, 1.754, 1.63, 1.506, 1.382, 1.258, 1.149, 1.04, 0.931, 0.852, 0.773, 0.694, 0.63, 0.566, 0.502, 0.438, 0.374, 0.31, 0.246];
                break;
            case 500:
                $MarginFactor = [2, 1.894, 1.788, 1.682, 1.576, 1.47, 1.364, 1.258, 1.152, 1.056, 0.96, 0.864, 0.788, 0.712, 0.636, 0.57, 0.504, 0.438, 0.372, 0.306, 0.24, 0.174];
                break;
            case 1000:
                $MarginFactor = [1.75, 1.662, 1.574, 1.486, 1.398, 1.31, 1.222, 1.134, 1.046, 0.963, 0.88, 0.797, 0.724, 0.651, 0.578, 0.51, 0.442, 0.374, 0.306, 0.238, 0.17, 0.102];
                break;
            case 2000:
                $MarginFactor = [1.5, 1.43, 1.36, 1.29, 1.22, 1.15, 1.08, 1.01, 0.94, 0.87, 0.8, 0.73, 0.66, 0.59, 0.52, 0.45, 0.38, 0.31, 0.24, 0.17, 0.1, 0.03];
                break;
            default:
                $MarginFactor = [1.5, 1.43, 1.36, 1.29, 1.22, 1.15, 1.08, 1.01, 0.94, 0.87, 0.8, 0.73, 0.66, 0.59, 0.52, 0.45, 0.38, 0.31, 0.24, 0.17, 0.1, 0.03];
                break;
        }

        $PPrangeLimit = [0.05, 0.1, 0.2, 0.5, 0.75, 1, 1.5, 2, 3, 5, 7.5, 10, 12, 15, 20, 25, 30, 50, 100, 200, 500, 10000];

        $steps = 22;

        for ($i = 0; $i < $steps; $i++) {
            if ($PurchasePriceEUR <= $PPrangeLimit[$i]) {
                $Price1 = $CostEUR * (1 + $MarginFactor[$i]);
                break;
            }
        }
        // echo "Price 1: ". $Price1; // 15.45

        // calculating price2
        $PaymentCharge = 0.03;
        $PaymentChargeFixum = 0.35;
        $WarehouseFixum = 0.1;

        switch ($ManyComponentsItemRating) {
            case 1: $ItemFactorManyComponents = 1;
                break;
            case 2: $ItemFactorManyComponents = 1.1;
                break;
            case 3: $ItemFactorManyComponents = 1.2;
                break;
            default:
                $ItemFactorManyComponents = 10; // change to 10 on asan reuqest by Joschua
                break;
        }

        switch ($ItemEffortRating) {

            case 1:
                $ItemFactorEffort = 1;
                break;

            case 2:
                $ItemFactorEffort = 1.05;
                break;

            case 3:
                $ItemFactorEffort = 1.1;
                break;

            case 4:
                $ItemFactorEffort = 1.15;
                break;

            case 5:
                $ItemFactorEffort = 1.2;
                break;

            default:
                $ItemFactorEffort = 20; // change to 10 on asan reuqest by Joschua
                break;
        }
        // echo "ItemFactorEffort: " . $ItemFactorEffort . "\n";

        $SPdeNET = $Price1 * $ItemFactorManyComponents * $ItemFactorEffort * (1 + $PaymentCharge) + $PaymentChargeFixum + $WarehouseFixum;

        return $SPdeNET;
    }
}
if (! function_exists('SPebay')) {
    function SPebay($SPnet)
    {
        $ebayCharges = 1.05;
        $SPbrut = $SPnet * 1.19;
        $Price = round($SPbrut * $ebayCharges, 1) - 0.01;

        // Round to 0.09 prices
        if ($Price <= 1) {
            $Price = 1.09; // Prices for ebay CANNOT be less than 1€ so smaller than 1€ will be set to 1.09€
        } elseif ($Price === null || $Price == 0) {
            $Price = 999.99; // Precaution: zero prices will be set to 999.99
        }

        return $Price;
    }

}


if (!function_exists('round_up')) {
    function round_up($number, $precision = 2) {
        $factor = pow(10, $precision);
        return ceil($number * $factor) / $factor;
    }
}

