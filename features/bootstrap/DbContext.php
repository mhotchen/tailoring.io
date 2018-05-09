<?php
/**
 * Created by PhpStorm.
 * User: mhn
 * Date: 08/05/18
 * Time: 10:04
 */

use Behat\Behat\Context\Context;

class DbContext implements Context
{
    /**
     * @Given /^the database should contain the submitted company$/
     */
    public function theDatabaseShouldContainTheSubmittedCompany()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException();
    }
}