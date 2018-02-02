<?php
/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 14:52
 */

namespace SoupFFA\Listener;


use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use SoupFFA\Player\SoupPlayer;
use SoupFFA\SoupFFA;

class onDamage implements Listener {

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        $cause = $event->getCause();

        $plugin = SoupFFA::getInstance();

        $welt = $entity->getLevel()->getFolderName();
        $arenaname = $plugin->getConfig()->get("arena");
        if ($arenaname == $welt) {

            if ($cause == EntityDamageEvent::CAUSE_ENTITY_ATTACK) {
                if ($event instanceof EntityDamageByEntityEvent) {
                    $killer = $event->getDamager();
                    if ($killer instanceof SoupPlayer) {
                        $x = $entity->getX();
                        $y = $entity->getY();
                        $z = $entity->getZ();

                        $sx = $entity->getLevel()->getSafeSpawn()->getX();
                        $sy = $entity->getLevel()->getSafeSpawn()->getY();
                        $sz = $entity->getLevel()->getSafeSpawn()->getZ();

                        $cp = $plugin->getConfig()->get("spawnprotection");

                        if (abs($sx - $x) < $cp && abs($sy - $y) < $cp && abs($sz - $z) < $cp) {

                            $event->setCancelled(true);

                            $killer->sendMessage(SoupFFA::PREFIX . $plugin->getLanguage()->get("player.spawnprotection"));
                            return;
                        } elseif ($event->getDamage() >= $entity->getHealth()) {
                            $event->setCancelled(true);

                            $arenalevel = $plugin->getServer()->getLevelByName($arenaname);
                            $arenaspawn = $arenalevel->getSafeSpawn();
                            $entity->teleport($arenaspawn, 0, 0);

                            $entity->addTitle("§4Death", $killer->getDisplayName());
                            $killer->addTitle("§2Kill", $entity->getDisplayName());

                            $entity->giveKit();
                            $killer->giveKit();

                            $deathmsg = $plugin->getLanguage()->get("player.death");
                            $deathmsg = str_replace("{player}", $killer->getDisplayName(), $deathmsg);
                            $entity->sendMessage(SoupFFA::PREFIX . $deathmsg);

                            $killmsg = $plugin->getLanguage()->get("player.kill");
                            $killmsg = str_replace("{player}", $entity->getDisplayName(), $killmsg);
                            $killer->sendMessage(SoupFFA::PREFIX . $killmsg);
                            return;
                        }
                    }
                }
            }else{
                $event->setCancelled();
            }
        }
    }
}