<?php
namespace ExternalModules;

require_once __DIR__ . '/../classes/framework/v2/Framework.php';

class FrameworkV2Test extends BaseTest
{
	function __construct()
	{
		$this->framework = new FrameworkVersion2\Framework(new BaseTestExternalModule());
	}

	protected function getReflectionClass()
	{
		return $this->framework;
	}

	function testGetSubSettings_complexNesting()
	{
		$m = $this->getInstance();
		$_GET['pid'] = 1;

		// This json file can be copied into a module for hands on testing/modification via the settings dialog.
		$this->setConfig(json_decode(file_get_contents(__DIR__ . '/complex-nested-settings.json'), true));

		// These values were copied directly from the database after saving them through the settings dialog (as configured by the json file above).
		$m->setProjectSetting('countries', ["true","true"]);
		$m->setProjectSetting('country-name', ["USA","Canada"]);
		$m->setProjectSetting('states', [["true","true"],["true"]]);
		$m->setProjectSetting('state-name', [["Tennessee","Alabama"],["Ontario"]]);
		$m->setProjectSetting('cities', [[["true","true"],["true"]],[["true"]]]);
		$m->setProjectSetting('city-name', [[["Nashville","Franklin"],["Huntsville"]],[["Toronto"]]]);
		$m->setProjectSetting('city-size', [[["large","small"],["medium"]],[[null]]]); // The null is an important scenario to test here, as it can change output behavior.

		$expectedCountries = [
			[
				"states" => [
					[
						"state-name" => "Tennessee",
						"cities" => [
							[
								"city-name" => "Nashville",
								"city-size" => "large"
							],
							[
								"city-name" => "Franklin",
								"city-size" => "small"
							]
						]
					],
					[
						"state-name" => "Alabama",
						"cities" => [
							[
								"city-name" => "Huntsville",
								"city-size" => "medium"
							]
						]
					]
				],
				"country-name" => "USA"
			],
			[
				"states" => [
					[
						"state-name" => "Ontario",
						"cities" => [
							[
								"city-name" => "Toronto",
								"city-size" => null
							]
						]
					]
				],
				"country-name" => "Canada"
			]
		];

		$this->assertEquals($expectedCountries, $this->framework->getSubSettings('countries'));
	}
}