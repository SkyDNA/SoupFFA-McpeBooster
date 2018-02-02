<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 14:42
 */

namespace SoupFFA\Listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\tile\Sign;
use SoupFFA\SoupFFA;

class onInteract implements Listener {

    public function onInteract(PlayerInteractEvent $event){
        $player = $event->getPlayer();
        $item = $event->getItem();
        $block = $event->getBlock();
        $plugin = SoupFFA::getInstance();
        if ($item->getId() === Item::MUSHROOM_STEW) {
            $player->getInventory()->removeItem($item);
            $player->setHealth($player->getHealth() + 5);
            $player->setFood(20);
            $player->sendTip($plugin->getLanguage()->get("player.healed"));
            return;
        } elseif ($player->getLevel()->getTile($block) instanceof Sign) {
            $tile = $player->getLevel()->getTile($block);
            $text = $tile->getText();
            if ($text[0] == SoupFFA::PREFIX) {
                if ($text[2] == "§aJoin") {
                    $player->ArenaJoin();
                    return;
                }
                $player->sendMessage($plugin->getLanguage()->get("player.join.error"));
                return;
            }
        }
    }
}