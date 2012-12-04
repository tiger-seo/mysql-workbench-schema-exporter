<?php

use MwbExporter\Formatter\Doctrine2\Yaml\Formatter;

/**
 * @author tiger
 */
class Doctrine2Test extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var \MwbExporter\Bootstrap
	 */
	protected $mwb;

	/**
	 * @var \MwbExporter\Formatter\Doctrine2\Yaml\Formatter
	 */
	protected $formatter;

	protected $fixturesPath;
	protected $mappingsPath;
	protected $tempPath;

	public static function setUpBeforeClass()
	{
		parent::setUpBeforeClass();
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->fixturesPath = realpath(__DIR__ . '/../fixtures/doctrine2') . DIRECTORY_SEPARATOR;
		$this->mappingsPath = $this->fixturesPath . 'mappings' . DIRECTORY_SEPARATOR;
		$this->tempPath = realpath(__DIR__ . '/../temp') . DIRECTORY_SEPARATOR;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function mappingsProvider()
	{
		// formatter setup
		$setup = array(
		    Formatter::CFG_USE_LOGGED_STORAGE            => true,
		    Formatter::CFG_INDENTATION                   => 4,
		    Formatter::CFG_FILENAME                      => '%entity%.orm.%extension%',
		    Formatter::CFG_BACKUP_FILE                   => false,
		    Formatter::CFG_BUNDLE_NAMESPACE              => 'Test\TestBundle',
		    Formatter::CFG_ENTITY_NAMESPACE              => '',
		    Formatter::CFG_REPOSITORY_NAMESPACE          => '',
		    Formatter::CFG_EXTEND_TABLENAME_WITH_SCHEMA  => false,
		    Formatter::CFG_AUTOMATIC_REPOSITORY          => false,
		);

		$this->mwb = new \MwbExporter\Bootstrap();
		$this->formatter = $this->mwb->getFormatter('doctrine2-yaml');
		$this->formatter->setup($setup);

		$this->setUp();

		$filename = $this->fixturesPath . 'sakila.mwb';
		$document = $this->mwb->export($this->formatter, $filename, $this->tempPath, 'file');

		return array(
			array('Actor'),
			array('Address'),
			array('Category'),
			array('City'),
			array('Country'),
			array('Customer'),
			array('Film'),
			array('FilmText'),
			array('Inventory'),
			array('Language'),
			array('Payment'),
			array('Rental'),
			array('Staff'),
			array('Store'),
		);
	}

	/**
	 * @param $entityName@
	 * @dataProvider mappingsProvider
	 */
	public function testGeneratedMapping($entityName)
	{
		$this->assertFileExists($this->tempPath . '/' . $entityName . '.orm.yml');
		$this->assertFileEquals($this->tempPath . '/' . $entityName . '.orm.yml', $this->mappingsPath . $entityName . '.orm.yml');
	}
}
