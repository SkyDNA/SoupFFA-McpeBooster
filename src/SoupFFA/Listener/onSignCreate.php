<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 14:51
 */

namespace SoupFFA\Listener;


use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use SoupFFA\SoupFFA;

class onSignCreate implements Listener {

    public function onSignCreate(SignChangeEvent $event){
        $player = $event->getPlayer();
        $plugin = SoupFFA::getInstance();
        if ($event->getLine(0) === "SoupFFA") {
            if ($player->isOp()) {
                $event->setLine(0, SoupFFA::PREFIX);
                $event->setLine(2, "§aJoin");
                $event->setLine(3, "§f0 §7/ §c" . $plugin->getConfig()->get("maxplayer"));
                $player->sendMessage(SoupFFA::PREFIX . $plugin->getLanguage()->get("settings.sign.set"));
                return;
            }else {
                $event->setCancelled(true);
                $player->sendMessage(SoupFFA::PREFIX . $plugin->getLanguage()->get("player.noperm"));
            }
            return;
        }
    }
}