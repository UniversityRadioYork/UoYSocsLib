<?php
// Imports, in turn, UoY_DateConstants.php
require_once dirname(__FILE__) . '/../UoY_Date.php';


/**
 * Test class for UoY_Date.
 * Generated by PHPUnit on 2011-07-07 at 14:50:04.
 */
class UoY_DateTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var UoY_Date
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new UoY_Date(
            2010,
            UoY_DateConstants::TERM_AUTUMN,
            false,
            1,
            UoY_DateConstants::DAY_MONDAY
        );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    /**
     * Tests the getYear function.
     */
    public function testGetYear()
    {
        $this->assertSame($this->object->getYear(), 2010);
        
        // Ensure the year is properly stored for next-calendar-year terms.
        $summerTest = new UoY_Date(
            2010,
            UoY_DateConstants::TERM_SUMMER,
            false,
            1,
            UoY_DateConstants::DAY_MONDAY
        );
        
        $this->assertSame($summerTest->getYear(), 2010);
    }
    
    /**
     * Data provider for testGetTerm.
     */
    public function dataForGetTerm()
    {
        return array(
            array(
                UoY_DateConstants::TERM_AUTUMN,
                UoY_DateConstants::NAME_TERM_AUTUMN,
                false
            ),
            array(
                UoY_DateConstants::TERM_SPRING,
                UoY_DateConstants::NAME_TERM_SPRING,
                false
            ),
            array(
                UoY_DateConstants::TERM_SUMMER,
                UoY_DateConstants::NAME_TERM_SUMMER,
                false
            ),
            array(
                UoY_DateConstants::BREAK_WINTER,
                UoY_DateConstants::NAME_BREAK_WINTER,
                true
            ),
            array(
                UoY_DateConstants::BREAK_SPRING,
                UoY_DateConstants::NAME_BREAK_SPRING,
                true
            ),
            array(
                UoY_DateConstants::BREAK_SUMMER,
                UoY_DateConstants::NAME_BREAK_SUMMER,
                true
            ),
        );
    }
    

    /**
     * Tests the getTerm function.
     * 
     * @dataProvider dataForGetTerm
     * 
     * @param integer term    The term for which the test object should be 
     *                        created.
     * 
     * @param integer name    The expected name of the above term.
     * 
     * @param integer isBreak Whether or not the test object's term is a break.
     */
    public function testGetTerm($term, $name, $isBreak)
    {
        $test = new UoY_Date(
            2010,
            $term,
            $isBreak,
            1,
            UoY_DateConstants::DAY_MONDAY
        );
        
        $this->assertEquals($test->getTerm(), $term);
    }

    /**
     * Tests the getTermName function.
     * 
     * @dataProvider dataForGetTerm
     * 
     * @param integer term    The term for which the test object should be 
     *                        created.
     * 
     * @param integer name    The expected name of the above term.
     * 
     * @param integer isBreak Whether or not the test object's term is a break.
     */
    public function testGetTermName($term, $name, $isBreak)
    {
        $test = new UoY_Date(
            2010,
            $term,
            $isBreak,
            1,
            UoY_DateConstants::DAY_MONDAY
        );
        
        $this->assertEquals($test->getTermName(), $name);
    }

    /**
     * Tests the isInBreak function.
     * 
     * @dataProvider dataForGetTerm
     * 
     * @param integer term    The term for which the test object should be 
     *                        created.
     * 
     * @param integer name    The expected name of the above term.
     * 
     * @param integer isBreak Whether or not the test object's term is a break.
     */
    public function testIsInBreak($term, $name, $isBreak)
    {
        $test = new UoY_Date(
            2010,
            $term,
            $isBreak,
            1,
            UoY_DateConstants::DAY_MONDAY
        );
        
        $this->assertEquals($test->isInBreak(), $isBreak);
    }

    /**
     * @todo Implement testGetWeek().
     */
    public function testGetWeek()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDay().
     */
    public function testGetDay()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetDayName().
     */
    public function testGetDayName()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testToString().
     */
    public function testToString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

}

?>
