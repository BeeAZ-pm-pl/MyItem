<?php

declare(strict_types=1);

namespace BeeAZ\MyItem\customenchants;

use DaPigGuy\PiggyCustomEnchants\Main;
use DaPigGuy\PiggyCustomEnchants\PiggyCustomEnchants;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

final class PiggyCustomEnchantsLoader {

    private static $customEnchants;

    private static $isNewVersion;

    public static function getPlugin() : PluginBase {
        return self::$customEnchants;
    }

    public static function load() {
        $ce = Server::getInstance()->getPluginManager()->getPlugin("PiggyCustomEnchants");
        if($ce !== null) {
            self::$customEnchants = $ce;
            return true;
        }
        if($ce instanceof PiggyCustomEnchants) self::$isNewVersion = true;
        else self::$isNewVersion = false;
    }

    public static function isNewVersion() : bool {
        return self::$isNewVersion ?? true;
    }

    public static function isPluginLoaded() : bool {
        return isset(self::$customEnchants);
    }
}
