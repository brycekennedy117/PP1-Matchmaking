<?php

namespace Tests\Browser;

use App\Http\Controllers\MatchController;
use App\MingleLibrary\MatchMaker;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\User;
use App\MingleLibrary\Models\Match;
use Facebook\WebDriver\WebDriverBy;
use App\MingleLibrary\Models\UserAttributes;

class MatchPageTest extends DuskTestCase
{
    private $appUrl;
    private $user = null;

    protected function setUp(): void
    {
        parent::setUp();
        if ($this->user == null) {
            $this->appUrl = getenv('APP_URL', 'http://localhost:8888');
            $this->user = User::all()->where('id', '=',1)->first();                                                                                                                                                                                                                        ;
        }
        foreach (static::$browsers as $browser) {
            $browser->driver->manage()->deleteAllCookies();
        }
    }

    public function testPageLimit()
    {
        $this->browse(function (Browser $browser) {

            $userID = $this->user->id;
            $matches = $browser->loginAs($this->user)->visit('/matches')
                ->elements('#match-container');
            self::assertLessThanOrEqual(10, sizeof($matches), "Match Limit Is Greater Than 10");
            self::assertGreaterThanOrEqual(0, sizeof($matches), "Match Limit Is Less Than 0");
        });
    }

    public function testCellValues()
    {
        $this->browse(function (Browser $browser) {

            $userID = $this->user->id;
            //echo $userID;
            $matches1 = Match::all(['user_id_2', 'user_id_1'])
                ->where('user_id_1', $userID)->pluck('user_id_2');

            $matches2 = Match::all(['user_id_2', 'user_id_1'])
                ->where('user_id_2', $userID)->pluck('user_id_1');
            $matches =$matches1->merge($matches2)->unique();
            $attributes = UserAttributes::all()->whereIn('user_id', $matches->toArray());
            $attributesArray = array();
            foreach($attributes as $attribute) {
                array_push($attributesArray, $attribute);
            }

            $pageItems = $browser->loginAs($this->user)->visit('/matches')
                ->elements('.page-item');
            $numOfPages = sizeof($pageItems) - 2;
            $matches = $browser->loginAs($this->user)->visit('/matches')
                ->elements('#match-container');

            for($page=1; $page<=$numOfPages; $page++) {
                foreach($matches as $index => $match) {
                    $matchName =  $match->findElement(WebDriverBy::id('match-name'))->getText();
                    $matchSuburb =  $match->findElement(WebDriverBy::id('match-suburb'))->getText();
                    $matchDistance = $match->findElement(WebDriverBy::id('match-distance'))->getText();
                    $matchAttributes = $attributesArray[(($page*10)-10) +$index];
                    $user1PC = $this->user->Attributes->postcodeObject;
                    $user2PC = $matchAttributes->postcodeObject;

                    $attributeDistance = MatchController::distanceBetweenMatches($user1PC->latitude, $user1PC->longitude, $user2PC->latitude, $user2PC->longitude);

                    self::assertEquals($matchAttributes->user->name, $matchName, "Name displaying incorrectly in Match Page cell on page: $page / $numOfPages");
                    self::assertEquals($matchAttributes->postcodeObject->suburb, $matchSuburb, "Suburb displaying incorrectly in Match Page cell.");
                    self::assertEquals("Distance: ".$attributeDistance."km", $matchDistance, "Distance displaying incorrectly in Match Page cell.");

                }
                $matches = $browser->loginAs($this->user)->visit('/matches?page='.($page+1))
                    ->elements('#match-container');


            }


        });
    }

    public function testPagination()
    {
        $this->browse(function (Browser $browser) {

            $userID = $this->user->id;
            //echo $userID;
            $matches1 = Match::all(['user_id_2', 'user_id_1'])
                ->where('user_id_1', $userID)->pluck('user_id_2');

            $matches2 = Match::all(['user_id_2', 'user_id_1'])
                ->where('user_id_2', $userID)->pluck('user_id_1');
            $matches =$matches1->merge($matches2)->unique();
            $attributes = UserAttributes::all()->whereIn('user_id', $matches->toArray());
            $attributesArray = array();
            foreach($attributes as $attribute) {
                array_push($attributesArray, $attribute);
            }

            $pageItems = $browser->loginAs($this->user)->visit('/matches')
                ->elements('.page-item');
            $expectedNumberOfPages = ceil(sizeof($attributesArray)/10);
            echo sizeof($attributesArray);

            if ($expectedNumberOfPages <= 1) {
                $numOfPageItems = sizeof($pageItems);
            }
            else {
                $numOfPageItems = sizeof($pageItems) - 2;
            }
            self::assertEquals($expectedNumberOfPages, $numOfPageItems);
        });
    }

}
