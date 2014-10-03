<?php

namespace OroCRM\Bundle\SalesBundle\Tests\Selenium\Pages;

use Oro\Bundle\TestFrameworkBundle\Pages\AbstractPageEntity;
use OroCRM\Bundle\ContactBundle\Tests\Selenium\Pages\Contacts;

/**
 * Class Opportunity
 *
 * @package OroCRM\Bundle\SalesBundle\Tests\Selenium\Pages
 */
class Opportunity extends AbstractPageEntity
{
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $name;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $contact;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $b2b_customer;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $probability;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $budget;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $customerNeed;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $proposedSolution;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element_Select */
    protected $closeReason;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $closeRevenue;
    /** @var  \PHPUnit_Extensions_Selenium2TestCase_Element */
    protected $closeDate;

    protected $owned = "//div[starts-with(@id,'s2id_orocrm_sales_opportunity_form_owner')]/a";

    public function __construct($testCase, $redirect = true)
    {
        parent::__construct($testCase, $redirect);
    }

    public function init()
    {
        $this->name = $this->test->byId('orocrm_sales_opportunity_form_name');
        $this->contact = $this->test->byXpath("//div[starts-with(@id,'s2id_orocrm_sales_opportunity_form_contact')]/a");
        $this->b2b_customer = $this->test
            ->byXpath("//div[starts-with(@id,'s2id_orocrm_sales_opportunity_form_customer')]/a");
        $this->probability = $this->test->byId('orocrm_sales_opportunity_form_probability');
        $this->budget = $this->test->byId('orocrm_sales_opportunity_form_budgetAmount');
        $this->customerNeed = $this->test->byId('orocrm_sales_opportunity_form_customerNeed');
        $this->proposedSolution = $this->test->byId('orocrm_sales_opportunity_form_proposedSolution');
        $this->closeReason = $this->test->select($this->test->byId('orocrm_sales_opportunity_form_closeReason'));
        $this->closeRevenue = $this->test->byId('orocrm_sales_opportunity_form_closeRevenue');
        $this->closeDate = $this->test->byId('date_selector_orocrm_sales_opportunity_form_closeDate');

        return $this;
    }

    public function setName($name)
    {
        $this->name->clear();
        $this->name->value($name);

        return $this;
    }

    public function getName()
    {
        return $this->name->value();
    }

    public function setContact($contact)
    {
        $this->contact->click();
        $this->waitForAjax();
        $this->test->byXpath("//div[@id='select2-drop']/div/input")->value($contact);
        $this->waitForAjax();
        $this->assertElementPresent(
            "//div[@id='select2-drop']//div[contains(., '{$contact}')]",
            "Contact autocomplete doesn't return search value"
        );
        $this->test->byXpath("//div[@id='select2-drop']//div[contains(., '{$contact}')]")->click();

        return $this;
    }

    public function getContact()
    {
        return $this->test->byXpath(
            "//div[starts-with(@id,'s2id_orocrm_sales_opportunity_form_contact')]/a/span"
        )->text();
    }

    public function setB2BCustomer($customer)
    {
        $this->b2b_customer->click();
        $this->waitForAjax();
        $this->test->byXpath("//div[@id='select2-drop']/div/input")->value($customer);
        $this->waitForAjax();
        $this->assertElementPresent(
            "//div[@id='select2-drop']//div[contains(., '{$customer}')]",
            "Account autocomplete doesn't return search value"
        );
        $this->test->byXpath("//div[@id='select2-drop']//div[contains(., '{$customer}')]")->click();

        return $this;
    }

    public function getB2BCustomer()
    {
        return $this->test
            ->byXpath("//div[starts-with(@id,'s2id_orocrm_sales_opportunity_form_customer')]/a/span")->text();
    }

    public function setProbability($probability)
    {
        $this->probability->clear();
        $this->probability->value($probability);

        return $this;
    }

    public function getProbability()
    {
        return $this->probability->value();
    }

    public function seBudget($budget)
    {
        $this->budget->clear();
        $this->budget->value($budget);

        return $this;
    }

    public function getBudget()
    {
        return $this->budget->value();
    }

    public function setCustomerNeed($customerNeed)
    {
        $this->customerNeed->clear();
        $this->customerNeed->value($customerNeed);

        return $this;
    }

    public function getCustomerNeed()
    {
        return $this->customerNeed->value();
    }

    public function setProposedSolution($proposedSolution)
    {
        $this->proposedSolution->clear();
        $this->proposedSolution->value($proposedSolution);

        return $this;
    }

    public function getPhone()
    {
        return $this->proposedSolution->value();
    }

    public function setCloseReason($closeReason)
    {
        $this->closeReason->selectOptionByLabel($closeReason);

        return $this;
    }

    public function setCloseRevenue($closeRevenue)
    {
        $this->closeRevenue->clear();
        $this->closeRevenue->value($closeRevenue);

        return $this;
    }

    public function getCloseRevenue()
    {
        return $this->closeRevenue->value();
    }

    public function setCloseDate($closeDate)
    {
        $this->closeDate->clear();
        $this->closeDate->value($closeDate);

        return $this;
    }

    public function getCloseDate()
    {
        return $this->closeDate->value();
    }

    public function checkStatus($status)
    {
        $this->assertElementPresent("//div[starts-with(@class, 'badge')][contains(., '{$status}')]");

        return $this;
    }

    public function edit()
    {
        $this->test
            ->byXpath("//div[@class='pull-left btn-group icons-holder']/a[@title = 'Edit Opportunity']")
            ->click();
        $this->waitPageToLoad();
        $this->waitForAjax();
        $this->init();

        return $this;
    }

    public function delete()
    {
        $this->test->byXpath("//div[@class='pull-left btn-group icons-holder']/a[contains(., 'Delete')]")->click();
        $this->test->byXpath("//div[div[contains(., 'Delete Confirmation')]]//a[text()='Yes, Delete']")->click();
        $this->waitPageToLoad();
        $this->waitForAjax();

        return new Contacts($this->test, false);
    }
}
