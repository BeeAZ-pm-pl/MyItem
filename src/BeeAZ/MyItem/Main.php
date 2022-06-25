<?php

namespace BeeAZ\MyItem;

use BeeAZ\MyItem\utils\Utils;
use BeeAZ\MyItem\customenchants\PiggyCustomEnchantsLoader;

use pocketmine\Server;
use pocketmine\player\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;

use pocketmine\command\Command;
use pocketmine\command\Commandsender;

use pocketmine\item\ItemFactory;
use pocketmine\item\Item;

use pocketmine\utils\TextFormat;

use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use DaPigGuy\PiggyCustomEnchants\CustomEnchantManager;
use DaPigGuy\PiggyCustomEnchants\CustomEnchants\CustomEnchants;
use pocketmine\data\bedrock\EnchantmentIdMap;

use pocketmine\world\sound\AnvilUseSound;

use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
    
    public static $namesave;

    public $storage;

    public $message;

    const PREFIX = "§6§6[§aMyItem§6]: ";

    public const ITEM_FORMAT = [
        "name" => "",
        "id" => 1,
        "meta" => 0,
        "count" => 1,
        "display_name" => "",
        "lore" => [

        ],
        "enchants" => [

        ],
    ];

    public function onEnable(): void{
        @mkdir($this->getDataFolder());
        $this->saveResource("message.yml");
        $this->storage = new Config($this->getDataFolder(). "storage.yml", Config::YAML);
        $this->message = new Config($this->getDataFolder() . "message.yml", Config::YAML);
        PiggyCustomEnchantsLoader::load();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
}
    public function onCommand(CommandSender $player, Command $cmd, string $label, array $args) : bool{
        if($player instanceof Player){
        $item = $player->getInventory()->getItemInHand();
        switch($cmd->getName()){
          case "mi":
            if(!isset($args[0])){
            $player->sendMessage(self::PREFIX . $this->getMessage("message.help"));
            $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
            return true;
            }else{
            switch(strtolower($args[0])){
             case "help":
             $player->sendMessage($this->getMessage("message.default-help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             break;
             case "name":
             case "setname":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX . $this->getMessage("setname.help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
            }else{
             if($item->isNull()){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.none-item"));  
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }            
             array_shift($args);
             $item->setCustomName(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))));   
             $player->getInventory()->setItemInHand($item);            
             $player->sendMessage(self::PREFIX . $this->getMessage("setname.success"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             }
             break;
             case "lore":
             case "setlore":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX . $this->getMessage("setlore.help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
            }else{
             if($item->isNull()){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.none-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }        
             array_shift($args);
             $item->setLore([(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))))]);
             $player->getInventory()->setItemInHand($item);
             $player->sendMessage(self::PREFIX . $this->getMessage("setlore.success"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             }
             break;
             case "adden":
             case "addenchant":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
            }else{
             if(!isset($args[2])){
             $args[2] = 1;
             }
             if($item->isNull()){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.none-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             if(!is_numeric($args[1])){        $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.error-num-en"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             if(!is_numeric($args[2])){        $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.error-level"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
            }else{
             if($args[2] > 30000){                  $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.error-level-30000"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
            }
             $enchantment = EnchantmentIdMap::getInstance()->fromId($args[1]);
             if($enchantment === null){              $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.error-num-en"));    
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             $enchInstance = new EnchantmentInstance($enchantment, $args[2]);          $item->addEnchantment($enchInstance);
             $player->getInventory()->setItemInHand($item);
             $player->sendMessage(self::PREFIX . $this->getMessage("addenchant.success"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             }
             break;
             case "id":
             case "idenchant":
             $player->sendMessage($this->getMessage("idenchant.id"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             break;
             case "itemsave":
             case "save":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX . $this->getMessage("save.help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
            }else{
             if($item->isNull()){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.none-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             array_shift($args); self::$namesave = str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args)));
             if($this->storage->exists(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))))){   $player->sendMessage(self::PREFIX . $this->getMessage("save.already-name-save"));  
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;    
             }
             $this->storage->set(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))), $this->itemToData($item));
             $this->storage->save();
             $player->sendMessage(self::PREFIX . $this->getMessage("save.success"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             }
             break;
             case "itemtake":
             case "take":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX . $this->getMessage("take.help1")."\n".self::PREFIX . $this->getMessage("take.help2"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             array_shift($args);
             if(!$this->storage->exists(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))))){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.not-found-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             $items = $this->dataToItem($this->storage->get(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args)))));
             $player->getInventory()->addItem($items);
             $player->sendMessage(self::PREFIX . $this->getMessage("take.success"));
             $this->storage->save();
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             break;
             case "itemdelete":
             case "delete":
             if(!isset($args[1])){
             $player->sendMessage(self::PREFIX. $this->getMessage("delete.help"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
              break;
              }
             array_shift($args);
             if(!$this->storage->exists(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))))){
             $player->sendMessage(self::PREFIX . $this->getMessage("message.not-found-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             break;
            }else{
             $this->storage->remove(str_replace(["{color}", "{line}"], ["§", "\n"], trim(implode(" ", $args))), [$this->itemToData($item)]);
             $player->sendMessage(self::PREFIX . $this->getMessage("delete.success"));
             $this->storage->save();
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
              }
             break;
             case "itemlist":
             case "list":
             $i = 1;
             if($this->storage->getAll() == null){
             $player->sendMessage(self::PREFIX . $this->getMessage("list.none-item"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             return true;
             }
             $player->sendMessage(self::PREFIX . $this->getMessage("list.save"));
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             foreach($this->storage->getAll() as $names){
             foreach($names as $name => $val){
             if ($name != "name"){
             continue;
             }
             $player->sendMessage(" §7". $i .">§d ". $val. "§7.");
             $i++;
             }
             }
             break;
             case "takeall":
             $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
             if($this->storage->getAll() == null){
             $player->sendMessage(self::PREFIX . $this->getMessage("list.none-item"));
             return true;
              }
             foreach($this->storage->getAll() as $names){
             $items = $this->dataToItem($names);
             $player->getInventory()->addItem($items);
             break;
              }
              }
              }
              }
            }else{
              $player->sendMessage(self::PREFIX . $this->getMessage("use-in-game"));
               }
             return true;
              }



    public static function dataToItem(array $itemData) : Item {
        $item = ItemFactory::getInstance()->get($itemData["id"], $itemData["meta"] ?? 0, $itemData["count"] ?? 1);
        if(isset($itemData["enchants"])) {
        foreach($itemData["enchants"] as $ename => $level) {
        $ench = EnchantmentIdMap::getInstance()->fromId($ename);
        if($ench == null){
        if(PiggyCustomEnchantsLoader::isPluginLoaded()){
        if(!PiggyCustomEnchantsLoader::isNewVersion()){
        $ench = CustomEnchants::getEnchantment($ename);
        }else{
         $ench = CustomEnchantManager::getEnchantment($ename);
         }
         }
         }
         if($ench == null) continue;
         if(!PiggyCustomEnchantsLoader::isNewVersion() && $ench instanceof CustomEnchants){
         PiggyCustomEnchantsLoader::getPlugin()->addEnchantment($item, $ench->getName(), $level);
        }else{
         $item->addEnchantment(new EnchantmentInstance($ench, $level));
          }
         }
        }
         if(isset($itemData["display_name"])){
         $item->setCustomName(TextFormat::colorize($itemData["display_name"]));
         if(isset($itemData["lore"])) {
          $lore = [];
         foreach($itemData["lore"] as $key => $ilore) {
         $lore[$key] = TextFormat::colorize($ilore);
          }
         $item->setLore($lore);
          }
          return $item;
    }
    }
    
     public static function itemToData(Item $item) : array {
        $itemData = self::ITEM_FORMAT;
        $itemData["name"] = self::$namesave;
        self::$namesave = "";
        $itemData["id"] = $item->getId();
        $itemData["meta"] = $item->getMeta();
        $itemData["count"] = $item->getCount();
        if($item->hasCustomName()) {
        $itemData["display_name"] = $item->getCustomName();
        }else{
         unset($itemData["display_name"]);
         }
         if($item->getLore() !== []) {
         $itemData["lore"] = $item->getLore();
         }else{
          unset($itemData["lore"]);
          }
          if($item->hasEnchantments()) {
          foreach($item->getEnchantments() as $enchantment) {
          $itemData["enchants"][(string)EnchantmentIdMap::getInstance()->toId($enchantment->getType())] = $enchantment->getLevel();
            }
          }else{
           unset($itemData["enchants"]);
           }
        return $itemData;
    }

    public function getMessage(string $key, array $tags = []): string{
        return Utils::translateColorTags(str_replace(array_keys($tags), $tags, $this->message->getNested($key, $key)));
    }
}

