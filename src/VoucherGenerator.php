<?php

namespace BeyondCode\Vouchers;

use Illuminate\Support\Str;

class VoucherGenerator
{
    protected string $characters;

    protected string $mask;

    protected ?string $prefix = null;

    protected ?string $suffix = null;

    protected string $separator = '-';

    protected array $generatedCodes = [];

    public function __construct(string $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZ01234567890', string $mask = '****-****')
    {
        $this->characters = $characters;
        $this->mask       = $mask;
    }

    public function setPrefix(?string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setSuffix(?string $suffix): void
    {
        $this->suffix = $suffix;
    }

    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }

    protected function getPrefix(): string
    {
        return $this->prefix !== null ? $this->prefix . $this->separator : '';
    }

    protected function getSuffix(): string
    {
        return $this->suffix !== null ? $this->separator . $this->suffix : '';
    }

    public function generateUnique(): string
    {
        $code = $this->generate();

        while (in_array($code, $this->generatedCodes) === true) {
            $code = $this->generate();
        }

        $this->generatedCodes[] = $code;
        return $code;
    }

    public function generate(): string
    {
        $length = substr_count($this->mask, '*');

        $code       = $this->getPrefix();
        $mask       = $this->mask;
        $characters = collect(str_split($this->characters));

        for ($i = 0; $i < $length; $i++) {
            $mask = Str::replaceFirst('*', $characters->random(1)->first(), $mask);
        }

        $code .= $mask;
        $code .= $this->getSuffix();

        return $code;
    }
}
