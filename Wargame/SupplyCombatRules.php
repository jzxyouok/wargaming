<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 12/5/16
 * Time: 9:05 PM
 *
 * /*
 * Copyright 2012-2016 David Rodal
 * This program is free software; you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wargame;
use Wargame\Battle;

use stdClass;

class SupplyCombatRules extends CombatRules
{

    public function selectSupply(MovableUnit $unit){
        if($unit->maxMove === 10){
            $unit->maxMove = 6;
            $unit->status = STATUS_UNAVAIL_THIS_PHASE;
        }else{
            $unit->maxMove = 10;
            $unit->status = STATUS_READY;
        }
    }
}