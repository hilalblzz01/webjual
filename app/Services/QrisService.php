<?php

namespace App\Services;

use App\Models\Setting;

class QrisService
{
    /**
     * Fallback Static QRIS String
     */
    public static string $defaultQris = '00020101021126570011ID.DANA.WWW011893600915375303080802097530308080303UMI51440014ID.CO.QRIS.WWW0215ID10243459986770303UMI5204481453033605802ID5914LALCLOUD STORE6014Kab. Mojokerto610561363630439DA';

    /**
     * Get active QRIS string (from Database setting or fallback)
     */
    public static function getQrisString(): string
    {
        return Setting::get('qris_string') ?: self::$defaultQris;
    }

    /**
     * Get QRIS Merchant Name (from Database setting or fallback)
     */
    public static function getMerchantName(): string
    {
        return Setting::get('qris_merchant_name') ?: 'LALCLOUD STORE';
    }

    /**
     * Convert static QRIS string to Dynamic QRIS string with specific amount
     */
    public static function generateDynamic(int $amount, ?string $qrisString = null): string
    {
        $baseQris = $qrisString ?? self::getQrisString();

        // Strip CRC16 (6304XXXX) if present at end
        $base = preg_replace('/6304[A-Fa-f0-9]{4}$/', '', $baseQris);

        // Tag 54 = Transaction Amount
        $strAmount = (string) $amount;
        $tag54 = '54' . sprintf('%02d', strlen($strAmount)) . $strAmount;

        // Insert Tag 54 before Tag 58 (Country Code ID) or Tag 59
        if (strpos($base, '5802ID') !== false) {
            $parts = explode('5802ID', $base, 2);
            $qris = $parts[0] . $tag54 . '5802ID' . $parts[1];
        } else {
            $qris = $base . $tag54;
        }

        // Add Tag 6304 for CRC
        $qrisForCrc = $qris . '6304';

        // Calculate CRC16 CCITT (0x1021, init 0xFFFF)
        $crc = self::calculateCrc16($qrisForCrc);

        return $qrisForCrc . $crc;
    }

    /**
     * Get QR Code Image URL for a given QRIS string
     */
    public static function getQrImageUrl(string $qrisString): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($qrisString);
    }

    /**
     * CRC16 CCITT False implementation (0x1021, poly 0x1021, init 0xFFFF)
     */
    private static function calculateCrc16(string $data): string
    {
        $crc = 0xFFFF;
        $len = strlen($data);

        for ($i = 0; $i < $len; $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return strtoupper(sprintf('%04X', $crc));
    }
}
