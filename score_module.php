<?php

$js_options = [
	"hide_correct" => false,
	"scoreScreen" => [
		"tableTitle" => 'Responses',
		"tableHeaders" => [
			'Question Score',
			'The Question',
			'Your Response',
			'Correct Answer'
		]
	]
];

$questions = [
	'f8Fsjk3-dfjkl-s9394-23nkl' => [
		"materiaType" => 'question',
		"id" => 'f8Fsjk3-dfjkl-s9394-23nkl',
		"type" => 'QA',
		"questions" => [
			["text" => "2 + 2 = ?"]
		],
		"answers" => [
			[
				"text" => "2",
				"value" => 100,
				"options" => [
					"feedback" => "Try again!"
				]
			]
		],
		"options" => [
			"x" => 12,
			"y" => 234
		]
	]
];

$log = [
	"item_id" => 'f8Fsjk3-dfjkl-s9394-23nkl',
	"text" => "24"
];

$options_json = json_encode($js_options);
$questions_json = json_encode($questions);
$log_json = json_encode($log);

$js_vars = <<< EOT
const OPTIONS = JSON.parse('{$options_json}')
const QUESTIONS = JSON.parse('{$questions_json}')
const LOG = JSON.parse('{$log_json}')
EOT;

$js_execute = <<< EOT

let result = WidgetScoreModule.checkAnswer(OPTIONS, QUESTIONS, LOG)

result
EOT;

$constants = file_get_contents('./score_module_constants.js');
$score_module = file_get_contents('./score_module.js');

$v8 = new V8Js();
try {
	$script = $constants."\n".$js_vars."\n".$score_module."\n".$js_execute;
  $output = $v8->executeString($script, 'enigma_score_module', V8Js::FLAG_FORCE_ARRAY);
  print_r($output);
} catch (V8JsException $e) {
  var_dump($e);
}


