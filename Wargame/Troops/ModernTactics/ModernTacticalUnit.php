<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 7/15/16
 * Time: 9:18 PM
 * 
 *  * Copyright 2016 David Rodal
 * User: David Markarian Rodal
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wargame\Troops\ModernTactics;
use Wargame\Battle;
use Wargame\Hexagon;
use stdClass;
use Wargame\MapData;
/**

 */
class ModernTacticalUnit extends \Wargame\BaseUnit implements \JsonSerializable
{

    const HARD_TARGET = 0;
    const SOFT_TARGET = 1;
    const AP_WEAPONS = 'A';
    const SM_WEAPONS = 'S';
    const IN_WEAPONE = 'I';

//    public $strength;
    public $attackStrength;
    public $range;
    public $isImproved = false;
    public $normalMoveAmount;
    public $forceMarch = true;
    public $target;
    public $pinned = false;
    public $weapons;



    public $isDisrupted = false;
    public $disruptLevel = 0;
    public $supplied = true;


    public function jsonSerialize()
    {
        if (is_object($this->hexagon)) {
            if ($this->hexagon->name) {
                $this->hexagon = $this->hexagon->getName();

            } else {
                $this->hexagon = $this->hexagon->parent;
            }
        }
        return $this;
    }

    public function getUnmodifiedStrength(){
        if ($this->isReduced) {
            $strength = $this->minStrength;
        } else {
            $strength = $this->maxStrength;
        }
        return $strength;
    }

    public function getUnmodifiedDefStrength(){
        return  $this->getUnmodifiedStrength();
    }

    public function __get($name)
    {
        if ($name !== "strength" && $name !== "defStrength" && $name !== "attStrength") {
            return false;
        }

        $strength = $this->attackStrength;
        $strength = $this->getCombatAdjustments($strength);

        return $strength;
    }

    public function pinUnit()
    {
        $this->pinned = true;
    }

    public function disruptionLevel($result){
        switch($result){
            case D1:
                return 1;
            case D2:
                return 2;
            case D3:
                return 3;
        }
    }

    public function disruptUnit($phase, $level){

        $dLevel = $this->disruptionLevel($level);

        $this->disruptLevel += $dLevel;
        if($this->disruptLevel >= 4){
            $this->damageUnit(true);
        }
        $this->isDisrupted = true;
    }

    public function attemptUnDisrupt(){
        /*
         * D1 3 or less D2 2 or less you get the picture
         */
        $dieNeeded = 4 - $this->disruptLevel;
        if(rand(1,6) <= $dieNeeded){
            $this->isDisrupted = false;
            $this->disruptLevel = 0;
        }
        $this->pinned = false;
    }

    public function enterImproved($force = false){
        if($force !== true && $this->moveAmountUsed > 0){
            return false;
        }
        if($force !== true && $this->nationality !== "French" && $this->class === "artillery") {
            return false;
        }
        $this->moveAmountUsed = 0;
        $b = Battle::getBattle();
        if($this->nationality === "British"){
            if($this->isDisrupted === false){
                $this->isImproved = true;
            }else{
                if(rand(1,6) <= 4){
                    $this->isImproved = true;
                }
            }
        }else{
            $dieNeeded = 2;
            if($this->nationality == "Russian"){
                $dieNeeded = 1;
            }
            if($this->nationality == "French" && $this->class === "infantry"){
                $dieNeeded = 1;
            }
            $roll = rand(1,6);
            if($roll <= $dieNeeded){
                $this->isImproved = true;
            }
        }
        if($force === true){
            $this->isImproved = true;
        }

        if($this->isImproved){
            $this->maxMove = 0;
        }


        $b->moveRules->stopMove($this, true);
        return true;
    }


    public function exitImproved($force = false){
        if($force !== true && $this->moveAmountUsed > 0){
            return false;
        }
        $b = Battle::getBattle();

        switch($this->nationality) {
            case "French":
                $dieNeeded = 5;
                break;
            case "British":
            case "German":
            case "Belgian":
            case "Austro-Hungarian":
                $dieNeeded = 4;
                break;
            case "Russian":
                $dieNeeded = 3;
        }

        if($force === true || rand(1,6) <= $dieNeeded){
            $this->isImproved = false;
            $this->maxMove = $this->normalMoveAmount;
            $this->moveAmountUnused = $this->maxMove;
        }else{
            $this->moveAmountUsed = 1;
            $b->moveRules->stopMove($this);
        }
        return true;
    }

    function set($unitForceId, $unitHexagon,  $attackStrength, $range, $defenseStrength, $unitMaxMove, $weapons, $target, $unitStatus, $unitReinforceZone, $unitReinforceTurn, $nationality = "neutral",  $class, $unitDesig)
    {
        $this->dirty = true;
        $this->forceId = $unitForceId;
        $this->class = $class;
        $this->hexagon = new Hexagon($unitHexagon);
        /* blah! this can get called from the constructor of Battle. so we can't get ourselves while creating ourselves */
//        $battle = Battle::getBattle();
//        $mapData = $battle->mapData;
        $mapData = MapData::getInstance();
        $mapHex = $mapData->getHex($this->hexagon->getName());
        if ($mapHex) {
            $mapHex->setUnit($this->forceId, $this);
        }
        $this->maxMove = $unitMaxMove;
        $this->normalMoveAmount = $this->moveAmountUnused = $unitMaxMove;
        $this->attackStrength = $attackStrength;
        $this->defenseStrength = $defenseStrength;
        $this->target = $target;
        $this->status = $unitStatus;
        $this->moveAmountUsed = 0;
        $this->weapons = $weapons;
        $this->reinforceZone = $unitReinforceZone;
        $this->reinforceTurn = $unitReinforceTurn;
        $this->combatNumber = 0;
        $this->combatIndex = 0;
        $this->combatOdds = "";
        $this->moveCount = 0;
        $this->combatResults = NE;
        $this->range = $range;
        $this->nationality = $nationality;
        $this->forceMarch = true;
        $this->unitDesig = $unitDesig;
    }

    public function checkLos(\Wargame\Los $los, $defenderId = false){
        $b = Battle::getBattle();
        if($this->weapons === ModernTacticalUnit::SM_WEAPONS){
            if($defenderId !== false){
                $defUnit = $b->force->units[$defenderId];
                if($defUnit->target === ModernTacticalUnit::HARD_TARGET){
                    if($los->getRange() > 1){
                       return false;
                    }
                }
            }
        }
        return true;
    }

    function damageUnit($kill = false)
    {
        $battle = Battle::getBattle();

        if ($this->isReduced || $kill) {
            $this->status = STATUS_ELIMINATING;
            $this->exchangeAmount = $this->getUnmodifiedStrength();
            $this->defExchangeAmount = $this->getUnmodifiedDefStrength();
            return true;
        } else {
            $this->damage = $this->maxStrength - $this->minStrength;
            $battle->victory->reduceUnit($this);
            $this->isReduced = true;
            $this->exchangeAmount = $this->damage;
            $this->defExchangeAmount = $this->damage;
        }
        return false;
    }

    function __construct($data = null)
    {
        if ($data) {
            foreach ($data as $k => $v) {
                if ($k == "hexagon") {
                    $this->hexagon = new Hexagon($v);
//                    $this->hexagon->parent = $data->parent;
                    continue;
                }
                $this->$k = $v;
            }
            $this->dirty = false;
        } else {
        }
    }


    public function fetchData(){
        $mapUnit = new stdClass();
        $mapUnit->isReduced = $this->isReduced;
        $mapUnit->parent = $this->hexagon->parent;
        $mapUnit->moveAmountUsed = $this->moveAmountUsed;
        $mapUnit->maxMove = $this->maxMove;
        $mapUnit->strength = $this->attackStrength;
        $mapUnit->supplied = $this->supplied;
        $mapUnit->reinforceZone = $this->reinforceZone;
        $mapUnit->forceId = $this->forceId;
        $mapUnit->isImproved = $this->isImproved;
        $mapUnit->isDisrupted = $this->isDisrupted;
        $mapUnit->disruptLevel = $this->disruptLevel;
        $mapUnit->range = $this->range;
        $mapUnit->class = $this->class;
        $mapUnit->status = $this->status;
        $mapUnit->target = $this->target;
        $mapUnit->defenseStrength = $this->defenseStrength;
        $mapUnit->pinned = $this->pinned;
        return $mapUnit;
    }

    public function getRange(){
        return $this->range;
    }
}
