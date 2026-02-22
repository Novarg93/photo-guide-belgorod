<?php

namespace App\Support;

use Illuminate\Support\Str;

class CategoryFilterSchema
{
    /**
     * @return array<int, array{
     *     key: string,
     *     label: string,
     *     options: array<int, array{key: string, label: string}>
     * }>
     */
    public static function normalize(mixed $rawGroups): array
    {
        if (! is_array($rawGroups)) {
            return [];
        }

        $normalizedGroups = [];

        foreach (array_values($rawGroups) as $groupIndex => $rawGroup) {
            if (! is_array($rawGroup)) {
                continue;
            }

            $groupLabel = trim((string) ($rawGroup['label'] ?? $rawGroup['name'] ?? ''));
            $groupBaseKey = Str::slug($groupLabel);

            if ($groupBaseKey === '') {
                $groupBaseKey = 'group-'.($groupIndex + 1);
            }

            $rawOptions = $rawGroup['options'] ?? [];
            if (! is_array($rawOptions)) {
                continue;
            }

            $normalizedOptions = [];
            $usedOptionKeys = [];

            foreach (array_values($rawOptions) as $optionIndex => $rawOption) {
                if (! is_array($rawOption)) {
                    continue;
                }

                $optionLabel = trim((string) ($rawOption['label'] ?? $rawOption['name'] ?? ''));
                if ($optionLabel === '') {
                    continue;
                }

                $optionBaseKey = Str::slug($optionLabel);

                if ($optionBaseKey === '') {
                    $optionBaseKey = 'option-'.($optionIndex + 1);
                }

                $optionKey = "{$groupBaseKey}.{$optionBaseKey}";
                $suffix = 2;

                while (isset($usedOptionKeys[$optionKey])) {
                    $optionKey = "{$groupBaseKey}.{$optionBaseKey}-{$suffix}";
                    $suffix++;
                }

                $usedOptionKeys[$optionKey] = true;

                $normalizedOptions[] = [
                    'key' => $optionKey,
                    'label' => $optionLabel,
                ];
            }

            if ($normalizedOptions === []) {
                continue;
            }

            $normalizedGroups[] = [
                'key' => $groupBaseKey,
                'label' => $groupLabel !== '' ? $groupLabel : Str::headline($groupBaseKey),
                'options' => $normalizedOptions,
            ];
        }

        return $normalizedGroups;
    }

    /**
     * @return array<string, string>
     */
    public static function flattenOptions(mixed $rawGroups): array
    {
        $options = [];

        foreach (static::normalize($rawGroups) as $group) {
            foreach ($group['options'] as $option) {
                $options[$option['key']] = "{$group['label']}: {$option['label']}";
            }
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public static function allowedOptionKeys(mixed $rawGroups): array
    {
        return array_keys(static::flattenOptions($rawGroups));
    }

    /**
     * @return array<int, string>
     */
    public static function filterSelected(mixed $rawGroups, mixed $selected): array
    {
        $allowed = array_fill_keys(static::allowedOptionKeys($rawGroups), true);
        $sanitized = [];

        if (! is_array($selected)) {
            return $sanitized;
        }

        foreach ($selected as $optionKey) {
            $optionKey = trim((string) $optionKey);

            if ($optionKey === '' || ! isset($allowed[$optionKey])) {
                continue;
            }

            if (! in_array($optionKey, $sanitized, true)) {
                $sanitized[] = $optionKey;
            }
        }

        return $sanitized;
    }
}
