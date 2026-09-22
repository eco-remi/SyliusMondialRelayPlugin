<?php

namespace EResponsable\SyliusMondialRelayPlugin\Tests\MondialRelay\Api;

use PHPUnit\Framework\TestCase;
use EResponsable\SyliusMondialRelayPlugin\MondialRelay\Api\ErrorCode;

class ErrorCodeTest extends TestCase
{
    /**
     * @dataProvider errorCodeProvider
     */
    public function testGetErrorMessageKey(int $errorCode, string $expectedKey): void
    {
        $this->assertSame($expectedKey, ErrorCode::getErrorMessageKey($errorCode));
    }

    public static function errorCodeProvider(): array
    {
        return [
            [1, 'invalid_merchant'],
            [2, 'empty_merchant_number'],
            [3, 'invalid_merchant_account_number'],
            [5, 'invalid_merchant_shipment_reference'],
            [7, 'invalid_consignee_reference'],
            [8, 'invalid_password_or_hash'],
            [9, 'unknown_or_not_unique_city'],
            [10, 'invalid_collection_type'],
            [11, 'invalid_collection_pickup_point_number'],
            [12, 'invalid_collection_pickup_point_country'],
            [13, 'invalid_delivery_type'],
            [14, 'invalid_delivery_pickup_point_number'],
            [15, 'invalid_delivery_pickup_point_country'],
            [20, 'invalid_parcel_weight'],
            [21, 'invalid_developed_length'],
            [22, 'invalid_parcel_size'],
            [24, 'invalid_shipment_number'],
            [26, 'invalid_assembly_time'],
            [27, 'invalid_collection_or_delivery_mode'],
            [28, 'invalid_collection_mode'],
            [29, 'invalid_delivery_mode'],
            [30, 'invalid_address_l1'],
            [31, 'invalid_address_l2'],
            [33, 'invalid_address_l3'],
            [34, 'invalid_address_l4'],
            [35, 'invalid_city'],
            [36, 'invalid_zipcode'],
            [37, 'invalid_country'],
            [38, 'invalid_phone_number'],
            [39, 'invalid_email'],
            [40, 'missing_parameters'],
            [42, 'invalid_code_value'],
            [43, 'invalid_code_currency'],
            [44, 'invalid_shipment_value'],
            [45, 'invalid_shipment_value_currency'],
            [46, 'end_of_shipments_number_range_reached'],
            [47, 'invalid_number_of_parcels'],
            [48, 'multi_parcel_not_allowed'],
            [49, 'invalid_action'],
            [60, 'invalid_text_field'],
            [61, 'invalid_notification_request'],
            [62, 'invalid_extra_delivery_information'],
            [63, 'invalid_insurance'],
            [64, 'invalid_assembly_time'],
            [65, 'invalid_appointment'],
            [66, 'invalid_take_back'],
            [67, 'invalid_latitude'],
            [68, 'invalid_longitude'],
            [69, 'invalid_merchant_code'],
            [70, 'invalid_pickup_point_number'],
            [71, 'invalid_pickup_point_type'],
            [74, 'invalid_language'],
            [78, 'invalid_collection_country'],
            [79, 'invalid_delivery_country'],
            [80, 'invalid_tracking_code_recorded_parcel'],
            [81, 'invalid_tracking_code_processing_parcel'],
            [82, 'invalid_tracking_code_delivered_parcel'],
            [83, 'invalid_tracking_code_anomaly'],
            [94, 'unknown_parcel'],
            [95, 'merchant_account_not_enabled'],
            [96, 'invalid_store_type'],
            [97, 'invalid_security_key'],
            [98, 'generic_error'],
            [99, 'system_generic_error'],
            [999, 'default'],
            [-1, 'default'],
        ];
    }
}
