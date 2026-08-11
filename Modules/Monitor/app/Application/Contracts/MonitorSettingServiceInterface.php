<?php

namespace Modules\Monitor\Application\Contracts;

interface MonitorSettingServiceInterface
{
    /**
     * Cài đặt hiệu lực = config defaults + override từ DB.
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * Upsert các cài đặt từ request.
     *
     * @param array<string, mixed> $data
     */
    public function update(array $data): void;

    /**
     * Merge cài đặt từ DB vào config (để Domain::badge() đọc đúng ngưỡng).
     */
    public function loadIntoConfig(): void;
}