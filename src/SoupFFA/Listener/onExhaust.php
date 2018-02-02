<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 16:05
 */

namespace SoupFFA\Listener;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use SoupFFA\SoupFFA;

class onExhaust implements Listener {

    public function onExhaust(PlayerExhaustEvent $event){
        $player = $event->getPlayer();

        $plugin = SoupFFA::getInstance();

        $welt = $player->getLevel()->getFolderName();
        $arenaname = $plugin->getConfig()->get("arena");
        if ($arenaname == $welt) {
            $event->setCancelled();
        }
    }
}