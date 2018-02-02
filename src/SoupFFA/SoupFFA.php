<?php

/*
 *
 * o     o                       .oPYo.                        o
 * 8b   d8                       8   `8                        8
 * 8`b d'8 .oPYo. .oPYo. .oPYo. o8YooP' .oPYo. .oPYo. .oPYo.  o8P .oPYo. oPYo.
 * 8 `o' 8 8    ' 8    8 8oooo8  8   `b 8    8 8    8 Yb..     8  8oooo8 8  `'
 * 8     8 8    . 8    8 8.      8    8 8    8 8    8   'Yb.   8  8.     8
 * 8     8 `YooP' 8YooP' `Yooo'  8oooP' `YooP' `YooP' `YooP'   8  `Yooo' 8
 * ..::::..:.....:8 ....::.....::......::.....::.....::.....:::..::.....:..::::
 * :::::::::::::::8 :::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 * :::::::::::::::..:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
 *
 *
 * Plugin made by McpeBooster
 *
 * Author: McpeBooster
 * Twitter: @McpeBooster
 * Website: McpeBooster.tk
 * E-Mail: mcpebooster@gmail.com
 * YouTube: http://YouTube.com/c/McpeBooster
 * GitHub: http://GitHub.com/McpeBooster
 *
 * ©McpeBooster
 */

/**
 * Created by PhpStorm.
 * User: McpeBooster
 * Date: 02.02.2018
 * Time: 14:27
 */

namespace SoupFFA;


use pocketmine\lang\BaseLang;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use SoupFFA\Commands\CommandSoupFFA;
use SoupFFA\Listener\onBreak;
use SoupFFA\Listener\onDamage;
use SoupFFA\Listener\onExhaust;
use SoupFFA\Listener\onInteract;
use SoupFFA\Listener\onPlace;
use SoupFFA\Listener\onPlayerCreation;
use SoupFFA\Listener\onSignCreate;

class SoupFFA extends PluginBase {

    const PREFIX = "§7[§2SoupFFA§7]";

    public static $instance;
    public $baseLang;

    public function onEnable() {
        $this->getLogger()->info(self::PREFIX . " by §6McpeBooster§7!");

        self::$instance = $this;

        $this->saveDefaultConfig();

        $lang = $this->getConfig()->get("language", BaseLang::FALLBACK_LANGUAGE);
        $this->baseLang = new BaseLang($lang, $this->getFile() . "resources/");

        $this->getLogger()->info(self::PREFIX . " Language: " . $lang);

        if ($this->checkUpdate()) {
            $this->getServer()->reload();
        }

        if ($this->getConfig()->get("arena") == "debug123") {
            $plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
            $this->getLogger()->emergency("######################################################");
            $this->getLogger()->emergency($this->getLanguage()->get("setting.world.change"));
            $this->getLogger()->emergency("######################################################");
            $this->getServer()->getPluginManager()->disablePlugin($plugin);
            return;
        }

        $this->getServer()->getPluginManager()->registerEvents(new onDamage(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onInteract(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onSignCreate(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onPlayerCreation(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onBreak(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onPlace(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new onExhaust(), $this);

        $this->getServer()->getCommandMap()->register("SoupFFA", new CommandSoupFFA());

        $this->getServer()->loadLevel($this->getConfig()->get("arena"));

        $this->updateSign($this->getConfig()->get("arena"));
    }

    /**
     * @return mixed
     */
    public static function getInstance() {
        return self::$instance;
    }

    /**
     * @return BaseLang
     */
    public function getLanguage(): BaseLang {
        return $this->baseLang;
    }

    /**
     * @return bool
     */
    public function checkUpdate() {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $datei = file_get_contents("https://raw.githubusercontent.com/McpeBooster/SoupFFA-McpeBooster/master/plugin.yml", false, stream_context_create($arrContextOptions));
        if (!$datei)
            return false;

        $datei = str_replace("\n", "", $datei);
        $newversion = explode("version: ", $datei);
        $newversion = explode("api: ", $newversion[1]);
        $newversion = $newversion[0];
        //var_dump($newversion);

        $plugin = $this->getServer()->getPluginManager()->getPlugin("SoupFFA");
        $version = $plugin->getDescription()->getVersion();
        //var_dump($version);
        if (!($version === $newversion)) {
            $update = false;
            if (intval($version[0]) < intval($newversion[0])) {
                $update = true;
            } elseif (intval($version[0]) === intval($newversion[0])) {
                if (intval($version[1]) < intval($newversion[1])) {
                    $update = true;
                } elseif (intval($version[1]) === intval($newversion[1])) {
                    if (intval($version[2]) < intval($newversion[2])) {
                        $update = true;
                    }
                }
            }

            if ($update) {
                $this->getLogger()->info("§aNew Update available!");
                $this->getLogger()->info("§7Local Version: §6" . $version);
                $this->getLogger()->info("§7Newest Version: §6" . $newversion);
                $this->getLogger()->info("§aDownloading Newest Version... §7(" . $newversion . ")");
                $path = dirname(__FILE__);
                if (is_dir($path)) {
                    $this->updateDir(str_replace("src/SoupFFA", "", $path));
                } else {
                    $file = @file_get_contents("https://raw.githubusercontent.com/McpeBooster/SoupFFA-McpeBooster/master/release/SoupFFA_v" . $newversion . ".phar", false, stream_context_create($arrContextOptions));
                    if ($file) {
                        file_put_contents($path, $file);
                    } else {
                        $this->getLogger()->emergency("Error while downloading... §7(" . $newversion . ")");
                        return false;
                    }
                }
                $this->getLogger()->info("§aSuccessfully downloaded Newest Version... §7(" . $newversion . ")");
                return true;
            }
        }
        $this->getLogger()->info("§aSoupFFA has the Latest Version!");
        $this->getLogger()->info("§7Local Version: §6" . $version);
        $this->getLogger()->info("§7Newest Version: §6" . $newversion);

        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    public function updateDir($path) {
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );


        //foreach(scandir($path) as $f) {
        foreach (glob($path . "*") as $f) {
            if (!in_array($f, [".", ".."]) && !($f == $path)) {

                if (is_dir($f)) {
                    $this->updateDir($f . "/");
                } else {
                    $url1 = str_replace($this->getServer()->getDataPath(), "", str_replace("plugins", "", $f));
                    /* var_dump("Server: " . $this->getServer()->getDataPath());
                      var_dump("Datei: " . $f);
                      var_dump("New Path: " . $url1); */
                    $url2 = explode("/", $url1);
                    unset($url2[1]);
                    $url3 = "";
                    foreach ($url2 as $u2) {
                        if (!($u2 == "")) {
                            $url3 = $url3 . "/" . $u2;
                        }
                    }
                    //var_dump($url3);
                    if ($d = @file_get_contents("https://raw.githubusercontent.com/McpeBooster/SoupFFA-McpeBooster/master" . $url3, false, stream_context_create($arrContextOptions))) {
                        file_put_contents($f, $d);
                    }
                }
            }
        }
        return true;
    }

    /**
     * @param string $arena
     * @return bool
     */
    public function updateSign(string $arena) {
        $lobby = $this->getServer()->getDefaultLevel();
        if ($this->getServer()->isLevelLoaded($lobby->getFolderName())) {
            foreach ($lobby->getTiles() as $tile) {
                if ($tile instanceof Sign) {
                    $signt = $tile->getText();
                    if ($signt[0] == SoupFFA::PREFIX) {
                        if ($signt[1] == $arena) {
                            $arenalevel = $this->getServer()->getLevelByName($arena);
                            $playercount = count($arenalevel->getPlayers());
                            $maxplayer = $this->getConfig()->get("maxplayer");
                            if ($playercount >= $maxplayer) {
                                $tile->setText($signt[0], $signt[1], "§cVoll", "§f" . $playercount . " §7/ §c" . $maxplayer);
                            } else {
                                $tile->setText($signt[0], $signt[1], "§aJoin", "§f" . $playercount . " §7/ §c" . $maxplayer);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

}