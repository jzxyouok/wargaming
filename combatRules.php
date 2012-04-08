<?php
// combatRules->js

// copyright (c) 2009-2011 Mark Butler
// This program is free software; you can redistribute it 
// and/or modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation;
// either version 2 of the License, or (at your option) any later version-> 

class CombatRules
{
	// Class references
    /* @var Force */
    public $force;
    /* @var Terrain */
    public $terrain;
    
    // local publiciables
    public $crt;
    public $currentCombatNumber;
    public $maximumCombatNumberUsed;

    function save()
    {
        $data = new StdClass();
        foreach ($this as $k => $v) {
            if (is_object($v) || $k == "crt") {
                continue;
            }
            $data->$k = $v;
        }
        return $data;
    }
    function __construct($Force, $Terrain, $data = null){
    $this->force = $Force;
    $this->terrain = $Terrain;

        if($data){
            foreach($data as $k => $v){
                $this->$k = $v;
            }
        }else{
    $this->currentCombatNumber = 0;
    $this->maximumCombatNumberUsed = 0;
    }
        $this->crt = new CombatResultsTable();
    }


function setupCombat( $id ) {

    if ($this->force->unitIsEnemy($id) == true)
    {
        echo "Is the enemy";
        // defender is already in combatRules, so make it currently selected
        if ($this->force->unitIsInCombat($id) == true)
        {
            $this->currentCombatNumber = $this->force->getUnitCombatNumber($id);
        }
        else
        {
            echo "Not in combat already";
            $this->maximumCombatNumberUsed++;
            $this->currentCombatNumber = $this->maximumCombatNumberUsed;

            $this->force->setupDefender($id, $this->currentCombatNumber);
        }
    }
    else
    // attacker
    {
        if ($this->currentCombatNumber > 0)
        {
            $los = new Los();
            $los->setOrigin($this->force->getUnitHexagon($id));
            $los->setEndPoint($this->force->getCombatHexagon($this->currentCombatNumber));
            $range = $los->getRange();
            echo "RANGE $range";
            if ($range == 1)
            {
                if ($this->force->unitIsAttacking($id) == true)
                {
                    $this->force->undoAttackerSetup($id);
                    $this->setCombatIndex($this->currentCombatNumber);
                }
                else
                {
                    $this->force->setupAttacker($id, $this->currentCombatNumber);
                    $this->setCombatIndex($this->currentCombatNumber);
                }
            }
        }
    }
}

function setupFireCombat( $id ){
}

function getDefenderTerrainCombatEffect($combatNumber)
{

    $terrainCombatEffect = $this->terrain->getDefenderTerrainCombatEffect($this->force->getCombatHexagon($combatNumber));

    if ($this->allAreAttackingAcrossRiver($combatNumber)) {

        $terrainCombatEffect = $this->terrain->getAllAreAttackingAcrossRiverCombatEffect();
    }
    
	return $terrainCombatEffect;
}


function setCombatIndex($combatNumber)
{
    $attackStrength = $this->force->getAttackerStrength($combatNumber);
    $defenseStrength = $this->force->getDefenderStrength($combatNumber);

    $combatIndex = $attackStrength - $defenseStrength;

    $terrainCombatEffect = $this->getDefenderTerrainCombatEffect($combatNumber);

    $combatIndex -= $terrainCombatEffect;

    if ($combatIndex < 1) $combatIndex = 0;

    if ($combatIndex >= $this->crt->maxCombatIndex) {
        $combatIndex = $this->crt->maxCombatIndex;
    }

    $this->force->storeCombatIndex($combatNumber, $combatIndex);
}

function resolveCombat( $id ) {
 
    // Math->random yields number between 0 and 1
    //  6 * Math->random yields number between 0 and 6
    //  Math->floor gives lower integer, which is now 0,1,2,3,4,5

    $Die = floor($this->crt->dieSideCount * (rand()/getrandmax()));
    $index = $this->force->getUnitCombatIndex($id);
    $combatResults = $this->crt->getCombatResults($Die, $index);
    /*
     * TODO: is force really supposed to be $this->force?????
     */
    $this->force->applyCRTresults($this->force->getUnitCombatNumber($id), $combatResults, $Die);
    echo "Resolved";
}

function resolveFireCombat( $id ) {
}

function allAreAttackingAcrossRiver($combatNumber) {

    $allAttackingAcrossRiver = true;

     $attackerHexagonList = array();
    $attackerHexagonList = $this->force->getAttackerHexagonList($combatNumber);

    $defenderHexagon = $this->force->getCombatHexagon($combatNumber);

    for ($i = 0; $i < count($attackerHexagonList); $i++) {

        $hexsideX = ($defenderHexagon->getX() + $attackerHexagonList[$i]->getX()) / 2;
        $hexsideY = ($defenderHexagon->getY() + $attackerHexagonList[$i]->getY()) / 2;
        
        $hexside = new Hexpart($hexsideX, $hexsideY);
        
        if ($this->terrain->terrainIs($hexside, "river") == false) {

            $allAttackingAcrossRiver = false;
        }
    }
    
    return $allAttackingAcrossRiver;
}

function getCombatOddsList($combatIndex)
{
   return $this->crt->getCombatOddsList($combatIndex);
}
}