<?php

use Tribe\Tickets\Test\Traits\REST\Auth;


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class Restv1Tester extends \Codeception\Actor
{
    use _generated\Restv1TesterActions;
    use Auth;

   /**
    * Define custom actions here
    */
}
