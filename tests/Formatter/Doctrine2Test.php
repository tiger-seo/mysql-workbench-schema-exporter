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

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
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
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown()
	{
	}

	public function testDefault()
	{
		$mappings = array(
			'Actor',
			'Address',
			'Category',
			'City',
			'Country',
			'Customer',
			'Film',
			'FilmText',
			'Inventory',
			'Language',
			'Payment',
			'Rental',
			'Staff',
			'Store',
		);
		$filename = realpath(__DIR__ . '/../fixtures/doctrine2/sakila.mwb');
		$outDir = realpath(__DIR__ . '/../temp');

		$document = $this->mwb->export($this->formatter, $filename, $outDir, 'file');

		foreach($mappings as $entityName) {
			$this->assertFileExists($outDir . '/' . $entityName . '.orm.yml');
		}
	}
}
