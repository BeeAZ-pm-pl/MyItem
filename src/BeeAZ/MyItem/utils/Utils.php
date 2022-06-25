<?php

declare(strict_types=1);

namespace BeeAZ\MyItem\utils;

use pocketmine\utils\TextFormat;

class Utils
{
    public static function translateColorTags(string $message): string
    {
        $replacements = [
            "&0" => TextFormat::BLACK,
            "&1" => TextFormat::DARK_BLUE,
            "&2" => TextFormat::DARK_GREEN,
            "&3" => TextFormat::DARK_AQUA,
            "&4" => TextFormat::DARK_RED,
            "&5" => TextFormat::DARK_PURPLE,
            "&6" => TextFormat::GOLD,
            "&7" => TextFormat::GRAY,
            "&8" => TextFormat::DARK_GRAY,
            "&9" => TextFormat::BLUE,
            "&a" => TextFormat::GREEN,
            "&b" => TextFormat::AQUA,
            "&c" => TextFormat::RED,
            "&d" => TextFormat::LIGHT_PURPLE,
            "&e" => TextFormat::YELLOW,
            "&f" => TextFormat::WHITE,
            "&k" => TextFormat::OBFUSCATED,
            "&l" => TextFormat::BOLD,
            "&m" => TextFormat::STRIKETHROUGH,
            "&n" => TextFormat::UNDERLINE,
            "&o" => TextFormat::ITALIC,
            "&r" => TextFormat::RESET
        ];
        return str_replace(array_keys($replacements), $replacements, $message);
    }
}
