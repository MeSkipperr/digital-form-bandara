<?php

namespace Tests\Feature;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class QrCodeComponentTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function alatIds(): array
    {
        return [
            'uppercase id' => ['XRAY-01'],
            'mixed case id with underscore' => ['metl_09-Gerbang1'],
            'longest allowed id' => [str_repeat('A', 20)],
        ];
    }

    #[DataProvider('alatIds')]
    public function test_draws_only_the_alat_id_as_black_modules_on_white(string $alatId): void
    {
        $matrix = Encoder::encode($alatId, ErrorCorrectionLevel::M())->getMatrix();
        $quietZone = 4;

        $svg = (string) $this->blade('<x-qr-code :value="$value" />', ['value' => $alatId]);

        $this->assertStringContainsString('viewBox="0 0 '.($matrix->getWidth() + $quietZone * 2).' ', $svg);
        $this->assertStringContainsString('fill="#FFFFFF"', $svg);
        $this->assertSame(1, preg_match('/<path d="([^"]+)" fill="#000000"/', $svg, $path));

        $drawnModules = [];
        preg_match_all('/M(\d+) (\d+)h(\d+)v1h-\3z/', $path[1], $runs, PREG_SET_ORDER);

        foreach ($runs as [, $x, $y, $length]) {
            for ($offset = 0; $offset < (int) $length; $offset++) {
                $drawnModules[] = ((int) $x + $offset - $quietZone).','.((int) $y - $quietZone);
            }
        }

        $expectedModules = [];

        for ($y = 0; $y < $matrix->getHeight(); $y++) {
            for ($x = 0; $x < $matrix->getWidth(); $x++) {
                if ($matrix->get($x, $y) === 1) {
                    $expectedModules[] = $x.','.$y;
                }
            }
        }

        $this->assertSame($expectedModules, $drawnModules);
    }
}
