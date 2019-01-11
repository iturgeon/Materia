// Score module functions used in all the widgets
// public function __construct($play_id, $inst)
// public function check_answer($log)
// protected function handle_log_question_answered($log)
// protected function handle_log_client_final_score($log)
// protected function calculate_score()
// protected function details_for_question_answered($log)
// protected function get_score_details()
// protected function handle_log_widget_interaction($log)
// public function get_ss_answer($log, $question)
// protected function get_feedback($log, $answers)
// protected function get_overview_items()
// protected function get_ss_expected_answers($log, $question)
// public function get_ss_question($log, $question)


class BaseScoreModule{

	static table_title(){
		return 'Responses:'
	}

	static table_headers(){
		return ['Question Score', 'The Question', 'Your Response', 'Correct Answer'];
	}

	static checkAnswer(options, questions, log) {
		return 0
	};

	static getScoreDetails(options, questions, logs){
		details = []

		for(const log on logs){
			if(log.type === TYPE_QUESTION_ANSWERED && questions[log.item_id]){
				details.push(this.getDetailsForAnsweredQuestion(options, questions, logs, log))
			}
		}

		return {
			title: this.table_title(),
			header: this.table_headers(),
			table: details
		}
	};

	static get_ss_answer(log, question)
	{
		return log.text
	}

	static get_ss_question(log, question)
	{
		return question.questions[0].text
	}

	static get_feedback(log, answers)
	{
		for(const answer of answers){
			if (log.text == answer.text && $answer.options.feedback && answer.options.feedback.length > 0)
			{
				return answer.options.feedback
			}
		}
	}

	static getDetailsForAnsweredQuestion(options, questions, logs, log){
		$q     = $this->questions[$log->item_id];
		$score = $this->check_answer($log);

		return [
			'data' => [
				$this->get_ss_question($log, $q),
				$this->get_ss_answer($log, $q),
				$this->get_ss_expected_answers($log, $q)
			],
			'data_style'    => ['question', 'response', 'answer'],
			'score'         => $score,
			'feedback'      => $this->get_feedback($log, $q->answers),
			'type'          => $log->type,
			'style'         => $this->get_detail_style($score),
			'tag'           => 'div',
			'symbol'        => '%',
			'graphic'       => 'score',
			'display_score' => true
		];
	}

	static get_detail_style(score){
		let style = '';
		switch (score)
		{
			case -1:
			case '-1':
				style = 'ignored-value';
				break;

			case 100:
			case '100':
				style = 'full-value';
				break;

			case '0':
			case 0:
				style = 'no-value';
				break;

			default:
				style = 'partial-value';
				break;
		}

		return style;
	}
}


class WidgetScoreModule extends BaseScoreModule{
	static checkAnswer(options, questions, log) {
		if(typeof questions[log.item_id] != 'undefined'){
			let q = questions[log.item_id];
			for(const answer of q.answers){
				if(log.text.trim() == answer.text.trim()){
					return answer.value
				}
			}
		}

		return 0
	}

	static getScoreDetails(options, questions, logs){
		if(!options.hideCorrect){
			return -1
		}

		let details = []
		logs.forEach(log => {
			if(log.type === TYPE_QUESTION_ANSWERED && questions[log.item_id])
				details.push(this.getDetailsForAnsweredQuestion(options, questions, logs, log))
			}
		})

		return [{
			title: options.scoreScreen.tableTitle,
			header: ['Score', 'The Question', 'Your Response'],
			table: details
		}]
	}

	static getDetailsForAnsweredQuestion(options, questions, logs, log){
		if ( ! options.hide_correct){
			return BaseScoreModule.details_for_question_answered(options, questions, logs, log)
		}

		const q = questions[log.item_id]
		const score = this.checkAnswer(log)
		return {
			data: [
				this.get_ss_question(log, q),
				this.get_ss_answer(log, q),
			],
			data_style: ['question', 'response'],
			score: score,
			feedback: this.get_feedback(log, q.answers),
			type: log.type,
			style: this.get_detail_style(score),
			tag: 'div',
			symbol: '%',
			graphic: 'score',
			display_score: true
		}
	}
}
